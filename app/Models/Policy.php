<?php

// app/Models/Policy.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Policy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'code',
        'description',
        'category',
        'version',
        'document_path',
        'status',
        'requires_acknowledgment',
        'effective_date',
        'review_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'requires_acknowledgment' => 'boolean',
        'effective_date' => 'date',
        'review_date' => 'date',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function acknowledgments()
    {
        return $this->hasMany(PolicyAcknowledgment::class);
    }

    public function versions()
    {
        return $this->hasMany(PolicyVersion::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Helper Methods
    public function isAcknowledgedBy(Employee $employee)
    {
        return $this->acknowledgments()
            ->where('employee_id', $employee->id)
            ->exists();
    }

    public function getAcknowledgmentRate()
    {
        $totalEmployees = Employee::where('employment_status', 'active')->count();
        
        if ($totalEmployees === 0) {
            return 0;
        }

        $acknowledgedCount = $this->acknowledgments()->count();
        
        return round(($acknowledgedCount / $totalEmployees) * 100, 2);
    }

    public function isPastReviewDate()
    {
        return $this->review_date && $this->review_date->isPast();
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'active' => 'bg-green-100 text-green-800',
            'draft' => 'bg-yellow-100 text-yellow-800',
            'archived' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getCategoryBadgeClass()
    {
        return match($this->category) {
            'hr' => 'bg-blue-100 text-blue-800',
            'code_of_conduct' => 'bg-purple-100 text-purple-800',
            'health_safety' => 'bg-red-100 text-red-800',
            'it_security' => 'bg-indigo-100 text-indigo-800',
            'leave' => 'bg-green-100 text-green-800',
            'attendance' => 'bg-orange-100 text-orange-800',
            'compensation' => 'bg-pink-100 text-pink-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public static function getCategories()
    {
        return [
            'general' => 'General',
            'hr' => 'Human Resources',
            'code_of_conduct' => 'Code of Conduct',
            'health_safety' => 'Health & Safety',
            'it_security' => 'IT Security',
            'leave' => 'Leave Policy',
            'attendance' => 'Attendance',
            'compensation' => 'Compensation & Benefits',
            'other' => 'Other',
        ];
    }
}