<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

/**
 * عوارض مشاغل دائم و موقت — L09
 *
 * فرمول: A = base × street_factor × delay_penalty
 *  base          = مبلغ پایه صنف (ریال) — از itemParam یا سطر جدول params
 *  street_factor = ضریب معبر (اصلی 1.0، فرعی 0.7) — از itemParam
 *  delay_penalty = ضریب جریمه تأخیر (1.0 = بدون تأخیر، 1.025 = ۲.۵٪)
 */
class BusinessesCalculator extends BaseCalculator
{
    public function key(): string { return 'businesses'; }
    public function label(): string { return 'عوارض مشاغل'; }

    public function inputSchema(TariffItem $item): array
    {
        return [
            ['key' => 'business_type', 'label' => 'نوع صنف', 'type' => 'select', 'required' => true, 'options' => [
                'general'     => 'عمومی',
                'food'        => 'مواد غذایی',
                'clothing'    => 'پوشاک',
                'services'    => 'خدماتی',
                'repair'      => 'تعمیرات',
                'other'       => 'سایر',
            ]],
            ['key' => 'street_type', 'label' => 'نوع معبر', 'type' => 'select', 'required' => true, 'options' => [
                'main'   => 'معبر اصلی (۱۰۰٪)',
                'side'   => 'معبر فرعی (۷۰٪)',
            ]],
            ['key' => 'delay', 'label' => 'تأخیر در پرداخت', 'type' => 'select', 'required' => true, 'options' => [
                'no'  => 'بدون تأخیر',
                'yes' => 'با تأخیر (۲٫۵٪ جریمه)',
            ]],
        ];
    }

    public function calculate(TariffItem $item, array $inputs, ?TariffSet $set = null): CalculationResult
    {
        $type  = $this->requiredChoice($inputs, 'business_type', ['general','food','clothing','services','repair','other'], 'نوع صنف');
        $street= $this->requiredChoice($inputs, 'street_type', ['main','side'], 'نوع معبر');
        $delay = $this->requiredChoice($inputs, 'delay', ['no','yes'], 'تأخیر');

        $baseRate       = (float) $this->itemParam($item, "base_rate_$type", 0);
        $streetFactor   = (float) $this->itemParam($item, "street_factor_$street", $street === 'main' ? 1.0 : 0.7);
        $delayPenalty   = $delay === 'yes' ? (float) $this->itemParam($item, 'delay_penalty_factor', 1.025) : 1.0;

        $afterStreet = $baseRate * $streetFactor;
        $total       = $this->toInt($afterStreet * $delayPenalty);

        return new CalculationResult(
            total: $total,
            steps: [
                $this->step('مبلغ پایه صنف', $this->toInt($baseRate), number_format($baseRate) . ' ریال'),
                $this->step('اعمال ضریب معبر', $this->toInt($afterStreet), '× ' . $streetFactor),
                $this->step('جریمه تأخیر', $total, '× ' . $delayPenalty),
            ],
            meta: ['type' => $type, 'street' => $street, 'delay' => $delay],
        );
    }
}
