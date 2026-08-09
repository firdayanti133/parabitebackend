<?php

namespace App\Http\Repositories\Admin;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    private const SELECT_COLUMNS = [
        'id',
        'name',
        'email',
        'phone_number',
        'photo',
        'role_name',
        'is_merchant',
        'is_active',
        'created_at',
        'updated_at',
    ];

    public static function getList(int $page, int $limit, string $search, ?string $role): array
    {
        $query = DB::table('users')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->when($role !== null, fn ($query) => $query->where('role_name', $role));

        $totalData = (clone $query)->count();
        $data = $query
            ->select(self::SELECT_COLUMNS)
            ->orderByDesc('id')
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->get()
            ->map(fn (object $user) => self::normalizeUser($user));

        return [
            'total_data' => $totalData,
            'page' => $page,
            'limit' => $limit,
            'total_page' => (int) ceil($totalData / $limit),
            'data' => $data,
        ];
    }

    public static function getDetail(int $userId): ?object
    {
        $user = DB::table('users')
            ->select(self::SELECT_COLUMNS)
            ->where('id', $userId)
            ->first();

        return $user ? self::normalizeUser($user) : null;
    }

    public static function create(array $data): int
    {
        return DB::table('users')->insertGetId([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'role_name' => $data['role_name'],
            'is_merchant' => $data['role_name'] === User::ROLE_MERCHANT ? '1' : '0',
            'is_active' => true,
            'password' => $data['password'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public static function update(int $userId, array $data): int
    {
        $updates = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'role_name' => $data['role_name'],
            'is_merchant' => $data['role_name'] === User::ROLE_MERCHANT ? '1' : '0',
            'is_active' => $data['is_active'],
            'updated_at' => now(),
        ];

        if (isset($data['password'])) {
            $updates['password'] = $data['password'];
        }

        return DB::table('users')->where('id', $userId)->update($updates);
    }

    public static function deactivate(int $userId): int
    {
        return DB::table('users')
            ->where('id', $userId)
            ->update([
                'is_active' => false,
                'updated_at' => now(),
            ]);
    }

    public static function getRelatedRecordCounts(int $userId): array
    {
        return [
            'buyer_orders' => DB::table('user_orders')->where('user_id', $userId)->count(),
            'merchant_orders' => DB::table('user_orders')->where('merchant_id', $userId)->count(),
            'menus' => DB::table('merchant_menu_list')->where('merchant_id', $userId)->count(),
            'temporary_orders' => DB::table('temp_user_order')
                ->where('user_id', $userId)
                ->orWhere('merchant_id', $userId)
                ->count(),
        ];
    }

    private static function normalizeUser(object $user): object
    {
        $user->is_merchant = (bool) $user->is_merchant;
        $user->is_active = (bool) $user->is_active;

        return $user;
    }
}
