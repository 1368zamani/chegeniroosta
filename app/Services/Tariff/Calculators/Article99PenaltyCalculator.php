<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

/**
 * جریمه تخلفات ساختمانی کمیسیون ماده ۹۹ — P01
 *
 * ساختار جدول‌محور:
 *  - نوع بنا × طبقه × مرحله ساخت → ضریب
 *  - مبلغ پایه = مساحت × نرخ روز
 *
 * ⚠️ TODO(verify): جدول دقیق ماده ۹۹ در متن OCR نبود. این پیاده‌سازی ساختار را می‌سازد
 * و ضرایب باید از itemParam وارد شود.
 */
class Article99PenaltyCalculator extends BaseCalculator
{
    public function key(): string { return 'article_99_penalty'; }
    public function label(): string { return 'جریمه ماده ۹۹'; }

    public function inputSchema(TariffItem $item): array
    {
        return [
            ['key' => 'building_type', 'label' => 'نوع بنا', 'type' => 'select', 'required' => true, 'options' => [
                'residential' => 'مسکونی',
                'commercial'  => 'تجاری',
                'industrial'  => 'صنعتی',
            ]],
            ['key' => 'stage', 'label' => 'مرحله ساخت', 'type' => 'select', 'required' => true, 'options' => [
                'foundation' => 'فونداسیون',
                'skeleton'   => 'اسکلت',
                'roof'       => 'سقف',
                'finish'     => 'نازک‌کاری و پایان',
            ]],
            ['key' => 'floors', 'label' => 'تعداد طبقات', 'type' => 'number', 'required' => true],
            ['key' => 'area',   'label' => 'مساحت', 'type' => 'number', 'unit' => 'مترمربع', 'required' => true],
        ];
    }

    public function calculate(TariffItem $item, array $inputs, ?TariffSet $set = null): CalculationResult
    {
        $type   = $this->requiredChoice($inputs, 'building_type', ['residential','commercial','industrial'], 'نوع بنا');
        $stage  = $this->requiredChoice($inputs, 'stage', ['foundation','skeleton','roof','finish'], 'مرحله');
        $floors = $this->requiredNumber($inputs, 'floors', 'تعداد طبقات');
        $area   = $this->requiredNumber($inputs, 'area', 'مساحت');

        // ضرایب از itemParam (اگر نبود پیش‌فرض منطقی)
        $baseRate = (float) $this->itemParam($item, 'base_rate_per_m2', 500000);
        $typeFactor = (float) $this->itemParam($item, "type_factor_$type", $type === 'residential' ? 1.0 : ($type === 'commercial' ? 2.0 : 2.5));
        $stageFactor = (float) $this->itemParam($item, "stage_factor_$stage", match($stage) {
            'foundation' => 0.25,
            'skeleton'   => 0.50,
            'roof'       => 0.75,
            'finish'     => 1.00,
        });
        $floorFactor = (float) $this->itemParam($item, 'floor_multiplier', 1.0) + (($floors - 1) * (float) $this->itemParam($item, 'extra_floor_factor', 0.15));

        $base = $area * $baseRate;
        $afterType = $base * $typeFactor;
        $afterStage = $afterType * $stageFactor;
        $total = $this->toInt($afterStage * $floorFactor);

        return new CalculationResult(
            total: $total,
            steps: [
                $this->step('مساحت × نرخ پایه', $this->toInt($base), number_format($area) . ' م² × ' . number_format($baseRate)),
                $this->step('ضریب نوع بنا', $this->toInt($afterType), '× ' . $typeFactor),
                $this->step('ضریب مرحله ساخت', $this->toInt($afterStage), '× ' . $stageFactor),
                $this->step('ضریب طبقات', $total, '× ' . number_format($floorFactor, 2)),
            ],
            meta: ['type' => $type, 'stage' => $stage, 'floors' => $floors, 'non_installment' => true],
        );
    }
}
