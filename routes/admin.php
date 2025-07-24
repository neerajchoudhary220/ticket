<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.test');
});

Route::controller(AuthController::class)->group(function () {
    Route::get('login', 'showLoginForm')->name('admin.login');
    Route::post('login', 'login');
});
