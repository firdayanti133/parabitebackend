<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    public function test_merchant_can_create_a_snack_and_the_image_is_stored(): void
    {
        Storage::fake('public');
        $merchant = User::factory()->create(['role_name' => User::ROLE_MERCHANT, 'is_merchant' => 1]);

        $response = $this->withToken(JWTAuth::fromUser($merchant))->post('/api/v1/merchant/menu', [
            'name' => 'Potato Chips',
            'description' => 'Crispy snack',
            'image' => $this->validImage(),
            'type' => 3,
            'nutrition_facts' => '120 kcal',
            'price' => 15000,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('code', 201)
            ->assertJsonPath('error_code', null)
            ->assertJsonPath('errors', null);

        $menu = DB::table('merchant_menu_list')->where('name', 'Potato Chips')->first();
        $this->assertSame('3', (string) $menu->type);
        Storage::disk('public')->assertExists(str_replace('storage/', '', $menu->image));
    }

    public function test_invalid_snack_request_returns_frontend_friendly_validation_errors(): void
    {
        $merchant = User::factory()->create(['role_name' => User::ROLE_MERCHANT, 'is_merchant' => 1]);

        $response = $this->withToken(JWTAuth::fromUser($merchant))->postJson('/api/v1/merchant/menu', [
            'name' => 'Invalid snack',
            'description' => 'Missing image and invalid type',
            'type' => 4,
            'nutrition_facts' => 'Unknown',
            'price' => 0,
        ]);

        $response
            ->assertUnprocessable()
            ->assertHeader('X-Request-ID')
            ->assertJsonPath('error_code', 'VALIDATION_ERROR')
            ->assertJsonPath('data', null)
            ->assertJsonStructure(['errors' => ['image', 'type', 'price'], 'request_id']);
    }

    public function test_buyer_cannot_create_a_merchant_menu(): void
    {
        $buyer = User::factory()->create(['role_name' => User::ROLE_USER]);

        $this->withToken(JWTAuth::fromUser($buyer))
            ->postJson('/api/v1/merchant/menu', [])
            ->assertForbidden()
            ->assertJsonPath('error_code', 'AUTH_ROLE_FORBIDDEN');
    }

    public function test_unhandled_api_errors_do_not_expose_exception_details(): void
    {
        Route::middleware('api')->get('/api/v1/test/internal-error', function () {
            throw new \RuntimeException('Database password must never reach the client');
        });

        $response = $this->getJson('/api/v1/test/internal-error');

        $response
            ->assertInternalServerError()
            ->assertHeader('X-Request-ID')
            ->assertJsonPath('error_code', 'INTERNAL_SERVER_ERROR')
            ->assertJsonPath('errors', null)
            ->assertJsonMissing(['Database password must never reach the client']);
    }

    public function test_unknown_api_route_returns_a_stable_error_code(): void
    {
        $this->getJson('/api/v1/route-that-does-not-exist')
            ->assertNotFound()
            ->assertJsonPath('error_code', 'ROUTE_NOT_FOUND')
            ->assertJsonStructure(['request_id']);
    }

    public function test_merchant_can_update_a_snack_without_reuploading_its_image(): void
    {
        $merchant = User::factory()->create(['role_name' => User::ROLE_MERCHANT, 'is_merchant' => 1]);
        $menuId = DB::table('merchant_menu_list')->insertGetId([
            'merchant_id' => $merchant->id,
            'name' => 'Old snack',
            'description' => 'Old description',
            'image' => 'storage/img/menu/existing.png',
            'type' => 3,
            'nutrition_facts' => '100 kcal',
            'price' => 10000,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->withToken(JWTAuth::fromUser($merchant))->putJson("/api/v1/merchant/menu/{$menuId}", [
            'name' => 'Updated snack',
            'description' => 'Updated description',
            'type' => 3,
            'nutrition_facts' => '110 kcal',
            'price' => 25000,
            'status' => 1,
        ])->assertOk();

        $this->assertDatabaseHas('merchant_menu_list', [
            'id' => $menuId,
            'name' => 'Updated snack',
            'image' => 'storage/img/menu/existing.png',
            'price' => 25000,
        ]);
    }

    public function test_ten_thousand_menu_route_returns_paginated_real_data(): void
    {
        $buyer = User::factory()->create(['role_name' => User::ROLE_USER]);
        $merchant = User::factory()->create(['role_name' => User::ROLE_MERCHANT, 'is_merchant' => 1]);
        DB::table('merchant_menu_list')->insert([
            'merchant_id' => $merchant->id,
            'name' => 'Affordable snack',
            'description' => 'Budget item',
            'type' => 3,
            'nutrition_facts' => '90 kcal',
            'price' => 10000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->withToken(JWTAuth::fromUser($buyer))
            ->getJson('/api/v1/user/menu/sepuluh-ribu')
            ->assertOk()
            ->assertJsonPath('data.total_data', 1)
            ->assertJsonPath('data.data.0.menu_name', 'Affordable snack')
            ->assertJsonMissingPath('data.data.0.menu_rating')
            ->assertJsonMissingPath('data.data.0.menu_is_favorite');
    }

    public function test_unused_menu_engagement_schema_is_removed(): void
    {
        $this->assertFalse(Schema::hasTable('menu_ratings'));
        $this->assertFalse(Schema::hasTable('user_wishlist'));
        $this->assertFalse(Schema::hasTable('user_favorite_menu'));
        $this->assertFalse(Schema::hasColumn('merchant_menu_list', 'is_favorite'));
    }

    public function test_favorite_menu_endpoints_are_removed(): void
    {
        $buyer = User::factory()->create(['role_name' => User::ROLE_USER]);
        $merchant = User::factory()->create(['role_name' => User::ROLE_MERCHANT, 'is_merchant' => 1]);

        $this->withToken(JWTAuth::fromUser($buyer))
            ->getJson('/api/v1/user/favorite')
            ->assertNotFound()
            ->assertJsonPath('error_code', 'ROUTE_NOT_FOUND');

        $this->withToken(JWTAuth::fromUser($merchant))
            ->getJson('/api/v1/merchant/menu/favorite')
            ->assertMethodNotAllowed()
            ->assertJsonPath('error_code', 'METHOD_NOT_ALLOWED');
    }

    public function test_unused_menu_engagement_migration_is_reversible(): void
    {
        $migration = require database_path('migrations/2026_08_03_000001_remove_unused_menu_engagement_features.php');

        $migration->down();

        $this->assertTrue(Schema::hasTable('menu_ratings'));
        $this->assertTrue(Schema::hasTable('user_wishlist'));
        $this->assertTrue(Schema::hasTable('user_favorite_menu'));
        $this->assertTrue(Schema::hasColumn('merchant_menu_list', 'is_favorite'));

        $migration->up();

        $this->assertFalse(Schema::hasTable('menu_ratings'));
        $this->assertFalse(Schema::hasTable('user_wishlist'));
        $this->assertFalse(Schema::hasTable('user_favorite_menu'));
        $this->assertFalse(Schema::hasColumn('merchant_menu_list', 'is_favorite'));
    }

    private function validImage(): UploadedFile
    {
        $content = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Wl2n3sAAAAASUVORK5CYII=');

        return UploadedFile::fake()->createWithContent('snack.png', $content);
    }
}
