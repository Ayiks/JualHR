@extends('layouts.app')

@section('title', 'Complaint #' . $complaint->id)
@section('header', 'Complaint Details')
@section('description', 'Review and respond to employee complaint.')

@section('header-actions')
<a href="{{ route('admin.complaints.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
    ← Back to Complaints
</a>
@endsection

@section('content')
<div class="max-w-4xl space-y-6">

    <!-- Complaint Details -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex-1">
                    <h2 class="text-xl font-semibold text-gray-900">{{ $complaint->subject }}</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $complaint->getStatusBadgeClass() }}">
                            {{ ucwords(str_replace('_', ' ', $complaint->status)) }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $complaint->getPriorityBadgeClass() }}">
                            {{ ucfirst($complaint->priority) }} Priority
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                            {{ ucwords(str_replace('_', ' ', $complaint->category)) }}
                        </span>
                        @if($complaint->is_anonymous)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                            Anonymous
                        </span>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-wrap gap-2">
                    @if(!$complaint->is_resolved)
                    <form method="POST" action="{{ route('admin.complaints.resolve', $complaint) }}">
                        @csrf @method('PUT')
                        <button type="submit" class="px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition-colors">
                            Mark Resolved
                        </button>
                    </form>
                    @endif
                    @if($complaint->status !== 'closed')
                    <form method="POST" action="{{ route('admin.complaints.close', $complaint) }}">
                        @csrf @method('PUT')
                        <button type="submit" class="px-3 py-1.5 bg-gray-600 text-white text-xs font-medium rounded-lg hover:bg-gray-700 transition-colors">
                            Close
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Submitter -->
            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-3">
                @if($complaint->is_anonymous)
                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700 italic">Anonymous Employee</p>
                    <p class="text-xs text-gray-400">Identity hidden by request</p>
                </div>
                @else
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-semibold text-sm">
                    {{ $complaint->employee->initials }}
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $complaint->employee->full_name }}</p>
                    <p class="text-xs text-gray-500">{{ $complaint->employee->employee_number }} · {{ $complaint->employee->department?->name }}</p>
                </div>
                @endif
                <p class="ml-auto text-xs text-gray-400">{{ $complaint->created_at->format('M d, Y \a\t g:i A') }}</p>
            </div>

            <!-- Description -->
            <div class="mt-4">
                <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Description</h4>
                <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $complaint->description }}</p>
            </div>

            @if($complaint->attachment)
            <div class="mt-4">
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
    </div>

    <!-- Assignment -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Assignment</h3>
        @if($complaint->assignedTo)
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-semibold text-xs">
                {{ $complaint->assignedTo->initials }}
            </div>
            <div>
                <p class="text-sm font-medium text-gray-900">{{ $complaint->assignedTo->full_name }}</p>
                <p class="text-xs text-gray-500">Currently assigned</p>
            </div>
        </div>
        @endif
        <form method="POST" action="{{ route('admin.complaints.assign', $complaint) }}" class="flex items-end gap-3">
            @csrf @method('PUT')
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">
                    {{ $complaint->assignedTo ? 'Reassign to' : 'Assign to' }}
                </label>
                <select name="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select handler...</option>
                    @foreach($handlers as $handler)
                    <option value="{{ $handler->id }}" @selected($complaint->assigned_to == $handler->id)>{{ $handler->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                Assign
            </button>
        </form>
    </div>

    <!-- Response Thread -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">
            Response Thread
            <span class="ml-1.5 text-xs font-normal text-gray-400">({{ $complaint->responses->count() }} message{{ $complaint->responses->count() !== 1 ? 's' : '' }})</span>
        </h3>

        @forelse($complaint->responses as $response)
        <div class="flex gap-3 mb-4 last:mb-0">
            <div class="w-9 h-9 flex-shrink-0 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-semibold text-xs">
                {{ $response->respondedBy?->initials ?? '?' }}
            </div>
            <div class="flex-1 bg-gray-50 rounded-xl px-4 py-3">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-sm font-medium text-gray-900">{{ $response->respondedBy?->full_name ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-400">{{ $response->created_at->format('M d, Y g:i A') }}</p>
                </div>
                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $response->response }}</p>
                @if($response->attachment)
                <a href="{{ Storage::url($response->attachment) }}" target="_blank" class="inline-flex items-center gap-1 mt-2 text-xs text-blue-600 hover:underline">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                    Attachment
                </a>
                @endif
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-400 italic">No responses yet.</p>
        @endforelse

        @if($complaint->status !== 'closed')
        <form method="POST" action="{{ route('admin.complaints.respond', $complaint) }}" enctype="multipart/form-data" class="mt-6 pt-5 border-t border-gray-100">
            @csrf
            <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-2">Add Response</label>
            <textarea name="response" rows="4"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 @error('response') border-red-300 @enderror"
                      placeholder="Type your response..." required></textarea>
            @error('response')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            <div class="flex items-center justify-between mt-3">
                <label class="inline-flex items-center gap-2 text-sm text-gray-600 cursor-pointer hover:text-gray-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                    Attach file
                    <input type="file" name="attachment" class="sr-only" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                </label>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Send Response
                </button>
            </div>
        </form>
        @endif
    </div>

</div>
@endsection
