<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Cms\MerchantController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['jwt.auth'])->group(function () {

});

Route::get('/', [AuthController::class, 'test']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::prefix('cms')->group(function () {
    Route::prefix('merchant')->group(function () {
        Route::get('/', [MerchantController::class, 'getAllMerchants']);
        Route::post('/', [MerchantController::class, 'createMerchant']);
        Route::put('/{id}', [MerchantController::class, 'updateMerchant']);
        Route::delete('/{id}', [MerchantController::class, 'deleteMerchant']);
    });
});