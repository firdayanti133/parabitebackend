<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        try {
            $user = User::where('email', $credentials['email'])->firstOrFail();

            if (!Hash::check($credentials['password'], $user->password)) {
                return response()->json([
                    'message' => 'Invalid Password',
                    'status' => 401,
                ], 401);
            }

            if ($user->role !== 'admin') {
                return response()->json([
                    'message' => 'Forbidden: Admin access only',
                    'status' => 403,
                ], 403);
            }

            $customClaims = [
                'exp' => now()->addDays(3)->timestamp,
                'role' => 'admin'
            ];

            $user->role = 'admin';

            $token = JWTAuth::claims($customClaims)->fromUser($user);

            return response()->json([
                'message' => 'Admin successfully logged in',
                'status' => 200,
                'token' => $token,
                'token_type' => 'bearer',
                'user' => $user
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'User Not Found',
                'status' => 404,
            ], 404);
        }
    }
}
