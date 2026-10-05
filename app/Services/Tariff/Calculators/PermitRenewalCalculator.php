<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

/**
 * تمدید/تجدید پروانه ساختمانی:
 *   سال اول: رایگان
 *   سال دوم: حداکثر ۳٪ عوارض صدور
 *   سال ۳+: هر سال +۲٪ تا سقف ۲۰٪
 */
class PermitRenewalCalculator implements TariffCalculator
{
    public function key(): string { return 'permit_renewal'; }
    public function label(): string { return 'تمدید و تجدید پروانه'; }

    public function inputSchema(): array
    {
        return [
            'years_since_issue'    => ['label' => 'سال‌های سپری‌شده از صدور', 'type' => 'number', 'required' => true, 'min' => 1],
            'original_permit_fee'  => ['label' => 'عوارض صدور اصلی (ریال)', 'type' => 'number', 'required' => true],
        ];
    }

    public function calculate(TariffSet $set, TariffItem $item, array $inputs): CalculationResult
    {
        $years  = max(1, (int) ($inputs['years_since_issue'] ?? 1));
        $origin = (float) ($inputs['original_permit_fee'] ?? 0);

        $year2Rate  = (float) ($item->param('year2_rate') ?? 0.03);
        $stepRate   = (float) ($item->param('step_rate') ?? 0.02);
        $cap        = (float) ($item->param('cap') ?? 0.20);

        if ($years === 1) {
            $pct = 0.0;
        } elseif ($years === 2) {
            $pct = min($year2Rate, $cap);
        } else {
            $pct = min($year2Rate + ($years - 2) * $stepRate, $cap);
        }

        $amount = (int) round($origin * $pct);
        return CalculationResult::make($amount, [
            'سال‌ها'        => $years,
            'درصد اعمالی'   => ($pct * 100) . '٪',
            'سقف'           => ($cap * 100) . '٪',
        ]);
    }
}
