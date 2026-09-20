<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
   
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
                'errors' => ['email' => ['Invalid credentials']]
            ], 422);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'Your account has been deactivated. Please contact administrator.'
            ], 403);
        }

        // Delete old tokens for this user
        $user->tokens()->delete();

        // Create token with 30 minute expiry (activity tracker handles inactivity)
        $token = $user->createToken('auth_token', ['*'], now()->addMinutes(30))->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'employee_id' => $user->employee_id,
                'department' => $user->department,
                'phone' => $user->phone,
                'status' => $user->status,
            ],
            'token' => $token,
            'expires_at' => now()->addMinutes(30)->toIso8601String(),
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'employee_id' => 'required|string|unique:users',
            'department' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'employee_id' => $request->employee_id,
            'department' => $request->department,
            'phone' => $request->phone,
            'role' => 'worker',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create token with 5 minute expiry
        $token = $user->createToken('auth_token', ['*'], now()->addMinutes(5))->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'employee_id' => $user->employee_id,
                'department' => $user->department,
            ],
            'token' => $token,
            'expires_at' => now()->addMinutes(5)->toIso8601String(),
        ], 201);
    }

    // NEW: Refresh token endpoint
    public function refreshToken(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Delete current token
        $request->user()->currentAccessToken()->delete();

        // Create new token with fresh 5 minute expiry
        $token = $user->createToken('auth_token', ['*'], now()->addMinutes(5))->plainTextToken;

        return response()->json([
            'message' => 'Token refreshed successfully',
            'token' => $token,
            'expires_at' => now()->addMinutes(5)->toIso8601String(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'role' => $request->user()->role,
                'employee_id' => $request->user()->employee_id,
                'department' => $request->user()->department,
                'phone' => $request->user()->phone,
                'status' => $request->user()->status,
            ]
        ]);
    }
}