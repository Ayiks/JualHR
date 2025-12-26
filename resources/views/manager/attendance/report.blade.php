
@extends('layouts.app')

@section('title', 'Team Attendance Report')
@section('header', 'Team Attendance Report')

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

            <div>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Generate Report
                </button>
            </div>
        </form>
    </div>

    <!-- Overall Team Statistics -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Overall Team Statistics</h3>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Total Records</p>
                <p class="text-2xl font-bold text-blue-600">{{ $overallStats['total_records'] }}</p>
            </div>

            <div class="text-center p-4 bg-green-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Present</p>
                <p class="text-2xl font-bold text-green-600">{{ $overallStats['present'] }}</p>
            </div>

            <div class="text-center p-4 bg-red-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Absent</p>
                <p class="text-2xl font-bold text-red-600">{{ $overallStats['absent'] }}</p>
            </div>

            <div class="text-center p-4 bg-orange-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Late</p>
                <p class="text-2xl font-bold text-orange-600">{{ $overallStats['late'] }}</p>
            </div>

            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Total Hours</p>
                <p class="text-2xl font-bold text-purple-600">{{ number_format($overallStats['total_hours'], 1) }}</p>
            </div>

            <div class="text-center p-4 bg-indigo-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Avg Hours</p>
                <p class="text-2xl font-bold text-indigo-600">{{ number_format($overallStats['avg_hours'], 1) }}</p>
            </div>
        </div>
    </div>

    <!-- Employee-wise Statistics -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Employee Performance</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Days</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Present</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Absent</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Late</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Hours</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Avg Hours</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Rate</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($employeeStats as $stat)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-blue-600 font-semibold text-sm">
                                                {{ $stat['employee']->initials }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $stat['employee']->full_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $stat['employee']->employee_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ $stat['total_days'] }}
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
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ number_format($stat['total_hours'], 1) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ number_format($stat['avg_hours'], 1) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-semibold" 
                                      style="color: {{ $stat['attendance_rate'] >= 90 ? '#10B981' : ($stat['attendance_rate'] >= 75 ? '#F59E0B' : '#EF4444') }}">
                                    {{ number_format($stat['attendance_rate'], 1) }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection