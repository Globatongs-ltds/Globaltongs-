<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'home'])->name('home');

Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
Route::get('/user/dashboard', [DashboardController::class, 'user'])->name('user.dashboard');
Route::post('/translate', [DashboardController::class, 'translate'])->name('translate');

Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
Route::post('/payments/initialize', [PaymentController::class, 'initialize'])->name('payments.initialize');

Route::get('/register', [MarketingController::class, 'register'])->name('register');
Route::post('/register', [MarketingController::class, 'registerSubmit'])->name('register.submit');

Route::get('/trial', [MarketingController::class, 'trial'])->name('trial');
Route::post('/trial', [MarketingController::class, 'trialSubmit'])->name('trial.submit');

Route::get('/pricing', [MarketingController::class, 'pricing'])->name('pricing');
Route::get('/referral-package', [MarketingController::class, 'referral'])->name('referral');
Route::get('/contact-us', [MarketingController::class, 'contact'])->name('contact');
Route::post('/contact-us', [MarketingController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/about-team', [MarketingController::class, 'team'])->name('team');
