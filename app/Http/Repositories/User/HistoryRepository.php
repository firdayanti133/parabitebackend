<?php

namespace App\Http\Repositories\User;

use Illuminate\Support\Facades\DB;

class HistoryRepository
{
    public static function getListPurchaseHistory($user_id)
    {
        $query = DB::table('user_orders')
            ->where('user_id', $user_id)
            ->select([
                'id',
                'queue_number',
                'bill as total_price',
                'created_at',
            ])
            ->get();

        $data = $query->map(function ($item) {
            $subQuery = DB::table('user_order_list as uol')
                ->leftJoin('merchant_menu_list as mml', 'mml.id', '=', 'uol.menu_id')
                ->leftJoin('users as u', 'u.id', '=', 'mml.merchant_id')
                ->select(
                    'mml.name as menu_name',
                    'u.name as merchant_name',
                    'mml.image as menu_image',
                    'mml.type as menu_type',
                    'uol.price',
                    'uol.quantity',
                    'uol.notes',
                )
                ->where('uol.order_id', $item->id)
                ->first();

            $item->total_menu = self::countTotalMenu($item->id);
            if ($subQuery) {
                foreach ($subQuery as $key => $value) {
                    $item->$key = $value;
                }
            }

            return $item;
        });

        return $data;
    }

    public static function getHistoryDetail($order_id)
    {
        $query = DB::table('user_orders')
            ->leftJoin('users', 'users.id', '=', 'user_orders.user_id')
            ->leftJoin('locations', 'locations.id', '=', 'user_orders.location_id')
            ->where('user_orders.id', $order_id)
            ->select([
                'user_orders.id',
                'user_orders.queue_number',
                'users.name as user_name',
                'locations.name as location_name',
                'user_orders.bill as total_price',
                'user_orders.type',
                'user_orders.payment_method',
                'user_orders.status',
                'user_orders.schedule',
                'user_orders.is_preorder',
            ])
            ->get();

        $data = $query->map(function ($item) {
            $userOrderList = DB::table('user_order_list')
                ->leftJoin('merchant_menu_list', 'merchant_menu_list.id', '=', 'user_order_list.menu_id')
                ->leftJoin('users', 'users.id', '=', 'merchant_menu_list.merchant_id')
                ->where('order_id', $item->id)
                ->select(
                    'user_order_list.id',
                    'user_order_list.order_id',
                    'user_order_list.menu_id',
                    'merchant_menu_list.name as menu_name',
                    'users.name as merchant_name',
                    'merchant_menu_list.image as menu_image',
                    'merchant_menu_list.type as menu_type',
                    'user_order_list.price',
                    'user_order_list.quantity',
                    'user_order_list.notes',
                )
                ->get();
            $item->order_list = $userOrderList;

            return $item;
        });

        return $data;
    }

    public static function countTotalMenu($order_id)
    {
        $query = DB::table('user_order_list')
            ->where('order_id', $order_id)
            ->count();

        return $query;
    }
}
