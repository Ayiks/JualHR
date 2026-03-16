@extends('layouts.app')

@section('title', 'Survey Responses')
@section('header', $survey->title)
@section('description', 'View all employee responses to this survey.')

@section('header-actions')
<a href="{{ route('admin.surveys.export', $survey) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
    </svg>
    Export CSV
</a>
<a href="{{ route('admin.surveys.show', $survey) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
    ← Survey
</a>
@endsection

@section('content')
<div class="space-y-6">

    @if($responses->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <p class="text-gray-500">No responses yet.</p>
    </div>
    @else

    @foreach($responses as $response)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($survey->is_anonymous)
                <div class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-600 italic">Anonymous</p>
                @else
                <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-semibold text-xs">
                    {{ $response->employee?->initials ?? '?' }}
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $response->employee?->full_name ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-400">{{ $response->employee?->employee_number }}</p>
                </div>
                @endif
            </div>
            <p class="text-xs text-gray-400">{{ ($response->submitted_at ?? $response->created_at)->format('M d, Y g:i A') }}</p>
        </div>

        <div class="p-6 space-y-4">
            @foreach($survey->questions as $q)
            @php $answer = $response->responses[$q->id] ?? null; @endphp
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">{{ $q->question }}</p>
                @if($answer !== null && $answer !== '' && $answer !== [])
                    @if(is_array($answer))
                    <p class="text-sm text-gray-900">{{ implode(', ', $answer) }}</p>
                    @elseif($q->question_type === 'rating')
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= $answer ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        @endfor
                        <span class="text-xs text-gray-500 ml-1">{{ $answer }}/5</span>
                    </div>
                    @else
                    <p class="text-sm text-gray-900">{{ $answer }}</p>
                    @endif
                @else
                <p class="text-sm text-gray-400 italic">No answer</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    @if($responses->hasPages())
    <div>{{ $responses->links() }}</div>
    @endif

    @endif
</div>
@endsection
