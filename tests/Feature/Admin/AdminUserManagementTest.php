<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role_name' => User::ROLE_ADMIN,
        ]);
    }

    public function test_admin_can_list_users_with_search_role_filter_and_pagination(): void
    {
        User::factory()->create([
            'name' => 'Target Buyer',
            'email' => 'target@example.com',
            'role_name' => User::ROLE_USER,
        ]);
        User::factory()->create([
            'name' => 'Other Merchant',
            'role_name' => User::ROLE_MERCHANT,
            'is_merchant' => true,
        ]);

        $response = $this->adminRequest()
            ->getJson('/api/v1/admin/users?search=Target&role=user&page=1&limit=10');

        $response
            ->assertOk()
            ->assertJsonPath('data.total_data', 1)
            ->assertJsonPath('data.data.0.email', 'target@example.com')
            ->assertJsonMissingPath('data.data.0.password');
    }

    public function test_admin_can_create_a_valid_user_with_a_hashed_password(): void
    {
        $response = $this->adminRequest()->postJson('/api/v1/admin/users', [
            'name' => 'New Merchant',
            'email' => 'merchant@example.com',
            'phone_number' => '081234567890',
            'role_name' => User::ROLE_MERCHANT,
            'password' => 'secure-password',
            'confirmed_password' => 'secure-password',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.role_name', User::ROLE_MERCHANT)
            ->assertJsonPath('data.is_merchant', true)
            ->assertJsonMissingPath('data.password');

        $user = User::where('email', 'merchant@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('secure-password', $user->password));
    }

    public function test_duplicate_email_is_rejected(): void
    {
        User::factory()->create(['email' => 'duplicate@example.com']);

        $this->adminRequest()->postJson('/api/v1/admin/users', [
            'name' => 'Duplicate User',
            'email' => 'duplicate@example.com',
            'phone_number' => '081234567890',
            'role_name' => User::ROLE_USER,
            'password' => 'secure-password',
            'confirmed_password' => 'secure-password',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_invalid_role_is_rejected(): void
    {
        $this->adminRequest()->postJson('/api/v1/admin/users', [
            'name' => 'Invalid Role',
            'email' => 'invalid-role@example.com',
            'phone_number' => '081234567890',
            'role_name' => 'super-admin',
            'password' => 'secure-password',
            'confirmed_password' => 'secure-password',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('role_name');
    }

    public function test_admin_can_update_a_user_without_overwriting_the_password(): void
    {
        $user = User::factory()->create([
            'email' => 'before@example.com',
            'password' => Hash::make('original-password'),
        ]);

        $response = $this->adminRequest()->putJson("/api/v1/admin/users/{$user->id}", [
            'name' => 'Updated User',
            'email' => 'after@example.com',
            'phone_number' => '089999999999',
            'role_name' => User::ROLE_MERCHANT,
            'is_active' => true,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated User')
            ->assertJsonPath('data.role_name', User::ROLE_MERCHANT);

        $user->refresh();
        $this->assertTrue(Hash::check('original-password', $user->password));
        $this->assertTrue($user->is_merchant);
    }

    public function test_admin_cannot_deactivate_their_own_account(): void
    {
        $this->adminRequest()
            ->deleteJson("/api/v1/admin/users/{$this->admin->id}")
            ->assertStatus(409);

        $this->assertTrue($this->admin->fresh()->is_active);
    }

    public function test_user_with_related_records_is_deactivated_without_deleting_history(): void
    {
        $buyer = User::factory()->create();
        $merchant = User::factory()->create([
            'role_name' => User::ROLE_MERCHANT,
            'is_merchant' => true,
        ]);
        $locationId = DB::table('locations')->insertGetId([
            'name' => 'Order Location',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $orderId = DB::table('user_orders')->insertGetId([
            'user_id' => $buyer->id,
            'merchant_id' => $merchant->id,
            'location_id' => $locationId,
            'bill' => 25000,
            'type' => 1,
            'payment_method' => 1,
            'status' => 1,
            'is_preorder' => false,
            'is_paid' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->adminRequest()
            ->deleteJson("/api/v1/admin/users/{$buyer->id}")
            ->assertOk()
            ->assertJsonPath('data.is_active', false)
            ->assertJsonPath('data.related_records_preserved', true);

        $this->assertDatabaseHas('users', [
            'id' => $buyer->id,
            'is_active' => false,
        ]);
        $this->assertDatabaseHas('user_orders', [
            'id' => $orderId,
            'user_id' => $buyer->id,
        ]);
    }

    private function adminRequest(): self
    {
        return $this->withToken(JWTAuth::fromUser($this->admin));
    }
}
