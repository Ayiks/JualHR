@extends('layouts.app')

@section('title', 'Surveys')
@section('header', 'Survey Management')
@section('description', 'Create and manage employee surveys and feedback.')

@section('header-actions')
<a href="{{ route('admin.surveys.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
    </svg>
    New Survey
</a>
@endsection

@section('content')
<div class="space-y-4">

    @forelse($surveys as $survey)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex flex-col sm:flex-row sm:items-start gap-4">
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    @if($survey->getRawOriginal('is_active'))
                        @if($survey->isAvailable())
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                        @elseif($survey->start_date && $survey->start_date->isFuture())
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Upcoming</span>
                        @else
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Closed</span>
                        @endif
                    @else
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Draft</span>
                    @endif
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
                <p class="text-xs text-gray-400 mt-1.5">
                    {{ $survey->responses_count }} response{{ $survey->responses_count !== 1 ? 's' : '' }}
                    @if($survey->start_date) · From {{ $survey->start_date->format('M d, Y') }} @endif
                    @if($survey->end_date) · Until {{ $survey->end_date->format('M d, Y') }} @endif
                </p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="{{ route('admin.surveys.responses', $survey) }}" class="px-3 py-1.5 text-xs text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                    Responses
                </a>
                <a href="{{ route('admin.surveys.show', $survey) }}" class="px-3 py-1.5 text-xs text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition-colors font-medium">
                    View
                </a>
                <a href="{{ route('admin.surveys.edit', $survey) }}" class="px-3 py-1.5 text-xs text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                    Edit
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
        </svg>
        <p class="text-gray-500 font-medium">No surveys yet</p>
        <p class="text-sm text-gray-400 mt-1">Create a survey to gather employee feedback.</p>
    </div>
    @endforelse

    @if($surveys->hasPages())
    <div>{{ $surveys->links() }}</div>
    @endif

</div>
@endsection
