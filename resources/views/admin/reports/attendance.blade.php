@extends('layouts.app')

@section('title', 'Attendance Report')
@section('header', 'Attendance Report')
@section('description', 'Check-in/out records and work hours by date range.')

@section('header-actions')
<a href="{{ route('admin.reports.export', ['type' => 'attendance', 'from' => $from, 'to' => $to]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
    Export CSV
</a>
<a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
    ← Reports
</a>
@endsection

@section('content')
<div class="space-y-5">

    {{-- Summary --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $summary['present'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Present</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-red-600">{{ $summary['absent'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Absent</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-yellow-600">{{ $summary['late'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Late</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-orange-600">{{ $summary['half_day'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Half Day</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $summary['avg_hours'] }}h</p>
            <p class="text-xs text-gray-500 mt-0.5">Avg Hours</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('admin.reports.attendance') }}" class="flex flex-wrap gap-3 items-end">
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
                    @foreach(['present','absent','late','half_day'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
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
            <a href="{{ route('admin.reports.attendance') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Reset</a>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($records as $record)
                    @php
                    $badge = match($record->status) {
                        'present'  => 'bg-green-100 text-green-700',
                        'absent'   => 'bg-red-100 text-red-700',
                        'late'     => 'bg-yellow-100 text-yellow-700',
                        'half_day' => 'bg-orange-100 text-orange-700',
                        default    => 'bg-gray-100 text-gray-600',
                    };
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-900 font-medium">{{ $record->date->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-gray-900">{{ $record->employee->full_name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $record->employee->department?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $record->check_in ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $record->check_out ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $record->work_hours ? $record->work_hours.'h' : '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-sm text-gray-400">No attendance records for the selected period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $records->links() }}</div>
        @endif
    </div>

</div>
@endsection
