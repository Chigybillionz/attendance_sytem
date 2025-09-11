<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function adminDashboard(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $today = Carbon::now()->toDateString();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Basic counts
        $totalEmployees = User::workers()->count();
        $activeEmployees = User::workers()->active()->count();
        $totalDepartments = User::distinct()->whereNotNull('department')->count('department');

        // Today's attendance
        $todayAttendance = Attendance::whereDate('date', $today)->count();
        $todayPresent = Attendance::whereDate('date', $today)->where('status', 'present')->count();
        $todayLate = Attendance::whereDate('date', $today)->where('status', 'late')->count();
        $todayAbsent = $activeEmployees - $todayAttendance;

        // This month's stats
        $monthlyAttendance = Attendance::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();

        $monthlyHours = Attendance::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('total_hours');

        // Recent attendance records
        $recentAttendance = Attendance::with('user')
            ->orderBy('date', 'desc')
            ->orderBy('clock_in_time', 'desc')
            ->limit(10)
            ->get();

        // Department-wise attendance for today
        $departmentStats = User::workers()
            ->active()
            ->with(['attendances' => function($query) use ($today) {
                $query->where('date', $today);
            }])
            ->get()
            ->groupBy('department')
            ->map(function($users, $department) {
                $total = $users->count();
                $present = $users->filter(function($user) {
                    return $user->attendances->count() > 0;
                })->count();
                
                return [
                    'department' => $department ?: 'No Department',
                    'total_employees' => $total,
                    'present_today' => $present,
                    'absent_today' => $total - $present,
                    'attendance_rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0
                ];
            })
            ->values();

        // Weekly attendance chart data
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayAttendance = Attendance::whereDate('date', $date->toDateString())->count();
            
            $weeklyData[] = [
                'date' => $date->format('M d'),
                'day' => $date->format('D'),
                'attendance' => $dayAttendance
            ];
        }

        return response()->json([
            'overview' => [
                'total_employees' => $totalEmployees,
                'active_employees' => $activeEmployees,
                'total_departments' => $totalDepartments,
                'today_attendance' => $todayAttendance,
                'today_present' => $todayPresent,
                'today_late' => $todayLate,
                'today_absent' => $todayAbsent,
                'monthly_attendance' => $monthlyAttendance,
                'monthly_hours' => round($monthlyHours, 2),
            ],
            'recent_attendance' => $recentAttendance,
            'department_stats' => $departmentStats,
            'weekly_chart' => $weeklyData,
            'attendance_summary' => [
                'present_rate' => $todayAttendance > 0 ? round(($todayPresent / $todayAttendance) * 100, 1) : 0,
                'late_rate' => $todayAttendance > 0 ? round(($todayLate / $todayAttendance) * 100, 1) : 0,
                'absent_rate' => $activeEmployees > 0 ? round(($todayAbsent / $activeEmployees) * 100, 1) : 0,
            ]
        ]);
    }

    public function workerDashboard(Request $request)
    {
        $user = $request->user();
        $today = Carbon::now()->toDateString();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Today's attendance
        $todayAttendance = $user->getTodayAttendance();

        // This month's stats
        $monthlyAttendance = $user->attendances()
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->get();

        $monthlyStats = [
            'days_worked' => $monthlyAttendance->count(),
            'total_hours' => $monthlyAttendance->sum('total_hours'),
            'average_hours' => $monthlyAttendance->avg('total_hours'),
            'late_days' => $monthlyAttendance->where('status', 'late')->count(),
            'on_time_days' => $monthlyAttendance->where('status', 'present')->count(),
        ];

        // Recent attendance (last 7 days)
        $recentAttendance = $user->attendances()
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get();

        // Weekly hours chart
        $weeklyHours = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $attendance = $user->attendances()
                ->where('date', $date->toDateString())
                ->first();
            
            $weeklyHours[] = [
                'date' => $date->format('M d'),
                'day' => $date->format('D'),
                'hours' => $attendance ? $attendance->total_hours : 0
            ];
        }

        return response()->json([
            'user' => [
                'name' => $user->name,
                'employee_id' => $user->employee_id,
                'department' => $user->department,
            ],
            'today_attendance' => $todayAttendance,
            'monthly_stats' => [
                'days_worked' => $monthlyStats['days_worked'],
                'total_hours' => round($monthlyStats['total_hours'], 2),
                'average_hours' => round($monthlyStats['average_hours'], 2),
                'late_days' => $monthlyStats['late_days'],
                'on_time_days' => $monthlyStats['on_time_days'],
            ],
            'recent_attendance' => $recentAttendance,
            'weekly_hours' => $weeklyHours,
            'can_clock_in' => !$todayAttendance || !$todayAttendance->clock_in_time,
            'can_clock_out' => $todayAttendance && $todayAttendance->clock_in_time && !$todayAttendance->clock_out_time,
        ]);
    }
}