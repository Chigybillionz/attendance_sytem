<?php
// File: backend/routes/api.php
// Location: backend/routes/api.php
// UPDATED VERSION WITH COMPLETE USER MANAGEMENT ROUTES

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Admin\FeedbackController;

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

// Add this after the auth routes (before protected routes)
Route::post('contact', [ContactController::class, 'submitContactForm']);

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
        Route::post('refresh-token', [AuthController::class, 'refreshToken']);
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

    // Admin routes - FEEDBACK MANAGEMENT (MOVED HERE)
    Route::prefix('admin')->group(function () {
        Route::get('feedbacks', [FeedbackController::class, 'index']);
        Route::delete('feedbacks/{id}', [FeedbackController::class, 'destroy']);
    });

    // User management routes - ENHANCED VERSION
    Route::prefix('users')->group(function () {
        // Basic CRUD operations
        Route::get('/', [UserController::class, 'index']); // List all users (admin only)
        Route::post('/', [UserController::class, 'store']); // Create new user (admin only)
        Route::get('{id}', [UserController::class, 'show']); // Get specific user
        Route::put('{id}', [UserController::class, 'update']); // Update user
        Route::delete('{id}', [UserController::class, 'destroy']); // Delete user (admin only)
        
        // Additional user management endpoints
        Route::patch('{id}/status', [UserController::class, 'updateStatus']); // Update status (admin only)
        Route::get('departments/list', [UserController::class, 'getDepartments']); // Get departments
        Route::get('stats/overview', [UserController::class, 'getStats']); // Get user statistics (admin only)
        Route::post('bulk-action', [UserController::class, 'bulkAction']); // Bulk operations (admin only)
        
        // For your existing routes compatibility
        Route::get('departments', [UserController::class, 'getDepartments']); // Alias for departments
    });
    
    // contact form
    Route::prefix('contacts')->group(function () {
        Route::get('/', [ContactController::class, 'index']);
        Route::get('{id}', [ContactController::class, 'show']);
        Route::patch('{id}/status', [ContactController::class, 'updateStatus']);
        Route::delete('{id}', [ContactController::class, 'destroy']);
    });
});

// Fallback route for API - handles 404s
Route::fallback(function () {
    return response()->json([
        'message' => 'API endpoint not found. Please check your URL.',
        'available_endpoints' => [
            'POST /api/auth/login' => 'User login',
            'POST /api/auth/register' => 'User registration',
            'POST /api/contact' => 'Submit contact form',
            'GET /api/test' => 'Test API connection',
            'GET /api/admin/feedbacks' => 'List feedbacks (Admin)',
            'DELETE /api/admin/feedbacks/{id}' => 'Delete feedback (Admin)',
            'GET /api/users' => 'List users (Admin)',
            'POST /api/users' => 'Create user (Admin)',
            'GET /api/users/{id}' => 'Get user details',
            'PUT /api/users/{id}' => 'Update user',
            'DELETE /api/users/{id}' => 'Delete user (Admin)',
            'PATCH /api/users/{id}/status' => 'Update user status (Admin)',
            'POST /api/users/bulk-action' => 'Bulk user actions (Admin)',
            'GET /api/contacts' => 'List contacts (Admin)',
            'GET /api/contacts/{id}' => 'View contact (Admin)',
            'PATCH /api/contacts/{id}/status' => 'Update contact status (Admin)',
            'DELETE /api/contacts/{id}' => 'Delete contact (Admin)'
        ]
    ], 404);
});