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
 *  - اعتبارسنجی نرخ افزایش در برابر تورم
 *  - قفل کردن پس از finalized
 *
 * ⚠️ تمام مبالغ به ریال و به صورت bigint ذخیره می‌شوند.
 * ⚠️ TODO(verify): مهلت‌های قانونی (۱۵ و پایان بهمن) در متد createForVillage هاردکد شده — در صورت نیاز از settings بخوان.
 */
class TariffSetService
{
    /**
     * ساخت مجموعه تعرفه برای یک روستا در یک سال مشخص.
     *
     * @param Village    $village   روستا
     * @param int        $year      سال شمسی (مثلاً 1405)
     * @param TariffSet|null $copyFrom  مجموعه سال قبل برای کپی (اختیاری)
     * @param User|null  $creator   سازنده
     */
    public function createForVillage(
        Village $village,
        int $year,
        ?TariffSet $copyFrom = null,
        ?User $creator = null
    ): TariffSet {
        // اگر قبلاً برای این روستا و سال مجموعه‌ای هست، خطا
        $existing = TariffSet::where('village_id', $village->id)
            ->where('jalali_year', $year)
            ->first();

        if ($existing) {
            throw new InvalidArgumentException(
                "برای روستای {$village->name} در سال {$year} قبلاً مجموعه تعرفه ساخته شده است."
            );
        }

        return DB::transaction(function () use ($village, $year, $copyFrom, $creator) {
            // نرخ تورم پیش‌فرض سال (TODO(verify): از جدول تنظیمات بخوان)
            $defaultInflation = $this->defaultInflationForYear($year);

            // مهلت‌های قانونی (TODO(verify))
            [$deadlineApproval, $deadlineAnnouncement] = $this->legalDeadlinesForYear($year);

            $set = TariffSet::create([
                'village_id'            => $village->id,
                'jalali_year'           => $year,
                'inflation_rate'        => $defaultInflation,
                'status'                => 'draft',
                'created_by'            => $creator?->id,
                'deadline_approval'     => $deadlineApproval,
                'deadline_announcement' => $deadlineAnnouncement,
            ]);

            // اگر copyFrom داده شد، کپی کن
            if ($copyFrom) {
                $this->copyFromPreviousYear($set, $copyFrom);
            } else {
                // در غیر این صورت، همه عناوین فعال کاتالوگ را با مقدار صفر اضافه کن
                $this->seedEmptyItems($set);
            }

            return $set;
        });
    }

    /**
     * کپی از مجموعه سال قبل، با اعمال درصد افزایش پیش‌فرض = نرخ تورم.
     */
    public function copyFromPreviousYear(TariffSet $newSet, TariffSet $oldSet): void
    {
        // کپی پارامترها
        foreach ($oldSet->parameters as $param) {
            TariffParameter::create([
                'tariff_set_id' => $newSet->id,
                'key'           => $param->key,
                'value'         => $param->value,
                'unit'          => $param->unit,
                'description'   => $param->description,
            ]);
        }

        // کپی آیتم‌ها
        foreach ($oldSet->items as $oldItem) {
            $increase = (float) $newSet->inflation_rate; // پیش‌فرض = تورم

            TariffItem::create([
                'tariff_set_id'    => $newSet->id,
                'tariff_title_id'  => $oldItem->tariff_title_id,
                'is_active'        => $oldItem->is_active,
                'previous_amount'  => $oldItem->final_amount, // مبلغ سال قبل = مبلغ نهایی سال پیش
                'increase_percent' => $increase,
                'params'           => $oldItem->params,
                'final_amount'     => $this->applyIncrease($oldItem->final_amount, $increase),
                'unit'             => $oldItem->unit,
                'notes'            => $oldItem->notes,
                'formula_display'  => $oldItem->formula_display,
            ]);
        }
    }

    /**
     * اضافه کردن همه عناوین کاتالوگ با مقدار صفر.
     */
    public function seedEmptyItems(TariffSet $set): void
    {
        $titles = TariffTitle::active()->orderBy('sort_order')->get();

        foreach ($titles as $title) {
            TariffItem::updateOrCreate(
                [
                    'tariff_set_id'   => $set->id,
                    'tariff_title_id' => $title->id,
                ],
                [
                    'is_active'        => !$title->is_optional, // اختیاری پیش‌فرض غیرفعال
                    'previous_amount'  => 0,
                    'increase_percent' => 0,
                    'final_amount'     => 0,
                    'unit'             => 'rial',
                ]
            );
        }
    }

    /**
     * اعتبارسنجی: آیا هیچ آیتمی از نرخ تورم بیشتر افزایش یافته؟
     *
     * @return array{ok: bool, violations: array<int, array{title: string, increase: float, max: float}>}
     */
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

        return [
            'ok'         => empty($violations),
            'violations' => $violations,
        ];
    }

    /**
     * تغییر وضعیت از draft به proposed.
     */
    public function propose(TariffSet $set): void
    {
        $this->assertStatus($set, 'draft');
        $this->assertDeadlineNotPassed($set);
        $set->update(['status' => 'proposed']);
    }

    /**
     * تصویب شورای روستا (از proposed).
     * ⚠️ قبل از این، اعتبارسنجی تورم باید انجام شود.
     */
    public function approveByVillageCouncil(TariffSet $set): void
    {
        $this->assertStatus($set, 'proposed');

        $validation = $this->validateInflation($set);
        if (!$validation['ok']) {
            $msgs = array_map(
                fn($v) => "«{$v['title']}» ({$v['increase']}% > {$v['max']}%)",
                $validation['violations']
            );
            throw new InvalidArgumentException(
                "نرخ افزایش از تورم بیشتر است در: " . implode('، ', $msgs)
            );
        }

        $set->update([
            'status'      => 'village_council_approved',
            'approved_at' => now(),
        ]);
    }

    /**
     * تأیید شورای بخش.
     */
    public function approveByDistrictCouncil(TariffSet $set): void
    {
        $this->assertStatus($set, 'village_council_approved');
        $set->update(['status' => 'district_council_approved']);
    }

    /**
     * قطعی کردن (بعد از تأیید استاندار/هیئت تطبیق).
     * بعد از این، مجموعه قفل می‌شود.
     */
    public function finalize(TariffSet $set): void
    {
        $this->assertStatus($set, 'district_council_approved');
        $set->update([
            'status'    => 'finalized',
            'locked_at' => now(),
        ]);
    }

    /**
     * اعلام عمومی.
     */
    public function publish(TariffSet $set): void
    {
        $this->assertStatus($set, 'finalized');
        $set->update([
            'status'       => 'published',
            'announced_at' => now(),
        ]);
    }

    /**
     * محاسبه مبلغ جدید بر اساس مبلغ قبلی و درصد افزایش.
     */
    public function applyIncrease(int $previousAmount, float $increasePercent): int
    {
        return (int) round($previousAmount * (1 + $increasePercent / 100));
    }

    /**
     * مقدار پیش‌فرض نرخ تورم هر سال.
     * TODO(verify): به جدول تنظیمات منتقل شود.
     */
    private function defaultInflationForYear(int $year): float
    {
        // نمونه — قابل تنظیم
        $rates = [
            1404 => 35.0,
            1405 => 40.0,
        ];
        return $rates[$year] ?? 0.0;
    }

    /**
     * مهلت‌های قانونی هر سال.
     * TODO(verify): از تنظیمات بخوان.
     */
    private function legalDeadlinesForYear(int $year): array
    {
        // ۱۵ بهمن و پایان بهمن
        return [
            sprintf('%d-11-15', $year),
            sprintf('%d-12-29', $year),
        ];
    }

    /**
     * اطمینان از اینکه مجموعه در وضعیت مورد انتظار است.
     */
    private function assertStatus(TariffSet $set, string $expected): void
    {
        if ($set->status !== $expected) {
            throw new InvalidArgumentException(
                "وضعیت فعلی «{$set->statusLabel()}» است، اما انتظار «{$expected}» می‌رفت."
            );
        }
    }

    /**
     * آیا مهلت قانونی تصویب گذشته است؟
     * طبق قانون: مهلت تصویب ۱۵ بهمن، اعلام عمومی تا پایان بهمن.
     */
    public function isPastDeadline(TariffSet $set): bool
    {
        if (!$set->deadline_approval) return false;

        // مقایسه شمسی — تاریخ‌ها به صورت رشته "1405/11/15" ذخیره می‌شوند
        $today = \Morilog\Jalali\Jalalian::now()->format('Y/m/d');
        return $today > $set->deadline_approval;
    }

    /**
     * اطمینان از اینکه مهلت نگذشته (برای تغییرات پس از finalized).
     */
    public function assertDeadlineNotPassed(TariffSet $set): void
    {
        if ($this->isPastDeadline($set)) {
            throw new InvalidArgumentException(
                'مهلت قانونی تغییر تعرفه (۱۵ بهمن) گذشته است. تغییر عناوین و ضوابط ممنوع است.'
            );
        }
    }
}
