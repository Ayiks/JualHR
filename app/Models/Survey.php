<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'is_active',
        'is_anonymous',
        'start_date',
        'end_date',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_anonymous' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(SurveyQuestion::class);
    }

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }

    // Scopes - Keep your existing + add what's needed
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
                     ->where(function ($q) {
                         $q->whereNull('start_date')
                           ->orWhere('start_date', '<=', now());
                     })
                     ->where(function ($q) {
                         $q->whereNull('end_date')
                           ->orWhere('end_date', '>=', now());
                     });
    }

    public function scopeDraft($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeClosed($query)
    {
        return $query->where(function ($q) {
            $q->where('is_active', false)
              ->orWhere('end_date', '<', now());
        });
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    // Helper Methods - Keep your existing
    public function isAvailable()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        
        if ($this->start_date && $this->start_date->isFuture()) {
            return false;
        }

        if ($this->end_date && $this->end_date->isPast()) {
            return false;
        }

        return true;
    }

    public function hasResponded($employeeId)
    {
        return $this->responses()->where('employee_id', $employeeId)->exists();
    }

    // Additional useful methods
    public function getIsActiveAttribute()
    {
        return $this->attributes['is_active'] && 
               (!$this->start_date || $this->start_date <= now()) && 
               (!$this->end_date || $this->end_date >= now());
    }

    public function getIsClosedAttribute()
    {
        return !$this->attributes['is_active'] || 
               ($this->end_date && $this->end_date < now());
    }

    public function getResponseCountAttribute()
    {
        return $this->responses()->count();
    }

    public function isAvailableFor(Employee $employee)
    {
        if (!$this->isAvailable()) {
            return false;
        }

        // Check if already responded
        if ($this->hasResponded($employee->id)) {
            return false;
        }

        return true;
    }
}