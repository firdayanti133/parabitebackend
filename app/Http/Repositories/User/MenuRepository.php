<?php

namespace App\Http\Repositories\User;

use Illuminate\Support\Facades\DB;

class MenuRepository
{
    public static function getMenuPrice($menu_id)
    {
        $query = DB::table('merchant_menu_list')
            ->where('id', $menu_id)
            ->first();

        return $query->price;
    }

    public static function getMenuMerchant($menu_id)
    {
        $query = DB::table('merchant_menu_list')
            ->where('id', $menu_id)
            ->select('merchant_id')
            ->first();

        return $query->merchant_id;
    }

    public static function getListMenu($page, $limit, $search, $type)
    {
        if ($type == null) {
            $query = DB::table('merchant_menu_list as mml')
                ->leftJoin('users', 'users.id', '=', 'mml.merchant_id')
                ->where('mml.name', 'like', '%'.$search.'%')
                ->select([
                    'mml.id as menu_id',
                    'users.name as merchant_name',
                    'mml.name as menu_name',
                    'mml.description as menu_description',
                    'mml.image as menu_image',
                    'mml.type as menu_type',
                    'mml.price as menu_price',
                    'mml.status as menu_status',
                ])
                ->offset(($page - 1) * $limit)
                ->limit($limit)
                ->get();
        } else {
            $query = DB::table('merchant_menu_list as mml')
                ->leftJoin('users', 'users.id', '=', 'mml.merchant_id')
                ->where('mml.type', $type)
                ->where('mml.name', 'like', '%'.$search.'%')
                ->select([
                    'mml.id as menu_id',
                    'users.name as merchant_name',
                    'mml.name as menu_name',
                    'mml.description as menu_description',
                    'mml.image as menu_image',
                    'mml.type as menu_type',
                    'mml.price as menu_price',
                    'mml.status as menu_status',
                ])
                ->offset(($page - 1) * $limit)
                ->limit($limit)
                ->get();
        }

        $data = $query->map(function ($item) {
            return [
                'menu_id' => $item->menu_id,
                'merchant_name' => $item->merchant_name,
                'menu_name' => $item->menu_name,
                'menu_description' => $item->menu_description,
                'menu_image' => $item->menu_image,
                'menu_type' => $item->menu_type,
                'menu_price' => $item->menu_price,
                'menu_status' => $item->menu_status,
            ];
        });

        return $data;
    }

    public static function countListMenu($search, $type)
    {
        if ($type == null) {
            return DB::table('merchant_menu_list')
                ->where('name', 'like', '%'.$search.'%')
                ->count();
        }

        return DB::table('merchant_menu_list')
            ->where('type', $type)
            ->where('name', 'like', '%'.$search.'%')
            ->count();
    }

    public static function getMerchantListMenu($merchant_id, $page, $limit, $search)
    {
        $query = DB::table('merchant_menu_list')
            ->where('merchant_id', $merchant_id)
            ->where('name', 'like', '%'.$search.'%')
            ->select([
                'id',
                'name',
                'description',
                'image',
                'type',
                'price',
                'status',
            ])
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->get();

        $data = $query->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'image' => $item->image,
                'type' => $item->type,
                'price' => $item->price,
                'status' => $item->status,
            ];
        });

        return $data;
    }

    public static function countMerchantListMenu($merchant_id, $search)
    {
        return DB::table('merchant_menu_list')
            ->where('merchant_id', $merchant_id)
            ->where('name', 'like', '%'.$search.'%')
            ->count();
    }

    public static function getMenuDetail($menu_id)
    {
        $query = DB::table('merchant_menu_list as mml')
            ->leftJoin('users', 'users.id', '=', 'mml.merchant_id')
            ->where('mml.id', $menu_id)
            ->select([
                'mml.id as menu_id',
                'mml.merchant_id as merchant_id',
                'users.name as merchant_name',
                'mml.name as menu_name',
                'mml.description as menu_description',
                'mml.image as menu_image',
                'mml.type as menu_type',
                'mml.price as menu_price',
                'mml.status as menu_status',
                'mml.nutrition_facts as menu_nutrition_facts',
            ])
            ->first();

        return [
            'menu_id' => $query->menu_id,
            'merchant_id' => $query->merchant_id,
            'merchant_name' => $query->merchant_name,
            'menu_name' => $query->menu_name,
            'menu_description' => $query->menu_description,
            'menu_image' => $query->menu_image,
            'menu_type' => $query->menu_type,
            'menu_price' => $query->menu_price,
            'menu_status' => $query->menu_status,
            'menu_nutrition_facts' => $query->menu_nutrition_facts,
        ];
    }

    public static function getMenusUnderPrice($page, $limit, $search, $maxPrice = 10000)
    {
        $query = DB::table('merchant_menu_list as mml')
            ->leftJoin('users', 'users.id', '=', 'mml.merchant_id')
            ->where('mml.price', '<=', $maxPrice)
            ->where('mml.name', 'like', '%'.$search.'%')
            ->select([
                'mml.id as menu_id',
                'users.name as merchant_name',
                'mml.name as menu_name',
                'mml.description as menu_description',
                'mml.image as menu_image',
                'mml.type as menu_type',
                'mml.price as menu_price',
                'mml.status as menu_status',
            ])
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->get();

        return $query;
    }

    public static function countMenusUnderPrice($search, $maxPrice = 10000)
    {
        return DB::table('merchant_menu_list')
            ->where('price', '<=', $maxPrice)
            ->where('name', 'like', '%'.$search.'%')
            ->count();
    }
}
