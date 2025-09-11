<?php
// File: backend/app/Models/User.php
// Location: backend/app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'employee_id',
        'department',
        'phone',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeWorkers($query)
    {
        return $query->where('role', 'worker');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isWorker()
    {
        return $this->role === 'worker';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function getTodayAttendance()
    {
        return $this->attendances()
            ->where('date', now()->toDateString())
            ->first();
    }
}

// =====================================

// File: backend/app/Models/Attendance.php
// Location: backend/app/Models/Attendance.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'clock_in_time',
        'clock_out_time',
        'total_hours',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in_time' => 'datetime:H:i:s',
        'clock_out_time' => 'datetime:H:i:s',
        'total_hours' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods
    public function calculateTotalHours()
    {
        if ($this->clock_in_time && $this->clock_out_time) {
            $clockIn = Carbon::parse($this->clock_in_time);
            $clockOut = Carbon::parse($this->clock_out_time);
            
            $totalMinutes = $clockOut->diffInMinutes($clockIn);
            $this->total_hours = round($totalMinutes / 60, 2);
            
            return $this->total_hours;
        }
        
        return 0;
    }

    public function determineStatus()
    {
        if (!$this->clock_in_time) {
            return 'absent';
        }

        $clockInTime = Carbon::parse($this->clock_in_time);
        $workStartTime = Carbon::parse('09:00:00'); // 9 AM start time
        
        if ($clockInTime->gt($workStartTime)) {
            return 'late';
        }

        return 'present';
    }

    // Boot method to auto-calculate hours and status
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($attendance) {
            $attendance->calculateTotalHours();
            $attendance->status = $attendance->determineStatus();
        });
    }
}