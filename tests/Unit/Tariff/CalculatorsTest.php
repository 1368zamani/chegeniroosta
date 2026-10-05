<?php

namespace Tests\Unit\Tariff;

use App\Models\TariffItem;
use App\Models\TariffParameter;
use App\Models\TariffSet;
use App\Models\TariffTitle;
use App\Services\Tariff\Calculators\AnnualPropertyCalculator;
use App\Services\Tariff\Calculators\BusinessCalculator;
use App\Services\Tariff\Calculators\ExcavationCalculator;
use App\Services\Tariff\Calculators\FenceCalculator;
use App\Services\Tariff\Calculators\MachineryCalculator;
use App\Services\Tariff\Calculators\PermitRenewalCalculator;
use App\Services\Tariff\Calculators\SubBuildingCalculator;
use App\Services\Tariff\Calculators\TreeCuttingCalculator;
use App\Services\Tariff\Calculators\ValueAddedCalculator;
use App\Services\Tariff\Calculators\WasteCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CalculatorsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // SQLite در حافظه - غیرفعال کردن FK enforcement (Schema::disable... در SQLite کار نمی‌کند)
        \Illuminate\Support\Facades\DB::statement('PRAGMA foreign_keys = OFF');
    }

    private function makeSet(): TariffSet
    {
        return TariffSet::create([
            'village_id'     => 1,
            'jalali_year'    => 1405,
            'inflation_rate' => 40,
            'status'         => 'draft',
        ]);
    }

    private function makeItem(TariffSet $set, string $formulaKey, array $params = []): TariffItem
    {
        $title = TariffTitle::create([
            'code'        => 'T' . uniqid(),
            'type'        => 'levy',
            'name_fa'     => 'تست ' . $formulaKey,
            'formula_key' => $formulaKey,
            'is_active'   => true,
        ]);
        return TariffItem::create([
            'tariff_set_id'   => $set->id,
            'tariff_title_id' => $title->id,
            'is_active'       => true,
            'params'          => $params,
        ]);
    }

    // ─── عوارض سالیانه املاک ───
    public function test_annual_property_calculator(): void
    {
        $set = $this->makeSet();
        TariffParameter::put($set, 'land_value_per_m2', 50_000_000, 'rial');
        TariffParameter::put($set, 'building_value_per_m2', 20_000_000, 'rial');
        TariffParameter::put($set, 'adjustment_coefficient', 0.115, null);
        $item = $this->makeItem($set, 'annual_property');

        $result = (new AnnualPropertyCalculator())->calculate($set, $item, [
            'land_area' => 300, 'building_area' => 200, 'usage' => 'residential',
        ]);

        // (50M*300 + 20M*200) * 0.115 * 0.12 = 262,200,000
        $this->assertSame(262_200_000, $result->amount);
        $this->assertNotEmpty($result->warnings);
    }

    public function test_annual_property_user_usage_factors_override(): void
    {
        $set = $this->makeSet();
        TariffParameter::put($set, 'land_value_per_m2', 10_000_000, 'rial');
        TariffParameter::put($set, 'building_value_per_m2', 0, 'rial');
        TariffParameter::put($set, 'adjustment_coefficient', 0.10, null);
        $item = $this->makeItem($set, 'annual_property', [
            'usage_factors' => ['residential' => 0.50],
        ]);

        $result = (new AnnualPropertyCalculator())->calculate($set, $item, [
            'land_area' => 100, 'building_area' => 0, 'usage' => 'residential',
        ]);

        // (10M*100) * 0.10 * 0.50 = 50,000,000
        $this->assertSame(50_000_000, $result->amount);
    }

    // ─── زیربنا ───
    public function test_sub_building_calculator(): void
    {
        $set = $this->makeSet();
        TariffParameter::put($set, 'building_value_per_m2', 10_000_000, 'rial');
        TariffParameter::put($set, 'adjustment_coefficient', 0.115, null);
        $item = $this->makeItem($set, 'sub_building');

        $result = (new SubBuildingCalculator())->calculate($set, $item, [
            'area' => 200, 'kind' => 'residential_new',
        ]);

        // 10M * 0.115 * 200 * 1.0 = 230,000,000
        $this->assertSame(230_000_000, $result->amount);
    }

    // ─── حصارکشی ───
    public function test_fence_calculator_basic(): void
    {
        $set = $this->makeSet();
        $item = $this->makeItem($set, 'fence_permit', [
            'type_rates_per_meter' => ['brick' => 2_000_000],
            'height_factor_over_1_5' => 1.3,
        ]);

        $result = (new FenceCalculator())->calculate($set, $item, [
            'length' => 20, 'type' => 'brick', 'height_over_1_5' => false,
        ]);

        // 20 * 2M = 40,000,000
        $this->assertSame(40_000_000, $result->amount);
    }

    public function test_fence_calculator_height_over_1_5(): void
    {
        $set = $this->makeSet();
        $item = $this->makeItem($set, 'fence_permit', [
            'type_rates_per_meter' => ['brick' => 2_000_000],
            'height_factor_over_1_5' => 1.5,
        ]);

        $result = (new FenceCalculator())->calculate($set, $item, [
            'length' => 10, 'type' => 'brick', 'height_over_1_5' => true,
        ]);

        // 10 * 2M * 1.5 = 30,000,000
        $this->assertSame(30_000_000, $result->amount);
    }

    // ─── قطع درخت ───
    public function test_tree_cutting_with_permit(): void
    {
        $set = $this->makeSet();
        $item = $this->makeItem($set, 'tree_cutting', [
            'rate_per_cm' => 500_000,
            'sapling_value' => 1_000_000,
            'sapling_count' => 5,
        ]);

        $result = (new TreeCuttingCalculator())->calculate($set, $item, [
            'circumference_cm' => 100, 'has_permit' => true,
        ]);

        // 100 * 500k + 5 * 1M = 50M + 5M = 55,000,000
        $this->assertSame(55_000_000, $result->amount);
    }

    public function test_tree_cutting_without_permit_5x(): void
    {
        $set = $this->makeSet();
        $item = $this->makeItem($set, 'tree_cutting', [
            'rate_per_cm' => 500_000,
            'sapling_value' => 1_000_000,
            'sapling_count' => 5,
            'unauthorized_factor' => 5,
        ]);

        $result = (new TreeCuttingCalculator())->calculate($set, $item, [
            'circumference_cm' => 100, 'has_permit' => false,
        ]);

        // (50M + 5M) * 5 = 275,000,000
        $this->assertSame(275_000_000, $result->amount);
    }

    // ─── مشاغل ───
    public function test_business_calculator_main_street(): void
    {
        $set = $this->makeSet();
        $item = $this->makeItem($set, 'businesses');

        $result = (new BusinessCalculator())->calculate($set, $item, [
            'base_rate' => 10_000_000, 'street' => 'main',
        ]);

        $this->assertSame(10_000_000, $result->amount);
    }

    public function test_business_calculator_secondary_street(): void
    {
        $set = $this->makeSet();
        $item = $this->makeItem($set, 'businesses');

        $result = (new BusinessCalculator())->calculate($set, $item, [
            'base_rate' => 10_000_000, 'street' => 'secondary',
        ]);

        // 10M * 0.7 = 7,000,000
        $this->assertSame(7_000_000, $result->amount);
    }

    // ─── تمدید پروانه ───
    public function test_permit_renewal_year1_free(): void
    {
        $set = $this->makeSet();
        $item = $this->makeItem($set, 'permit_renewal');

        $result = (new PermitRenewalCalculator())->calculate($set, $item, [
            'years_since_issue' => 1, 'original_permit_fee' => 100_000_000,
        ]);

        $this->assertSame(0, $result->amount);
    }

    public function test_permit_renewal_year2_3_percent(): void
    {
        $set = $this->makeSet();
        $item = $this->makeItem($set, 'permit_renewal');

        $result = (new PermitRenewalCalculator())->calculate($set, $item, [
            'years_since_issue' => 2, 'original_permit_fee' => 100_000_000,
        ]);

        $this->assertSame(3_000_000, $result->amount);
    }

    public function test_permit_renewal_caps_at_20_percent(): void
    {
        $set = $this->makeSet();
        $item = $this->makeItem($set, 'permit_renewal');

        $result = (new PermitRenewalCalculator())->calculate($set, $item, [
            'years_since_issue' => 50, 'original_permit_fee' => 100_000_000,
        ]);

        // سقف ۲۰٪
        $this->assertSame(20_000_000, $result->amount);
    }

    // ─── ارزش افزوده ───
    public function test_value_added_normal(): void
    {
        $set = $this->makeSet();
        $item = $this->makeItem($set, 'value_added');

        $result = (new ValueAddedCalculator())->calculate($set, $item, [
            'value_added' => 1_000_000_000, 'coefficient' => 0.20,
        ]);

        $this->assertSame(200_000_000, $result->amount);
    }

    public function test_value_added_capped_at_30_percent(): void
    {
        $set = $this->makeSet();
        $item = $this->makeItem($set, 'value_added');

        $result = (new ValueAddedCalculator())->calculate($set, $item, [
            'value_added' => 1_000_000_000, 'coefficient' => 0.50,
        ]);

        // به سقف ۳۰٪ محدود می‌شود
        $this->assertSame(300_000_000, $result->amount);
        $this->assertNotEmpty($result->warnings);
    }

    // ─── ماشین‌آلات ───
    public function test_machinery_hourly(): void
    {
        $set = $this->makeSet();
        $item = $this->makeItem($set, 'machinery_hourly');

        $result = (new MachineryCalculator())->calculate($set, $item, [
            'hourly_rate' => 5_000_000, 'hours' => 8,
        ]);

        $this->assertSame(40_000_000, $result->amount);
    }

    // ─── حفاری ───
    public function test_excavation_calculator(): void
    {
        $set = $this->makeSet();
        $item = $this->makeItem($set, 'excavation');

        $result = (new ExcavationCalculator())->calculate($set, $item, [
            'length_m' => 50, 'street' => 'asphalt', 'width_factor' => 1.0,
        ]);

        // 50 * 800k = 40,000,000
        $this->assertSame(40_000_000, $result->amount);
    }

    // ─── پسماند ───
    public function test_waste_calculator_residential(): void
    {
        $set = $this->makeSet();
        $item = $this->makeItem($set, 'waste_residential');

        $result = (new WasteCalculator())->calculate($set, $item, [
            'category' => 'residential', 'quantity' => 3,
        ]);

        // 3 * 2M = 6,000,000
        $this->assertSame(6_000_000, $result->amount);
    }

    public function test_waste_calculator_commercial(): void
    {
        $set = $this->makeSet();
        $item = $this->makeItem($set, 'waste_commercial');

        $result = (new WasteCalculator())->calculate($set, $item, [
            'category' => 'commercial', 'quantity' => 1,
        ]);

        $this->assertSame(5_000_000, $result->amount);
    }
}
