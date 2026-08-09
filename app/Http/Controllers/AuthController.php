<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Merchant\MerchantRepository;
use App\Http\Repositories\User\UserRepository;
use App\Models\User;
use App\Rules\PhoneNumber;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function userRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone_number' => ['required', 'string', new PhoneNumber],
            'password' => 'required|string|min:8',
            'confirmed_password' => 'required|string|min:8|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $password = Hash::make($request->password);

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => trim($request->phone_number),
                'password' => $password,
            ];

            UserRepository::createUser($data);

            return response()->json([
                'code' => 201,
                'message' => 'Success',
                'data' => null,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e,
            ], 500);
        }
    }

    public function merchantRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone_number' => ['required', 'string', new PhoneNumber],
            'password' => 'required|string|min:8',
            'confirmed_password' => 'required|string|min:8|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $password = Hash::make($request->password);

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => trim($request->phone_number),
                'password' => $password,
            ];

            MerchantRepository::createMerchant($data);

            return response()->json([
                'code' => 201,
                'message' => 'Success',
                'data' => null,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e,
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:users,email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (! $user->is_active) {
                return response()->json([
                    'code' => 403,
                    'message' => 'Account is inactive',
                    'errors' => null,
                ], 403);
            }

            if (! Hash::check($request->password, $user->password)) {
                return response()->json([
                    'code' => 401,
                    'message' => 'Wrong Email or Password',
                    'errors' => null,
                ], 401);
            }

            $customClaims = ['role_name' => $user->role_name];

            $token = JWTAuth::fromUser($user, $customClaims);
            $payload = JWTAuth::setToken($token)->getPayload();

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expired_at' => $payload->get('exp'),
                    'refreshable_until' => $payload->get('iat') + (config('auth_tokens.refresh_ttl') * 60),
                    'account' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone_number' => $user->phone_number,
                        'role_name' => $user->role_name,
                        'dashboard_path' => match ($user->role_name) {
                            User::ROLE_ADMIN => '/admin/dashboard',
                            User::ROLE_MERCHANT => '/merchant/dashboard',
                            default => '/user/dashboard',
                        },
                    ],
                ],
            ]);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => null,
            ], 500);
        }
    }

    public function refresh()
    {
        try {
            $token = JWTAuth::parseToken()->refresh();
            $user = JWTAuth::setToken($token)->authenticate();

            if (! $user) {
                JWTAuth::setToken($token)->invalidate();

                return response()->json([
                    'code' => 401,
                    'message' => 'Unauthorized',
                    'data' => null,
                ], 401);
            }

            if (! $user->is_active) {
                JWTAuth::setToken($token)->invalidate();

                return response()->json([
                    'code' => 403,
                    'message' => 'Forbidden: Account is inactive',
                    'data' => null,
                ], 403);
            }

            $payload = JWTAuth::setToken($token)->getPayload();

            return response()->json([
                'code' => 200,
                'message' => 'Token refreshed successfully',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expired_at' => $payload->get('exp'),
                    'refreshable_until' => $payload->get('iat') + (config('auth_tokens.refresh_ttl') * 60),
                    'account' => [
                        'id' => $user->id,
                        'role_name' => $user->role_name,
                        'dashboard_path' => match ($user->role_name) {
                            User::ROLE_ADMIN => '/admin/dashboard',
                            User::ROLE_MERCHANT => '/merchant/dashboard',
                            default => '/user/dashboard',
                        },
                    ],
                ],
            ]);
        } catch (TokenBlacklistedException|TokenInvalidException $exception) {
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized: Invalid token',
                'data' => null,
            ], 401);
        } catch (TokenExpiredException $exception) {
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized: Refresh period expired',
                'data' => null,
            ], 401);
        } catch (JWTException $exception) {
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized: Token error',
                'data' => null,
            ], 401);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => null,
            ], 500);
        }
    }

    public function logout()
    {
        try {
            JWTAuth::parseToken()->invalidate();

            return response()->json([
                'code' => 200,
                'message' => 'Logged out successfully',
                'data' => null,
            ]);
        } catch (TokenBlacklistedException|TokenInvalidException|TokenExpiredException $exception) {
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized: Invalid token',
                'data' => null,
            ], 401);
        } catch (JWTException $exception) {
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized: Token error',
                'data' => null,
            ], 401);
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
