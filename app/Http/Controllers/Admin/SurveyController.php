<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurveyController extends Controller
{
    public function index()
    {
        $surveys = Survey::withCount('responses')
            ->with('creator')
            ->latest()
            ->paginate(15);

        return view('admin.surveys.index', compact('surveys'));
    }

    public function create()
    {
        return view('admin.surveys.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string|max:2000',
            'type'         => 'required|in:exit_interview,general_review,satisfaction,other',
            'is_active'    => 'nullable|boolean',
            'is_anonymous' => 'nullable|boolean',
            'start_date'   => 'nullable|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'questions'              => 'required|array|min:1',
            'questions.*.question'   => 'required|string|max:500',
            'questions.*.type'       => 'required|in:text,multiple_choice,rating,checkbox',
            'questions.*.required'   => 'nullable|boolean',
            'questions.*.options'    => 'nullable|array',
            'questions.*.options.*'  => 'nullable|string|max:200',
        ]);

        $survey = Survey::create([
            'title'        => $validated['title'],
            'description'  => $validated['description'] ?? null,
            'type'         => $validated['type'],
            'is_active'    => $request->boolean('is_active'),
            'is_anonymous' => $request->boolean('is_anonymous'),
            'start_date'   => $validated['start_date'] ?? null,
            'end_date'     => $validated['end_date'] ?? null,
            'created_by'   => Auth::id(),
        ]);

        foreach ($validated['questions'] as $index => $q) {
            $options = null;
            if (in_array($q['type'], ['multiple_choice', 'checkbox'])) {
                $options = array_values(array_filter($q['options'] ?? []));
            }

            SurveyQuestion::create([
                'survey_id'     => $survey->id,
                'question'      => $q['question'],
                'question_type' => $q['type'],
                'options'       => $options,
                'is_required'   => !empty($q['required']),
                'order_number'  => $index + 1,
            ]);
        }

        return redirect()->route('admin.surveys.show', $survey)
            ->with('success', 'Survey created successfully.');
    }

    public function show(Survey $survey)
    {
        $survey->load(['questions' => fn($q) => $q->ordered(), 'creator']);
        $responseCount = $survey->responses()->count();

        return view('admin.surveys.show', compact('survey', 'responseCount'));
    }

    public function edit(Survey $survey)
    {
        $survey->load(['questions' => fn($q) => $q->ordered()]);

        return view('admin.surveys.edit', compact('survey'));
    }

    public function update(Request $request, Survey $survey)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'type'        => 'required|in:exit_interview,general_review,satisfaction,other',
            'is_active'   => 'nullable|boolean',
            'is_anonymous'=> 'nullable|boolean',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
        ]);

        $survey->update([
            'title'        => $validated['title'],
            'description'  => $validated['description'] ?? null,
            'type'         => $validated['type'],
            'is_active'    => $request->boolean('is_active'),
            'is_anonymous' => $request->boolean('is_anonymous'),
            'start_date'   => $validated['start_date'] ?? null,
            'end_date'     => $validated['end_date'] ?? null,
        ]);

        return redirect()->route('admin.surveys.show', $survey)
            ->with('success', 'Survey updated successfully.');
    }

    public function destroy(Survey $survey)
    {
        $survey->delete();

        return redirect()->route('admin.surveys.index')
            ->with('success', 'Survey deleted.');
    }

    public function responses(Survey $survey)
    {
        $survey->load(['questions' => fn($q) => $q->ordered()]);
        $responses = $survey->responses()->with('employee')->latest()->paginate(20);

        return view('admin.surveys.responses', compact('survey', 'responses'));
    }

    public function export(Survey $survey)
    {
        $survey->load(['questions' => fn($q) => $q->ordered()]);
        $responses = $survey->responses()->with('employee')->get();

        $filename = 'survey-' . $survey->id . '-responses.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($survey, $responses) {
            $handle = fopen('php://output', 'w');

            // Header row
            $cols = ['Response ID', 'Employee', 'Submitted At'];
            foreach ($survey->questions as $q) {
                $cols[] = $q->question;
            }
            fputcsv($handle, $cols);

            // Data rows
            foreach ($responses as $response) {
                $row = [
                    $response->id,
                    $survey->is_anonymous ? 'Anonymous' : ($response->employee?->full_name ?? '—'),
                    $response->submitted_at?->format('Y-m-d H:i') ?? $response->created_at->format('Y-m-d H:i'),
                ];
                foreach ($survey->questions as $q) {
                    $answer = $response->responses[$q->id] ?? '';
                    $row[] = is_array($answer) ? implode(', ', $answer) : $answer;
                }
                fputcsv($handle, $row);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
