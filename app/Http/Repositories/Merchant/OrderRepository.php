<?php

namespace App\Http\Repositories\Merchant;

use Illuminate\Support\Facades\DB;

class OrderRepository
{
    public static function getListOrder($merchant_id, $page, $limit, $status)
    {
        if ($status == null) {
            $query = DB::table('user_orders')
                ->leftJoin('users', 'users.id', '=', 'user_orders.user_id')
                ->leftJoin('locations', 'locations.id', '=', 'user_orders.location_id')
                ->where('user_orders.merchant_id', $merchant_id)
                ->select([
                    'user_orders.id',
                    'user_orders.queue_number',
                    'users.name as user_name',
                    'locations.name as location_name',
                    'user_orders.bill',
                    'user_orders.type',
                    'user_orders.payment_method',
                    'user_orders.status',
                    'user_orders.schedule',
                    'user_orders.is_preorder',
                    'user_orders.is_paid',
                ])
                ->offset(($page - 1) * $limit)
                ->limit($limit)
                ->get();
        } else {
            $query = DB::table('user_orders')
                ->leftJoin('users', 'users.id', '=', 'user_orders.user_id')
                ->leftJoin('locations', 'locations.id', '=', 'user_orders.location_id')
                ->where('user_orders.merchant_id', $merchant_id)
                ->where('user_orders.status', $status)
                ->select([
                    'user_orders.id',
                    'user_orders.queue_number',
                    'users.name as user_name',
                    'locations.name as location_name',
                    'user_orders.bill',
                    'user_orders.type',
                    'user_orders.payment_method',
                    'user_orders.status',
                    'user_orders.schedule',
                    'user_orders.is_preorder',
                    'user_orders.is_paid',
                ])
                ->offset(($page - 1) * $limit)
                ->limit($limit)
                ->get();
        }

        $data = $query->map(function ($item) {
            $userOrderList = DB::table('user_order_list as uol')
                ->leftJoin('merchant_menu_list as mml', 'mml.id', '=', 'uol.menu_id')
                ->where('order_id', $item->id)
                ->select(
                    'uol.id',
                    'uol.order_id',
                    'uol.menu_id',
                    'mml.name as menu_name',
                    'uol.price',
                    'uol.quantity',
                    'uol.notes',
                )
                ->get();

            return [
                'id' => $item->id,
                'queue_number' => $item->queue_number,
                'user_name' => $item->user_name,
                'location_name' => $item->location_name,
                'bill' => $item->bill,
                'type' => $item->type,
                'payment_method' => $item->payment_method,
                'is_paid' => $item->is_paid,
                'status' => $item->status,
                'schedule' => $item->schedule,
                'is_preorder' => $item->is_preorder,
                'user_order_list' => $userOrderList,
            ];
        });

        return $data;
    }

    public static function countListOrder($merchant_id, $status)
    {
        if ($status == null) {
            return DB::table('user_orders')
                ->where('merchant_id', $merchant_id)
                ->count();
        } else {
            return DB::table('user_orders')
                ->where('merchant_id', $merchant_id)
                ->where('status', $status)
                ->count();
        }
    }

    public static function getOrderDetail($order_id)
    {
        $query = DB::table('user_orders')
            ->where('id', $order_id)
            ->select([
                'id',
                'queue_number',
                'user_id',
                'merchant_id',
                'location_id',
                'bill',
                'type',
                'payment_method',
                'status',
                'schedule',
                'is_preorder',
            ])
            ->first();

        $subQuery = DB::table('user_order_list')
            ->where('order_id', $order_id)
            ->get();

        $data = [
            'id' => $query->id,
            'queue_number' => $query->queue_number,
            'user_id' => $query->user_id,
            'merchant_id' => $query->merchant_id,
            'location_id' => $query->location_id,
            'bill' => $query->bill,
            'type' => $query->type,
            'payment_method' => $query->payment_method,
            'status' => $query->status,
            'schedule' => $query->schedule,
            'is_preorder' => $query->is_preorder,
            'user_order_list' => $subQuery,
        ];

        return $data;
    }

    public static function updateOrderStatus($order_id, $status)
    {
        DB::table('user_orders')
            ->where('id', $order_id)
            ->update(['status' => $status]);

        return true;
    }

    public static function updateOrderPaymentStatus($order_id)
    {
        DB::table('user_orders')
            ->where('id', $order_id)
            ->update(['is_paid' => true]);
    }
}
