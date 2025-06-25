<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Exception;

use App\Models\User;
use App\Models\Merchant;

use App\Http\Repositories\User\UserRepository;
use App\Http\Repositories\Merchant\MerchantRepository;

class AuthController extends Controller
{
    public function userRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'confirmed_password' => 'required|string|min:8|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $password = Hash::make($request->password);

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => $password,
            ];

            UserRepository::createUser($data);

            return response()->json([
                'code' => 201,
                'message' => 'Success',
                'data' => null
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e
            ], 500);
        }
    }

    public function merchantRegister(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:merchants',
            'phone_number' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'confirmed_password' => 'required|string|min:8|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $password = Hash::make($request->password);
                    
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => $password,
            ];

            MerchantRepository::createMerchant($data);

            return response()->json([
                'code' => 201,
                'message' => 'Success',
                'data' => null
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e
            ], 500);
        }
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:users,email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'code' => 401,
                    'message' => 'Wrong Email or Password',
                    'errors' => null
                ], 401);
            }

            $customClaims = [
                'exp' => now()->addDays(3)->timestamp,
                'role_name' => $user->role_name
            ];
            
            $token = JWTAuth::fromUser($user, $customClaims);

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => [
                    'token' => $token,
                    'expired_at' => $customClaims['exp'],
                    'account' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role_name' => $user->role_name
                    ],
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e
            ], 500);
        }
    }
}
