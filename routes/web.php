<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::controller(AuthController::class)->group(function () {
    Route::get('login', 'showLoginForm')->name('login');
    Route::post('login', 'login');
});

// Authentication Routes
Route::middleware('auth')->group(function () {

    Route::controller(DashboardController::class)->prefix('dashboard')->group(function () {
        Route::get('/', 'index')->name('dashboard');
        Route::get('/option-list', 'optionList')->name('dashboard.option.list');
        Route::get('/draw-details-list', 'drawDetailsList')->name('dashboard.draw.details.list');
        Route::get('/draw-ticket-number-list', 'numberDetailsList')->name('dashboard.draw.ticket.number.list');
    });

    // Tickets Route
    Route::controller(TicketController::class)->prefix('ticket')->group(function () {
        Route::get('/', 'index')->name('ticket');
        Route::get('add-ticket/{ticket_id?}', 'addTicket')->name('ticket.add');

    });

    // Logout User
    Route::get('logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    })->name('logout');
});
