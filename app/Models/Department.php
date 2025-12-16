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
        'description',
    ];

    // Relationships
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    // Accessors & Mutators
    public function getEmployeeCountAttribute()
    {
        return $this->employees()->count();
    }
}