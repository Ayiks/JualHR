<?php

// app/Models/ComplaintResponse.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'responded_by',
        'response',
        'attachment',
    ];

    // Relationships
    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function respondedBy()
    {
        return $this->belongsTo(Employee::class, 'responded_by');
    }

    // Boot method to update complaint status
    protected static function booted()
    {
        static::created(function ($response) {
            if ($response->complaint->status === 'open') {
                $response->complaint->update(['status' => 'in_progress']);
            }
        });
    }
}