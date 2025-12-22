{{-- ============================================================================ --}}

{{-- resources/views/admin/leaves/show.blade.php --}}

@extends('layouts.app')

@section('title', 'Leave Request')
@section('header', 'Leave Request Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Status Banner -->
        <div class="px-6 py-4 {{ $leave->status === 'pending' ? 'bg-yellow-50 border-b border-yellow-100' : ($leave->status === 'approved' ? 'bg-green-50 border-b border-green-100' : 'bg-red-50 border-b border-red-100') }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $leave->getStatusBadgeClass() }}">
                        {{ ucfirst($leave->status) }}
                    </span>
                    @if($leave->status === 'approved')
                        <p class="text-sm text-green-800">
                            Approved by {{ $leave->approvedBy->full_name }} on {{ $leave->approved_at->format('M d, Y') }}
                        </p>
                    @elseif($leave->status === 'rejected')
                        <p class="text-sm text-red-800">
                            Rejected by {{ $leave->approvedBy->full_name }} on {{ $leave->approved_at->format('M d, Y') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Employee Info -->
            <div class="flex items-center gap-4 pb-6 border-b border-gray-200">
                @if($leave->employee->profile_photo)
                    <img class="h-16 w-16 rounded-full" src="{{ Storage::url($leave->employee->profile_photo) }}" alt="">
                @else
                    <div class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-xl">
                        {{ $leave->employee->initials }}
                    </div>
                @endif
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $leave->employee->full_name }}</h3>
                    <p class="text-sm text-gray-600">{{ $leave->employee->job_title }}</p>
                    <p class="text-sm text-gray-500">{{ $leave->employee->department?->name }}</p>
                </div>
            </div>

            <!-- Leave Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
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

            <!-- Actions -->
            @if($leave->status === 'pending')
                <div class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-200" x-data="{ showRejectModal: false }">
                    <form action="{{ route('admin.leaves.approve', $leave) }}" method="POST" class="inline">
                        @csrf
                        @method('PUT')
                        <button type="submit" 
                            onclick="return confirm('Are you sure you want to approve this leave request?')"
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-green-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Approve Leave
                        </button>
                    </form>

                    <button @click="showRejectModal = true"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-red-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Reject Leave
                    </button>

                    <!-- Reject Modal -->
                    <div x-show="showRejectModal" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 z-50 overflow-y-auto" 
                         style="display: none;">
                        <div class="flex items-center justify-center min-h-screen px-4">
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showRejectModal = false"></div>
                            
                            <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Reject Leave Request</h3>
                                
                                <form action="{{ route('admin.leaves.reject', $leave) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="mb-4">
                                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                                            Reason for Rejection <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="rejection_reason" id="rejection_reason" rows="4" required
                                                  class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                                  placeholder="Please provide a reason for rejecting this leave request..."></textarea>
                                    </div>

                                    <div class="flex items-center justify-end gap-3">
                                        <button type="button" @click="showRejectModal = false"
                                            class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            Cancel
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 bg-red-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-red-700">
                                            Reject Leave
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection