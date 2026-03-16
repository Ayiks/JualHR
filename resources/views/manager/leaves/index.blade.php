@extends('layouts.app')

@section('title', 'Team Leave Schedule')
@section('header', 'Team Leave Schedule')
@section('description', 'View your team\'s leave requests. Approvals are handled by HR.')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($leaves as $leave)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-semibold text-xs">
                                {{ $leave->employee->initials }}
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $leave->employee->full_name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $leave->leaveType->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $leave->start_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $leave->end_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $leave->number_of_days ?? '—' }}</td>
                    <td class="px-6 py-4">
                        @php
                            $badge = match($leave->status) {
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'approved' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                'cancelled' => 'bg-gray-100 text-gray-600',
                                default => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                            {{ ucfirst($leave->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('manager.leaves.show', $leave) }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-400">No leave requests from your team.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($leaves->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $leaves->links() }}</div>
    @endif
</div>
@endsection
