@extends('layouts.app')

@section('title', 'Leave Report')
@section('header', 'Leave Report')
@section('description', 'Leave requests filtered by date range, type, and status.')

@section('header-actions')
<a href="{{ route('admin.reports.export', ['type' => 'leaves', 'from' => $from, 'to' => $to]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
    Export CSV
</a>
<a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
    ← Reports
</a>
@endsection

@section('content')
<div class="space-y-5">

    {{-- Summary --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $summary['total'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Total Requests</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-yellow-600">{{ $summary['pending'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Pending</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $summary['approved'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Approved</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-red-600">{{ $summary['rejected'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Rejected</p>
        </div>
    </div>

    {{-- By Type --}}
    @if($summary['by_type']->count())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">By Leave Type</h3>
        <div class="flex flex-wrap gap-3">
            @foreach($summary['by_type'] as $row)
            <div class="px-3 py-2 bg-blue-50 rounded-lg text-sm">
                <span class="text-gray-600">{{ $row->leaveType?->name ?? 'Unknown' }}:</span>
                <span class="font-semibold text-gray-900 ml-1">{{ $row->total }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('admin.reports.leaves') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">From</label>
                <input type="date" name="from" value="{{ $from }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">To</label>
                <input type="date" name="to" value="{{ $to }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">All</option>
                    @foreach(['pending','approved','rejected','cancelled'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Leave Type</label>
                <select name="leave_type_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach($leaveTypes as $lt)
                    <option value="{{ $lt->id }}" @selected(request('leave_type_id') == $lt->id)>{{ $lt->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Department</label>
                <select name="department_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">All</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">Filter</button>
            <a href="{{ route('admin.reports.leaves') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Reset</a>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($leaves as $leave)
                    @php
                    $badge = match($leave->status) {
                        'pending'   => 'bg-yellow-100 text-yellow-800',
                        'approved'  => 'bg-green-100 text-green-800',
                        'rejected'  => 'bg-red-100 text-red-800',
                        default     => 'bg-gray-100 text-gray-600',
                    };
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $leave->employee->full_name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $leave->employee->department?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $leave->leaveType->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $leave->start_date->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $leave->end_date->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $leave->number_of_days ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                {{ ucfirst($leave->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-sm text-gray-400">No leave records found for the selected period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leaves->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $leaves->links() }}</div>
        @endif
    </div>

</div>
@endsection
