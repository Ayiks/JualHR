{{-- resources/views/admin/attendance/report.blade.php --}}

@extends('layouts.app')

@section('title', 'Attendance Report')
@section('header', 'Attendance Report')
@section('description', 'Comprehensive attendance analytics and insights')

@section('content')
<div class="space-y-6">
    <!-- Date Range Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" 
                       name="start_date" 
                       value="{{ $startDate }}"
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" 
                       name="end_date" 
                       value="{{ $endDate }}"
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                <select name="department_id" 
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Generate Report
                </button>
                <a href="{{ route('admin.attendance.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                   class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export CSV
                </a>
            </div>
        </form>
    </div>

    <!-- Overall Statistics -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Overall Statistics</h3>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Total Records</p>
                <p class="text-2xl font-bold text-blue-600">{{ $overallStats['total_records'] }}</p>
            </div>

            <div class="text-center p-4 bg-green-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Present</p>
                <p class="text-2xl font-bold text-green-600">{{ $overallStats['present'] }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $overallStats['total_records'] > 0 ? round(($overallStats['present'] / $overallStats['total_records']) * 100, 1) : 0 }}%
                </p>
            </div>

            <div class="text-center p-4 bg-red-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Absent</p>
                <p class="text-2xl font-bold text-red-600">{{ $overallStats['absent'] }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $overallStats['total_records'] > 0 ? round(($overallStats['absent'] / $overallStats['total_records']) * 100, 1) : 0 }}%
                </p>
            </div>

            <div class="text-center p-4 bg-orange-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Late</p>
                <p class="text-2xl font-bold text-orange-600">{{ $overallStats['late'] }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $overallStats['total_records'] > 0 ? round(($overallStats['late'] / $overallStats['total_records']) * 100, 1) : 0 }}%
                </p>
            </div>

            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Half Day</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $overallStats['half_day'] }}</p>
            </div>

            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">On Leave</p>
                <p class="text-2xl font-bold text-blue-600">{{ $overallStats['on_leave'] }}</p>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Total Work Hours</p>
                <p class="text-3xl font-bold text-purple-600">{{ number_format($overallStats['total_hours'], 1) }}</p>
                <p class="text-xs text-gray-500 mt-1">hours</p>
            </div>

            <div class="text-center p-4 bg-indigo-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Average Hours/Day</p>
                <p class="text-3xl font-bold text-indigo-600">{{ number_format($overallStats['avg_hours'], 1) }}</p>
                <p class="text-xs text-gray-500 mt-1">hours per record</p>
            </div>
        </div>
    </div>

    <!-- Department-wise Statistics -->
    @if($departmentStats->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Department-wise Analysis</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Present</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Absent</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Late</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Avg Hours</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Attendance %</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($departmentStats as $stat)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                    {{ $stat['department'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-gray-900">
                                    {{ $stat['total'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-green-600 font-semibold">{{ $stat['present'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-red-600 font-semibold">{{ $stat['absent'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-orange-600 font-semibold">{{ $stat['late'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-gray-900">
                                    {{ number_format($stat['avg_hours'], 1) }} hrs
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="flex-1 max-w-[100px] bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full" 
                                                 style="width: {{ $stat['total'] > 0 ? round(($stat['present'] / $stat['total']) * 100) : 0 }}%"></div>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900">
                                            {{ $stat['total'] > 0 ? round(($stat['present'] / $stat['total']) * 100, 1) : 0 }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h4 class="text-sm font-semibold text-blue-900 mb-1">Report Period</h4>
                <p class="text-sm text-blue-800">
                    This report covers attendance data from <strong>{{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }}</strong> 
                    to <strong>{{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}</strong>
                    ({{ \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1 }} days).
                </p>
            </div>
        </div>
    </div>
</div>
@endsection