<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTokenLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_access_and_refresh_metadata(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secure-password'),
            'role_name' => User::ROLE_ADMIN,
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $admin->email,
            'password' => 'secure-password',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.account.dashboard_path', '/admin/dashboard')
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'expired_at',
                    'refreshable_until',
                ],
            ]);

        $this->assertGreaterThan(
            $response->json('data.expired_at'),
            $response->json('data.refreshable_until')
        );
    }

    public function test_refresh_rotates_the_token_and_invalidates_the_previous_token(): void
    {
        $admin = User::factory()->create([
            'role_name' => User::ROLE_ADMIN,
        ]);
        $oldToken = JWTAuth::fromUser($admin, ['role_name' => $admin->role_name]);

        $response = $this->withToken($oldToken)->postJson('/api/v1/refresh');
        $response->assertOk();

        $newToken = $response->json('data.token');
        $this->assertNotSame($oldToken, $newToken);
        $response->assertJsonPath('data.account.role_name', User::ROLE_ADMIN);

        $this->withToken($oldToken)
            ->getJson('/api/v1/admin/dashboard')
            ->assertUnauthorized();

        $this->withToken($newToken)
            ->getJson('/api/v1/admin/dashboard')
            ->assertOk();
    }

    public function test_logout_invalidates_the_current_token(): void
    {
        $admin = User::factory()->create([
            'role_name' => User::ROLE_ADMIN,
        ]);
        $token = JWTAuth::fromUser($admin);

        $this->withToken($token)
            ->postJson('/api/v1/logout')
            ->assertOk();

        $this->withToken($token)
            ->getJson('/api/v1/admin/dashboard')
            ->assertUnauthorized();
    }

    public function test_refresh_requires_a_token(): void
    {
        $this->postJson('/api/v1/refresh')->assertUnauthorized();
    }

    public function test_expired_access_token_can_be_refreshed_within_the_refresh_window(): void
    {
        $user = User::factory()->create();
        JWTAuth::factory()->setTTL(1);
        $token = JWTAuth::fromUser($user);

        try {
            $this->travel(2)->minutes();

            $this->withToken($token)
                ->postJson('/api/v1/refresh')
                ->assertOk()
                ->assertJsonStructure(['data' => ['token', 'expired_at', 'refreshable_until']]);
        } finally {
            JWTAuth::factory()->setTTL(config('auth_tokens.access_ttl'));
        }
    }

    public function test_inactive_account_cannot_refresh_a_token(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $user->update(['is_active' => false]);

        $this->withToken($token)
            ->postJson('/api/v1/refresh')
            ->assertForbidden();
    }
}
