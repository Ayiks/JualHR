<?php
// app/Models/PolicyVersion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_id',
        'version',
        'changes',
        'document_path',
        'created_by',
    ];

    // Relationships
    public function policy()
    {
        return $this->belongsTo(Policy::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}