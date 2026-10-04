<?php

namespace App\Services\Tariff;

use App\Models\TariffInstallmentRule;
use InvalidArgumentException;

/**
 * محاسبه‌گر تقسیط عوارض.
 *
 * قواعد:
 *  ۱. یک‌سوم نقد → ۶ قسط مساوی بدون کارمزد
 *  ۲. ۲۵٪ پیش‌پرداخت → تا ۳۶ قسط با ۲٪ ماهانه (سقف ۲۴٪)
 *  ۳. جرایم ماده ۹۹ قابل تقسیط نیستند
 */
class InstallmentCalculator
{
    /**
     * @return array{
     *   ok: bool,
     *   error?: string,
     *   kind?: string,
     *   down_payment?: int,
     *   remaining?: int,
     *   installments_count?: int,
     *   installment_amount?: int,
     *   total_with_interest?: int,
     *   total_interest?: int,
     *   schedule?: array<int, array{number:int, due_date:string, amount:int}>
     * }
     */
    public function calculate(int $totalAmount, string $kind, bool $isPenalty = false, ?int $startYear = null, ?int $startMonth = null): array
    {
        if ($isPenalty) {
            return ['ok' => false, 'error' => 'جرایم ماده ۹۹ قابل تقسیط نیستند.'];
        }

        if ($totalAmount <= 0) {
            return ['ok' => false, 'error' => 'مبلغ نامعتبر است.'];
        }

        $rule = TariffInstallmentRule::where('kind', $kind)->where('is_active', true)->first();
        if (!$rule) {
            return ['ok' => false, 'error' => 'قاعده تقسیط یافت نشد.'];
        }

        $down = (int) round($totalAmount * ($rule->down_payment_percent / 100));
        $remaining = $totalAmount - $down;

        $count = $rule->max_installments;
        $monthlyInterest = (float) $rule->monthly_interest_percent / 100;
        $maxTotalInterest = (float) $rule->max_total_interest_percent / 100;

        // محاسبه سود کل (ساده — روی مبلغ باقی‌مانده)
        $totalInterest = (int) round($remaining * $monthlyInterest * $count);
        if ($maxTotalInterest > 0) {
            $cap = (int) round($remaining * $maxTotalInterest);
            if ($totalInterest > $cap) $totalInterest = $cap;
        }

        $installmentAmount = (int) round(($remaining + $totalInterest) / $count);

        // ساخت جدول
        $schedule = [];
        $year = $startYear ?? (int) \Morilog\Jalali\Jalalian::now()->getYear();
        $month = $startMonth ?? (int) \Morilog\Jalali\Jalalian::now()->getMonth();

        for ($i = 1; $i <= $count; $i++) {
            $month++;
            if ($month > 12) { $month = 1; $year++; }
            $schedule[] = [
                'number' => $i,
                'due_date' => sprintf('%04d/%02d/15', $year, $month),
                'amount' => $installmentAmount,
            ];
        }

        return [
            'ok' => true,
            'kind' => $rule->kindLabel(),
            'down_payment' => $down,
            'remaining' => $remaining,
            'installments_count' => $count,
            'installment_amount' => $installmentAmount,
            'total_with_interest' => $totalAmount + $totalInterest,
            'total_interest' => $totalInterest,
            'schedule' => $schedule,
        ];
    }
}
