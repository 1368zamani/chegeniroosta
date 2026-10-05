<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffParameter;
use App\Models\TariffSet;

/**
 * قطع درخت: محیط بن (cm) × نرخ + ۵ نهال جایگزین × ارزش نهال.
 * بدون مجوز × ۵.
 */
class TreeCuttingCalculator implements TariffCalculator
{
    public function key(): string { return 'tree_cutting'; }
    public function label(): string { return 'عوارض قطع درختان'; }

    public function inputSchema(): array
    {
        return [
            'circumference_cm' => ['label' => 'محیط بن (سانتی‌متر)', 'type' => 'number', 'required' => true],
            'has_permit'       => ['label' => 'دارای مجوز؟', 'type' => 'checkbox', 'default' => true],
        ];
    }

    public function calculate(TariffSet $set, TariffItem $item, array $inputs): CalculationResult
    {
        $circ = (float) ($inputs['circumference_cm'] ?? 0);
        $hasPermit = (bool) ($inputs['has_permit'] ?? true);

        $ratePerCm   = (float) ($item->param('rate_per_cm') ?? TariffParameter::getValue($set, 'tree_rate_per_cm', 500000));
        $sapling     = (float) ($item->param('sapling_value') ?? TariffParameter::getValue($set, 'sapling_value', 3000000));
        $saplingQty  = (int)   ($item->param('sapling_count') ?? 5);
        $unauthorizedFactor = (float) ($item->param('unauthorized_factor') ?? 5.0);

        $base = ($circ * $ratePerCm) + ($saplingQty * $sapling);
        $amount = (int) round($hasPermit ? $base : $base * $unauthorizedFactor);

        return CalculationResult::make($amount, [
            'محیط بن (cm)'         => $circ,
            'نرخ هر cm'            => $ratePerCm,
            'نهال جایگزین'         => $saplingQty . ' × ' . number_format($sapling),
            'بدون مجوز × ۵'        => $hasPermit ? 'خیر' : 'بله',
            'مبلغ پایه'            => $base,
        ]);
    }
}
