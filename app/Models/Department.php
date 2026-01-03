<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'head_of_department_id',
    ];

    /**
     * Relationships
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function head()
    {
        return $this->belongsTo(Employee::class, 'head_of_department_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->whereHas('employees', function ($q) {
            $q->where('employment_status', 'active');
        });
    }

    /**
     * Accessors
     */
    public function getEmployeeCountAttribute()
    {
        return $this->employees()->count();
    }

    public function getActiveEmployeeCountAttribute()
    {
        return $this->employees()->where('employment_status', 'active')->count();
    }

    public function getHasHeadAttribute()
    {
        return !is_null($this->head_of_department_id);
    }
}