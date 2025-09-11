<?php>
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

    // Scopes
    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)
                    ->whereMonth('date', $month);
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    // Mutators & Accessors
    public function setClockInTimeAttribute($value)
    {
        if ($value) {
            $this->attributes['clock_in_time'] = Carbon::parse($value)->format('H:i:s');
        }
    }

    public function setClockOutTimeAttribute($value)
    {
        if ($value) {
            $this->attributes['clock_out_time'] = Carbon::parse($value)->format('H:i:s');
        }
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

        if ($this->clock_out_time) {
            $clockOutTime = Carbon::parse($this->clock_out_time);
            $workEndTime = Carbon::parse('17:00:00'); // 5 PM end time
            
            if ($clockOutTime->lt($workEndTime)) {
                return 'early_departure';
            }
        }

        return 'present';
    }

    public function isLate()
    {
        return $this->status === 'late';
    }

    public function isPresent()
    {
        return $this->status === 'present';
    }

    public function hasClockIn()
    {
        return !is_null($this->clock_in_time);
    }

    public function hasClockOut()
    {
        return !is_null($this->clock_out_time);
    }

    public function getFormattedClockInAttribute()
    {
        return $this->clock_in_time ? 
            Carbon::parse($this->clock_in_time)->format('H:i A') : 
            null;
    }

    public function getFormattedClockOutAttribute()
    {
        return $this->clock_out_time ? 
            Carbon::parse($this->clock_out_time)->format('H:i A') : 
            null;
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