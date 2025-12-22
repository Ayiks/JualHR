{{-- resources/views/employee/leaves/show.blade.php --}}

@extends('layouts.app')

@section('title', 'Leave Details')
@section('header', 'Leave Request Details')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Status Banner -->
        <div class="px-6 py-4 {{ $leave->status === 'pending' ? 'bg-yellow-50' : ($leave->status === 'approved' ? 'bg-green-50' : 'bg-red-50') }}">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $leave->getStatusBadgeClass() }}">
                    {{ ucfirst($leave->status) }}
                </span>
                @if($leave->status === 'approved')
                    <p class="text-sm text-green-800">
                        Approved by {{ $leave->approvedBy->full_name }}
                    </p>
                @elseif($leave->status === 'rejected')
                    <p class="text-sm text-red-800">
                        Rejected by {{ $leave->approvedBy->full_name }}
                    </p>
                @endif
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500 mb-1">Leave Type</dt>
                    <dd class="text-sm text-gray-900">{{ $leave->leaveType->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 mb-1">Duration</dt>
                    <dd class="text-sm text-gray-900">{{ $leave->number_of_days }} {{ Str::plural('day', $leave->number_of_days) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 mb-1">Start Date</dt>
                    <dd class="text-sm text-gray-900">{{ $leave->start_date->format('F d, Y') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 mb-1">End Date</dt>
                    <dd class="text-sm text-gray-900">{{ $leave->end_date->format('F d, Y') }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 mb-1">Reason</dt>
                    <dd class="text-sm text-gray-900">{{ $leave->reason }}</dd>
                </div>

                @if($leave->status === 'rejected' && $leave->rejection_reason)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Rejection Reason</dt>
                        <dd class="text-sm text-red-600">{{ $leave->rejection_reason }}</dd>
                    </div>
                @endif

                @if($leave->attachment)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Attachment</dt>
                        <dd>
                            <a href="{{ Storage::url($leave->attachment) }}" target="_blank"
                               class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                                View Attachment
                            </a>
                        </dd>
                    </div>
                @endif
            </div>

            @if($leave->status === 'pending')
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <form action="{{ route('employee.leaves.cancel', $leave) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to cancel this leave request?');">
                        @csrf
                        @method('PUT')
                        <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-red-700">
                            Cancel Request
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection