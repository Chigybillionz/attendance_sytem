<?php
// File: backend/app/Http/Controllers/Api/UserController.php
// Location: backend/app/Http/Controllers/Api/UserController.php
// COMPLETE VERSION THAT EXTENDS YOUR EXISTING CONTROLLER

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users with pagination, search, and filters
     */
    public function index(Request $request)
    {
        try {
            // Check if user is admin (you can implement this check in middleware)
            if (!auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $query = User::query();

            // Search functionality
            if ($request->has('search') && $request->search) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('employee_id', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('department', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Filter by role
            if ($request->has('role') && $request->role) {
                $query->where('role', $request->role);
            }

            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            // Filter by department
            if ($request->has('department') && $request->department) {
                $query->where('department', $request->department);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            // Validate sort fields to prevent SQL injection
            $allowedSortFields = ['name', 'email', 'employee_id', 'role', 'department', 'status', 'created_at'];
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
            }

            // Pagination
            $perPage = min($request->get('per_page', 15), 50); // Max 50 per page
            $users = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $users,
                'message' => 'Users retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve users',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Store a newly created user
     * This method extends your existing user creation but for admin use
     */
    public function store(Request $request)
    {
        try {
            // Check if user is admin
            if (!auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Validation rules
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:255',
                'email' => 'required|email|unique:users,email',
                'employee_id' => 'required|string|unique:users,employee_id|min:3|max:50',
                'password' => 'required|string|min:6|confirmed',
                'role' => 'required|in:admin,worker',
                'department' => 'nullable|string|max:100',
                'phone' => 'nullable|string|max:20',
                'status' => 'nullable|in:active,inactive'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'employee_id' => $request->employee_id,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'department' => $request->department,
                'phone' => $request->phone,
                'status' => $request->status ?? 'active'
            ]);

            // Remove sensitive data from response
            $user->makeHidden(['password']);

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User created successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified user with detailed information
     */
    public function show($id)
    {
        try {
            // Check if user is admin or viewing their own profile
            if (!auth()->user()->isAdmin() && auth()->id() != $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $user = User::with(['attendances' => function($query) {
                $query->latest()->take(10);
            }])->findOrFail($id);

            // Get user statistics
            $stats = [
                'total_attendance_days' => $user->attendances()->count(),
                'present_days' => $user->attendances()->where('status', 'present')->count(),
                'late_days' => $user->attendances()->where('status', 'late')->count(),
                'absent_days' => $user->attendances()->where('status', 'absent')->count(),
                'this_month_attendance' => $user->attendances()
                    ->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year)
                    ->count(),
                'today_attendance' => $user->getTodayAttendance()
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'stats' => $stats
                ],
                'message' => 'User retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'error' => config('app.debug') ? $e->getMessage() : 'User not found'
            ], 404);
        }
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        try {
            // Check if user is admin or updating their own profile
            if (!auth()->user()->isAdmin() && auth()->id() != $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $user = User::findOrFail($id);

            // Different validation rules for admin vs self-update
            $validationRules = [
                'name' => 'required|string|min:2|max:255',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')->ignore($user->id)
                ],
                'employee_id' => [
                    'required',
                    'string',
                    'min:3',
                    'max:50',
                    Rule::unique('users', 'employee_id')->ignore($user->id)
                ],
                'department' => 'nullable|string|max:100',
                'phone' => 'nullable|string|max:20',
                'password' => 'nullable|string|min:6|confirmed'
            ];

            // Only admins can update role and status
            if (auth()->user()->isAdmin()) {
                $validationRules['role'] = 'required|in:admin,worker';
                $validationRules['status'] = 'required|in:active,inactive';
            }

            $validator = Validator::make($request->all(), $validationRules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Prepare update data
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'employee_id' => $request->employee_id,
                'department' => $request->department,
                'phone' => $request->phone
            ];

            // Only admins can update role and status
            if (auth()->user()->isAdmin()) {
                $updateData['role'] = $request->role;
                $updateData['status'] = $request->status;
            }

            // Update password if provided
            if ($request->password) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // Hide sensitive data
            $user->makeHidden(['password']);

            return response()->json([
                'success' => true,
                'data' => $user->fresh(),
                'message' => 'User updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove the specified user (admin only)
     */
    public function destroy($id)
    {
        try {
            // Only admins can delete users
            if (!auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $user = User::findOrFail($id);

            // Prevent admin from deleting themselves
            if (auth()->id() === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own account'
                ], 403);
            }

            // Check if user has attendance records
            $hasAttendance = $user->attendances()->exists();
            
            if ($hasAttendance) {
                // Soft delete by setting status to inactive
                $user->update(['status' => 'inactive']);
                $message = 'User deactivated successfully (has attendance records)';
            } else {
                // Hard delete if no attendance records
                $user->delete();
                $message = 'User deleted successfully';
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update user status (admin only)
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            // Only admins can update status
            if (!auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:active,inactive'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status value',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::findOrFail($id);
            
            // Prevent deactivating yourself
            if (auth()->id() === $user->id && $request->status === 'inactive') {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot deactivate your own account'
                ], 403);
            }

            $user->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => "User {$request->status} successfully"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user status',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get available departments
     */
    public function getDepartments()
    {
        try {
            $departments = User::select('department')
                ->whereNotNull('department')
                ->where('department', '!=', '')
                ->distinct()
                ->orderBy('department')
                ->pluck('department');

            return response()->json([
                'success' => true,
                'data' => $departments,
                'message' => 'Departments retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve departments',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get user statistics for admin dashboard
     */
    public function getStats()
    {
        try {
            // Only admins can view stats
            if (!auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('status', 'active')->count(),
                'inactive_users' => User::where('status', 'inactive')->count(),
                'admin_users' => User::where('role', 'admin')->count(),
                'worker_users' => User::where('role', 'worker')->count(),
                'departments' => User::select('department')
                    ->whereNotNull('department')
                    ->where('department', '!=', '')
                    ->distinct()
                    ->orderBy('department')
                    ->pluck('department'),
                'recent_users' => User::latest()->take(5)->get(['id', 'name', 'email', 'role', 'status', 'created_at'])
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Bulk actions for users (admin only)
     */
    public function bulkAction(Request $request)
    {
        try {
            // Only admins can perform bulk actions
            if (!auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'action' => 'required|in:activate,deactivate,delete',
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userIds = $request->user_ids;
            $action = $request->action;
            $currentUserId = auth()->id();

            // Remove current user from bulk actions
            $userIds = array_filter($userIds, function($id) use ($currentUserId) {
                return $id != $currentUserId;
            });

            if (empty($userIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid users selected for bulk action'
                ], 400);
            }

            switch ($action) {
                case 'activate':
                    User::whereIn('id', $userIds)->update(['status' => 'active']);
                    $message = 'Users activated successfully';
                    break;
                
                case 'deactivate':
                    User::whereIn('id', $userIds)->update(['status' => 'inactive']);
                    $message = 'Users deactivated successfully';
                    break;
                
                case 'delete':
                    // Only delete users without attendance records
                    $usersWithAttendance = User::whereIn('id', $userIds)
                        ->whereHas('attendances')
                        ->pluck('id');
                    
                    $usersWithoutAttendance = array_diff($userIds, $usersWithAttendance->toArray());
                    
                    // Deactivate users with attendance
                    if (!empty($usersWithAttendance->toArray())) {
                        User::whereIn('id', $usersWithAttendance)->update(['status' => 'inactive']);
                    }
                    
                    // Delete users without attendance
                    if (!empty($usersWithoutAttendance)) {
                        User::whereIn('id', $usersWithoutAttendance)->delete();
                    }
                    
                    $message = 'Bulk action completed successfully';
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk action failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}