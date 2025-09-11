<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Admin only
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');

        $query = User::query();

        // Search functionality
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        $users = $query->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json($users);
    }

    public function show(Request $request, $id)
    {
        $currentUser = $request->user();
        
        // Users can view their own profile, admins can view any profile
        if (!$currentUser->isAdmin() && $currentUser->id != $id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::with(['attendances' => function($query) {
            $query->orderBy('date', 'desc')->limit(10);
        }])->findOrFail($id);

        // Calculate some stats
        $stats = [
            'total_working_days' => $user->attendances()->count(),
            'total_hours' => round($user->attendances()->sum('total_hours'), 2),
            'average_hours_per_day' => round($user->attendances()->avg('total_hours'), 2),
            'late_days' => $user->attendances()->where('status', 'late')->count(),
            'present_days' => $user->attendances()->where('status', 'present')->count(),
        ];

        return response()->json([
            'user' => $user,
            'stats' => $stats
        ]);
    }

    public function update(Request $request, $id)
    {
        $currentUser = $request->user();
        
        // Users can update their own profile, admins can update any profile
        if (!$currentUser->isAdmin() && $currentUser->id != $id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);

        $rules = [
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'employee_id' => ['sometimes', 'required', 'string', Rule::unique('users')->ignore($id)],
            'department' => 'sometimes|nullable|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
            'password' => 'sometimes|nullable|string|min:6|confirmed',
        ];

        // Only admins can update role and status
        if ($currentUser->isAdmin()) {
            $rules['role'] = 'sometimes|required|in:admin,worker';
            $rules['status'] = 'sometimes|required|in:active,inactive';
        }

        $validatedData = $request->validate($rules);

        // Hash password if provided
        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($validatedData);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    public function destroy(Request $request, $id)
    {
        // Admin only
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);

        // Prevent admin from deleting themselves
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'You cannot delete your own account'], 400);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        // Admin only
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        $user = User::findOrFail($id);
        $user->update(['status' => $request->status]);

        return response()->json([
            'message' => 'User status updated successfully',
            'user' => $user
        ]);
    }

    public function getDepartments(Request $request)
    {
        // Get unique departments
        $departments = User::distinct()
            ->whereNotNull('department')
            ->pluck('department')
            ->filter()
            ->sort()
            ->values();

        return response()->json([
            'departments' => $departments
        ]);
    }
}