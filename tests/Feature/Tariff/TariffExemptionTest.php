<?php

namespace Tests\Feature\Tariff;

use App\Models\TariffExemption;
use App\Models\TariffSet;
use App\Models\TariffTitle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TariffExemptionTest extends TestCase
{
    use RefreshDatabase;

    private function makeSet(): TariffSet
    {
        return TariffSet::create([
            'village_id'     => 1,
            'jalali_year'    => 1405,
            'inflation_rate' => 40,
            'status'         => 'draft',
        ]);
    }

    public function test_full_exemption_returns_zero(): void
    {
        $set = $this->makeSet();
        $ex = TariffExemption::create([
            'tariff_set_id'    => $set->id,
            'kind'             => 'disabled',
            'discount_percent' => 100,
            'description'      => 'معلولان',
            'is_active'        => true,
        ]);

        $this->assertTrue($ex->isFullExemption());
        $this->assertSame(0, $ex->applyDiscount(1_000_000));
    }

    public function test_partial_exemption_returns_half(): void
    {
        $set = $this->makeSet();
        $ex = TariffExemption::create([
            'tariff_set_id'    => $set->id,
            'kind'             => 'elderly',
            'discount_percent' => 50,
            'description'      => 'سالمندان',
            'is_active'        => true,
        ]);

        $this->assertFalse($ex->isFullExemption());
        $this->assertSame(500_000, $ex->applyDiscount(1_000_000));
    }

    public function test_inactive_exemption_does_not_apply(): void
    {
        $set = $this->makeSet();
        $ex = TariffExemption::create([
            'tariff_set_id'    => $set->id,
            'kind'             => 'custom',
            'discount_percent' => 100,
            'description'      => 'تست',
            'is_active'        => false,
        ]);

        $this->assertFalse($ex->appliesNow());
    }

    public function test_kind_label_translation(): void
    {
        $set = $this->makeSet();
        $ex = TariffExemption::create([
            'tariff_set_id'    => $set->id,
            'kind'             => 'martyr_family',
            'discount_percent' => 100,
            'description'      => 'x',
            'is_active'        => true,
        ]);

        $this->assertStringContainsString('شهدا', $ex->kindLabel());
    }
}
