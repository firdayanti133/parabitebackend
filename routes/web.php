<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LocationController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    Route::middleware('check.admin')->group(function () {
        Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('admin.dashboard');

        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('admin.users.index');
            Route::get('/create', [UserController::class, 'create'])->name('admin.users.create');
            Route::post('/', [UserController::class, 'store'])->name('admin.users.store');
            Route::get('/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
            Route::put('/{id}', [UserController::class, 'update'])->name('admin.users.update');
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        });

        Route::prefix('locations')->group(function () {
            Route::get('/', [LocationController::class, 'index'])->name('admin.locations.index');
            Route::get('/create', [LocationController::class, 'create'])->name('admin.locations.create');
            Route::post('/', [LocationController::class, 'store'])->name('admin.locations.store');
            Route::get('/{id}/edit', [LocationController::class, 'edit'])->name('admin.locations.edit');
            Route::put('/{id}', [LocationController::class, 'update'])->name('admin.locations.update');
            Route::delete('/{id}', [LocationController::class, 'destroy'])->name('admin.locations.destroy');
        });
    });
});