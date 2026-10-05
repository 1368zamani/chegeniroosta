<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffParameter;
use App\Models\TariffSet;

/**
 * زیربنا (مسکونی/غیرمسکونی/بالکن/پیش‌آمدگی/اضافه بنا/مازاد تراکم).
 *
 * فرمول نامزد: A = P × P1 × M × B
 *   P  = ارزش پایه هر مترمربع زیربنا (پارامتر building_value_per_m2)
 *   P1 = ضریب ۱۱٫۵٪ (پارامتر adjustment_coefficient)
 *   M  = مساحت زیربنا (ورودی area)
 *   B  = ضریب نوع (params → type_factors یا ورودی type)
 *
 * TODO(verify): واحد B در OCR مبهم بود — ممکن است ضریب یا درصد باشد.
 *               فیلد unit برای نوع ضریب نگه داشته شده است.
 */
class SubBuildingCalculator implements TariffCalculator
{
    public function key(): string { return 'sub_building'; }
    public function label(): string { return 'عوارض زیربنا و اضافه بنا'; }

    public function inputSchema(): array
    {
        return [
            'area'   => ['label' => 'مساحت زیربنا (مترمربع)', 'type' => 'number', 'required' => true],
            'kind'   => [
                'label' => 'نوع',
                'type'  => 'select',
                'options' => [
                    'residential_new'      => 'احداث مسکونی جدید',
                    'residential_extra'    => 'اضافه بنا بعد از پروانه',
                    'non_residential'      => 'غیرمسکونی',
                    'balcony'              => 'بالکن',
                    'overhang'             => 'پیش‌آمدگی',
                    'excess_density'       => 'مازاد بر تراکم',
                ],
                'default' => 'residential_new',
            ],
        ];
    }

    public function calculate(TariffSet $set, TariffItem $item, array $inputs): CalculationResult
    {
        $area = (float) ($inputs['area'] ?? 0);
        $kind = (string) ($inputs['kind'] ?? 'residential_new');

        $P  = (float) TariffParameter::getValue($set, 'building_value_per_m2', 0);
        $P1 = (float) TariffParameter::getValue($set, 'adjustment_coefficient', 0.115);

        $factors = $item->param('type_factors');
        if (is_string($factors)) $factors = json_decode($factors, true) ?: [];
        if (!is_array($factors) || empty($factors)) {
            // TODO(verify): مقادیر پیش‌فرض موقت
            $factors = [
                'residential_new'   => 1.0,
                'residential_extra' => 1.2,
                'non_residential'   => 1.5,
                'balcony'           => 0.5,
                'overhang'          => 0.7,
                'excess_density'    => 2.0,
            ];
        }
        $B = (float) ($factors[$kind] ?? 1.0);

        $amount = (int) round($P * $P1 * $area * $B);

        return CalculationResult::make($amount, [
            'P (ارزش پایه m²)'  => $P,
            'P1 (ضریب تعدیل)'   => $P1,
            'M (مساحت)'         => $area,
            'B (ضریب نوع)'      => $B,
        ], ['TODO(verify): واحد B و ضرایب نوع نیاز به تأیید دارد.']);
    }
}
