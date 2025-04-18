<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;

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
    Route::get('/user', [UserController::class, 'profile']);
});

Route::get('/', [AuthController::class, 'test']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/order', [OrderController::class, 'createOrder']);
Route::get('/order', [OrderController::class, 'allOrders']);
Route::get('/order/{id}', [OrderController::class, 'getOrder']);
Route::put('/order/{id}', [OrderController::class, 'updateStatus']);
Route::delete('/order/{id}', [OrderController::class, 'deleteOrder']);
