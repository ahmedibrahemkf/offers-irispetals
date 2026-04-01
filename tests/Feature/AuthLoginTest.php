<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_username(): void
    {
        $user = User::query()->create([
            'name' => 'Owner',
            'username' => 'owner',
            'role' => 'owner',
            'is_active' => true,
            'password' => Hash::make('Owner@123456'),
        ]);

        $response = $this->post('/admin/login', [
            'identity' => 'owner',
            'password' => 'Owner@123456',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertEquals($user->id, session('auth_user_id'));
    }
}
