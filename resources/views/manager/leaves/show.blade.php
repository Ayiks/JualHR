@extends('layouts.app')

@section('title', 'Leave Request')
@section('header', 'Leave Request Details')
@section('description', 'View team member leave details.')

@section('header-actions')
<a href="{{ route('manager.leaves.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
    ← Team Leaves
</a>
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-lg">
                {{ $leave->employee->initials }}
            </div>
            <div>
                <p class="font-semibold text-gray-900">{{ $leave->employee->full_name }}</p>
                <p class="text-sm text-gray-500">{{ $leave->employee->department?->name }}</p>
            </div>
            @php
                $badge = match($leave->status) {
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'approved' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    'cancelled' => 'bg-gray-100 text-gray-600',
                    default => 'bg-gray-100 text-gray-600',
                };
            @endphp
            <span class="ml-auto inline-flex px-3 py-1 rounded-full text-sm font-medium {{ $badge }}">
                {{ ucfirst($leave->status) }}
            </span>
        </div>

        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $leave->leaveType->name }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $leave->start_date->format('M d') }} – {{ $leave->end_date->format('M d, Y') }}</dd>
            </div>
            @if($leave->reason)
            <div class="col-span-2">
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</dt>
                <dd class="mt-1 text-gray-700">{{ $leave->reason }}</dd>
            </div>
            @endif
            @if($leave->rejection_reason)
            <div class="col-span-2">
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Rejection Reason</dt>
                <dd class="mt-1 text-red-600">{{ $leave->rejection_reason }}</dd>
            </div>
            @endif
            @if($leave->approvedBy)
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Approved By</dt>
                <dd class="mt-1 text-gray-700">{{ $leave->approvedBy->full_name }}</dd>
            </div>
            @endif
        </dl>

        <p class="text-xs text-gray-400 mt-6 pt-4 border-t border-gray-100">
            Submitted {{ $leave->created_at->format('M d, Y \a\t g:i A') }}
        </p>
    </div>

    <p class="text-xs text-center text-gray-400 mt-4">
        Leave approvals are handled by HR. Contact HR to discuss this request.
    </p>
</div>
@endsection
