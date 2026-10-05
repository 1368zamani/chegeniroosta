<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

/**
 * ارزش افزوده الحاق به بافت / تغییر کاربری / طرح‌های عمران.
 * ضریب × ارزش افزوده، سقف ۳۰٪.
 */
class ValueAddedCalculator implements TariffCalculator
{
    public function key(): string { return 'value_added'; }
    public function label(): string { return 'ارزش افزوده'; }

    public function inputSchema(): array
    {
        return [
            'value_added' => ['label' => 'ارزش افزوده (ریال)', 'type' => 'number', 'required' => true],
            'coefficient' => ['label' => 'ضریب مصوب شورا (۰ تا ۱)', 'type' => 'number', 'default' => 0.20, 'min' => 0, 'max' => 1],
        ];
    }

    public function calculate(TariffSet $set, TariffItem $item, array $inputs): CalculationResult
    {
        $value = (float) ($inputs['value_added'] ?? 0);
        $coef  = (float) ($inputs['coefficient'] ?? 0.20);

        $statutoryCap = (float) ($item->param('statutory_cap') ?? 0.30);
        $warnings = [];
        if ($coef > $statutoryCap) {
            $warnings[] = "ضریب {$coef} از سقف قانونی {$statutoryCap} بیشتر است — به سقف محدود شد.";
            $coef = $statutoryCap;
        }

        $amount = (int) round($value * $coef);
        return CalculationResult::make($amount, [
            'ارزش افزوده'   => $value,
            'ضریب اعمالی'   => $coef,
            'سقف قانونی'    => $statutoryCap,
        ], $warnings);
    }
}
