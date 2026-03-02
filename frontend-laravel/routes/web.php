<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'home'])->name('home');

Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
Route::get('/user/dashboard', [DashboardController::class, 'user'])->name('user.dashboard');
Route::post('/translate', [DashboardController::class, 'translate'])->name('translate');

Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
Route::post('/payments/initialize', [PaymentController::class, 'initialize'])->name('payments.initialize');
