<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VehiclesController;
use App\Http\Controllers\BookingController;

Route::post('/logout', [AuthController::class, 'logout'])->name('logout-action');
Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'Login'])->name('login-action');
    Route::get('/', [AuthController::class, 'loginView'])->name('login');
    Route::get('/login', [AuthController::class, 'loginView'])->name('login-view');
});
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin only routes
    Route::middleware('admin')->group(function () {
        Route::resource('users', UsersController::class);
        Route::get('/users/{user}/configure-superior', [UsersController::class, 'configureSuperior'])->name('users.configure-superior');
        Route::post('/users/{user}/configure-superior', [UsersController::class, 'updateSuperior'])->name('users.update-superior');
        Route::resource('vehicles', VehiclesController::class);
        Route::get('/vehicles/{vehicle}/history', [VehiclesController::class, 'history'])->name('vehicles.history');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::get('/reports/export', [ReportController::class, 'exportExcel'])->name('reports.export');
    });

    // Booking routes
    Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
    Route::get('/booking/list', [BookingController::class, 'index'])->name('booking.list');
    Route::get('/booking/add', [BookingController::class, 'show'])->name('booking.add');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/{id}/detail', [BookingController::class, 'detailView'])->name('booking.detail');
    Route::post('/booking/{id}/approve', [BookingController::class, 'approve'])->name('booking.approve');
    Route::post('/booking/{id}/change-driver', [BookingController::class, 'changeDriver'])->name('booking.change-driver');
});