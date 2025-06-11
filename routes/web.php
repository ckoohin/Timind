<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityController;

Route::get('/', function () {
    return view('welcome'); 
})->name('home');

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
    Route::patch('/activities/{activity}/status', [ActivityController::class, 'updateStatus'])->name('activities.updateStatus');
    
    Route::get('/activities/create', function () {
        return view('dashboard.index');
    })->name('activities.create');
    
    Route::get('/goals', function () {
        return view('dashboard.index');
    })->name('goals.index');
    
    Route::get('/analytics', function () {
        return view('dashboard.index');
    })->name('analytics.index');
});