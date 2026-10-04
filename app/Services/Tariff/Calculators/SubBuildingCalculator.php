<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

/**
 * عوارض زیربنا / اضافه بنا / مازاد تراکم — L05
 *
 * فرمول: A = P × P1 × M × B
 *  P  = ارزش عرصه (ریال/m²) — از setParam
 *  P1 = ضریب معادل ۱۱٫۵٪ — از setParam
 *  M  = مساحت زیربنا (m²) — ورودی
 *  B  = ضریب نوع (واحد درصد یا ضریب)
 *
 * TODO(verify): واحد B مبهم است؛ فیلد unit در ورودی نگه داشته شده.
 */
class SubBuildingCalculator extends BaseCalculator
{
    public function key(): string { return 'sub_building'; }
    public function label(): string { return 'عوارض زیربنا'; }

    public function inputSchema(TariffItem $item): array
    {
        return [
            ['key' => 'area',       'label' => 'مساحت زیربنا', 'type' => 'number', 'unit' => 'مترمربع', 'required' => true],
            ['key' => 'kind',       'label' => 'نوع',           'type' => 'select', 'required' => true, 'options' => [
                'normal'        => 'احداث بنا',
                'extra_after'   => 'اضافه بنا بعد از پروانه',
                'over_density'  => 'مازاد بر تراکم',
                'balcony'       => 'بالکن و پیش‌آمدگی',
            ]],
        ];
    }

    public function calculate(TariffItem $item, array $inputs, ?TariffSet $set = null): CalculationResult
    {
        $M = $this->requiredNumber($inputs, 'area', 'مساحت زیربنا');
        $k = $this->requiredChoice($inputs, 'kind', ['normal','extra_after','over_density','balcony'], 'نوع');

        $P  = (float) $this->setParam($set, 'land_value_per_m2', 0);
        $P1 = (float) $this->setParam($set, 'subbuilding_coefficient', 0.115);
        $B  = (float) $this->itemParam($item, "kind_factor_$k", 1.0);

        $step1 = $P * $P1;
        $step2 = $step1 * $M;
        $total = $this->toInt($step2 * $B);

        return new CalculationResult(
            total: $total,
            steps: [
                $this->step('ارزش عرصه × ضریب', $this->toInt($step1), sprintf('%s × %s', number_format($P), $P1)),
                $this->step('ضرب در مساحت زیربنا', $this->toInt($step2), number_format($M) . ' م²'),
                $this->step('اعمال ضریب نوع', $total, '× ' . $B),
            ],
            meta: ['kind' => $k],
        );
    }
}
