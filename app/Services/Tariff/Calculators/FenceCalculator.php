<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffParameter;
use App\Models\TariffSet;

/**
 * حصارکشی و دیوارکشی املاک فاقد مستحدثات.
 * طول × نرخ نوع × ضریب ارتفاع.
 * TODO(verify): نرخ هر نوع در OCR نبود.
 */
class FenceCalculator implements TariffCalculator
{
    public function key(): string { return 'fence_permit'; }
    public function label(): string { return 'عوارض مجوز حصارکشی'; }

    public function inputSchema(): array
    {
        return [
            'length' => ['label' => 'طول حصار (متر)', 'type' => 'number', 'required' => true],
            'type'   => [
                'label' => 'نوع',
                'type'  => 'select',
                'options' => [
                    'concrete' => 'بتنی',
                    'brick'    => 'آجری',
                    'metal'    => 'فلزی',
                ],
                'default' => 'brick',
            ],
            'height_over_1_5' => ['label' => 'ارتفاع بیش از ۱٫۵ متر؟', 'type' => 'checkbox', 'default' => false],
        ];
    }

    public function calculate(TariffSet $set, TariffItem $item, array $inputs): CalculationResult
    {
        $length = (float) ($inputs['length'] ?? 0);
        $type   = (string) ($inputs['type'] ?? 'brick');
        $over   = (bool) ($inputs['height_over_1_5'] ?? false);

        $rates = $item->param('type_rates_per_meter')
            ?? TariffParameter::getValue($set, 'fence_rates_per_meter', null);
        if (is_string($rates)) $rates = json_decode($rates, true) ?: [];
        if (!is_array($rates) || empty($rates)) {
            // TODO(verify): این‌ها نمونه هستند
            $rates = ['concrete' => 3000000, 'brick' => 2000000, 'metal' => 1500000];
        }

        $heightFactor = (float) ($item->param('height_factor_over_1_5') ?? 1.3);
        $factor = $over ? $heightFactor : 1.0;
        $rate   = (float) ($rates[$type] ?? 0);
        $amount = (int) round($length * $rate * $factor);

        return CalculationResult::make($amount, [
            'طول (m)'        => $length,
            'نوع'            => $type,
            'نرخ هر متر'     => $rate,
            'ضریب ارتفاع'    => $factor,
        ], ['TODO(verify): نرخ‌های نمونه — نیازمند نرخ رسمی.']);
    }
}
