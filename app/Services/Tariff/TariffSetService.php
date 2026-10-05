<?php

namespace App\Services\Tariff;

use App\Models\TariffItem;
use App\Models\TariffParameter;
use App\Models\TariffSet;
use App\Models\TariffTitle;
use App\Models\User;
use App\Models\Village;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * سرویس مدیریت مجموعه تعرفه سالانه یک روستا.
 *
 * مسئولیت‌ها:
 *  - ایجاد مجموعه جدید (خالی یا کپی از سال قبل)
 *  - تغییر وضعیت workflow با اعتبارسنجی
 *  - اعتبارسنجی نرخ افزایش در برابر تورم (ماده ۸)
 *  - قفل کردن پس از finalized
 *  - چک مهلت قانونی (۱۵ بهمن و پایان بهمن)
 *
 * ⚠️ TODO(verify): نرخ تورم پیش‌فرض و مهلت‌ها از جدول تنظیمات خوانده شوند.
 */
class TariffSetService
{
    public function createForVillage(
        Village $village,
        int $year,
        ?TariffSet $copyFrom = null,
        ?User $creator = null
    ): TariffSet {
        if (TariffSet::where('village_id', $village->id)->where('jalali_year', $year)->exists()) {
            throw new InvalidArgumentException("برای روستای {$village->name} در سال {$year} قبلاً مجموعه ساخته شده است.");
        }

        return DB::transaction(function () use ($village, $year, $copyFrom, $creator) {
            [$deadlineApproval, $deadlineAnnouncement] = $this->legalDeadlinesForYear($year);

            $set = TariffSet::create([
                'village_id'            => $village->id,
                'jalali_year'           => $year,
                'inflation_rate'        => $this->defaultInflationForYear($year),
                'status'                => 'draft',
                'created_by'            => $creator?->id,
                'deadline_approval'     => $deadlineApproval,
                'deadline_announcement' => $deadlineAnnouncement,
            ]);

            if ($copyFrom) {
                $this->copyFromPreviousYear($set, $copyFrom);
            } else {
                $this->seedEmptyItems($set);
            }

            return $set;
        });
    }

    public function copyFromPreviousYear(TariffSet $newSet, TariffSet $oldSet): void
    {
        foreach ($oldSet->parameters as $param) {
            TariffParameter::create([
                'tariff_set_id' => $newSet->id,
                'key'           => $param->key,
                'value'         => $param->value,
                'unit'          => $param->unit,
                'description'   => $param->description,
            ]);
        }

        foreach ($oldSet->items as $oldItem) {
            $increase = (float) $newSet->inflation_rate;

            TariffItem::create([
                'tariff_set_id'    => $newSet->id,
                'tariff_title_id'  => $oldItem->tariff_title_id,
                'is_active'        => $oldItem->is_active,
                'previous_amount'  => $oldItem->final_amount,
                'increase_percent' => $increase,
                'params'           => $oldItem->params,
                'final_amount'     => $this->applyIncrease($oldItem->final_amount, $increase),
                'unit'             => $oldItem->unit,
                'notes'            => $oldItem->notes,
                'formula_display'  => $oldItem->formula_display,
            ]);
        }
    }

    public function seedEmptyItems(TariffSet $set): void
    {
        foreach (TariffTitle::active()->orderBy('sort_order')->get() as $title) {
            TariffItem::updateOrCreate(
                ['tariff_set_id' => $set->id, 'tariff_title_id' => $title->id],
                [
                    'is_active'        => !$title->is_optional,
                    'previous_amount'  => 0,
                    'increase_percent' => 0,
                    'final_amount'     => 0,
                    'unit'             => 'rial',
                ]
            );
        }
    }

    /** اعتبارسنجی نرخ تورم (ماده ۸) */
    public function validateInflation(TariffSet $set): array
    {
        $violations = [];

        foreach ($set->items()->with('title')->get() as $item) {
            if (!$item->is_active) continue;
            $increase = (float) $item->increase_percent;
            $max      = (float) $set->inflation_rate;
            if ($increase > $max) {
                $violations[] = [
                    'title'    => $item->title?->name_fa ?? '-',
                    'increase' => $increase,
                    'max'      => $max,
                ];
            }
        }

        return ['ok' => empty($violations), 'violations' => $violations];
    }

    /** چک مهلت قانونی */
    public function isPastDeadline(TariffSet $set): bool
    {
        if (!$set->deadline_approval) return false;
        $today = \Morilog\Jalali\Jalalian::now()->format('Y/m/d');
        return $today > $set->deadline_approval;
    }

    public function assertDeadlineNotPassed(TariffSet $set): void
    {
        if ($this->isPastDeadline($set)) {
            throw new InvalidArgumentException(
                'مهلت قانونی تغییر تعرفه (۱۵ بهمن) گذشته است. تغییر عناوین ممنوع است.'
            );
        }
    }

    /** transitions */
    public function propose(TariffSet $set): void
    {
        $this->assertStatus($set, 'draft');
        $this->assertDeadlineNotPassed($set);
        $set->update(['status' => 'proposed']);
    }

    public function approveByVillageCouncil(TariffSet $set): void
    {
        $this->assertStatus($set, 'proposed');

        $v = $this->validateInflation($set);
        if (!$v['ok']) {
            $msgs = array_map(fn($x) => "«{$x['title']}» ({$x['increase']}% > {$x['max']}%)", $v['violations']);
            throw new InvalidArgumentException('افزایش از تورم بیشتر: ' . implode('، ', $msgs));
        }

        $set->update(['status' => 'village_council_approved', 'approved_at' => now()]);
    }

    public function approveByDistrictCouncil(TariffSet $set): void
    {
        $this->assertStatus($set, 'village_council_approved');
        $set->update(['status' => 'district_council_approved']);
    }

    public function finalize(TariffSet $set): void
    {
        $this->assertStatus($set, 'district_council_approved');
        $set->update(['status' => 'finalized', 'locked_at' => now()]);
    }

    public function publish(TariffSet $set): void
    {
        $this->assertStatus($set, 'finalized');
        $set->update(['status' => 'published', 'announced_at' => now()]);
    }

    public function applyIncrease(int $previous, float $percent): int
    {
        return (int) round($previous * (1 + $percent / 100));
    }

    private function defaultInflationForYear(int $year): float
    {
        // TODO(verify): از جدول تنظیمات خوانده شود
        return match ($year) {
            1404 => 35.0,
            1405 => 40.0,
            default => 30.0,
        };
    }

    private function legalDeadlinesForYear(int $year): array
    {
        // TODO(verify): از جدول تنظیمات خوانده شود
        return [
            sprintf('%04d/11/15', $year),
            sprintf('%04d/12/29', $year),
        ];
    }

    private function assertStatus(TariffSet $set, string $expected): void
    {
        if ($set->status !== $expected) {
            throw new InvalidArgumentException(
                "وضعیت فعلی «{$set->statusLabel()}» است، انتظار «{$expected}»."
            );
        }
    }
}
