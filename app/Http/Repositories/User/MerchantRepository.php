<?php

namespace App\Http\Repositories\User;

use Illuminate\Support\Facades\DB;

class MerchantRepository {
    public static function getMerchantList() {
        $query = DB::table('users')
        ->where('role_name', 'merchant')
        ->select([
            'id',
            'name',
            'photo',
        ])
        ->get();

        return $query;
    }

    public static function getMerchantTopMenu() {
        $query = DB::table('merchant_menu_list as mml')
            ->leftJoin('users', 'users.id', '=', 'mml.merchant_id')
            ->leftJoin('user_order_list as uol', 'uol.menu_id', '=', 'mml.id')
            ->select('mml.merchant_id', 'mml.id as menu_id', 'mml.name as menu_name', DB::raw('SUM(uol.quantity) as total_quantity'))
            ->groupBy('mml.merchant_id', 'mml.id', 'mml.name')
            ->orderBy('total_quantity', 'desc')
            ->get()
            ->map(function ($item) {
                $item->top_menu = $item->menu_name;
                unset($item->menu_name);
                return $item;
            })
            ->groupBy('merchant_id')
            ->map(function ($group) {
                return $group->first();
            });

        return $query;
    }
}