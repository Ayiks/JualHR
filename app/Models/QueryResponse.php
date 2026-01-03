<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class QueryResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'query_id',
        'employee_id',
        'response',
        'document_path',
    ];

    // Relationships
    public function queryRecord(): BelongsTo
    {
        return $this->belongsTo(Query::class, 'query_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}