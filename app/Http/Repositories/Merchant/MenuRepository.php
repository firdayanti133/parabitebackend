<?php

namespace App\Http\Repositories\Merchant;

use Illuminate\Support\Facades\DB;

class MenuRepository
{
    public static function belongsToMerchant($menu_id, $merchant_id)
    {
        return DB::table('merchant_menu_list')
            ->where('id', $menu_id)
            ->where('merchant_id', $merchant_id)
            ->exists();
    }

    public static function getListMenu($merchant_id, $page, $limit, $type)
    {
        if ($type == null) {
            $query = DB::table('merchant_menu_list')
                ->where('merchant_id', $merchant_id)
                ->select([
                    'id',
                    'name',
                    'type',
                    'price',
                    'description',
                    'image',
                ])
                ->offset(($page - 1) * $limit)
                ->limit($limit)
                ->get();
        } else {
            $query = DB::table('merchant_menu_list')
                ->where('merchant_id', $merchant_id)
                ->where('type', $type)
                ->select([
                    'id',
                    'name',
                    'type',
                    'description',
                    'image',
                    'price',
                ])
                ->offset(($page - 1) * $limit)
                ->limit($limit)
                ->get();
        }

        $data = $query->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'type' => $item->type,
                'description' => $item->description,
                'image' => $item->image,
                'price' => $item->price,
            ];
        });

        return $data;
    }

    public static function countListMenu($merchant_id, $type)
    {
        if ($type == null) {
            return DB::table('merchant_menu_list')
                ->where('merchant_id', $merchant_id)
                ->count();
        } else {
            return DB::table('merchant_menu_list')
                ->where('merchant_id', $merchant_id)
                ->where('type', $type)
                ->count();
        }
    }

    public static function getMenuDetail($menu_id)
    {
        $query = DB::table('merchant_menu_list')
            ->where('id', $menu_id)
            ->select([
                'id',
                'name',
                'description',
                'image',
                'type',
                'nutrition_facts',
                'price',
                'status',
            ])
            ->first();

        $data = [
            'id' => $query->id,
            'name' => $query->name,
            'description' => $query->description,
            'image' => $query->image,
            'type' => $query->type,
            'nutrition_facts' => $query->nutrition_facts,
            'price' => $query->price,
            'status' => $query->status,
        ];

        return $data;
    }

    public static function createMenu($data)
    {
        DB::table('merchant_menu_list')
            ->insert([
                'merchant_id' => $data['merchant_id'],
                'name' => $data['name'],
                'description' => $data['description'],
                'image' => $data['image'],
                'type' => $data['type'],
                'nutrition_facts' => $data['nutrition_facts'],
                'price' => $data['price'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        return true;
    }

    public static function updateMenu($data)
    {
        DB::table('merchant_menu_list')
            ->where('id', $data['id'])
            ->update([
                'name' => $data['name'],
                'description' => $data['description'],
                'image' => $data['image'],
                'type' => $data['type'],
                'nutrition_facts' => $data['nutrition_facts'],
                'price' => $data['price'],
                'status' => $data['status'],
                'updated_at' => now(),
            ]);

        return true;
    }

    public static function deleteMenu($menu_id)
    {
        DB::table('merchant_menu_list')
            ->where('id', $menu_id)
            ->delete();

        return true;
    }
}
