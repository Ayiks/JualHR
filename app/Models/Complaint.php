<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'subject',
        'description',
        'category',
        'priority',
        'status',
        'is_anonymous',
        'attachment',
        'assigned_to',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function responses()
    {
        return $this->hasMany(ComplaintResponse::class);
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeAssignedTo($query, $employeeId)
    {
        return $query->where('assigned_to', $employeeId);
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    // Helper Methods
    public function resolve()
    {
        $this->status = 'resolved';
        $this->save();
    }

    public function close()
    {
        $this->status = 'closed';
        $this->save();
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'open' => 'bg-yellow-100 text-yellow-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'resolved' => 'bg-green-100 text-green-800',
            'closed' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getPriorityBadgeClass()
    {
        return match($this->priority) {
            'low' => 'bg-green-100 text-green-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'high' => 'bg-orange-100 text-orange-800',
            'urgent' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // Additional helper methods for better functionality
    public function getIsAssignedAttribute()
    {
        return !is_null($this->assigned_to);
    }

    public function getIsResolvedAttribute()
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    public function canBeViewedBy(Employee $employee)
    {
        // HR and Super Admin can view all
        if ($employee->user->hasRole(['super_admin', 'hr_admin'])) {
            return true;
        }

        // Employee can view their own complaints
        if (!$this->is_anonymous && $this->employee_id === $employee->id) {
            return true;
        }

        // Assigned handler can view
        if ($this->assigned_to === $employee->id) {
            return true;
        }

        return false;
    }

    public function canBeRespondedBy(Employee $employee)
    {
        return $employee->user->hasRole(['super_admin', 'hr_admin']) ||
               $this->assigned_to === $employee->id;
    }
}