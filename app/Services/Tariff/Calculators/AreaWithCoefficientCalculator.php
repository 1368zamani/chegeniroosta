<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

class AreaWithCoefficientCalculator extends BaseCalculator
{
    public function key(): string   { return 'area_with_coefficient'; }
    public function label(): string { return 'مساحت × ضریب × نرخ (پیشرفته)'; }
    public function type(): string  { return 'levy'; }

    public function inputSchema(): array
    {
        return [
            'area' => [
                'label' => 'مساحت (متر مربع)', 'type' => 'number',
                'default' => 200, 'min' => 0, 'required' => true,
            ],
            'floor_count' => [
                'label' => 'تعداد طبقات', 'type' => 'number',
                'default' => 1, 'min' => 1,
            ],
            'usage_type' => [
                'label' => 'نوع کاربری', 'type' => 'select', 'default' => 'residential',
                'options' => [
                    'residential' => 'مسکونی',
                    'commercial'  => 'تجاری',
                    'office'      => 'اداری',
                    'industrial'  => 'صنعتی',
                ],
            ],
            'has_elevator' => [
                'label' => 'آسانسور دارد', 'type' => 'checkbox', 'default' => false,
            ],
        ];
    }

    public function calculate(TariffSet $set, TariffItem $item, array $inputs): CalculationResult
    {
        $area     = (float) ($inputs['area'] ?? 0);
        $floors   = (int)   ($inputs['floor_count'] ?? 1);
        $usage    = $inputs['usage_type'] ?? 'residential';
        $hasElev  = (bool)  ($inputs['has_elevator'] ?? false);
        $rate     = $this->baseAmount($item);

        $usageCoef = match ($usage) {
            'residential' => 1.0, 'commercial' => 2.0,
            'office' => 1.5, 'industrial' => 2.5, default => 1.0,
        };
        $floorCoef = 1 + ($floors - 1) * 0.1;
        $elevCoef  = $hasElev ? 1.05 : 1.0;

        $amount = $area * $rate * $usageCoef * $floorCoef * $elevCoef;

        return CalculationResult::make(
            amount:    (int) round($amount),
            breakdown: [
                'مساحت'        => $area . ' م²',
                'نرخ پایه'     => number_format($rate) . ' ریال',
                'ضریب کاربری'  => '× ' . $usageCoef,
                'ضریب طبقات'   => '× ' . round($floorCoef, 2),
                'ضریب آسانسور' => '× ' . $elevCoef,
                '— فرمول'      => 'مساحت × نرخ × کاربری × طبقات × آسانسور',
            ],
            warnings: [],
            meta: [
                'formula_display' => 'A × R × U × F × E',
                'params' => [
                    'area' => $area, 'usage' => $usage,
                    'floor_count' => $floors, 'has_elevator' => $hasElev,
                ],
            ]
        );
    }
}
