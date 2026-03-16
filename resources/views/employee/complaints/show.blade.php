@extends('layouts.app')

@section('title', 'Complaint Details')
@section('header', 'Complaint Details')
@section('description', 'View your complaint and responses from HR.')

@section('header-actions')
<a href="{{ route('employee.complaints.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
    ← My Complaints
</a>
@endsection

@section('content')
<div class="max-w-3xl space-y-6">

    <!-- Complaint Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-wrap items-center gap-2 mb-3">
            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $complaint->getStatusBadgeClass() }}">
                {{ ucwords(str_replace('_', ' ', $complaint->status)) }}
            </span>
            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $complaint->getPriorityBadgeClass() }}">
                {{ ucfirst($complaint->priority) }} Priority
            </span>
            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                {{ ucwords(str_replace('_', ' ', $complaint->category)) }}
            </span>
            @if($complaint->is_anonymous)
            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Anonymous</span>
            @endif
        </div>

        <h2 class="text-xl font-semibold text-gray-900">{{ $complaint->subject }}</h2>
        <p class="text-xs text-gray-400 mt-1">Submitted {{ $complaint->created_at->format('F d, Y \a\t g:i A') }}</p>

        <div class="mt-4">
            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Your Description</h4>
            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $complaint->description }}</p>
        </div>

        @if($complaint->attachment)
        <div class="mt-3">
            <a href="{{ Storage::url($complaint->attachment) }}" target="_blank"
               class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                </svg>
                View Attachment
            </a>
        </div>
        @endif
    </div>

    <!-- Response Thread -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">
            Responses
            <span class="ml-1 text-xs font-normal text-gray-400">({{ $complaint->responses->count() }})</span>
        </h3>

        @forelse($complaint->responses as $response)
        @php
            $isMe = $response->responded_by === Auth::user()->employee?->id;
        @endphp
        <div class="flex gap-3 mb-4 last:mb-0 {{ $isMe ? 'flex-row-reverse' : '' }}">
            <div class="w-9 h-9 flex-shrink-0 rounded-full {{ $isMe ? 'bg-blue-600' : 'bg-gray-200' }} flex items-center justify-center {{ $isMe ? 'text-white' : 'text-gray-600' }} font-semibold text-xs">
                {{ $response->respondedBy?->initials ?? 'HR' }}
            </div>
            <div class="flex-1 {{ $isMe ? 'bg-blue-50' : 'bg-gray-50' }} rounded-xl px-4 py-3 max-w-lg {{ $isMe ? 'ml-auto' : '' }}">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-xs font-medium {{ $isMe ? 'text-blue-700' : 'text-gray-700' }}">
                        {{ $isMe ? 'You' : ($response->respondedBy?->full_name ?? 'HR') }}
                    </p>
                    <p class="text-xs text-gray-400">{{ $response->created_at->format('M d, g:i A') }}</p>
                </div>
                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $response->response }}</p>
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-400 italic">No responses yet. HR will respond shortly.</p>
        @endforelse

        @if($complaint->status !== 'closed')
        <form method="POST" action="{{ route('employee.complaints.response', $complaint) }}" class="mt-5 pt-5 border-t border-gray-100">
            @csrf
            <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-2">Add a Follow-up</label>
            <textarea name="response" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 @error('response') border-red-300 @enderror"
                      placeholder="Add more information or follow up..." required></textarea>
            @error('response')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            <div class="flex justify-end mt-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Send
                </button>
            </div>
        </form>
        @else
        <div class="mt-4 pt-4 border-t border-gray-100 text-center">
            <p class="text-sm text-gray-400">This complaint has been closed.</p>
        </div>
        @endif
    </div>

</div>
@endsection
