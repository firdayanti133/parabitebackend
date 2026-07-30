<?php

namespace Tests\Feature;

use App\Http\Repositories\User\OrderRepository;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderQueueNumberTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_order_gets_queue_number_one_and_second_order_gets_two(): void
    {
        $buyer = User::factory()->create();
        $merchant = $this->createMerchant();

        $firstOrder = $this->createOrder($buyer, $merchant);
        $secondOrder = $this->createOrder($buyer, $merchant);

        $this->assertSame(1, $firstOrder['queue_number']);
        $this->assertSame(2, $secondOrder['queue_number']);
        $this->assertDatabaseHas('user_orders', [
            'id' => $firstOrder['id'],
            'queue_number' => 1,
        ]);
        $this->assertDatabaseHas('user_orders', [
            'id' => $secondOrder['id'],
            'queue_number' => 2,
        ]);
    }

    public function test_queue_number_resets_on_the_next_day(): void
    {
        $buyer = User::factory()->create();
        $merchant = $this->createMerchant();

        $todayOrder = $this->createOrder($buyer, $merchant);

        try {
            $this->travelTo(now()->addDay()->startOfDay());
            $tomorrowOrder = $this->createOrder($buyer, $merchant);
        } finally {
            $this->travelBack();
        }

        $this->assertSame(1, $todayOrder['queue_number']);
        $this->assertSame(1, $tomorrowOrder['queue_number']);
    }

    public function test_different_merchants_have_independent_queues(): void
    {
        $buyer = User::factory()->create();
        $firstMerchant = $this->createMerchant();
        $secondMerchant = $this->createMerchant();

        $firstMerchantOrder = $this->createOrder($buyer, $firstMerchant);
        $secondMerchantOrder = $this->createOrder($buyer, $secondMerchant);

        $this->assertSame(1, $firstMerchantOrder['queue_number']);
        $this->assertSame(1, $secondMerchantOrder['queue_number']);
    }

    public function test_daily_counter_row_is_unique_and_rapid_allocations_do_not_duplicate_numbers(): void
    {
        $buyer = User::factory()->create();
        $merchant = $this->createMerchant();
        $queueNumbers = [];

        for ($order = 0; $order < 25; $order++) {
            $queueNumbers[] = $this->createOrder($buyer, $merchant)['queue_number'];
        }

        $this->assertSame(range(1, 25), $queueNumbers);
        $this->assertSame($queueNumbers, array_values(array_unique($queueNumbers)));
        $this->assertDatabaseCount('merchant_daily_queue_counters', 1);

        $counter = DB::table('merchant_daily_queue_counters')->first();

        $this->expectException(QueryException::class);
        DB::table('merchant_daily_queue_counters')->insert([
            'merchant_id' => $counter->merchant_id,
            'queue_date' => $counter->queue_date,
            'last_number' => 25,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_order_creation_api_returns_the_assigned_queue_number(): void
    {
        $buyer = User::factory()->create();
        $merchant = $this->createMerchant();
        $menuId = $this->createMenu($merchant);

        DB::table('temp_user_order')->insert([
            'user_id' => $buyer->id,
            'merchant_id' => $merchant->id,
            'menu_id' => $menuId,
            'price' => 25000,
            'quantity' => 1,
            'notes' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->withToken(JWTAuth::fromUser($buyer))
            ->postJson('/api/v1/user/order', [
                'merchant_id' => $merchant->id,
                'type' => 2,
                'payment_method' => 1,
                'is_preorder' => false,
            ])
            ->assertCreated()
            ->assertJsonPath('data.queue_number', 1)
            ->assertJsonStructure(['data' => ['id', 'queue_number']]);
    }

    public function test_buyer_and_merchant_order_apis_return_queue_number(): void
    {
        $buyer = User::factory()->create();
        $merchant = $this->createMerchant();
        $menuId = $this->createMenu($merchant);
        $order = $this->createOrder($buyer, $merchant, collect([
            (object) [
                'menu_id' => $menuId,
                'price' => 25000,
                'quantity' => 1,
                'notes' => null,
            ],
        ]));

        $buyerToken = JWTAuth::fromUser($buyer);
        $merchantToken = JWTAuth::fromUser($merchant);

        $this->withToken($buyerToken)
            ->getJson('/api/v1/user/order/current')
            ->assertOk()
            ->assertJsonPath('data.queue_number', 1);

        $this->withToken($buyerToken)
            ->getJson('/api/v1/user/history')
            ->assertOk()
            ->assertJsonPath('data.0.queue_number', 1);

        $this->withToken($buyerToken)
            ->getJson("/api/v1/user/history/{$order['id']}")
            ->assertOk()
            ->assertJsonPath('data.0.queue_number', 1);

        $this->withToken($merchantToken)
            ->getJson('/api/v1/merchant/order/list')
            ->assertOk()
            ->assertJsonPath('data.data.0.queue_number', 1);

        $this->withToken($merchantToken)
            ->getJson("/api/v1/merchant/order/detail/{$order['id']}")
            ->assertOk()
            ->assertJsonPath('data.queue_number', 1);
    }

    public function test_queue_number_does_not_change_during_order_lifecycle(): void
    {
        $buyer = User::factory()->create();
        $merchant = $this->createMerchant();
        $order = $this->createOrder($buyer, $merchant);
        $merchantToken = JWTAuth::fromUser($merchant);

        $this->withToken($merchantToken)
            ->putJson("/api/v1/merchant/order/status/{$order['id']}", [
                'status' => '3',
            ])
            ->assertOk();

        $this->withToken($merchantToken)
            ->putJson("/api/v1/merchant/order/payment/{$order['id']}")
            ->assertOk();

        $this->assertDatabaseHas('user_orders', [
            'id' => $order['id'],
            'queue_number' => 1,
            'status' => 3,
            'is_paid' => true,
        ]);
    }

    private function createMerchant(): User
    {
        return User::factory()->create([
            'role_name' => User::ROLE_MERCHANT,
            'is_merchant' => true,
        ]);
    }

    private function createMenu(User $merchant): int
    {
        return DB::table('merchant_menu_list')->insertGetId([
            'merchant_id' => $merchant->id,
            'name' => 'Queue Test Menu',
            'description' => 'Queue test menu description',
            'type' => 1,
            'price' => 25000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createOrder(User $buyer, User $merchant, $orderList = null): array
    {
        return OrderRepository::createOrder([
            'user_id' => $buyer->id,
            'merchant_id' => $merchant->id,
            'location_id' => null,
            'bill' => 25000,
            'type' => 2,
            'payment_method' => 1,
            'status' => 1,
            'schedule' => null,
            'is_preorder' => false,
            'order_list' => $orderList ?? collect(),
        ]);
    }
}
