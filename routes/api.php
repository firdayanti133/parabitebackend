<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\Merchant\DashboardController as MerchantDashboardController;
use App\Http\Controllers\Merchant\MenuController as MerchantMenuController;
use App\Http\Controllers\Merchant\OrderController as MerchantOrderController;
use App\Http\Controllers\Merchant\ReportController as MerchantReportController;

use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\User\HistoryController as UserHistoryController;
use App\Http\Controllers\User\ProfileController as UserProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    // User-specific routes
    Route::prefix('user')->group(function () {
        Route::post('/register', [AuthController::class, 'userRegister']);

        Route::middleware('auth:user')->group(function () {
            Route::prefix('menu')->group(function () {
                Route::get('/list', [UserDashboardController::class, 'getListMenu']);
                Route::get('/list/{merchant_id}', [UserDashboardController::class, 'getMerchantListMenu']);
                Route::get('/detail/{menu_id}', [UserDashboardController::class, 'getMenuDetail']);
                Route::get('/sepuluh-ribu', [UserDashboardController::class, 'getSepuluhRibuMenu']);
            });

            Route::prefix('order')->group(function () {
                Route::prefix('temp')->group(function () {
                    Route::get('/', [UserOrderController::class, 'getListTempOrder']);
                    Route::post('/', [UserOrderController::class, 'createTempOrder']);
                    Route::put('/{temp_order_id}', [UserOrderController::class, 'updateTempOrder']);
                    Route::delete('/{temp_order_id}', [UserOrderController::class, 'removeTempOrder']);
                });

                Route::post('/', [UserOrderController::class, 'createOrder']);
            });

            Route::prefix('history')->group(function () {
               Route::get('/', [UserHistoryController::class, 'getListPurchaseHistory']); 
               Route::get('/{order_id}', [UserHistoryController::class, 'getHistoryDetail']);
            });

            Route::prefix('profile')->group(function () {
               Route::get('/stat', [UserProfileController::class, 'getUserOrderStats']); 
            });

            Route::prefix('favorite')->group(function () {
               Route::get('/', [UserDashboardController::class, 'getUserFavoriteList']);
               Route::put('/{menu_id}', [UserDashboardController::class, 'userFavoriteHandler']);
            });
        });
    });

    // Merchant-specific routes
    Route::prefix('merchant')->group(function () {
        Route::post('/register', [AuthController::class, 'merchantRegister']);

        Route::middleware('auth:merchant')->group(function () {
            Route::prefix('menu')->group(function () {
                Route::get('/list', [MerchantDashboardController::class, 'getListMenu']);
                Route::get('/detail/{menu_id}', [MerchantMenuController::class, 'getMenuDetail']);
                Route::get('/favorite', [MerchantDashboardController::class, 'getFavoriteMenu']);

                Route::post('/', [MerchantMenuController::class, 'createMenu']);
                Route::put('/{menu_id}', [MerchantMenuController::class, 'updateMenu']);
                Route::delete('/{menu_id}', [MerchantMenuController::class, 'deleteMenu']);
            });

            Route::prefix('order')->group(function () {
                Route::get('/list', [MerchantOrderController::class, 'getListOrder']);
                Route::get('/detail/{order_id}', [MerchantOrderController::class, 'getOrderDetail']);

                Route::put('/status/{order_id}', [MerchantOrderController::class, 'updateOrderStatus']);
                Route::put('/payment/{order_id}', [MerchantOrderController::class, 'updateOrderPaymentStatus']);
            });

            Route::prefix('report')->group(function () {
               Route::get('/', [MerchantDashboardController::class, 'checkDailyIncome']);
               Route::get('/daily', [MerchantReportController::class, 'getListDailyIncome']); 
               Route::get('/weekly', [MerchantReportController::class, 'getWeeklyReport']);
            });
        });
    });
});