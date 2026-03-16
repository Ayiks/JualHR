@extends('layouts.app')

@section('title', 'My Complaints')
@section('header', 'My Complaints')
@section('description', 'View and manage complaints you have submitted.')

@section('header-actions')
<a href="{{ route('employee.complaints.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
    </svg>
    New Complaint
</a>
@endsection

@section('content')
<div class="space-y-4">

    @forelse($complaints as $complaint)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-blue-200 transition-colors">
        <div class="flex flex-col sm:flex-row sm:items-start gap-3">
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $complaint->getStatusBadgeClass() }}">
                        {{ ucwords(str_replace('_', ' ', $complaint->status)) }}
                    </span>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $complaint->getPriorityBadgeClass() }}">
                        {{ ucfirst($complaint->priority) }}
                    </span>
                    @if($complaint->is_anonymous)
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Anonymous</span>
                    @endif
                </div>
                <h3 class="text-base font-semibold text-gray-900">{{ $complaint->subject }}</h3>
                <p class="text-sm text-gray-500 mt-1">
                    {{ ucwords(str_replace('_', ' ', $complaint->category)) }} ·
                    {{ $complaint->responses->count() }} response{{ $complaint->responses->count() !== 1 ? 's' : '' }} ·
                    {{ $complaint->created_at->format('M d, Y') }}
                </p>
            </div>
            <a href="{{ route('employee.complaints.show', $complaint) }}"
               class="flex-shrink-0 px-4 py-2 text-sm text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition-colors font-medium">
                View
            </a>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
        </svg>
        <p class="text-gray-500 font-medium">No complaints submitted yet</p>
        <p class="text-sm text-gray-400 mt-1">Use the button above to submit a new complaint.</p>
    </div>
    @endforelse

    @if($complaints->hasPages())
    <div>{{ $complaints->links() }}</div>
    @endif

</div>
@endsection
