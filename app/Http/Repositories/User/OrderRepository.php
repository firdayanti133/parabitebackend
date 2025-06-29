<?php

namespace App\Http\Repositories\User;

use Illuminate\Support\Facades\DB;

class OrderRepository {
    public static function getTempOrderDetail($temp_order_id) {
        $query = DB::table('temp_user_order')
        ->where('temp_user_order.id', $temp_order_id)
        ->first();

        return $query;
    }
    public static function getListTempOrder($user_id) {
        $query = DB::table('temp_user_order as tuo')
        ->leftJoin('users', 'users.id', '=', 'tuo.merchant_id')
        ->leftJoin('merchant_menu_list as mml', 'mml.id', '=', 'tuo.menu_id')
        ->where('tuo.user_id', $user_id)
        ->select([
            'tuo.id',
            'tuo.menu_id',
            'tuo.merchant_id',
            'users.name as merchant_name',
            'mml.name as menu_name',
            'mml.image as menu_image',
            'tuo.price',
            'tuo.quantity',
            'tuo.notes',
        ])
        ->get();
        
        return $query;
    }

    public static function createOrder($data) {
        $query = DB::table('user_orders')
        ->insertGetId([
            'user_id' => $data['user_id'],
            'merchant_id' => $data['merchant_id'],
            'location_id' => $data['location_id'] ?? null,
            'bill' => $data['bill'],
            'type' => $data['type'],
            'payment_method' => $data['payment_method'],
            'status' => $data['status'],
            'schedule' => $data['schedule'] ?? null,
            'is_preorder' => $data['is_preorder'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $data['order_list']->map(function ($item) use ($query) {
            DB::table('user_order_list')
            ->insert([
                'order_id' => $query,
                'menu_id' => $item->menu_id,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'notes' => $item->notes ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return true;
    }

    public static function createTempOrder($data) {
        DB::table('temp_user_order')
        ->insert([
            'user_id' => $data['user_id'],
            'merchant_id' => $data['merchant_id'],
            'menu_id' => $data['menu_id'],
            'price' => $data['price'],
            'quantity' => $data['quantity'],
            'notes' => $data['notes'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return true;
    }

    public static function updateTempOrder($data) {
        if ($data['notes'] == null) {
            DB::table('temp_user_order')
            ->where('id', $data['temp_order_id'])
            ->update([
                'price' => $data['price'],
                'quantity' => $data['quantity'],
                'updated_at' => now(),
            ]);
        } else {
            DB::table('temp_user_order')
            ->where('id', $data['temp_order_id'])
            ->update([
                'price' => $data['price'],
                'quantity' => $data['quantity'],
                'notes' => $data['notes'],
                'updated_at' => now(),
            ]);
        }

        return true;
    }

    public static function removeTempOrder($tempOrderId) {
        DB::table('temp_user_order')
        ->where('id', $tempOrderId)
        ->delete();

        return true;
    }

    public static function removeUserTempOrder($user_id) {
        DB::table('temp_user_order')
        ->where('user_id', $user_id)
        ->delete();

        return true;
    }
}