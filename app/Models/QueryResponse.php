<?php
// app/Models/QueryResponse.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueryResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'query_id',
        'responded_by',
        'response',
        'attachment',
    ];

    // Relationships
    public function query()
    {
        return $this->belongsTo(Query::class);
    }

    public function respondedBy()
    {
        return $this->belongsTo(Employee::class, 'responded_by');
    }

    // Boot method to update query status
    protected static function booted()
    {
        static::created(function ($response) {
            $response->query->update(['status' => 'responded']);
        });
    }
}