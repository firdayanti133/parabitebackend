<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Admin\DashboardRepository;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $admin = $request->get('auth_user');
            $statistics = DashboardRepository::getStatistics();

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => [
                    ...$statistics,
                    'admin' => [
                        'id' => $admin->id,
                        'name' => $admin->name,
                        'email' => $admin->email,
                        'phone_number' => $admin->phone_number,
                        'role_name' => $admin->role_name,
                    ],
                    'navigation' => [
                        'users' => '/api/v1/admin/users',
                        'locations' => '/api/v1/admin/locations',
                    ],
                ],
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => null,
            ], 500);
        }
    }
}
