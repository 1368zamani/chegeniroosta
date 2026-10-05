<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

/**
 * مجوز حفاری/لکه‌گیری/ترمیم آسفالت.
 * متر طول × نرخ هر متر (بر اساس نوع معبر) × ضریب عرض تخریب.
 */
class ExcavationCalculator implements TariffCalculator
{
    public function key(): string { return 'excavation'; }
    public function label(): string { return 'حفاری و ترمیم آسفالت'; }

    public function inputSchema(): array
    {
        return [
            'length_m' => ['label' => 'طول حفاری (متر)', 'type' => 'number', 'required' => true],
            'street'   => [
                'label' => 'نوع معبر',
                'type'  => 'select',
                'options' => [
                    'asphalt'   => 'آسفالت',
                    'stone'     => 'سنگ‌فرش',
                    'unpaved'   => 'خاکی',
                ],
                'default' => 'asphalt',
            ],
            'width_factor' => ['label' => 'ضریب عرض تخریب', 'type' => 'number', 'default' => 1.0, 'min' => 0.5, 'max' => 5],
        ];
    }

    public function calculate(TariffSet $set, TariffItem $item, array $inputs): CalculationResult
    {
        $length = (float) ($inputs['length_m'] ?? 0);
        $street = (string) ($inputs['street'] ?? 'asphalt');
        $widthF = (float) ($inputs['width_factor'] ?? 1.0);

        $rates = $item->param('rates_per_meter') ?? [
            'asphalt' => 800000, 'stone' => 600000, 'unpaved' => 300000,
        ];
        if (is_string($rates)) $rates = json_decode($rates, true) ?: [];

        $rate = (float) ($rates[$street] ?? 0);
        $amount = (int) round($length * $rate * $widthF);

        return CalculationResult::make($amount, [
            'طول (m)'         => $length,
            'نوع معبر'        => $street,
            'نرخ هر متر'      => $rate,
            'ضریب عرض'        => $widthF,
        ], ['TODO(verify): نرخ‌های نمونه.']);
    }
}
