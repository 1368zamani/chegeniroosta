<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // از دیتابیس واقعی استفاده کن، نه in-memory
        config(['database.default' => 'mysql']);
        \DB::purge('mysql');
    }

    public function test_section_manager_can_open_fee_categories(): void
    {
        $u = User::where('email', 'visian@chegeniroosta.ir')->firstOrFail();

        $res = $this->actingAs($u)->get('/admin/fees/categories');
        $res->assertStatus(200);
    }
}
