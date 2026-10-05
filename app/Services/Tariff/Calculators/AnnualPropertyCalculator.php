<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffParameter;
use App\Models\TariffSet;

/**
 * عوارض سالیانه املاک و مستقلات.
 *
 * فرمول نامزد (نیازمند راستی‌آزمایی):
 *   A = (P·S + P1·M) × k × B
 *
 *   P  = ارزش معاملاتی هر مترمربع عرصه (ریال)  ← پارامتر: land_value_per_m2
 *   S  = مساحت عرصه (مترمربع)                    ← ورودی: land_area
 *   P1 = ارزش معادل هر مترمربع اعیانی            ← پارامتر: building_value_per_m2
 *   M  = مساحت اعیانی (مترمربع)                  ← ورودی: building_area
 *   k  = ضریب تعدیل سالانه                       ← پارامتر: adjustment_coefficient
 *   B  = ضریب کاربری (درصد یا ضریب)              ← پارامتر: usage_factors (JSON) + ورودی: usage
 *
 * TODO(verify): ترتیب پرانتزها در OCR مبهم بود. اگر فرمول رسمی این نیست،
 *               فقط همین کلاس باید تغییر کند (بقیه بی‌اثرند).
 * TODO(verify): ضرایب کاربری نمونه‌ی اردبیل هستند، نه نرخ رسمی چگنی.
 */
class AnnualPropertyCalculator implements TariffCalculator
{
    public function key(): string { return 'annual_property'; }
    public function label(): string { return 'عوارض سالیانه املاک و مستقلات'; }

    public function inputSchema(): array
    {
        return [
            'land_area'     => ['label' => 'مساحت عرصه (مترمربع)', 'type' => 'number', 'required' => true],
            'building_area' => ['label' => 'مساحت اعیانی (مترمربع)', 'type' => 'number', 'default' => 0],
            'usage'         => [
                'label'   => 'کاربری',
                'type'    => 'select',
                'options' => [
                    'residential' => 'مسکونی',
                    'commercial'  => 'تجاری',
                    'industrial'  => 'صنعتی',
                    'office'      => 'اداری/عمومی',
                    'tourism'     => 'گردشگری',
                    'sports'      => 'ورزشی',
                    'agricultural'=> 'کشاورزی',
                    'workshop'    => 'کارگاهی',
                ],
                'default' => 'residential',
            ],
        ];
    }

    public function calculate(TariffSet $set, TariffItem $item, array $inputs): CalculationResult
    {
        $landArea     = (float) ($inputs['land_area'] ?? 0);
        $buildingArea = (float) ($inputs['building_area'] ?? 0);
        $usage        = (string) ($inputs['usage'] ?? 'residential');

        $P  = (float) TariffParameter::getValue($set, 'land_value_per_m2', 0);
        $P1 = (float) TariffParameter::getValue($set, 'building_value_per_m2', 0);
        $k  = (float) TariffParameter::getValue($set, 'adjustment_coefficient', 0.115);

        // ضرایب کاربری از params آیتم یا پارامتر سراسری
        $usageFactors = $item->param('usage_factors')
            ?? TariffParameter::getValue($set, 'usage_factors', null);

        if (is_string($usageFactors)) {
            $usageFactors = json_decode($usageFactors, true) ?: [];
        }
        if (!is_array($usageFactors) || empty($usageFactors)) {
            // TODO(verify): نمونه‌ی اردبیل — این اعداد رسمی چگنی نیستند
            $usageFactors = [
                'commercial'   => 0.20,
                'industrial'   => 0.13,
                'residential'  => 0.12,
                'office'       => 0.09,
                'tourism'      => 0.09,
                'sports'       => 0.09,
                'agricultural' => 0.07,
                'workshop'     => 0.07,
            ];
        }

        $B = (float) ($usageFactors[$usage] ?? 0);

        $base   = ($P * $landArea) + ($P1 * $buildingArea);
        $amount = (int) round($base * $k * $B);

        return CalculationResult::make($amount, [
            'P (ارزش عرصه هر m²)'       => $P,
            'S (مساحت عرصه)'            => $landArea,
            'P1 (ارزش اعیانی هر m²)'    => $P1,
            'M (مساحت اعیانی)'          => $buildingArea,
            'k (ضریب تعدیل)'            => $k,
            'B (ضریب کاربری — ' . $usage . ')' => $B,
            'پایه (P·S + P1·M)'         => $base,
        ], [
            'TODO(verify): ترتیب پرانتز فرمول در OCR مبهم بود.',
        ]);
    }
}
