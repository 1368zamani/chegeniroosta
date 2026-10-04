<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

/**
 * بهای خدمات مجوز حفاری / لکه‌گیری / ترمیم آسفالت — S10
 *
 * فرمول: A = length_m × rate × (width_factor) × (surface_type_factor)
 */
class ExcavationCalculator extends BaseCalculator
{
    public function key(): string { return 'excavation'; }
    public function label(): string { return 'بهای خدمات حفاری'; }

    public function inputSchema(TariffItem $item): array
    {
        return [
            ['key' => 'length',      'label' => 'متر طول',    'type' => 'number', 'unit' => 'متر', 'required' => true],
            ['key' => 'width',       'label' => 'عرض تخریب',  'type' => 'select', 'required' => true, 'options' => [
                'upto_50'  => 'تا ۵۰ سانتی‌متر',
                'over_50'  => 'بیش از ۵۰ سانتی‌متر',
            ]],
            ['key' => 'surface',     'label' => 'نوع معبر',    'type' => 'select', 'required' => true, 'options' => [
                'asphalt'  => 'آسفالت',
                'concrete' => 'بتنی',
                'dirt'     => 'خاکی',
                'stone'    => 'سنگ‌فرش',
            ]],
        ];
    }

    public function calculate(TariffItem $item, array $inputs, ?TariffSet $set = null): CalculationResult
    {
        $length  = $this->requiredNumber($inputs, 'length', 'متر طول');
        $width   = $this->requiredChoice($inputs, 'width', ['upto_50','over_50'], 'عرض');
        $surface = $this->requiredChoice($inputs, 'surface', ['asphalt','concrete','dirt','stone'], 'نوع معبر');

        $rate            = (float) $this->itemParam($item, "rate_$surface", 0);
        $widthFactor     = (float) $this->itemParam($item, "width_factor_$width", $width === 'upto_50' ? 1.0 : 1.5);

        $base  = $length * $rate;
        $total = $this->toInt($base * $widthFactor);

        return new CalculationResult(
            total: $total,
            steps: [
                $this->step('نرخ هر متر', $this->toInt($rate), number_format($rate) . ' ریال/متر'),
                $this->step('متر طول × نرخ', $this->toInt($base), $length . ' متر'),
                $this->step('ضریب عرض', $total, '× ' . $widthFactor),
            ],
            meta: ['surface' => $surface, 'width' => $width],
        );
    }
}
