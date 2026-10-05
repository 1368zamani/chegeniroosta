<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

/**
 * بهای خدمات پسماند (سالانه).
 * سه زیرگروه: مسکونی، تجاری-اداری، صنعتی.
 * مبلغ ثابت سالانه بر اساس params آیتم.
 */
class WasteCalculator implements TariffCalculator
{
    public function key(): string { return 'waste'; }
    public function label(): string { return 'پسماند'; }

    public function inputSchema(): array
    {
        return [
            'category' => [
                'label' => 'نوع',
                'type'  => 'select',
                'options' => [
                    'residential' => 'مسکونی',
                    'commercial'  => 'تجاری-اداری',
                    'industrial'  => 'صنعتی',
                ],
                'default' => 'residential',
            ],
            'quantity' => ['label' => 'تعداد واحد', 'type' => 'number', 'default' => 1, 'min' => 1],
        ];
    }

    public function calculate(TariffSet $set, TariffItem $item, array $inputs): CalculationResult
    {
        $cat = (string) ($inputs['category'] ?? 'residential');
        $qty = max(1, (int) ($inputs['quantity'] ?? 1));

        $rates = $item->param('annual_rates') ?? [
            'residential' => 2000000,
            'commercial'  => 5000000,
            'industrial'  => 15000000,
        ];
        if (is_string($rates)) $rates = json_decode($rates, true) ?: [];

        $rate = (float) ($rates[$cat] ?? 0);
        $amount = (int) round($rate * $qty);

        return CalculationResult::make($amount, [
            'نوع'      => $cat,
            'نرخ'      => $rate,
            'تعداد'    => $qty,
        ], ['TODO(verify): نرخ‌های نمونه.']);
    }
}
