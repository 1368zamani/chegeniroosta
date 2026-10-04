<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

/**
 * عوارض سالیانه املاک و مستقلات — L01
 *
 * فرمول (متن ماده): A = (P·S + P1·M) × k × B
 *  P  = ارزش هر متر زمین (ریال) — از setParam
 *  S  = مساحت زمین (m²) — ورودی کاربر
 *  P1 = ارزش هر متر بنا (ریال) — از setParam
 *  M  = مساحت بنا (m²) — ورودی کاربر
 *  k  = ضریب تعدیل (پیش‌فرض 0.115 = ۱۱٫۵٪) — از setParam
 *  B  = ضریب کاربری (مسکونی 12%, تجاری 20%, ...) — از itemParam
 *
 * TODO(verify): ترتیب پرانتزها در متن OCR مبهم بود. نسخه‌ی فعلی بر اساس تفسیر رایج است.
 * TODO(verify): ضریب B بر اساس نمونه‌ی اردبیل؛ نرخ رسمی چگنی باید در پنل ادمین وارد شود.
 */
class AnnualPropertyLevyCalculator extends BaseCalculator
{
    public function key(): string { return 'annual_property'; }
    public function label(): string { return 'عوارض سالیانه املاک و مستقلات'; }

    public function inputSchema(TariffItem $item): array
    {
        return [
            ['key' => 'land_area',     'label' => 'مساحت زمین',        'type' => 'number', 'unit' => 'مترمربع', 'required' => true],
            ['key' => 'building_area', 'label' => 'مساحت بنا',         'type' => 'number', 'unit' => 'مترمربع', 'required' => true],
            ['key' => 'usage',         'label' => 'کاربری',             'type' => 'select', 'required' => true, 'options' => $this->usageOptions()],
        ];
    }

    public function calculate(TariffItem $item, array $inputs, ?TariffSet $set = null): CalculationResult
    {
        $S  = $this->requiredNumber($inputs, 'land_area', 'مساحت زمین');
        $M  = $this->requiredNumber($inputs, 'building_area', 'مساحت بنا');
        $u  = $this->requiredChoice($inputs, 'usage', array_keys($this->usageOptions()), 'کاربری');

        $P  = (float) $this->setParam($set, 'land_value_per_m2', 0);
        $P1 = (float) $this->setParam($set, 'building_value_per_m2', 0);
        $k  = (float) $this->setParam($set, 'adjustment_coefficient', 0.115);
        $B  = (float) $this->itemParam($item, "usage_factor_$u", 0.12);

        if ($P <= 0 || $P1 <= 0) {
            // اگر پارامتر سراسری تنظیم نشده، خطا بده — نباید hardcode شود
            $P = $P > 0 ? $P : (float) $this->itemParam($item, 'land_value_per_m2', 0);
            $P1 = $P1 > 0 ? $P1 : (float) $this->itemParam($item, 'building_value_per_m2', 0);
        }

        $landValue  = $P * $S;
        $buildValue = $P1 * $M;
        $subtotal   = $landValue + $buildValue;
        $afterK     = $subtotal * $k;
        $total      = $this->toInt($afterK * $B);

        return new CalculationResult(
            total: $total,
            steps: [
                $this->step('ارزش زمین',  $this->toInt($landValue),  sprintf('%s م² × %s ریال', number_format($S), number_format($P))),
                $this->step('ارزش بنا',   $this->toInt($buildValue), sprintf('%s م² × %s ریال', number_format($M), number_format($P1))),
                $this->step('جمع ارزش',   $this->toInt($subtotal),   null),
                $this->step('اعمال ضریب تعدیل', $this->toInt($afterK), '× ' . $k),
                $this->step('اعمال ضریب کاربری', $total, '× ' . $B),
            ],
            meta: ['usage' => $u, 'k' => $k, 'B' => $B],
        );
    }

    private function usageOptions(): array
    {
        return [
            'residential' => 'مسکونی (۱۲٪)',
            'commercial'  => 'تجاری (۲۰٪)',
            'industrial'  => 'صنعتی (۱۳٪)',
            'office'      => 'اداری (۹٪)',
            'public'      => 'عمومی (۹٪)',
            'tourism'     => 'گردشگری (۹٪)',
            'sport'       => 'ورزشی (۹٪)',
            'agriculture' => 'کشاورزی (۷٪)',
            'workshop'    => 'کارگاهی (۷٪)',
        ];
    }
}
