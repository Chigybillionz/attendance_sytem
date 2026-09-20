<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function clockIn(Request $request)
    {
        try {
            $user = $request->user();
            $today = Carbon::now()->toDateString();
            
            // Check if user already clocked in today
            $existingAttendance = Attendance::where('user_id', $user->id)
                ->where('date', $today)
                ->first();
                
            if ($existingAttendance && $existingAttendance->clock_in_time) {
                return response()->json([
                    'message' => 'You have already clocked in today',
                    'attendance' => $existingAttendance
                ], 400);
            }
            
            $clockInTime = Carbon::now();
            
            if ($existingAttendance) {
                // Update existing record
                $existingAttendance->update([
                    'clock_in_time' => $clockInTime
                ]);
                $attendance = $existingAttendance;
            } else {
                // Create new record
                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'date' => $today,
                    'clock_in_time' => $clockInTime,
                ]);
            }
            
            return response()->json([
                'message' => 'Clocked in successfully',
                'attendance' => $attendance->load('user')
            ]);
        } catch (\Throwable $e) {
            \Log::error('Clock-in error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'Failed to clock in: ' . $e->getMessage()
            ], 500);
        }
    }

    public function clockOut(Request $request)
    {
        try {
            $user = $request->user();
            $today = Carbon::now()->toDateString();
            
            $attendance = Attendance::where('user_id', $user->id)
                ->where('date', $today)
                ->first();
                
            if (!$attendance || !$attendance->clock_in_time) {
                return response()->json([
                    'message' => 'You must clock in before clocking out'
                ], 400);
            }
            
            if ($attendance->clock_out_time) {
                return response()->json([
                    'message' => 'You have already clocked out today',
                    'attendance' => $attendance
                ], 400);
            }
            
            $attendance->update([
                'clock_out_time' => Carbon::now()
            ]);
            
            return response()->json([
                'message' => 'Clocked out successfully',
                'attendance' => $attendance->load('user')
            ]);
        } catch (\Throwable $e) {
            \Log::error('Clock-out error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'Failed to clock out: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTodayAttendance(Request $request)
    {
        try {
            $user = $request->user();
            $today = Carbon::now()->toDateString();
            
            $attendance = Attendance::where('user_id', $user->id)
                ->where('date', $today)
                ->with('user')
                ->first();
                
            return response()->json([
                'attendance' => $attendance
            ]);
        } catch (\Throwable $e) {
            \Log::error('Get today attendance error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'attendance' => null,
                'message' => 'Failed to load today attendance'
            ], 200); // Return 200 with null attendance so the frontend doesn't crash
        }
    }

    public function getAttendanceHistory(Request $request)
    {
        try {
            $user = $request->user();
            $perPage = $request->get('per_page', 15);
            
            $query = Attendance::where('user_id', $user->id)
                ->with('user')
                ->orderBy('date', 'desc');
                
            // Filter by date range if provided
            if ($request->filled('start_date')) {
                $query->where('date', '>=', $request->start_date);
            }
            
            if ($request->filled('end_date')) {
                $query->where('date', '<=', $request->end_date);
            }
            
            // Filter by month if provided
            if ($request->filled('month') && $request->filled('year')) {
                $query->whereMonth('date', $request->month)
                      ->whereYear('date', $request->year);
            }
            
            $attendances = $query->paginate($perPage);
            
            return response()->json($attendances);
        } catch (\Throwable $e) {
            \Log::error('Get attendance history error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'data' => [],
                'total' => 0,
                'message' => 'Failed to load attendance history'
            ], 500);
        }
    }

    public function getAllAttendance(Request $request)
    {
        try {
            // Admin only
            if (!$request->user()->isAdmin()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            
            $perPage = $request->get('per_page', 15);
            
            $query = Attendance::with('user')
                ->orderBy('date', 'desc');
                
            // Filter by user if provided
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
            
            // Filter by date range
            if ($request->filled('start_date')) {
                $query->where('date', '>=', $request->start_date);
            }
            
            if ($request->filled('end_date')) {
                $query->where('date', '<=', $request->end_date);
            }
            
            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            $attendances = $query->paginate($perPage);
            
            return response()->json($attendances);
        } catch (\Throwable $e) {
            \Log::error('Get all attendance error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'data' => [],
                'total' => 0,
                'message' => 'Failed to load all attendance records'
            ], 500);
        }
    }

    public function getUserAttendance(Request $request, $userId)
    {
        try {
            // Admin only
            if (!$request->user()->isAdmin()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            
            $perPage = $request->get('per_page', 15);
            
            $query = Attendance::where('user_id', $userId)
                ->with('user')
                ->orderBy('date', 'desc');
                
            // Apply same filters as getAttendanceHistory
            if ($request->filled('start_date')) {
                $query->where('date', '>=', $request->start_date);
            }
            
            if ($request->filled('end_date')) {
                $query->where('date', '<=', $request->end_date);
            }
            
            if ($request->filled('month') && $request->filled('year')) {
                $query->whereMonth('date', $request->month)
                      ->whereYear('date', $request->year);
            }
            
            $attendances = $query->paginate($perPage);
            
            return response()->json($attendances);
        } catch (\Throwable $e) {
            \Log::error('Get user attendance error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'data' => [],
                'total' => 0,
                'message' => 'Failed to load user attendance records'
            ], 500);
        }
    }
}
