<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EmployeeEducation extends Model
{
    use HasFactory;

    protected $table = 'employee_education';

    protected $fillable = [
        'employee_id',
        'institution_name',
        'program',
        'certificate_obtained',
        'certificates',
    ];

    protected $casts = [
        'certificates' => 'array',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Helper Methods
    public function getCertificatesUrlsAttribute()
    {
        if (!$this->certificates) {
            return [];
        }

        return collect($this->certificates)->map(function ($path) {
            return Storage::url($path);
        })->toArray();
    }

    // Delete certificates when education record is deleted
    protected static function booted()
    {
        static::deleting(function ($education) {
            if ($education->certificates) {
                foreach ($education->certificates as $certificate) {
                    Storage::disk('private')->delete($certificate);
                }
            }
        });
    }
}