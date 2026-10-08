<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

class PerPersonCalculator extends BaseCalculator
{
    public function key(): string   { return 'per_person'; }
    public function label(): string { return 'محاسبه بر اساس تعداد نفرات'; }
    public function type(): string  { return 'service_fee'; }

    public function inputSchema(): array
    {
        return [
            'persons' => [
                'label' => 'تعداد نفرات', 'type' => 'number',
                'default' => 1, 'min' => 1, 'required' => true,
            ],
            'months' => [
                'label' => 'تعداد ماه', 'type' => 'number',
                'default' => 12, 'min' => 1, 'max' => 12,
            ],
        ];
    }

    public function calculate(TariffSet $set, TariffItem $item, array $inputs): CalculationResult
    {
        $persons = (int) ($inputs['persons'] ?? 1);
        $months  = (int) ($inputs['months'] ?? 12);
        $rate    = $this->baseAmount($item);

        $amount = $persons * $months * $rate;

        return CalculationResult::make(
            amount:    (int) round($amount),
            breakdown: [
                'تعداد نفرات' => $persons,
                'تعداد ماه'   => $months,
                'نرخ نفر/ماه' => number_format($rate) . ' ریال',
                'فرمول'       => 'نفرات × ماه × نرخ',
            ],
            warnings: [],
            meta: [
                'formula_display' => 'N × M × R',
                'params'          => ['persons' => $persons, 'months' => $months],
            ]
        );
    }
}
