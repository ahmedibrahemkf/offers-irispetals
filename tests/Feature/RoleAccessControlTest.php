<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleAccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_cannot_open_settings_page(): void
    {
        $staff = User::query()->create([
            'name' => 'Staff',
            'username' => 'staff',
            'role' => 'staff',
            'is_active' => true,
            'password' => Hash::make('Password@123'),
        ]);

        $this->withSession(['auth_user_id' => $staff->id])
            ->get(route('admin.settings.index'))
            ->assertForbidden();
    }

    public function test_viewer_can_open_reports_but_not_orders(): void
    {
        $viewer = User::query()->create([
            'name' => 'Viewer',
            'username' => 'viewer',
            'role' => 'viewer',
            'is_active' => true,
            'password' => Hash::make('Password@123'),
        ]);

        $this->withSession(['auth_user_id' => $viewer->id])
            ->get(route('admin.reports.index'))
            ->assertStatus(200);

        $this->withSession(['auth_user_id' => $viewer->id])
            ->get(route('admin.orders.index'))
            ->assertForbidden();
    }
}
