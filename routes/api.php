<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LocationController as AdminLocationController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Merchant\DashboardController as MerchantDashboardController;
use App\Http\Controllers\Merchant\MenuController as MerchantMenuController;
use App\Http\Controllers\Merchant\OrderController as MerchantOrderController;
use App\Http\Controllers\Merchant\ReportController as MerchantReportController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\HistoryController as UserHistoryController;
use App\Http\Controllers\User\LocationController as UserLocationController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\User\ProfileController as UserProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

    Route::prefix('admin')->middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

        Route::prefix('users')->group(function () {
            Route::get('/', [AdminUserController::class, 'index']);
            Route::post('/', [AdminUserController::class, 'store']);
            Route::get('/{user_id}', [AdminUserController::class, 'show']);
            Route::put('/{user_id}', [AdminUserController::class, 'update']);
            Route::delete('/{user_id}', [AdminUserController::class, 'destroy']);
        });

        Route::prefix('locations')->group(function () {
            Route::get('/', [AdminLocationController::class, 'index']);
            Route::post('/', [AdminLocationController::class, 'store']);
            Route::get('/{location_id}', [AdminLocationController::class, 'show']);
            Route::put('/{location_id}', [AdminLocationController::class, 'update']);
            Route::delete('/{location_id}', [AdminLocationController::class, 'destroy']);
        });
    });

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

            Route::prefix('merchant')->group(function () {
                Route::get('/', [UserDashboardController::class, 'getMerchantList']);
                Route::get('/top', [UserDashboardController::class, 'getRecommendedMenu']);
            });

            Route::prefix('order')->group(function () {
                Route::prefix('temp')->group(function () {
                    Route::get('/', [UserOrderController::class, 'getListTempOrder']);
                    Route::post('/', [UserOrderController::class, 'createTempOrder']);
                    Route::put('/{temp_order_id}', [UserOrderController::class, 'updateTempOrder']);
                    Route::delete('/{temp_order_id}', [UserOrderController::class, 'removeTempOrder']);
                });

                Route::get('/current', [UserDashboardController::class, 'getCurrentOrder']);

                Route::post('/', [UserOrderController::class, 'createOrder']);
            });

            Route::prefix('history')->group(function () {
                Route::get('/', [UserHistoryController::class, 'getListPurchaseHistory']);
                Route::get('/{order_id}', [UserHistoryController::class, 'getHistoryDetail']);
            });

            Route::prefix('profile')->group(function () {
                Route::get('/stat', [UserProfileController::class, 'getUserOrderStats']);
            });

            Route::get('/locations', [UserLocationController::class, 'getListLocations']);
        });
    });

    // Merchant-specific routes
    Route::prefix('merchant')->group(function () {
        Route::post('/register', [AuthController::class, 'merchantRegister']);

        Route::middleware('auth:merchant')->group(function () {
            Route::prefix('menu')->group(function () {
                Route::get('/list', [MerchantDashboardController::class, 'getListMenu']);
                Route::get('/detail/{menu_id}', [MerchantMenuController::class, 'getMenuDetail']);
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
