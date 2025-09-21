
<?php
// File: backend/routes/api.php
// Location: backend/routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Test route to check if API is working
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working!',
        'timestamp' => now(),
        'version' => '1.0'
    ]);
});

// Public routes (no authentication required)
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
    });

    // Dashboard routes
    Route::prefix('dashboard')->group(function () {
        Route::get('admin', [DashboardController::class, 'adminDashboard']);
        Route::get('worker', [DashboardController::class, 'workerDashboard']);
    });

    // Attendance routes
    Route::prefix('attendance')->group(function () {
        // Worker routes - any authenticated user can access
        Route::post('clock-in', [AttendanceController::class, 'clockIn']);
        Route::post('clock-out', [AttendanceController::class, 'clockOut']);
        Route::get('today', [AttendanceController::class, 'getTodayAttendance']);
        Route::get('history', [AttendanceController::class, 'getAttendanceHistory']);
        
        // Admin routes - require admin role (controlled in controller)
        Route::get('all', [AttendanceController::class, 'getAllAttendance']);
        Route::get('user/{userId}', [AttendanceController::class, 'getUserAttendance']);
    });

    // User management routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']); // List all users (admin only)
        Route::get('departments', [UserController::class, 'getDepartments']); // Get departments
        Route::get('{id}', [UserController::class, 'show']); // Get specific user
        Route::put('{id}', [UserController::class, 'update']); // Update user
        Route::delete('{id}', [UserController::class, 'destroy']); // Delete user (admin only)
        Route::patch('{id}/status', [UserController::class, 'updateStatus']); // Update status (admin only)
    });
});

// Fallback route for API - handles 404s
Route::fallback(function () {
    return response()->json([
        'message' => 'API endpoint not found. Please check your URL.',
        'available_endpoints' => [
            'POST /api/auth/login' => 'User login',
            'POST /api/auth/register' => 'User registration',
            'GET /api/test' => 'Test API connection'
        ]
    ], 404);
});