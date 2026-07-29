<?php

namespace App\Http\Repositories\Admin;

use Illuminate\Support\Facades\DB;

class LocationRepository
{
    public static function getList(int $page, int $limit, string $search): array
    {
        $query = DB::table('locations')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%'.$search.'%'));

        $totalData = (clone $query)->count();
        $data = $query
            ->select(['id', 'name', 'created_at', 'updated_at'])
            ->orderBy('name')
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->get();

        return [
            'total_data' => $totalData,
            'page' => $page,
            'limit' => $limit,
            'total_page' => (int) ceil($totalData / $limit),
            'data' => $data,
        ];
    }

    public static function getDetail(int $locationId): ?object
    {
        return DB::table('locations')
            ->select(['id', 'name', 'created_at', 'updated_at'])
            ->where('id', $locationId)
            ->first();
    }

    public static function nameExists(string $name, ?int $exceptId = null): bool
    {
        return DB::table('locations')
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->when($exceptId !== null, fn ($query) => $query->where('id', '<>', $exceptId))
            ->exists();
    }

    public static function create(string $name): int
    {
        return DB::table('locations')->insertGetId([
            'name' => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public static function update(int $locationId, string $name): int
    {
        return DB::table('locations')
            ->where('id', $locationId)
            ->update([
                'name' => $name,
                'updated_at' => now(),
            ]);
    }

    public static function countOrders(int $locationId): int
    {
        return DB::table('user_orders')->where('location_id', $locationId)->count();
    }

    public static function delete(int $locationId): int
    {
        return DB::table('locations')->where('id', $locationId)->delete();
    }
}
