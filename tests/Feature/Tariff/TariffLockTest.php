<?php

namespace Tests\Feature\Tariff;

use App\Models\TariffSet;
use App\Services\Tariff\TariffSetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use Tests\TestCase;

class TariffLockTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
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

    public function test_status_lifecycle_reaches_published(): void
    {
        $svc = app(TariffSetService::class);
        $set = $this->makeSet();

        $svc->propose($set);
        $this->assertSame('proposed', $set->fresh()->status);

        $svc->approveByVillageCouncil($set);
        $this->assertSame('village_council_approved', $set->fresh()->status);

        $svc->approveByDistrictCouncil($set);
        $this->assertSame('district_council_approved', $set->fresh()->status);

        $svc->finalize($set);
        $this->assertSame('finalized', $set->fresh()->status);
        $this->assertTrue($set->fresh()->isLocked());

        $svc->publish($set);
        $this->assertSame('published', $set->fresh()->status);
        $this->assertTrue($set->fresh()->isLocked());
    }

    public function test_wrong_transition_is_rejected(): void
    {
        $svc = app(TariffSetService::class);
        $set = $this->makeSet();

        $this->expectException(InvalidArgumentException::class);
        // نمی‌توان از draft مستقیم به finalize رفت
        $svc->finalize($set);
    }

    public function test_publish_only_after_finalize(): void
    {
        $svc = app(TariffSetService::class);
        $set = $this->makeSet();

        $svc->propose($set);
        $this->expectException(InvalidArgumentException::class);
        $svc->publish($set);
    }

    public function test_inflation_validation_blocks_over_inflation(): void
    {
        $svc = app(TariffSetService::class);
        $set = $this->makeSet();

        // ساخت یک TariffItem با increase_percent بالاتر از تورم
        $title = \App\Models\TariffTitle::create([
            'code' => 'L99', 'type' => 'levy', 'name_fa' => 'تست',
            'formula_key' => 'custom', 'is_active' => true,
        ]);
        \App\Models\TariffItem::create([
            'tariff_set_id'    => $set->id,
            'tariff_title_id'  => $title->id,
            'is_active'        => true,
            'increase_percent' => 50, // تورم ۴۰٪ است
            'final_amount'     => 1000,
        ]);

        $result = $svc->validateInflation($set);
        $this->assertFalse($result['ok']);
        $this->assertNotEmpty($result['violations']);

        // باید در propose → approveByVillageCouncil شکست بخورد
        $svc->propose($set);
        $this->expectException(InvalidArgumentException::class);
        $svc->approveByVillageCouncil($set);
    }
}
