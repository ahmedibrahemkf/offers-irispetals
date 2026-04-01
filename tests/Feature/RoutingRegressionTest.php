<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoutingRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_without_redirect_to_index_html(): void
    {
        $this->get('/')
            ->assertStatus(200);
    }

    public function test_guest_admin_root_redirects_to_admin_login_not_admin_html(): void
    {
        $response = $this->get('/admin');

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.login', absolute: false));
        $this->assertStringNotContainsString('admin.html', $response->headers->get('Location', ''));
    }

    public function test_admin_login_route_is_accessible(): void
    {
        $this->get('/admin/login')
            ->assertStatus(200);
    }
}

