<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DrawController;
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

    // Shopkeeper's Routes
    Route::controller(ShopKeeperController::class)->prefix('shopkeepers')->group(function () {
        Route::get('/', 'index')->name('admin.shopkeepers');
        Route::get('shopkeeper-form/{user_id?}', 'addEditShopKeeper')->name('admin.shopkeeper_form');
        Route::get('shopkeeper-details', 'view')->name('admin.shopkeeper.view');

    });

    // Draw Routes
    Route::controller(DrawController::class)->prefix('draw')->group(function () {
        Route::get('/', 'index')->name('admin.draw');
        Route::get('add-draw', 'addDraw')->name('admin.add.draw');
        Route::get('draw-details-list/{drawDetail}', 'drawDetails')->name('admin.draw.detail.list');
        Route::get('draw-number-details-list', 'numberList')->name('admin.draw.number.details.list');
        Route::get('draw-tikcet-details-list', 'ticketDetailsList')->name('admin.draw.ticke.details.list');

    });

    // Logout Admin
    Route::get('logout', function () {
        Auth::guard('admin')->logout();
        // request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('admin.login');
    })->name('admin.logout');

});
