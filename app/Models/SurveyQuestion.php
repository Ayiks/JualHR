<?php
// app/Models/SurveyQuestion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'question',
        'question_type',
        'options',
        'is_required',
        'order_number',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'order_number' => 'integer',
    ];

    // Relationships
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function answers()
    {
        return $this->hasMany(SurveyAnswer::class);
    }

    // Scopes
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_number');
    }
}