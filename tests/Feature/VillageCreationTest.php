<?php

namespace Tests\Feature;

use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VillageCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_village_creates_dehyari_and_inactive_bank_account(): void
    {
        $village = Village::factory()->create();

        $this->assertDatabaseHas('dehyaris', [
            'village_id' => $village->id,
            'is_active' => 1,
        ]);

        $this->assertDatabaseHas('bank_accounts', [
            'village_id' => $village->id,
            'is_default' => 1,
            'is_active' => 0,
        ]);
    }

    public function test_village_slug_and_flags_are_persisted(): void
    {
        $village = Village::factory()->create([
            'name' => 'روستای نمونه',
            'slug' => 'sample-village',
        ]);

        $fresh = Village::where('slug', 'sample-village')->firstOrFail();

        $this->assertSame($village->id, $fresh->id);
        $this->assertSame('روستای نمونه', $fresh->name);
        $this->assertTrue((bool) $fresh->is_active);
    }
}
