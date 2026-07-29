<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->create([
            'role_name' => User::ROLE_ADMIN,
        ]);

        $response = $this->withToken(JWTAuth::fromUser($admin))
            ->getJson('/api/v1/admin/dashboard');

        $response
            ->assertOk()
            ->assertJsonPath('data.admin.id', $admin->id)
            ->assertJsonPath('data.admin.role_name', User::ROLE_ADMIN)
            ->assertJsonStructure([
                'data' => [
                    'total_users',
                    'total_buyers',
                    'total_merchants',
                    'total_locations',
                ],
            ]);
    }

    public function test_unauthenticated_user_cannot_access_admin_routes(): void
    {
        $this->getJson('/api/v1/admin/dashboard')->assertUnauthorized();
    }

    public function test_buyer_cannot_access_admin_routes(): void
    {
        $buyer = User::factory()->create([
            'role_name' => User::ROLE_USER,
        ]);

        $this->withToken(JWTAuth::fromUser($buyer))
            ->getJson('/api/v1/admin/dashboard')
            ->assertForbidden();
    }

    public function test_merchant_cannot_access_admin_routes(): void
    {
        $merchant = User::factory()->create([
            'role_name' => User::ROLE_MERCHANT,
            'is_merchant' => true,
        ]);

        $this->withToken(JWTAuth::fromUser($merchant))
            ->getJson('/api/v1/admin/dashboard')
            ->assertForbidden();
    }
}
