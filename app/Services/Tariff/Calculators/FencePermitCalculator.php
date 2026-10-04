<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

/**
 * عوارض مجوز حصارکشی و دیوارکشی — L04
 *
 * فرمول: A = length × height_factor × type_rate
 *  length       = طول حصار (متر) — ورودی
 *  height_factor= ضریب ارتفاع (تا ۱٫۵ متر = 1.0، بیش از ۱٫۵ = 1.5) — از itemParam
 *  type_rate    = نرخ هر متر بر اساس نوع (بتنی/آجری/فلزی) — از itemParam
 *
 * TODO(verify): نرخ‌های هر نوع باید در پنل ادمین وارد شود.
 */
class FencePermitCalculator extends BaseCalculator
{
    public function key(): string { return 'fence_permit'; }
    public function label(): string { return 'عوارض حصارکشی'; }

    public function inputSchema(TariffItem $item): array
    {
        return [
            ['key' => 'length', 'label' => 'طول حصار',  'type' => 'number', 'unit' => 'متر', 'required' => true],
            ['key' => 'height', 'label' => 'ارتفاع',    'type' => 'select', 'required' => true, 'options' => [
                'upto_150'  => 'تا ۱٫۵ متر',
                'over_150'  => 'بیش از ۱٫۵ متر',
            ]],
            ['key' => 'type', 'label' => 'نوع',         'type' => 'select', 'required' => true, 'options' => [
                'brick'  => 'آجری',
                'concrete' => 'بتنی',
                'metal'  => 'فلزی',
            ]],
        ];
    }

    public function calculate(TariffItem $item, array $inputs, ?TariffSet $set = null): CalculationResult
    {
        $length = $this->requiredNumber($inputs, 'length', 'طول حصار');
        $h      = $this->requiredChoice($inputs, 'height', ['upto_150','over_150'], 'ارتفاع');
        $type   = $this->requiredChoice($inputs, 'type', ['brick','concrete','metal'], 'نوع');

        $heightFactor = (float) $this->itemParam($item, "height_factor_$h", $h === 'upto_150' ? 1.0 : 1.5);
        $typeRate     = (float) $this->itemParam($item, "type_rate_$type", 0);

        $beforeHeight = $length * $typeRate;
        $total        = $this->toInt($beforeHeight * $heightFactor);

        return new CalculationResult(
            total: $total,
            steps: [
                $this->step('طول × نرخ نوع', $this->toInt($beforeHeight), sprintf('%s متر × %s ریال', number_format($length), number_format($typeRate))),
                $this->step('اعمال ضریب ارتفاع', $total, '× ' . $heightFactor),
            ],
            meta: ['type' => $type, 'height' => $h],
        );
    }
}
