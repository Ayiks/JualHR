
@extends('layouts.app')

@section('title', 'Team Attendance Report')
@section('header', 'Team Attendance Report')

@section('content')
<div class="space-y-6">
    <!-- Month Selector -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="GET" class="flex items-center gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                <select name="month" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                <select name="year" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @for($y = now()->year; $y >= now()->year - 2; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="pt-7">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    View Period
                </button>
            </div>
        </form>
    </div>

    <!-- Team Statistics -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                Team Attendance Summary - {{ \Carbon\Carbon::create($year, $month)->format('F Y') }}
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Days</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Present</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Absent</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Late</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Hours</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Attendance %</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($teamMembers as $member)
                        @php
                            $stats = $employeeStats[$member->id] ?? [
                                'total_days' => 0,
                                'present' => 0,
                                'absent' => 0,
                                'late' => 0,
                                'total_hours' => 0
                            ];
                            $attendanceRate = $stats['total_days'] > 0 
                                ? round((($stats['present'] + $stats['late']) / $stats['total_days']) * 100, 1)
                                : 0;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-blue-600 font-semibold text-sm">{{ $member->initials }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $member->full_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $member->employee_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ $stats['total_days'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-green-600 font-semibold">{{ $stats['present'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-red-600 font-semibold">{{ $stats['absent'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-orange-600 font-semibold">{{ $stats['late'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ number_format($stats['total_hours'], 1) }} hrs
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-20 bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $attendanceRate }}%"></div>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ $attendanceRate }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection