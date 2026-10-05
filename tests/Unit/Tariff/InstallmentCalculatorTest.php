<?php

namespace Tests\Unit\Tariff;

use App\Models\TariffInstallmentRule;
use App\Services\Tariff\InstallmentCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class InstallmentCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private function oneThirdRule(): TariffInstallmentRule
    {
        return TariffInstallmentRule::create([
            'kind'                     => 'one_third_cash',
            'down_payment_percent'     => 33.33,
            'max_installments'         => 6,
            'monthly_interest_percent' => 0,
            'max_total_interest_percent' => 0,
            'is_active'                => true,
        ]);
    }

    private function quarterRule(): TariffInstallmentRule
    {
        return TariffInstallmentRule::create([
            'kind'                     => 'quarter_down_payment',
            'down_payment_percent'     => 25,
            'max_installments'         => 36,
            'monthly_interest_percent' => 2,
            'max_total_interest_percent' => 24,
            'is_active'                => true,
        ]);
    }

    public function test_one_third_cash_no_interest(): void
    {
        $svc = app(InstallmentCalculator::class);
        $result = $svc->split(10_000_000, $this->oneThirdRule());

        $this->assertSame('one_third_cash', $result['kind']);
        $this->assertSame(6, $result['installments_count']);
        $this->assertSame(0, $result['total_interest']);
        $this->assertGreaterThan(3_000_000, $result['down_payment']);
        $this->assertLessThan(3_400_000, $result['down_payment']);
    }

    public function test_quarter_down_payment_with_cap(): void
    {
        $svc = app(InstallmentCalculator::class);
        $result = $svc->split(100_000_000, $this->quarterRule());

        $this->assertSame(25_000_000, $result['down_payment']);
        $this->assertSame(36, $result['installments_count']);
        // سود خام = 75M × 0.02 × 36 = 54M → به سقف 24M محدود شد
        $this->assertSame(24_000_000, $result['total_interest']);
        $this->assertNotEmpty($result['warnings']);
    }

    public function test_zero_amount_throws(): void
    {
        $svc = app(InstallmentCalculator::class);
        $this->expectException(InvalidArgumentException::class);
        $svc->split(0, $this->oneThirdRule());
    }

    public function test_penalty_title_not_installable(): void
    {
        $title = \App\Models\TariffTitle::create([
            'code' => 'P99', 'type' => 'penalty',
            'name_fa' => 'جریمه ۹۹', 'formula_key' => 'article_99_penalty',
            'is_active' => true,
        ]);

        $svc = app(InstallmentCalculator::class);
        $this->assertFalse($svc->canBeInstallment($title));
    }
}
