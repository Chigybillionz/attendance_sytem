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
});

// Fallback route for API - handles 404s
Route::fallback(function () {
    return response()->json([
        'message' => 'API endpoint not found. Please check your URL.',
        'available_endpoints' => [
            'POST /api/auth/login' => 'User login',
            'POST /api/auth/register' => 'User registration',
            'GET /api/test' => 'Test API connection',
            'GET /api/users' => 'List users (Admin)',
            'POST /api/users' => 'Create user (Admin)',
            'GET /api/users/{id}' => 'Get user details',
            'PUT /api/users/{id}' => 'Update user',
            'DELETE /api/users/{id}' => 'Delete user (Admin)',
            'PATCH /api/users/{id}/status' => 'Update user status (Admin)',
            'POST /api/users/bulk-action' => 'Bulk user actions (Admin)'
        ]
    ], 404);
});

/*
AVAILABLE ENDPOINTS SUMMARY:

Authentication:
- POST /api/auth/login              - User login
- POST /api/auth/register           - User registration  
- POST /api/auth/logout             - User logout
- GET  /api/auth/user               - Get current user

Dashboard:
- GET  /api/dashboard/admin         - Admin dashboard data
- GET  /api/dashboard/worker        - Worker dashboard data

Attendance:
- POST /api/attendance/clock-in     - Clock in
- POST /api/attendance/clock-out    - Clock out
- GET  /api/attendance/today        - Today's attendance
- GET  /api/attendance/history      - Attendance history
- GET  /api/attendance/all          - All attendance (Admin)
- GET  /api/attendance/user/{id}    - User attendance (Admin)

User Management (CRUD Operations):
- GET    /api/users                 - List users with pagination/search/filters (Admin)
- POST   /api/users                 - Create new user (Admin)
- GET    /api/users/{id}            - Get user details with statistics
- PUT    /api/users/{id}            - Update user information
- DELETE /api/users/{id}            - Delete user (Admin only)
- PATCH  /api/users/{id}/status     - Update user status (Admin)
- GET    /api/users/departments     - Get available departments
- GET    /api/users/stats/overview  - Get user statistics (Admin)
- POST   /api/users/bulk-action     - Bulk operations on users (Admin)

Query Parameters for GET /api/users:
- search: Search in name, email, employee_id, department
- role: Filter by role (admin/worker)  
- status: Filter by status (active/inactive)
- department: Filter by department
- sort_by: Sort field (name, email, employee_id, role, department, status, created_at)
- sort_order: Sort direction (asc/desc)
- page: Page number for pagination
- per_page: Items per page (max 50)

Bulk Action Types:
- activate: Set users status to active
- deactivate: Set users status to inactive  
- delete: Delete users (or deactivate if they have attendance records)
*/