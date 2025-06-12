<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\GoalController;

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
    Route::post('/activities', [ActivityController::class, 'store'])->name('activitiesStore');
    Route::patch('/activities/{activity}/status', [ActivityController::class, 'updateStatus'])->name('activities.updateStatus');
    
    Route::get('/activities/create', function () {
        return view('dashboard.index');
    })->name('activities.create');
    
    Route::get('/goals', [GoalController::class, 'index'])->name('goals.index');
    Route::post('/goals', [GoalController::class , 'postMessage'])->name('postMessage');
    // Route::get('/goals/create', [GoalController::class, 'create'])->name('goals.create');
    // Route::post('/goals', [GoalController::class, 'store'])->name('goals.store');
    // Route::get('/goals/{goal}', [GoalController::class, 'show'])->name('goals.show');
    // Route::get('/goals/{goal}/edit', [GoalController::class, 'edit'])->name('goals.edit');
    // Route::put('/goals/{goal}', [GoalController::class, 'update'])->name('goals.update');
    // Route::delete('/goals/{goal}', [GoalController::class, 'destroy'])->name('goals.destroy');
    
    Route::get('/analytics', function () {
        return view('dashboard.index');
    })->name('analytics.index');
});