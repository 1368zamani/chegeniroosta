<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

/**
 * بهای خدمات ماشین‌آلات: نرخ ساعتی × ساعت کار.
 */
class MachineryCalculator implements TariffCalculator
{
    public function key(): string { return 'machinery_hourly'; }
    public function label(): string { return 'ماشین‌آلات (ساعتی)'; }

    public function inputSchema(): array
    {
        return [
            'hourly_rate' => ['label' => 'نرخ ساعتی ماشین (ریال)', 'type' => 'number', 'required' => true],
            'hours'       => ['label' => 'ساعت کار', 'type' => 'number', 'required' => true],
        ];
    }

    public function calculate(TariffSet $set, TariffItem $item, array $inputs): CalculationResult
    {
        $rate  = (float) ($inputs['hourly_rate'] ?? 0);
        $hours = (float) ($inputs['hours'] ?? 0);
        $amount = (int) round($rate * $hours);
        return CalculationResult::make($amount, [
            'نرخ ساعتی' => $rate,
            'ساعت کار'  => $hours,
        ]);
    }
}
