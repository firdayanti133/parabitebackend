<?php

namespace App\Http\Repositories\Admin;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    public static function getStatistics(): array
    {
        $roleCounts = DB::table('users')
            ->select('role_name', DB::raw('COUNT(*) as total'))
            ->groupBy('role_name')
            ->pluck('total', 'role_name');

        return [
            'total_users' => (int) $roleCounts->sum(),
            'total_buyers' => (int) ($roleCounts[User::ROLE_USER] ?? 0),
            'total_merchants' => (int) ($roleCounts[User::ROLE_MERCHANT] ?? 0),
            'total_admins' => (int) ($roleCounts[User::ROLE_ADMIN] ?? 0),
            'total_locations' => DB::table('locations')->count(),
        ];
    }
}
