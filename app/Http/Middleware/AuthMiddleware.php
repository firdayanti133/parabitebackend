<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        try {
            $token = JWTAuth::parseToken();
            $user = $token->authenticate();

            if (! $user) {
                return response()->json([
                    'code' => 401,
                    'message' => 'Unauthorized',
                    'data' => null,
                ], 401);
            }

            if (! $user->is_active) {
                return response()->json([
                    'code' => 403,
                    'message' => 'Forbidden: Account is inactive',
                    'data' => null,
                ], 403);
            }

            if (! empty($roles) && ! in_array($user->role_name, $roles, true)) {
                return response()->json([
                    'code' => 403,
                    'message' => 'Forbidden: Insufficient permissions',
                    'data' => null,
                ], 403);
            }

            $request->attributes->add(['auth_user' => $user]);

        } catch (TokenExpiredException $e) {
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized: Token expired',
                'data' => null,
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized: Invalid token',
                'data' => null,
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized: Token error',
                'data' => null,
            ], 401);
        }

        return $next($request);
    }
}
