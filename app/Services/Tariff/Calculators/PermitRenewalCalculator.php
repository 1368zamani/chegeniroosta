<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

/**
 * عوارض تمدید / تجدید پروانه ساختمانی — L07
 *
 * قواعد قانونی:
 *  سال اول  = رایگان
 *  سال دوم  = حداکثر ۳٪ عوارض صدور پروانه
 *  سال‌های بعد = هر سال ۲٪ افزایش تا سقف ۲۰٪
 *
 * فرمول: A = permit_fee × cumulative_percent
 */
class PermitRenewalCalculator extends BaseCalculator
{
    public function key(): string { return 'permit_renewal'; }
    public function label(): string { return 'تمدید پروانه ساختمانی'; }

    public function inputSchema(TariffItem $item): array
    {
        return [
            ['key' => 'permit_fee', 'label' => 'مبلغ عوارض صدور پروانه', 'type' => 'number', 'unit' => 'ریال', 'required' => true],
            ['key' => 'years_since', 'label' => 'چند سال از صدور گذشته؟', 'type' => 'number', 'required' => true],
        ];
    }

    public function calculate(TariffItem $item, array $inputs, ?TariffSet $set = null): CalculationResult
    {
        $fee   = $this->requiredNumber($inputs, 'permit_fee', 'مبلغ عوارض صدور پروانه');
        $years = (int) $this->requiredNumber($inputs, 'years_since', 'تعداد سال');

        $firstYearFree  = (bool) $this->itemParam($item, 'first_year_free', true);
        $year2Percent   = (float) $this->itemParam($item, 'year2_percent', 3.0);
        $afterPercent   = (float) $this->itemParam($item, 'after_year_percent', 2.0);
        $capPercent     = (float) $this->itemParam($item, 'cap_percent', 20.0);

        if ($firstYearFree && $years <= 1) {
            return new CalculationResult(
                total: 0,
                steps: [$this->step('سال اول رایگان', 0, 'طبق قانون تمدید در سال اول هزینه ندارد')],
                meta: ['years' => $years, 'percent' => 0],
            );
        }

        if ($years === 2) {
            $percent = $year2Percent;
        } else {
            $percent = $year2Percent + (($years - 2) * $afterPercent);
        }

        if ($percent > $capPercent) {
            $percent = $capPercent;
        }

        $total = $this->toInt($fee * ($percent / 100));

        return new CalculationResult(
            total: $total,
            steps: [
                $this->step('مبلغ پایه', $this->toInt($fee), number_format($fee) . ' ریال'),
                $this->step('درصد تجمعی', $this->toInt($percent * 100), number_format($percent, 2) . '%'),
                $this->step('مبلغ تمدید', $total, null),
                $this->step('سقف قانونی', $this->toInt($capPercent * 100), 'حداکثر ' . $capPercent . '%'),
            ],
            meta: ['years' => $years, 'percent' => $percent],
        );
    }
}
