<?php

namespace Tests\Feature;

use App\Models\TariffSet;
use App\Models\TariffTitle;
use App\Models\User;
use App\Models\Village;
use App\Services\Tariff\TariffSetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TariffWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_workflow_from_draft_to_published(): void
    {
        $village = Village::factory()->create(['name' => 'تست', 'slug' => 'test']);
        $svc = app(TariffSetService::class);

        $set = $svc->createForVillage($village, 1405);
        $this->assertEquals('draft', $set->status);

        $svc->propose($set);
        $this->assertEquals('proposed', $set->fresh()->status);

        $svc->approveByVillageCouncil($set);
        $this->assertEquals('village_council_approved', $set->fresh()->status);

        $svc->approveByDistrictCouncil($set);
        $this->assertEquals('district_council_approved', $set->fresh()->status);

        $svc->finalize($set);
        $this->assertEquals('finalized', $set->fresh()->status);
        $this->assertTrue($set->fresh()->isLocked());

        $svc->publish($set);
        $this->assertEquals('published', $set->fresh()->status);
    }

    public function test_exceeds_inflation_throws_on_approve(): void
    {
        $village = Village::factory()->create();
        $svc = app(TariffSetService::class);
        $set = $svc->createForVillage($village, 1405);

        // افزایش از تورم بیشتر
        $item = $set->items()->first();
        $item->update(['increase_percent' => 100]); // تورم 40%

        $svc->propose($set);

        $this->expectException(\InvalidArgumentException::class);
        $svc->approveByVillageCouncil($set);
    }

    public function test_locked_set_cannot_be_modified(): void
    {
        $village = Village::factory()->create();
        $svc = app(TariffSetService::class);
        $set = $svc->createForVillage($village, 1405);

        $set->update(['status' => 'finalized', 'locked_at' => now()]);
        $this->assertTrue($set->fresh()->isLocked());
    }
}
