<?php

use App\Http\Controllers\Admin\AdminWebController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminWebController::class, 'login'])->name('login');
    Route::get('/dashboard', [AdminWebController::class, 'dashboard'])->name('dashboard');

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminWebController::class, 'usersIndex'])->name('index');
        Route::get('/create', [AdminWebController::class, 'usersCreate'])->name('create');
        Route::get('/{id}', [AdminWebController::class, 'usersShow'])->name('show');
        Route::get('/{id}/edit', [AdminWebController::class, 'usersEdit'])->name('edit');
    });

    Route::prefix('locations')->name('locations.')->group(function () {
        Route::get('/', [AdminWebController::class, 'locationsIndex'])->name('index');
        Route::get('/create', [AdminWebController::class, 'locationsCreate'])->name('create');
        Route::get('/{id}/edit', [AdminWebController::class, 'locationsEdit'])->name('edit');
    });
});
