<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Exception;

class AuthController extends Controller
{

    public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation Error',
            'status' => 422,
            'errors' => $validator->errors()
        ], 422);
    }

    $customClaims = [
        'exp' => now()->addDays(3)->timestamp,
    ];

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    return response()->json([
        'message' => 'User successfully registered',
        'status' => 201,
    ], 201);
}

    
    public function login(Request $request) {
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
            $validate = User::where('email', $credentials['email'])->firstOrFail();

            if (!$validate) {
                return response()->json([
                    'message' => 'User Not Found',
                    'status' => 404,
                ], 404);
            } else {
                $pass = Hash::check($credentials['password'], $validate->password);

                if (!$pass) {
                    return response()->json([
                        'message' => 'Invalid Password',
                        'status' => 401,
                    ], 401);
                }
            }

            $customClaims = [
                'exp' => now()->addDays(3)->timestamp,
            ];

            $validate['role'] = 'user';

            $token = JWTAuth::claims($customClaims)->fromUser($validate);

            return response()->json([
                'message' => 'User successfully logged in',
                'status' => 200,
                'token' => $token,
                'token_type' => 'bearer',
                'user' => $validate
            ], 200);
        
         } catch (Exception $e) {
            return response()->json([
                'message' => 'User Not Found',
                'status' => 404,
            ], 404);
        }
    }
}
