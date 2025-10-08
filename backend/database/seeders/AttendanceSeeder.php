<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $workers = User::where('role', 'worker')->get();
        if ($workers->isEmpty()) {
            $this->command->warn('No workers found. Please run AdminSeeder first.');
            return;
        }
        // Generate attendance records for the past 30 days
        for ($i = 30; $i >= 1; $i--) {
            $date = Carbon::now()->subDays($i);
            // Skip weekends
            if ($date->isWeekend()) {
                continue;
            }    
            foreach ($workers as $worker) {
                // 85% chance worker shows up (realistic attendance)
                if (rand(1, 100) <= 85) {
                    $this->createAttendanceRecord($worker, $date);
                }
            }
        }
        
        // Create today's attendance for some workers (for testing)
        $today = Carbon::now();
        if (!$today->isWeekend()) {
            $todayWorkers = $workers->random(3); 
            // 3 random workers for today  
            foreach ($todayWorkers as $worker) {
            $this->createTodayAttendance($worker, $today);
            }
        }
    }

    private function createAttendanceRecord($worker, $date)
    {
        // Check if attendance already exists
        $exists = Attendance::where('user_id', $worker->id)
            ->where('date', $date->toDateString())
            ->exists();
            
        if ($exists) {
            return;
            // Skip if already exists
        }
        // Random clock in time between 8:00 AM and 9:30 AM
        $clockInHour = rand(8, 9);
        $clockInMinute = $clockInHour == 9 ? rand(0, 30) : rand(0, 59);
        $clockInTime = $date->copy()->setTime($clockInHour, $clockInMinute, 0);
        // Random clock out time between 5:00 PM and 6:30 PM
        $clockOutHour = rand(17, 18);
        $clockOutMinute = $clockOutHour == 18 ? rand(0, 30) : rand(0, 59);
        $clockOutTime = $date->copy()->setTime($clockOutHour, $clockOutMinute, 0);
        
        // Some records might not have clock out (worker forgot)
        $hasClockOut = rand(1, 10) > 1; // 90% chance of having clock out
        
        Attendance::create([
            'user_id' => $worker->id,
            'date' => $date->toDateString(),
            'clock_in_time' => $clockInTime->format('H:i:s'),
            'clock_out_time' => $hasClockOut ? $clockOutTime->format('H:i:s') : null,
            'notes' => rand(1, 20) == 1 ? 'Had a meeting' : null,
        ]);
    }
    private function createTodayAttendance($worker, $date)
    {
        // Check if attendance already exists
        $exists = Attendance::where('user_id', $worker->id)
            ->where('date', $date->toDateString())
            ->exists();
            
        if ($exists) {
            return; // Skip if already exists
        }
        
        // Create partial attendance (only clock in) for testing clock out functionality
        $clockInHour = rand(8, 9);
        $clockInMinute = rand(0, 30);
        $clockInTime = $date->copy()->setTime($clockInHour, $clockInMinute, 0);
        
        Attendance::create([
            'user_id' => $worker->id,
            'date' => $date->toDateString(),
            'clock_in_time' => $clockInTime->format('H:i:s'),
            'clock_out_time' => null, // No clock out yet - for testing
        ]);
    }
}