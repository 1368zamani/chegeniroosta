<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

/**
 * بهای خدمات پسماند — S12/S13/S14
 *
 * فرمول: A = units × annual_rate × kind_factor × period_factor
 *  units        = تعداد واحد / خانوار — ورودی
 *  annual_rate  = نرخ سالانه هر واحد — از itemParam
 *  kind_factor  = ضریب نوع (مسکونی 1.0، تجاری 1.5، صنعتی 2.5) — از itemParam
 *  period_factor= ضریب دوره (سالانه 1، ماهانه 12/1 = 0.0833) — از itemParam
 */
class WasteCalculator extends BaseCalculator
{
    public function key(): string { return 'waste'; }
    public function label(): string { return 'بهای خدمات پسماند'; }

    public function inputSchema(TariffItem $item): array
    {
        return [
            ['key' => 'kind',   'label' => 'نوع پسماند', 'type' => 'select', 'required' => true, 'options' => [
                'residential' => 'مسکونی',
                'commercial'  => 'تجاری / اداری',
                'industrial'  => 'صنعتی',
            ]],
            ['key' => 'units',  'label' => 'تعداد واحد / خانوار', 'type' => 'number', 'required' => true],
            ['key' => 'period', 'label' => 'دوره', 'type' => 'select', 'required' => true, 'options' => [
                'annual'  => 'سالانه',
                'monthly' => 'ماهانه',
            ]],
        ];
    }

    public function calculate(TariffItem $item, array $inputs, ?TariffSet $set = null): CalculationResult
    {
        $kind   = $this->requiredChoice($inputs, 'kind', ['residential','commercial','industrial'], 'نوع');
        $units  = $this->requiredNumber($inputs, 'units', 'تعداد واحد');
        $period = $this->requiredChoice($inputs, 'period', ['annual','monthly'], 'دوره');

        $rate         = (float) $this->itemParam($item, "annual_rate_$kind", 0);
        $kindFactor   = (float) $this->itemParam($item, "kind_factor_$kind", $kind === 'residential' ? 1.0 : ($kind === 'commercial' ? 1.5 : 2.5));
        $periodFactor = $period === 'annual' ? 1.0 : (1 / 12);

        $base  = $units * $rate * $kindFactor;
        $total = $this->toInt($base * $periodFactor);

        return new CalculationResult(
            total: $total,
            steps: [
                $this->step('نرخ پایه سالانه', $this->toInt($rate), number_format($rate) . ' ریال'),
                $this->step('تعداد واحد × نرخ', $this->toInt($units * $rate), $units . ' واحد'),
                $this->step('ضریب نوع', $this->toInt($base), '× ' . $kindFactor),
                $this->step('ضریب دوره', $total, '× ' . number_format($periodFactor, 4)),
            ],
            meta: ['kind' => $kind, 'period' => $period],
        );
    }
}
