<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\AnalyticsController;

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
    Route::post('/goals/parse-schedule', [GoalController::class, 'parseScheduleFromAI'])->name('goals.parse-schedule');
    Route::post('/goals/save-schedule', [GoalController::class, 'saveSchedule'])->name('goals.save-schedule');
    Route::post('/goals/parse-and-save', [GoalController::class, 'parseAndSaveSchedule'])->name('goals.parse-and-save');
    Route::get('/api/free-times', [GoalController::class, 'getFreeTime'])->name('api.free-times');
    
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/data', [AnalyticsController::class, 'getAnalyticsData'])->name('analytics.data');
    Route::get('/analytics/monthly', [AnalyticsController::class, 'getMonthlyData'])->name('analytics.monthly');
    Route::get('/analytics/overview', [AnalyticsController::class, 'getOverviewStats'])->name('analytics.overview');
    Route::get('/analytics/categories', [AnalyticsController::class, 'getCategoryStats'])->name('analytics.categories');
});