<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeChild extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'name',
        'date_of_birth',
        'sex',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Accessors
    public function getAgeAttribute()
    {
        return $this->date_of_birth?->age;
    }
}