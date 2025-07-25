<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ShopKeeperController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::get('login', 'showLogin')->name('admin.login');
    Route::post('login', 'login');
});

Route::middleware(['auth:admin'])->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/', 'index')->name('admin.dashboard');
    });

    Route::controller(ShopKeeperController::class)->prefix('shopkeepers')->group(function () {
        Route::get('/', 'index')->name('admin.shopkeepers');
        Route::get('add', 'showAddForm')->name('admin.shopkeeper_form');

    });

    // Logout Admin
    Route::get('logout', function () {
        Auth::guard('admin')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('admin.login');
    })->name('admin.logout');

});
