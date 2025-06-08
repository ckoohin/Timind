<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\GoalController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\AiController;
use App\Http\Controllers\Api\NotificationController;

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::put('/auth/change-password', [AuthController::class, 'changePassword']);
    
    // User
    Route::apiResource('users', UserController::class);
    Route::put('/users/{user}/preferences', [UserController::class, 'updatePreferences']);
    
    // Activities
    Route::apiResource('activities', ActivityController::class);
    Route::get('/activities/calendar/{month}', [ActivityController::class, 'getCalendarData']);
    
    // Goals
    Route::apiResource('goals', GoalController::class);
    Route::post('/goals/{goal}/tasks', [GoalController::class, 'addTask']);
    Route::put('/goals/{goal}/tasks/{task}', [GoalController::class, 'updateTask']);
    
    // Analytics
    Route::get('/analytics/dashboard', [AnalyticsController::class, 'dashboard']);
    Route::get('/analytics/daily/{date}', [AnalyticsController::class, 'daily']);
    Route::get('/analytics/weekly/{week}', [AnalyticsController::class, 'weekly']);
    
    // AI
    Route::post('/ai/generate-schedule', [AiController::class, 'generateSchedule']);
    Route::get('/ai/suggestions', [AiController::class, 'getSuggestions']);
    Route::post('/ai/suggestions/{suggestion}/respond', [AiController::class, 'respondToSuggestion']);
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy']);
});