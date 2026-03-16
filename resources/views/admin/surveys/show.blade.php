@extends('layouts.app')

@section('title', $survey->title)
@section('header', $survey->title)
@section('description', 'Survey details and management.')

@section('header-actions')
<a href="{{ route('admin.surveys.responses', $survey) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
    View Responses ({{ $responseCount }})
</a>
<a href="{{ route('admin.surveys.edit', $survey) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
    Edit
</a>
@endsection

@section('content')
<div class="max-w-3xl space-y-6">

    <!-- Meta -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-wrap items-center gap-2 mb-3">
            @if($survey->getRawOriginal('is_active'))
                @if($survey->isAvailable())
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                @elseif($survey->start_date?->isFuture())
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Upcoming</span>
                @else
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Closed</span>
                @endif
            @else
            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Draft</span>
            @endif
            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                {{ ucwords(str_replace('_', ' ', $survey->type)) }}
            </span>
            @if($survey->is_anonymous)
            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Anonymous</span>
            @endif
        </div>

        @if($survey->description)
        <p class="text-sm text-gray-600 mb-4">{{ $survey->description }}</p>
        @endif

        <dl class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
            <div>
                <dt class="text-xs text-gray-400 uppercase tracking-wider">Responses</dt>
                <dd class="font-semibold text-gray-900 mt-0.5">{{ $responseCount }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 uppercase tracking-wider">Questions</dt>
                <dd class="font-semibold text-gray-900 mt-0.5">{{ $survey->questions->count() }}</dd>
            </div>
            @if($survey->start_date)
            <div>
                <dt class="text-xs text-gray-400 uppercase tracking-wider">Start Date</dt>
                <dd class="font-semibold text-gray-900 mt-0.5">{{ $survey->start_date->format('M d, Y') }}</dd>
            </div>
            @endif
            @if($survey->end_date)
            <div>
                <dt class="text-xs text-gray-400 uppercase tracking-wider">End Date</dt>
                <dd class="font-semibold text-gray-900 mt-0.5">{{ $survey->end_date->format('M d, Y') }}</dd>
            </div>
            @endif
            <div>
                <dt class="text-xs text-gray-400 uppercase tracking-wider">Created By</dt>
                <dd class="font-semibold text-gray-900 mt-0.5">{{ $survey->creator?->name ?? '—' }}</dd>
            </div>
        </dl>
    </div>

    <!-- Questions Preview -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Questions ({{ $survey->questions->count() }})</h3>
        <ol class="space-y-4">
            @foreach($survey->questions as $q)
            <li class="flex gap-3">
                <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold">{{ $loop->iteration }}</span>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">{{ $q->question }}
                        @if($q->is_required)<span class="text-red-400 ml-1">*</span>@endif
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ match($q->question_type) {
                            'text' => 'Text answer',
                            'multiple_choice' => 'Multiple choice (single)',
                            'checkbox' => 'Multiple choice (multiple)',
                            'rating' => 'Rating 1–5',
                            default => $q->question_type,
                        } }}
                    </p>
                    @if($q->options)
                    <ul class="mt-1 space-y-0.5">
                        @foreach($q->options as $opt)
                        <li class="text-xs text-gray-500 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 bg-gray-300 rounded-full"></span>
                            {{ $opt }}
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </li>
            @endforeach
        </ol>
    </div>

    <!-- Danger zone -->
    <div class="bg-white rounded-xl shadow-sm border border-red-100 p-5">
        <h3 class="text-sm font-semibold text-red-700 mb-2">Delete Survey</h3>
        <p class="text-xs text-gray-500 mb-3">This permanently deletes the survey and all responses.</p>
        <form method="POST" action="{{ route('admin.surveys.destroy', $survey) }}"
              onsubmit="return confirm('Delete this survey and all {{ $responseCount }} responses? This cannot be undone.')">
            @csrf @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition-colors">
                Delete Survey
            </button>
        </form>
    </div>

</div>
@endsection
