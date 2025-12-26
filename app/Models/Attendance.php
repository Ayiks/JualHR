<?php

// app/Models/Attendance.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'work_hours',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'work_hours' => 'decimal:2',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Scopes
    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeForMonth($query, $month, $year)
    {
        return $query->whereMonth('date', $month)
                     ->whereYear('date', $year);
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    // Helper Methods
    public function checkIn()
    {
        $this->check_in = now()->format('H:i:s');
        $this->status = 'present';
        $this->save();
    }

    public function checkOut()
    {
        $this->check_out = now()->format('H:i:s');
        $this->calculateWorkHours();
        $this->save();
    }

    public function calculateWorkHours()
    {
        if ($this->check_in && $this->check_out) {
            $checkIn = Carbon::parse($this->check_in);
            $checkOut = Carbon::parse($this->check_out);
            $this->work_hours = $checkOut->diffInHours($checkIn, true);
        }
    }

    public function isLate()
    {
        if (!$this->check_in) {
            return false;
        }

        $checkIn = Carbon::parse($this->check_in);
        $standardTime = Carbon::parse('09:00:00');
        
        return $checkIn->greaterThan($standardTime);
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'present' => 'bg-green-100 text-green-800',
            'absent' => 'bg-red-100 text-red-800',
            'half_day' => 'bg-yellow-100 text-yellow-800',
            'late' => 'bg-orange-100 text-orange-800',
            'on_leave' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // Accessor for full name (if needed in queries)
    public function getFullNameAttribute()
    {
        return $this->employee->full_name ?? 'N/A';
    }
}