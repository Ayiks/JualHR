{{-- ============================================================================ --}}

{{-- resources/views/employee/leaves/index.blade.php --}}

@extends('layouts.app')

@section('title', 'My Leaves')
@section('header', 'My Leave Requests')
@section('description', 'View and manage your leave history')

@section('header-actions')
    <a href="{{ route('employee.leaves.create') }}" 
        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Apply for Leave
    </a>
@endsection

@section('content')
<div class="space-y-4">
    @forelse($leaves as $leave)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $leave->leaveType->name }}</h3>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $leave->getStatusBadgeClass() }}">
                            {{ ucfirst($leave->status) }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                        <div>
                            <p class="text-xs text-gray-500">Duration</p>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d, Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Days</p>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $leave->number_of_days }} {{ Str::plural('day', $leave->number_of_days) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Applied</p>
                            <p class="text-sm font-medium text-gray-900">{{ $leave->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <p class="text-sm text-gray-600 line-clamp-2">{{ $leave->reason }}</p>

                    @if($leave->status === 'rejected' && $leave->rejection_reason)
                        <div class="mt-3 bg-red-50 border border-red-200 rounded-lg p-3">
                            <p class="text-xs font-medium text-red-800 mb-1">Rejection Reason:</p>
                            <p class="text-sm text-red-700">{{ $leave->rejection_reason }}</p>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-2 ml-4">
                    <a href="{{ route('employee.leaves.show', $leave) }}" 
                        class="inline-flex items-center justify-center p-2 bg-gray-100 border border-gray-200 rounded-lg text-gray-700 hover:bg-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </a>
                    
                    @if($leave->status === 'pending')
                        <form action="{{ route('employee.leaves.cancel', $leave) }}" method="POST" class="inline"
                            onsubmit="return confirm('Are you sure you want to cancel this leave request?');">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="inline-flex items-center justify-center p-2 bg-red-50 border border-red-200 rounded-lg text-red-600 hover:bg-red-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No leave requests</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by applying for a leave.</p>
            <div class="mt-6">
                <a href="{{ route('employee.leaves.create') }}" 
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700">
                    Apply for Leave
                </a>
            </div>
        </div>
    @endforelse

    @if($leaves->hasPages())
        <div class="mt-6">
            {{ $leaves->links() }}
        </div>
    @endif
</div>
@endsection