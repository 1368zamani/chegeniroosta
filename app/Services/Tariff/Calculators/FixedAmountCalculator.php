<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

class FixedAmountCalculator extends BaseCalculator
{
    public function key(): string   { return 'fixed_amount'; }
    public function label(): string { return 'مبلغ ثابت + ضریب'; }
    public function type(): string  { return 'service_fee'; }

    public function inputSchema(): array
    {
        return [
            'quantity' => ['label' => 'تعداد', 'type' => 'number', 'default' => 1, 'min' => 0],
            'discount' => ['label' => 'درصد تخفیف', 'type' => 'number', 'default' => 0, 'min' => 0, 'max' => 100],
        ];
    }

    public function calculate(TariffSet $set, TariffItem $item, array $inputs): CalculationResult
    {
        $quantity = (float) ($inputs['quantity'] ?? 1);
        $discount = (float) ($inputs['discount'] ?? 0);
        $base     = $this->baseAmount($item);

        $gross = $quantity * $base;
        $net   = $gross * (1 - $discount / 100);

        return CalculationResult::make(
            amount:    (int) round($net),
            breakdown: [
                'مبلغ پایه' => number_format($base) . ' ریال',
                'تعداد'     => $quantity,
                'تخفیف'     => $discount . '%',
                'قبل تخفیف' => number_format($gross) . ' ریال',
            ],
            warnings: [],
            meta: [
                'formula_display' => 'Q × B × (1 - D%)',
                'params'          => ['quantity' => $quantity, 'discount' => $discount],
            ]
        );
    }
}
