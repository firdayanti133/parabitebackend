<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json([
                    'message' => 'Unauthorized: Token not provided',
                    'status' => 401,
                ], 401);
            }

            $payload = JWTAuth::setToken($token)->getPayload();

            $role = $payload->get('role');

            if (!$role || !in_array($role, $roles)) {
                return response()->json([
                    'message' => 'Forbidden: You do not have permission to access this resource',
                    'status' => 403,
                    'role' => $role
                ], 403);
            }

            $request->attributes->add(['role' => $role]);

            return $next($request);

        } catch (TokenExpiredException $e) {
            return response()->json([
                'message' => 'Unauthorized: Token expired',
                'status' => 401,
            ], 401);

        } catch (TokenInvalidException $e) {
            return response()->json([
                'message' => 'Unauthorized: Invalid token',
                'status' => 401,
            ], 401);

        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Unauthorized: Could not parse token',
                'status' => 401,
            ], 401);
        }
    }
}