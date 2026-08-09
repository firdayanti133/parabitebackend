<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssetController extends Controller
{
    public function menuImage(string $filename): StreamedResponse|JsonResponse
    {
        $path = 'img/menu/'.$filename;

        if (! Storage::disk('public')->exists($path)) {
            return response()->json([
                'code' => 404,
                'message' => 'Menu image not found',
                'error_code' => 'ASSET_NOT_FOUND',
                'errors' => null,
            ], 404);
        }

        $response = Storage::disk('public')->response($path);
        $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        return $response;
    }
}
