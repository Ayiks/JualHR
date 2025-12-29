<?php
// app/Models/PolicyAcknowledgment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyAcknowledgment extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_id',
        'employee_id',
        'acknowledged_at',
        'ip_address',
        'notes',
    ];

    protected $casts = [
        'acknowledged_at' => 'datetime',
    ];

    // Relationships
    public function policy()
    {
        return $this->belongsTo(Policy::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}