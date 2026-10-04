<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

/**
 * بهای خدمات ماشین‌آلات — S07 (ساعتی)
 *
 * فرمول: A = hours × hourly_rate × (distance_factor)
 */
class MachineryHourlyCalculator extends BaseCalculator
{
    public function key(): string { return 'machinery_hourly'; }
    public function label(): string { return 'بهای خدمات ماشین‌آلات (ساعتی)'; }

    public function inputSchema(TariffItem $item): array
    {
        return [
            ['key' => 'machine', 'label' => 'نوع ماشین', 'type' => 'select', 'required' => true, 'options' => [
                'loader'    => 'لودر',
                'excavator' => 'بیل مکانیکی',
                'truck'     => 'کامیون',
                'grader'    => 'گریدر',
                'roller'    => 'غلطک',
            ]],
            ['key' => 'hours', 'label' => 'مدت کار', 'type' => 'number', 'unit' => 'ساعت', 'required' => true],
            ['key' => 'location', 'label' => 'محل', 'type' => 'select', 'required' => true, 'options' => [
                'inside'  => 'داخل محدوده',
                'outside' => 'خارج محدوده',
            ]],
        ];
    }

    public function calculate(TariffItem $item, array $inputs, ?TariffSet $set = null): CalculationResult
    {
        $machine  = $this->requiredChoice($inputs, 'machine', ['loader','excavator','truck','grader','roller'], 'نوع ماشین');
        $hours    = $this->requiredNumber($inputs, 'hours', 'مدت کار');
        $location = $this->requiredChoice($inputs, 'location', ['inside','outside'], 'محل');

        $rate       = (float) $this->itemParam($item, "hourly_rate_$machine", 0);
        $locFactor  = (float) $this->itemParam($item, "location_factor_$location", $location === 'inside' ? 1.0 : 1.3);

        $base  = $hours * $rate;
        $total = $this->toInt($base * $locFactor);

        return new CalculationResult(
            total: $total,
            steps: [
                $this->step('نرخ ساعتی', $this->toInt($rate), number_format($rate) . ' ریال/ساعت'),
                $this->step('ساعت × نرخ', $this->toInt($base), $hours . ' ساعت'),
                $this->step('ضریب محل', $total, '× ' . $locFactor),
            ],
            meta: ['machine' => $machine, 'location' => $location],
        );
    }
}
