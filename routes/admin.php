<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::get('login', 'showLoginForm')->name('admin.login');
    Route::post('login', 'login');
});

Route::middleware(['auth:admin'])->group(function () {
    Route::get('/', fn () => view('admin.dashboard.index'))->name('admin.dashboard');
});
