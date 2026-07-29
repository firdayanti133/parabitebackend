<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminLocationManagementTest extends TestCase
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

    public function test_admin_can_list_locations_with_search_and_pagination(): void
    {
        DB::table('locations')->insert([
            [
                'name' => 'Laboratorium Komputer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Main Lobby',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->adminRequest()
            ->getJson('/api/v1/admin/locations?search=Laboratorium&page=1&limit=10')
            ->assertOk()
            ->assertJsonPath('data.total_data', 1)
            ->assertJsonPath('data.data.0.name', 'Laboratorium Komputer');
    }

    public function test_admin_can_create_a_trimmed_location(): void
    {
        $this->adminRequest()
            ->postJson('/api/v1/admin/locations', [
                'name' => '  New Building  ',
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'New Building');

        $this->assertDatabaseHas('locations', ['name' => 'New Building']);
    }

    public function test_duplicate_location_is_rejected_case_insensitively(): void
    {
        DB::table('locations')->insert([
            'name' => 'Main Hall',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->adminRequest()
            ->postJson('/api/v1/admin/locations', ['name' => 'main hall'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_admin_can_update_a_location(): void
    {
        $locationId = DB::table('locations')->insertGetId([
            'name' => 'Old Name',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->adminRequest()
            ->putJson("/api/v1/admin/locations/{$locationId}", ['name' => 'Updated Name'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated Name');

        $this->assertDatabaseHas('locations', [
            'id' => $locationId,
            'name' => 'Updated Name',
        ]);
    }

    public function test_location_deletion_is_prevented_when_used_by_an_order(): void
    {
        $buyer = User::factory()->create();
        $merchant = User::factory()->create([
            'role_name' => User::ROLE_MERCHANT,
            'is_merchant' => true,
        ]);
        $locationId = DB::table('locations')->insertGetId([
            'name' => 'Used Location',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('user_orders')->insert([
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
            ->deleteJson("/api/v1/admin/locations/{$locationId}")
            ->assertStatus(409);

        $this->assertDatabaseHas('locations', ['id' => $locationId]);
    }

    private function adminRequest(): self
    {
        return $this->withToken(JWTAuth::fromUser($this->admin));
    }
}
