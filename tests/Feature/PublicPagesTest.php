<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_load(): void
    {
        $this->get('/')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/sitemap.xml')->assertOk();
    }

    public function test_guest_cannot_open_admin_pages(): void
    {
        $this->get('/admin/sections')->assertRedirect('/login');
    }
}
