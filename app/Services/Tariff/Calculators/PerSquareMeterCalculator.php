<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

class PerSquareMeterCalculator extends BaseCalculator
{
    public function key(): string   { return 'per_square_meter'; }
    public function label(): string { return 'محاسبه بر اساس متر مربع'; }
    public function type(): string  { return 'levy'; }

    public function inputSchema(): array
    {
        return [
            'area' => [
                'label'    => 'مساحت (متر مربع)',
                'type'     => 'number',
                'default'  => 100,
                'min'      => 0,
                'required' => true,
                'unit'     => 'م²',
            ],
            'coefficient' => [
                'label'    => 'ضریب کاربری',
                'type'     => 'select',
                'default'  => '1',
                'options'  => [
                    '1'   => 'مسکونی (× ۱)',
                    '1.5' => 'تجاری (× ۱.۵)',
                    '2'   => 'صنعتی (× ۲)',
                    '0.5' => 'کشاورزی (× ۰.۵)',
                ],
            ],
        ];
    }

    public function calculate(TariffSet $set, TariffItem $item, array $inputs): CalculationResult
    {
        $area        = (float) ($inputs['area'] ?? 0);
        $coefficient = (float) ($inputs['coefficient'] ?? 1);
        $rate        = $this->baseAmount($item);

        $amount = $area * $coefficient * $rate;

        return CalculationResult::make(
            amount:    (int) round($amount),
            breakdown: [
                'مساحت'    => $area . ' م²',
                'ضریب'     => '× ' . $coefficient,
                'نرخ پایه' => number_format($rate) . ' ریال',
                'فرمول'    => 'مساحت × ضریب × نرخ',
            ],
            warnings: [],
            meta: [
                'increase_percent' => 0,
                'formula_display'  => 'S × C × R',
                'params'           => ['area' => $area, 'coefficient' => $coefficient],
            ]
        );
    }
}
