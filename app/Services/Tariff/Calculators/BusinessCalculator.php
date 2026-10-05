<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffParameter;
use App\Models\TariffSet;

/**
 * عوارض مشاغل دائم و موقت.
 * نرخ پایه صنف × ضریب معبر (اصلی ۱۰۰٪، فرعی ۷۰٪).
 * جریمه تأخیر ۲٫۵٪ — محاسبه‌ی جریمه در فاز ۳.
 */
class BusinessCalculator implements TariffCalculator
{
    public function key(): string { return 'businesses'; }
    public function label(): string { return 'عوارض مشاغل'; }

    public function inputSchema(): array
    {
        return [
            'base_rate'  => ['label' => 'نرخ پایه صنف (ریال)', 'type' => 'number', 'required' => true],
            'street'     => [
                'label' => 'موقعیت معبر',
                'type'  => 'select',
                'options' => ['main' => 'معبر اصلی (۱۰۰٪)', 'secondary' => 'معبر فرعی (۷۰٪)'],
                'default' => 'main',
            ],
            'is_permanent' => ['label' => 'دائم (در غیر این صورت موقت)', 'type' => 'checkbox', 'default' => true],
        ];
    }

    public function calculate(TariffSet $set, TariffItem $item, array $inputs): CalculationResult
    {
        $base       = (float) ($inputs['base_rate'] ?? 0);
        $street     = (string) ($inputs['street'] ?? 'main');
        $isPermanent= (bool) ($inputs['is_permanent'] ?? true);

        $factors = $item->param('street_factors') ?? ['main' => 1.0, 'secondary' => 0.7];
        if (is_string($factors)) $factors = json_decode($factors, true) ?: ['main' => 1.0, 'secondary' => 0.7];

        $f = (float) ($factors[$street] ?? 1.0);
        $amount = (int) round($base * $f);

        return CalculationResult::make($amount, [
            'نرخ پایه'      => $base,
            'ضریب معبر'     => $f,
            'نوع'           => $isPermanent ? 'دائم' : 'موقت',
        ], ['مهلت پرداخت: تا پایان اردیبهشت. جریمه تأخیر ۲٫۵٪ (فاز ۳).']);
    }
}
