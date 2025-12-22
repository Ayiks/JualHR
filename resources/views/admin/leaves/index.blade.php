{{-- resources/views/admin/leaves/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Leave Requests')
@section('header', 'Leave Requests')
@section('description', 'Manage employee leave requests')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <!-- Filters -->
    <div class="p-6 border-b border-gray-200">
        <form method="GET" action="{{ route('admin.leaves.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Date Range -->
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Actions -->
            <div class="flex items-end gap-2">
                <button type="submit" 
                    class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gray-800 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-gray-700">
                    Apply
                </button>
                <a href="{{ route('admin.leaves.index') }}" 
                    class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Leaves Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Leave Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Applied</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($leaves as $leave)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($leave->employee->profile_photo)
                                    <img class="h-8 w-8 rounded-full" src="{{ Storage::url($leave->employee->profile_photo) }}" alt="">
                                @else
                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-semibold">
                                        {{ $leave->employee->initials }}
                                    </div>
                                @endif
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $leave->employee->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $leave->employee->employee_number }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                {{ $leave->leaveType->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $leave->number_of_days }} {{ Str::plural('day', $leave->number_of_days) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $leave->getStatusBadgeClass() }}">
                                {{ ucfirst($leave->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $leave->created_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.leaves.show', $leave) }}" 
                                class="text-blue-600 hover:text-blue-900">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="mt-2">No leave requests found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($leaves->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $leaves->links() }}
        </div>
    @endif
</div>
@endsection