<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Cms\MerchantController;
use App\Http\Controllers\Cms\FoodController;
use App\Http\Controllers\Cms\OrderController;
use App\Http\Controllers\Cms\MerchantAuthController;
use App\Http\Controllers\Cms\MenuController as MerchantMenuController;
use App\Http\Controllers\OrderController as PublicOrderController;
use App\Http\Controllers\User\MenuController as PublicMenuController;
use App\Http\Controllers\User\LocationController as PublicLocationController;

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

Route::middleware('check.role:user')->group(function () {
    Route::prefix('public')->group(function () {
        Route::get('/menu', [PublicMenuController::class, 'getAllMenus']);
        Route::get('/menu/{merchant_id}', [PublicMenuController::class, 'getMenusByMerchant']);
        Route::get('/location', [PublicLocationController::class, 'getLocations']);
    });
    
    Route::prefix('order')->group(function () {
        Route::post('/', [PublicOrderController::class, 'createOrder']);
        Route::get('/', [PublicOrderController::class, 'allOrders']);
        Route::get('/{id}', [PublicOrderController::class, 'getOrder']);
        Route::put('/{id}', [PublicOrderController::class, 'updateStatus']);
        Route::delete('/{id}', [PublicOrderController::class, 'deleteOrder']);
    });
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/login/merchant', [MerchantAuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('check.role:merchant')->group(function () {
    Route::prefix('cms')->group(function () {
        Route::prefix('merchant')->group(function () {
            Route::get('/', [MerchantController::class, 'getAllMerchants']);
            Route::post('/', [MerchantController::class, 'createMerchant']);
            Route::put('/{id}', [MerchantController::class, 'updateMerchant']);
            Route::delete('/{id}', [MerchantController::class, 'deleteMerchant']);
    
            Route::prefix('menu')->group(function () {
                Route::get('/{merchant_id}', [MerchantMenuController::class, 'getAllMenus']);
                Route::post('/{merchant_id}', [MerchantMenuController::class, 'createMenu']);
                Route::put('/{merchant_id}/{id}', [MerchantMenuController::class, 'updateMenu']);
                Route::delete('/{merchant_id}/{id}', [MerchantMenuController::class, 'deleteMenu']);
            }); 
    
            Route::prefix('order')->group(function () {
                Route::get('/{merchant_id}', [OrderController::class, 'getMerchantOrders']);
                Route::put('/{id}', [OrderController::class, 'updateMerchantOrders']);
                Route::delete('/{id}', [OrderController::class, 'deleteOrder']);
            });
        });
    
        Route::prefix('food')->group(function () {
            Route::get('/', [FoodController::class, 'getAllFoods']);
            Route::post('/', [FoodController::class, 'createFood']);
        });
    });
});