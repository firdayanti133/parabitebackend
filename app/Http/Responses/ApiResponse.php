<?php

namespace App\Http\Responses;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ApiResponse
{
    public static function normalize(JsonResponse $response, Request $request): JsonResponse
    {
        $status = $response->getStatusCode();
        $payload = $response->getData(true);
        $payload = is_array($payload) ? $payload : [];
        $isError = $status >= 400;
        $message = $payload['message'] ?? Response::$statusTexts[$status] ?? 'Unknown response';
        $errors = $isError ? ($payload['errors'] ?? null) : null;
        $requestId = $payload['request_id'] ?? self::requestId($request);

        if ($status >= 500) {
            Log::error('API request returned a server error', [
                'request_id' => $requestId,
                'method' => $request->method(),
                'path' => $request->path(),
                'errors' => $errors,
            ]);
            $errors = null;
        }

        $normalized = [
            'code' => $status,
            'message' => $message,
            'data' => $isError ? null : ($payload['data'] ?? null),
            'error_code' => $isError ? ($payload['error_code'] ?? self::errorCode($status, $message)) : null,
            'errors' => $errors,
        ];

        if ($isError) {
            $normalized['request_id'] = $requestId;
            $response->headers->set('X-Request-ID', $requestId);
        }

        $response->setData($normalized);

        return $response;
    }

    public static function fromException(Throwable $exception, Request $request): JsonResponse
    {
        [$status, $message, $errorCode, $errors] = self::classify($exception);
        $requestId = self::requestId($request);

        if ($status >= 500) {
            Log::error('Unhandled API exception', [
                'request_id' => $requestId,
                'method' => $request->method(),
                'path' => $request->path(),
                'exception' => $exception,
            ]);
        }

        return response()->json([
            'code' => $status,
            'message' => $message,
            'data' => null,
            'error_code' => $errorCode,
            'errors' => $errors,
            'request_id' => $requestId,
        ], $status, ['X-Request-ID' => $requestId]);
    }

    public static function errorCode(int $status, string $message): string
    {
        $message = strtolower($message);

        return match (true) {
            str_contains($message, 'token expired') => 'AUTH_TOKEN_EXPIRED',
            str_contains($message, 'invalid token') => 'AUTH_TOKEN_INVALID',
            str_contains($message, 'wrong email or password') => 'AUTH_INVALID_CREDENTIALS',
            str_contains($message, 'account is inactive') => 'AUTH_ACCOUNT_INACTIVE',
            str_contains($message, 'insufficient permissions') => 'AUTH_ROLE_FORBIDDEN',
            str_contains($message, 'cart is empty') => 'ORDER_CART_EMPTY',
            str_contains($message, 'different merchant') => 'ORDER_CART_MERCHANT_MISMATCH',
            str_contains($message, 'deactivate your own') => 'ADMIN_SELF_DEACTIVATION_FORBIDDEN',
            str_contains($message, 'remove the admin role') => 'ADMIN_SELF_ROLE_CHANGE_FORBIDDEN',
            str_contains($message, 'location cannot be deleted') => 'LOCATION_IN_USE',
            $status === 401 => 'AUTH_UNAUTHENTICATED',
            $status === 403 => 'AUTH_FORBIDDEN',
            $status === 404 => 'RESOURCE_NOT_FOUND',
            $status === 405 => 'METHOD_NOT_ALLOWED',
            $status === 409 => 'RESOURCE_CONFLICT',
            $status === 422 => 'VALIDATION_ERROR',
            $status === 429 => 'RATE_LIMIT_EXCEEDED',
            default => 'INTERNAL_SERVER_ERROR',
        };
    }

    private static function classify(Throwable $exception): array
    {
        if ($exception instanceof ValidationException) {
            return [422, 'Validation Error', 'VALIDATION_ERROR', $exception->errors()];
        }

        if ($exception instanceof AuthenticationException) {
            return [401, 'Unauthorized', 'AUTH_UNAUTHENTICATED', null];
        }

        if ($exception instanceof AuthorizationException) {
            return [403, 'Forbidden', 'AUTH_FORBIDDEN', null];
        }

        if ($exception instanceof NotFoundHttpException) {
            return [404, 'API route not found', 'ROUTE_NOT_FOUND', null];
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return [405, 'Method not allowed', 'METHOD_NOT_ALLOWED', null];
        }

        if ($exception instanceof FileException) {
            return [422, 'The uploaded file could not be stored', 'FILE_UPLOAD_FAILED', null];
        }

        if ($exception instanceof QueryException && str_starts_with((string) ($exception->errorInfo[0] ?? ''), '23')) {
            $duplicate = str_contains(strtolower($exception->getMessage()), 'unique')
                || str_contains(strtolower($exception->getMessage()), 'duplicate');

            return [
                409,
                $duplicate ? 'A record with the same unique value already exists' : 'The operation violates a database relationship',
                $duplicate ? 'DUPLICATE_RESOURCE' : 'DATABASE_CONSTRAINT_VIOLATION',
                null,
            ];
        }

        if ($exception instanceof HttpExceptionInterface) {
            $status = $exception->getStatusCode();

            return [
                $status,
                $exception->getMessage() ?: (Response::$statusTexts[$status] ?? 'Request failed'),
                self::errorCode($status, $exception->getMessage()),
                null,
            ];
        }

        return [500, 'Internal Server Error', 'INTERNAL_SERVER_ERROR', null];
    }

    private static function requestId(Request $request): string
    {
        return $request->header('X-Request-ID') ?: (string) Str::uuid();
    }
}
