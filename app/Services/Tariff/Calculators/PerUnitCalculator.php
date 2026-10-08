<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

class PerUnitCalculator extends BaseCalculator
{
    public function key(): string   { return 'per_unit'; }
    public function label(): string { return 'محاسبه بر اساس تعداد واحد'; }
    public function type(): string  { return 'levy'; }

    public function inputSchema(): array
    {
        return [
            'units' => [
                'label' => 'تعداد واحد', 'type' => 'number',
                'default' => 1, 'min' => 1, 'required' => true,
            ],
            'floor' => [
                'label' => 'طبقه', 'type' => 'select', 'default' => 'ground',
                'options' => [
                    'basement' => 'زیرزمین',
                    'ground'   => 'همکف',
                    'first'    => 'طبقه اول',
                    'second'   => 'طبقه دوم',
                    'higher'   => 'طبقات بالاتر',
                ],
            ],
        ];
    }

    public function calculate(TariffSet $set, TariffItem $item, array $inputs): CalculationResult
    {
        $units = (int) ($inputs['units'] ?? 1);
        $floor = $inputs['floor'] ?? 'ground';
        $rate  = $this->baseAmount($item);

        $floorCoef = match ($floor) {
            'basement' => 0.7, 'ground' => 1.0, 'first' => 0.9,
            'second' => 0.8, 'higher' => 0.7, default => 1.0,
        };

        $amount = $units * $rate * $floorCoef;

        return CalculationResult::make(
            amount:    (int) round($amount),
            breakdown: [
                'تعداد واحد' => $units,
                'نرخ واحد'   => number_format($rate) . ' ریال',
                'ضریب طبقه'  => '× ' . $floorCoef,
            ],
            warnings: [],
            meta: [
                'formula_display' => 'U × R × F',
                'params'          => ['units' => $units, 'floor' => $floor],
            ]
        );
    }
}
