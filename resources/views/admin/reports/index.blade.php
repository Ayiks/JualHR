@extends('layouts.app')

@section('title', 'Reports')
@section('header', 'Reports')
@section('description', 'Overview and quick access to all system reports.')

@section('content')
<div class="space-y-6">

    {{-- Quick Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @php
        $cards = [
            ['label' => 'Total Employees',    'value' => $stats['total_employees'],  'color' => 'blue'],
            ['label' => 'Active Employees',   'value' => $stats['active_employees'], 'color' => 'green'],
            ['label' => 'Pending Leaves',     'value' => $stats['pending_leaves'],   'color' => 'yellow'],
            ['label' => 'Approved (This Month)', 'value' => $stats['approved_leaves'], 'color' => 'teal'],
            ['label' => 'Attendance Today',   'value' => $stats['attendance_today'], 'color' => 'indigo'],
            ['label' => 'Open Queries',       'value' => $stats['open_queries'],     'color' => 'orange'],
            ['label' => 'Open Complaints',    'value' => $stats['open_complaints'],  'color' => 'red'],
            ['label' => 'Departments',        'value' => $stats['departments'],      'color' => 'purple'],
        ];
        @endphp
        @foreach($cards as $card)
        @php
        $bg  = "bg-{$card['color']}-50";
        $txt = "text-{$card['color']}-700";
        @endphp
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $card['label'] }}</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $card['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Quick Generate --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Generate Report</h3>
        <form method="POST" action="{{ route('admin.reports.generate') }}" class="flex flex-wrap gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Report Type</label>
                <select name="report_type" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="employees">Employees</option>
                    <option value="leaves">Leaves</option>
                    <option value="attendance">Attendance</option>
                    <option value="queries">Queries & Complaints</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">From</label>
                <input type="date" name="from" value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                       class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">To</label>
                <input type="date" name="to" value="{{ now()->format('Y-m-d') }}"
                       class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                View Report
            </button>
        </form>
    </div>

    {{-- Report Links --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @php
        $reports = [
            ['title' => 'Employee Report',       'desc' => 'Headcount, department breakdown, employment types.', 'route' => 'admin.reports.employees', 'icon' => '👥', 'export' => 'employees'],
            ['title' => 'Leave Report',          'desc' => 'Leave requests by type, status, and date range.',    'route' => 'admin.reports.leaves',    'icon' => '🗓️',  'export' => 'leaves'],
            ['title' => 'Attendance Report',     'desc' => 'Daily attendance, check-in/out times, work hours.',  'route' => 'admin.reports.attendance','icon' => '⏱️',  'export' => 'attendance'],
            ['title' => 'Queries & Complaints',  'desc' => 'Status of all queries and complaints raised.',       'route' => 'admin.reports.queries',   'icon' => '📋',  'export' => null],
        ];
        @endphp
        @foreach($reports as $r)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-start gap-4">
            <div class="text-2xl">{{ $r['icon'] }}</div>
            <div class="flex-1">
                <p class="font-semibold text-gray-900 text-sm">{{ $r['title'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $r['desc'] }}</p>
            </div>
            <div class="flex flex-col gap-1.5">
                <a href="{{ route($r['route']) }}" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors text-center">View</a>
                @if($r['export'])
                <a href="{{ route('admin.reports.export', ['type' => $r['export']]) }}" class="px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors text-center">Export CSV</a>
                @endif
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection
