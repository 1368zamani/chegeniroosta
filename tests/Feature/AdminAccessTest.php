<?php

namespace Tests\Feature;

use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $section = $this->makeSection('بخش تست', 'test-section');

        $this->get("/admin/sections/{$section->id}/edit")
            ->assertRedirect('/login');
    }

    public function test_section_admin_can_edit_only_their_own_section(): void
    {
        $own = $this->makeSection('بخش خودم', 'own-section');
        $other = $this->makeSection('بخش دیگر', 'other-section');

        $user = User::factory()->create([
            'role' => 'section_admin',
            'section_id' => $own->id,
        ]);

        $this->actingAs($user)
            ->get("/admin/sections/{$own->id}/edit")
            ->assertOk();

        $this->actingAs($user)
            ->get("/admin/sections/{$other->id}/edit")
            ->assertForbidden();
    }

    public function test_super_admin_can_edit_any_section(): void
    {
        $section = $this->makeSection('بخش سوپر', 'super-section');

        $user = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($user)
            ->get("/admin/sections/{$section->id}/edit")
            ->assertOk();
    }

    private function makeSection(string $name, string $slug): Section
    {
        return Section::create([
            'name' => $name,
            'slug' => $slug,
        ]);
    }
}
