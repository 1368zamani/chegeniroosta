<?php

namespace Tests\Feature\Tariff;

use App\Models\TariffSet;
use App\Models\TariffWorkflowLog;
use App\Services\Tariff\TariffSetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TariffWorkflowLogTest extends TestCase
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

    public function test_log_record_creates_entry(): void
    {
        $set = $this->makeSet();

        $log = TariffWorkflowLog::record($set, 'draft', 'proposed', null, 'تست');

        $this->assertDatabaseHas('tariff_workflow_logs', [
            'tariff_set_id' => $set->id,
            'from_status'   => 'draft',
            'to_status'     => 'proposed',
        ]);
        $this->assertSame('پیشنهادی دهیار', $log->toLabel());
    }

    public function test_transition_auto_logs(): void
    {
        $set = $this->makeSet();
        $svc = app(TariffSetService::class);

        $svc->propose($set);

        $this->assertDatabaseHas('tariff_workflow_logs', [
            'tariff_set_id' => $set->id,
            'to_status'     => 'proposed',
        ]);
    }

    public function test_full_lifecycle_logs_five_transitions(): void
    {
        $set = $this->makeSet();
        $svc = app(TariffSetService::class);

        $svc->propose($set);
        $svc->approveByVillageCouncil($set);
        $svc->approveByDistrictCouncil($set);
        $svc->finalize($set);
        $svc->publish($set);

        $logs = TariffWorkflowLog::where('tariff_set_id', $set->id)->get();
        $this->assertCount(5, $logs);
        $this->assertSame(
            ['proposed', 'village_council_approved', 'district_council_approved', 'finalized', 'published'],
            $logs->pluck('to_status')->toArray()
        );
    }
}
