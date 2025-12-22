@extends('layouts.app')

@section('title', 'Leave Balance')
@section('header', 'My Leave Balance')
@section('description', 'View your leave balances and history for ' . $year)

@section('content')
<div class="space-y-6">
    <!-- Leave Balances -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @forelse($leaveBalances as $balance)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-medium text-gray-700">{{ $balance->leaveType->name }}</h3>
                    <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>

                <div class="space-y-3">
                    <!-- Total -->
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Total</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $balance->total_days }} days</span>
                    </div>

                    <!-- Used -->
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Used</span>
                        <span class="text-sm font-semibold text-orange-600">{{ $balance->used_days }} days</span>
                    </div>

                    <!-- Remaining -->
                    <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                        <span class="text-xs font-medium text-gray-700">Remaining</span>
                        <span class="text-lg font-bold text-green-600">{{ $balance->remaining_days }} days</span>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mt-4">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" 
                             style="width: {{ $balance->total_days > 0 ? ($balance->used_days / $balance->total_days * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-4 bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No leave balance found</h3>
                <p class="mt-1 text-sm text-gray-500">Your leave balances will appear here once allocated.</p>
            </div>
        @endforelse
    </div>

    <!-- Leave History -->
    @if($leaveHistory->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Leave History ({{ $year }})</h3>
            </div>

            <div class="p-6">
                @foreach($leaveHistory as $leaveTypeId => $leaves)
                    @php
                        $leaveType = $leaves->first()->leaveType;
                    @endphp
                    <div class="mb-6 last:mb-0">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">{{ $leaveType->name }}</h4>
                        <div class="space-y-2">
                            @foreach($leaves as $leave)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d, Y') }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $leave->number_of_days }} {{ Str::plural('day', $leave->number_of_days) }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $leave->getStatusBadgeClass() }}">
                                        {{ ucfirst($leave->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="flex-1">
                <p class="text-sm font-medium text-blue-900 mb-2">Need to apply for leave?</p>
                <a href="{{ route('employee.leaves.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700">
                    Apply for Leave
                </a>
            </div>
        </div>
    </div>
</div>
@endsection