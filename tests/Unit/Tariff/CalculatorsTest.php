<?php

namespace Tests\Unit\Tariff;

use App\Models\TariffItem;
use App\Models\TariffParameter;
use App\Models\TariffSet;
use App\Services\Tariff\Calculators\AnnualPropertyLevyCalculator;
use App\Services\Tariff\Calculators\FencePermitCalculator;
use App\Services\Tariff\Calculators\SubBuildingCalculator;
use App\Services\Tariff\Calculators\TreeCuttingCalculator;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CalculatorsTest extends TestCase
{
    private function makeSet(array $params = []): TariffSet
    {
        $set = new TariffSet();
        $set->setRelation('parameters', collect(array_map(
            fn ($k, $v) => new TariffParameter(['key' => $k, 'value' => (string) $v]),
            array_keys($params),
            array_values($params)
        )));
        return $set;
    }

    private function makeItem(string $formulaKey, array $params = []): TariffItem
    {
        $item = new TariffItem(['params' => $params]);
        // title relation (بدون DB)
        $title = new class($formulaKey) extends \App\Models\TariffTitle {
            public function __construct(public ?string $fk = null) {}
        };
        $title->formula_key = $formulaKey;
        $item->setRelation('title', $title);
        return $item;
    }

    // ═══ AnnualPropertyLevy ═══
    public function test_annual_property_computes_correctly(): void
    {
        $set = $this->makeSet([
            'land_value_per_m2'     => 1_000_000,
            'building_value_per_m2' => 5_000_000,
            'adjustment_coefficient'=> 0.115,
        ]);
        $item = $this->makeItem('annual_property', ['usage_factor_residential' => 0.12]);

        $calc = new AnnualPropertyLevyCalculator();
        $r = $calc->calculate($item, [
            'land_area' => 100,
            'building_area' => 100,
            'usage' => 'residential',
        ], $set);

        // (100M + 500M) × 0.115 × 0.12 = 8,280,000
        $this->assertEquals(8_280_000, $r->total);
    }

    public function test_annual_property_rejects_negative_area(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $calc = new AnnualPropertyLevyCalculator();
        $calc->calculate($this->makeItem('annual_property'), ['land_area' => -1, 'building_area' => 10, 'usage' => 'residential'], $this->makeSet());
    }

    // ═══ SubBuilding ═══
    public function test_sub_building(): void
    {
        $set = $this->makeSet([
            'land_value_per_m2'        => 1_000_000,
            'subbuilding_coefficient'  => 0.115,
        ]);
        $item = $this->makeItem('sub_building', ['kind_factor_normal' => 1.0]);

        $calc = new SubBuildingCalculator();
        $r = $calc->calculate($item, ['area' => 100, 'kind' => 'normal'], $set);

        // 1M × 0.115 × 100 × 1 = 11,500,000
        $this->assertEquals(11_500_000, $r->total);
    }

    // ═══ FencePermit ═══
    public function test_fence_permit(): void
    {
        $item = $this->makeItem('fence_permit', [
            'type_rate_brick'    => 100_000,
            'height_factor_upto_150' => 1.0,
            'height_factor_over_150' => 1.5,
        ]);

        $calc = new FencePermitCalculator();

        $r1 = $calc->calculate($item, ['length' => 20, 'height' => 'upto_150', 'type' => 'brick']);
        $this->assertEquals(2_000_000, $r1->total); // 20 × 100k × 1

        $r2 = $calc->calculate($item, ['length' => 20, 'height' => 'over_150', 'type' => 'brick']);
        $this->assertEquals(3_000_000, $r2->total); // 20 × 100k × 1.5
    }

    // ═══ TreeCutting ═══
    public function test_tree_cutting_without_permit_is_5x(): void
    {
        $item = $this->makeItem('tree_cutting', ['rate_per_cm' => 50_000]);
        $calc = new TreeCuttingCalculator();

        $withPermit = $calc->calculate($item, ['trees_count' => 2, 'circumference' => 100, 'has_permit' => 'yes']);
        $this->assertEquals(10_000_000, $withPermit->total); // 2 × 100 × 50k

        $withoutPermit = $calc->calculate($item, ['trees_count' => 2, 'circumference' => 100, 'has_permit' => 'no']);
        $this->assertEquals(50_000_000, $withoutPermit->total); // ×5
    }

    // ═══ Validation ═══
    public function test_missing_required_input_throws(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $calc = new FencePermitCalculator();
        $calc->calculate($this->makeItem('fence_permit'), ['length' => 10], $this->makeSet());
    }

    public function test_invalid_choice_throws(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $calc = new FencePermitCalculator();
        $calc->calculate($this->makeItem('fence_permit'), [
            'length' => 10, 'height' => 'invalid', 'type' => 'brick',
        ], $this->makeSet());
    }
}
