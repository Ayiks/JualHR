<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Query extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'employee_id',
        'issued_by',
        'type',
        'subject',
        'description',
        'action_required',
        'severity',
        'status',
        'issued_date',
        'response_deadline',
        'responded_at',
        'closed_at',
        'closed_by',
        'closure_notes',
        'document_path',
    ];

    protected $casts = [
        'issued_date' => 'date',
        'response_deadline' => 'date',
        'responded_at' => 'date',
        'closed_at' => 'date',
    ];

    // ADD MISSING SCOPE - This was the cause of the error!
    public function scopePending($query)
    {
        return $query->where('status', 'open');
    }

    // Scopes - Keep your existing ones
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
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
        return $query->where('status', 'open')
                     ->whereNotNull('response_deadline')
                     ->whereDate('response_deadline', '<', today());
    }

    // Relationships - Keep your existing ones
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(QueryResponse::class);
    }

    // Helper methods - Keep your existing ones
    public static function generateReferenceNumber(): string
    {
        $prefix = 'QRY';
        $date = now()->format('Ym');
        $lastQuery = self::where('reference_number', 'LIKE', "{$prefix}{$date}%")
                      ->latest('id')
                      ->first();

        $number = $lastQuery ? (int) substr($lastQuery->reference_number, -4) + 1 : 1;
        $sequence = str_pad($number, 4, '0', STR_PAD_LEFT);

        return $prefix . $date . $sequence;
    }

    public function close(string $notes, int $userId): void
    {
        $this->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closed_by' => $userId,
            'closure_notes' => $notes,
        ]);
    }

    public function isOverdue(): bool
    {
        return $this->status === 'open' && 
               $this->response_deadline && 
               $this->response_deadline->isPast();
    }

    public function getTypeBadgeClass(): string
    {
        return match($this->type) {
            'verbal_warning' => 'bg-purple-100 text-purple-800',
            'written_warning' => 'bg-red-100 text-red-800',
            'final_warning' => 'bg-red-200 text-red-900',
            'query' => 'bg-blue-100 text-blue-800',
            'suspension' => 'bg-orange-100 text-orange-800',
            'other' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'open' => 'bg-yellow-100 text-yellow-800',
            'responded' => 'bg-blue-100 text-blue-800',
            'closed' => 'bg-green-100 text-green-800',
            'escalated' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getSeverityBadgeClass(): string
    {
        return match($this->severity) {
            'low' => 'bg-green-100 text-green-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'high' => 'bg-orange-100 text-orange-800',
            'critical' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'verbal_warning' => 'Verbal Warning',
            'written_warning' => 'Written Warning',
            'final_warning' => 'Final Warning',
            'query' => 'Query',
            'suspension' => 'Suspension',
            'other' => 'Other',
            default => ucfirst($this->type),
        };
    }

    public static function getTypes(): array
    {
        return [
            'verbal_warning' => 'Verbal Warning',
            'written_warning' => 'Written Warning',
            'final_warning' => 'Final Warning',
            'query' => 'Query',
            'suspension' => 'Suspension',
            'other' => 'Other',
        ];
    }

    public static function getSeverities(): array
    {
        return [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'critical' => 'Critical',
        ];
    }
}