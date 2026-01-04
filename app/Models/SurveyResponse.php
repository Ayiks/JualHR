<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'employee_id',
        'responses',
        'submitted_at',
    ];

    protected $casts = [
        'responses' => 'array',
        'submitted_at' => 'datetime',
    ];

    // Relationships
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Accessors
    public function getIsSubmittedAttribute()
    {
        return !is_null($this->submitted_at);
    }

    // Helper Methods
    public function getAnswer($questionId)
    {
        return $this->responses[$questionId] ?? null;
    }

    public function setAnswer($questionId, $answer)
    {
        $responses = $this->responses ?? [];
        $responses[$questionId] = $answer;
        $this->responses = $responses;
        $this->save();
    }
}