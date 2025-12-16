<?php

// app/Models/Query.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Query extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'issued_by',
        'type',
        'subject',
        'description',
        'severity',
        'status',
        'due_date',
        'attachment',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(Employee::class, 'issued_by');
    }

    public function responses()
    {
        return $this->hasMany(QueryResponse::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResponded($query)
    {
        return $query->where('status', 'responded');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
                     ->where('due_date', '<', now());
    }

    // Helper Methods
    public function close()
    {
        $this->status = 'closed';
        $this->save();
    }

    public function isOverdue()
    {
        return $this->status === 'pending' && $this->due_date && $this->due_date->isPast();
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'responded' => 'bg-blue-100 text-blue-800',
            'closed' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getSeverityBadgeClass()
    {
        return match($this->severity) {
            'low' => 'bg-green-100 text-green-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'high' => 'bg-orange-100 text-orange-800',
            'critical' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}