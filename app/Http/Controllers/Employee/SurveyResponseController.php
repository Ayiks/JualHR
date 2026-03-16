<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurveyResponseController extends Controller
{
    public function index()
    {
        $employee = Auth::user()->employee;

        $respondedIds = SurveyResponse::where('employee_id', $employee->id)
            ->pluck('survey_id');

        $available = Survey::available()
            ->whereNotIn('id', $respondedIds)
            ->withCount('questions')
            ->latest()
            ->get();

        $completed = Survey::whereIn('id', $respondedIds)
            ->latest()
            ->get();

        return view('employee.surveys.index', compact('available', 'completed'));
    }

    public function show(Survey $survey)
    {
        $employee = Auth::user()->employee;

        if (!$survey->isAvailable()) {
            return redirect()->route('employee.surveys.index')
                ->with('error', 'This survey is no longer available.');
        }

        if ($survey->hasResponded($employee->id)) {
            return redirect()->route('employee.surveys.index')
                ->with('info', 'You have already submitted a response for this survey.');
        }

        $survey->load(['questions' => fn($q) => $q->ordered()]);

        return view('employee.surveys.show', compact('survey'));
    }

    public function submit(Request $request, Survey $survey)
    {
        $employee = Auth::user()->employee;

        if (!$survey->isAvailable()) {
            return redirect()->route('employee.surveys.index')
                ->with('error', 'This survey is no longer available.');
        }

        if ($survey->hasResponded($employee->id)) {
            return redirect()->route('employee.surveys.index')
                ->with('error', 'You have already submitted a response for this survey.');
        }

        $survey->load(['questions' => fn($q) => $q->ordered()]);

        // Build validation rules from questions
        $rules = [];
        foreach ($survey->questions as $q) {
            $key = 'answers.' . $q->id;
            if ($q->is_required) {
                $rules[$key] = 'required';
                if ($q->question_type === 'rating') {
                    $rules[$key] .= '|integer|min:1|max:5';
                }
            } else {
                $rules[$key] = 'nullable';
            }
        }

        $request->validate($rules);

        SurveyResponse::create([
            'survey_id'    => $survey->id,
            'employee_id'  => $employee->id,
            'responses'    => $request->input('answers', []),
            'submitted_at' => now(),
        ]);

        return redirect()->route('employee.surveys.index')
            ->with('success', 'Thank you! Your survey response has been submitted.');
    }
}
