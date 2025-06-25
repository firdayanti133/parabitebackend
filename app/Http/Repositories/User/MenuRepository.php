<?php

namespace App\Http\Repositories\User;

use Illuminate\Support\Facades\DB;

class MenuRepository {
    public static function getMenuPrice($menu_id) {
        $query = DB::table('merchant_menu_list')
        ->where('id', $menu_id)
        ->first();

        return $query->price;
    }

    public static function getListMenu($page, $limit, $search) {
        $query = DB::table('merchant_menu_list')
        ->where('name', 'like', '%' . $search . '%')
        ->select([
            'id',
            'name',
            'description',
            'image',
            'type',
            'price',
            'status',
            'is_favorite',
        ])
        ->offset(($page - 1) * $limit)
        ->limit($limit)
        ->get();

        $data = $query->map(function ($item) {
            $getRating = DB::table('menu_ratings')
                ->where('menu_id', $item->id)
                ->avg('rating');

            $rating = $getRating ?: 0;

            return [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'image' => $item->image,
                'type' => $item->type,
                'price' => $item->price,
                'status' => $item->status,
                'is_favorite' => $item->is_favorite,
                'rating' => $rating,
            ];
        });

        return $data;
    }

    public static function countListMenu($search) {
        return DB::table('merchant_menu_list')
        ->where('name', 'like', '%' . $search . '%')
        ->count();
    }

    public static function getMerchantListMenu($merchant_id, $page, $limit, $search) {
        $query = DB::table('merchant_menu_list')
        ->where('merchant_id', $merchant_id)
        ->where('name', 'like', '%' . $search . '%')
        ->select([
            'id',
            'name',
            'description',
            'image',
            'type',
            'price',
            'status',
            'is_favorite',
        ])
        ->offset(($page - 1) * $limit)
        ->limit($limit)
        ->get();

        $data = $query->map(function ($item) {
            $getRating = DB::table('menu_ratings')
                ->where('menu_id', $item->id)
                ->avg('rating');

            $rating = $getRating ?: 0;

            return [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'image' => $item->image,
                'type' => $item->type,
                'price' => $item->price,
                'status' => $item->status,
                'is_favorite' => $item->is_favorite,
                'rating' => $rating,
            ];
        });

        return $data;
    }

    public static function countMerchantListMenu($merchant_id, $search) {
        return DB::table('merchant_menu_list')
        ->where('merchant_id', $merchant_id)
        ->where('name', 'like', '%' . $search . '%')
        ->count();
    }

    public static function getMenuDetail($menu_id) {
        $query = DB::table('merchant_menu_list')
        ->where('id', $menu_id)
        ->select([
            'id',
            'merchant_id',
            'name',
            'description',
            'image',
            'type',
            'price',
            'status',
            'is_favorite',
        ])
        ->first();

        $getRating = DB::table('menu_ratings')
            ->where('menu_id', $menu_id)
            ->avg('rating');

        $rating = $getRating ?: 0;

        return [
            'id' => $query->id,
            'merchant_id' => $query->merchant_id,
            'name' => $query->name,
            'description' => $query->description,
            'image' => $query->image,
            'type' => $query->type,
            'price' => $query->price,
            'status' => $query->status,
            'is_favorite' => $query->is_favorite,
            'rating' => $rating,
        ];
    }

    public static function getUserFavoriteList($user_id) {
        $query = DB::table('user_favorite_menu')
        ->where('user_id', $user_id)
        ->get();

        $data = $query->map(function ($item) {
            $subQuery = DB::table('merchant_menu_list')
            ->where('id', $item->menu_id)
            ->first();

            return [
                'id' => $subQuery->id,
                'name' => $subQuery->name,
                'image' => $subQuery->image,
                'type' => $subQuery->type,
            ];
        });

        return $data;
    }

    public static function userFavoriteHandler($user_id, $menu_id) {
        $query = DB::table('user_favorite_menu')
        ->where('user_id', $user_id)
        ->where('menu_id', $menu_id)
        ->first();

        if ($query) {
            DB::table('user_favorite_menu')
            ->where('user_id', $user_id)
            ->where('menu_id', $menu_id)
            ->delete();
        } else {
            DB::table('user_favorite_menu')
            ->insert([
                'user_id' => $user_id,
                'menu_id' => $menu_id,
            ]);
        }

        return true;
    }
}