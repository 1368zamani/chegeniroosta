<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

/**
 * عوارض قطع درختان — L08
 *
 * فرمول: A = circumference_cm × rate_per_cm × trees_count × (has_permit ? 1 : 5)
 *  circumference = محیط بن (cm) — ورودی
 *  trees_count   = تعداد درخت — ورودی
 *  rate_per_cm   = نرخ هر سانتی‌متر — از itemParam
 *  has_permit    = مجوز دارد؟ — ورودی
 *
 * بدون مجوز ۵ برابر. به‌علاوه ۵ اصله نهال جایگزین (فقط اطلاع‌رسانی).
 */
class TreeCuttingCalculator extends BaseCalculator
{
    public function key(): string { return 'tree_cutting'; }
    public function label(): string { return 'عوارض قطع درختان'; }

    public function inputSchema(TariffItem $item): array
    {
        return [
            ['key' => 'trees_count',   'label' => 'تعداد درخت',   'type' => 'number', 'required' => true],
            ['key' => 'circumference', 'label' => 'محیط بن هر درخت', 'type' => 'number', 'unit' => 'سانتی‌متر', 'required' => true],
            ['key' => 'has_permit',    'label' => 'مجوز دارد؟',   'type' => 'select', 'required' => true, 'options' => ['yes' => 'بله', 'no' => 'خیر']],
        ];
    }

    public function calculate(TariffItem $item, array $inputs, ?TariffSet $set = null): CalculationResult
    {
        $count = $this->requiredNumber($inputs, 'trees_count', 'تعداد درخت');
        $circ  = $this->requiredNumber($inputs, 'circumference', 'محیط بن');
        $permit= $this->requiredChoice($inputs, 'has_permit', ['yes','no'], 'مجوز');

        $rate     = (float) $this->itemParam($item, 'rate_per_cm', 0);
        $base     = $count * $circ * $rate;
        $mult     = $permit === 'yes' ? 1 : 5;
        $total    = $this->toInt($base * $mult);

        return new CalculationResult(
            total: $total,
            steps: [
                $this->step('محاسبه پایه', $this->toInt($base), sprintf('%s درخت × %s cm × %s ریال', number_format($count), number_format($circ), number_format($rate))),
                $this->step('اعمال ضریب مجوز', $total, '× ' . $mult),
                $this->step('نهال جایگزین (اطلاع‌رسانی)', 0, '۵ اصله نهال به ازای هر درخت'),
            ],
            meta: ['has_permit' => $permit],
        );
    }
}
