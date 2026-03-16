@extends('layouts.app')

@section('title', 'Surveys')
@section('header', 'Surveys')
@section('description', 'Complete surveys and share your feedback.')

@section('content')
<div class="space-y-6">

    <!-- Available Surveys -->
    <div>
        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Available for You</h2>
        @if($available->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            <p class="text-gray-500 text-sm">No surveys available right now.</p>
        </div>
        @else
        <div class="space-y-3">
            @foreach($available as $survey)
            <div class="bg-white rounded-xl shadow-sm border border-blue-100 p-5 hover:border-blue-300 transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Open</span>
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                {{ ucwords(str_replace('_', ' ', $survey->type)) }}
                            </span>
                            @if($survey->is_anonymous)
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Anonymous</span>
                            @endif
                        </div>
                        <h3 class="text-base font-semibold text-gray-900">{{ $survey->title }}</h3>
                        @if($survey->description)
                        <p class="text-sm text-gray-500 mt-0.5">{{ Str::limit($survey->description, 80) }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $survey->questions_count ?? '' }} {{ $survey->questions_count === 1 ? 'question' : 'questions' }}
                            @if($survey->end_date) · Closes {{ $survey->end_date->format('M d, Y') }} @endif
                        </p>
                    </div>
                    <a href="{{ route('employee.surveys.show', $survey) }}"
                       class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Take Survey
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Completed Surveys -->
    @if($completed->isNotEmpty())
    <div>
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Completed</h2>
        <div class="space-y-2">
            @foreach($completed as $survey)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-4">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">{{ $survey->title }}</p>
                    <p class="text-xs text-gray-400">{{ ucwords(str_replace('_', ' ', $survey->type)) }}</p>
                </div>
                <span class="text-xs text-green-600 font-medium">Submitted</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
