<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome'); 
})->name('home');

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

    // Placeholder routes for navigation
    Route::get('/activities', function () {
        return view('dashboard.index');
    })->name('activities.index');
    
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