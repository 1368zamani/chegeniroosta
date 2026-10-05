<?php

namespace App\Services\Tariff;

use App\Models\TariffInstallmentRule;
use InvalidArgumentException;

/**
 * تقسیط مبلغ عوارض/بهای خدمات.
 *
 * قواعد:
 *  - one_third_cash: ۱/۳ نقد + مابقی ۶ قسط مساوی بدون کارمزد
 *  - quarter_down_payment: ۲۵٪ پیش‌پرداخت + مابقی تا ۳۶ قسط با ۲٪ ماهانه (سقف ۲۴٪)
 *
 * جرایم ماده ۹۹ قابل تقسیط نیستند (is_penalty_eligible=false).
 *
 * TODO(verify): روش محاسبه‌ی سود (ساده روی کل مبلغ یا روش اقساط مساوی با فرمول).
 *              این پیاده‌سازی از سود ساده استفاده می‌کند.
 */
class InstallmentCalculator
{
    /**
     * @return array{
     *   kind: string,
     *   amount: int,
     *   down_payment: int,
     *   installments_count: int,
     *   per_installment: int,
     *   total_interest: int,
     *   total_payable: int,
     *   schedule: array<int, array{month:int, amount:int}>
     * }
     */
    public function split(int $amount, TariffInstallmentRule $rule): array
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('مبلغ باید بزرگ‌تر از صفر باشد.');
        }

        return match ($rule->kind) {
            'one_third_cash'       => $this->splitOneThirdCash($amount, $rule),
            'quarter_down_payment' => $this->splitQuarterDown($amount, $rule),
            default => throw new InvalidArgumentException("نوع قاعده‌ی تقسیط نامعتبر: {$rule->kind}"),
        };
    }

    private function splitOneThirdCash(int $amount, TariffInstallmentRule $rule): array
    {
        $downPct = (float) $rule->down_payment_percent ?: 33.33;
        $n = max(1, (int) $rule->max_installments);

        $down = (int) round($amount * $downPct / 100);
        $remaining = $amount - $down;
        $per = (int) ceil($remaining / $n);

        $schedule = [];
        for ($i = 1; $i <= $n; $i++) {
            $schedule[] = ['month' => $i, 'amount' => $per];
        }

        return [
            'kind'                => 'one_third_cash',
            'amount'              => $amount,
            'down_payment'        => $down,
            'installments_count'  => $n,
            'per_installment'     => $per,
            'total_interest'      => 0,
            'total_payable'       => $amount,
            'schedule'            => $schedule,
            'description'         => '۱/۳ نقد + ' . $n . ' قسط مساوی بدون کارمزد',
        ];
    }

    private function splitQuarterDown(int $amount, TariffInstallmentRule $rule): array
    {
        $downPct = (float) $rule->down_payment_percent ?: 25;
        $n = max(1, (int) $rule->max_installments);
        $monthlyRate = (float) $rule->monthly_interest_percent / 100;
        $capRate     = (float) $rule->max_total_interest_percent / 100;

        $down = (int) round($amount * $downPct / 100);
        $remaining = $amount - $down;

        $interest = (int) round($remaining * $monthlyRate * $n);
        $maxInterest = (int) round($amount * $capRate);
        $warnings = [];
        if ($interest > $maxInterest) {
            $warnings[] = 'سود محاسبه‌شده از سقف مجاز (' . ($capRate * 100) . '٪) بیشتر بود و به سقف محدود شد.';
            $interest = $maxInterest;
        }

        $totalPayable = $remaining + $interest;
        $per = (int) ceil($totalPayable / $n);

        $schedule = [];
        for ($i = 1; $i <= $n; $i++) {
            $schedule[] = ['month' => $i, 'amount' => $per];
        }

        return [
            'kind'                => 'quarter_down_payment',
            'amount'              => $amount,
            'down_payment'        => $down,
            'installments_count'  => $n,
            'per_installment'     => $per,
            'total_interest'      => $interest,
            'total_payable'       => $down + $totalPayable,
            'schedule'            => $schedule,
            'warnings'            => $warnings,
            'description'         => $downPct . '٪ پیش‌پرداخت + ' . $n . ' قسط با ' . ($monthlyRate * 100) . '٪ ماهانه',
        ];
    }

    /** بررسی اینکه آیا یک عنوان قابل تقسیط است */
    public function canBeInstallment(\App\Models\TariffTitle $title): bool
    {
        // جرایم ماده ۹۹ قابل تقسیط نیستند
        return $title->type !== 'penalty' && $title->formula_key !== 'article_99_penalty';
    }
}
