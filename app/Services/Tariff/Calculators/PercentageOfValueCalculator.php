<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

class PercentageOfValueCalculator extends BaseCalculator
{
    public function key(): string   { return 'percentage_of_value'; }
    public function label(): string { return 'درصدی از ارزش ملک'; }
    public function type(): string  { return 'levy'; }

    public function inputSchema(): array
    {
        return [
            'property_value' => [
                'label' => 'ارزش ملک (ریال)', 'type' => 'number',
                'default' => 100000000, 'min' => 0, 'required' => true,
            ],
            'percentage' => [
                'label' => 'درصد', 'type' => 'number',
                'default' => 1, 'min' => 0, 'max' => 100, 'step' => 0.01,
            ],
        ];
    }

    public function calculate(TariffSet $set, TariffItem $item, array $inputs): CalculationResult
    {
        $value = (float) ($inputs['property_value'] ?? 0);
        $pct   = (float) ($inputs['percentage'] ?? 1);

        $amount = $value * $pct / 100;

        return CalculationResult::make(
            amount:    (int) round($amount),
            breakdown: [
                'ارزش ملک' => number_format($value) . ' ریال',
                'درصد'     => $pct . '%',
                'فرمول'    => 'ارزش × درصد ÷ ۱۰۰',
            ],
            warnings: [],
            meta: [
                'formula_display' => 'V × P / 100',
                'params'          => ['value' => $value, 'percentage' => $pct],
            ]
        );
    }
}
