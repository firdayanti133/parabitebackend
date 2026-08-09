<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use App\Support\AssetUrl;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (! $request->is('api/*') || ! $response instanceof JsonResponse) {
            return $response;
        }

        $response = ApiResponse::normalize($response, $request);
        $payload = $response->getData(true);
        $payload['data'] = AssetUrl::transform($payload['data'] ?? null);
        $response->setData($payload);

        return $response;
    }
}
