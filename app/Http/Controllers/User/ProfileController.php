<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Repositories\User\UserRepository;

class ProfileController extends Controller
{
    public function getUserOrderStats(Request $request) {
        $user = $request->get('auth_user');

        $validateId = Validator::make(['user_id' => $user->id], [
            'user_id' => 'exists:users,id',
        ]);

        if ($validateId->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validateId->errors()
            ], 422);
        }

        try {
            $expense = UserRepository::getUserExpense($user->id);
            $totalOrder = UserRepository::getUserTotalOrder($user->id);
            $data = [
                'expense' => intval($expense),
                'total_order' => $totalOrder
            ];

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e
            ], 500);
        }
    }
}
