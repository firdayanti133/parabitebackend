<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AssetDeliveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_image_is_served_with_cors_and_cache_headers(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('img/menu/example.png', 'image-content');

        $this->withHeader('Origin', 'http://localhost:53218')
            ->get('/api/v1/assets/menu/example.png')
            ->assertOk()
            ->assertHeader('Access-Control-Allow-Origin', '*')
            ->assertHeader('Cache-Control', 'immutable, max-age=31536000, public')
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_asset_preflight_request_returns_cors_headers(): void
    {
        $this->call('OPTIONS', '/api/v1/assets/menu/example.png', server: [
            'HTTP_ORIGIN' => 'http://localhost:53218',
            'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'GET',
        ])->assertNoContent()
            ->assertHeader('Access-Control-Allow-Origin', '*')
            ->assertHeader('Access-Control-Allow-Methods', 'GET');
    }

    public function test_missing_asset_returns_a_stable_api_error(): void
    {
        Storage::fake('public');

        $this->getJson('/api/v1/assets/menu/missing.png')
            ->assertNotFound()
            ->assertJsonPath('error_code', 'ASSET_NOT_FOUND')
            ->assertJsonPath('data', null);
    }

    public function test_menu_apis_return_an_absolute_cors_enabled_image_url(): void
    {
        $buyer = User::factory()->create(['role_name' => User::ROLE_USER]);
        $merchant = User::factory()->create([
            'role_name' => User::ROLE_MERCHANT,
            'is_merchant' => true,
        ]);
        $menuId = DB::table('merchant_menu_list')->insertGetId([
            'merchant_id' => $merchant->id,
            'name' => 'Menu with image',
            'description' => 'Image URL test',
            'image' => 'storage/img/menu/example.png',
            'type' => 1,
            'nutrition_facts' => '100 kcal',
            'price' => 10000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $expectedUrl = route('assets.menu.show', ['filename' => 'example.png']);

        $this->withToken(JWTAuth::fromUser($buyer))
            ->getJson("/api/v1/user/menu/detail/{$menuId}")
            ->assertOk()
            ->assertJsonPath('data.menu_image', $expectedUrl);

        $this->withToken(JWTAuth::fromUser($merchant))
            ->getJson("/api/v1/merchant/menu/detail/{$menuId}")
            ->assertOk()
            ->assertJsonPath('data.image', $expectedUrl);
    }
}
