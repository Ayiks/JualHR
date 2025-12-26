{{-- resources/views/employee/attendance/index.blade.php --}}

@extends('layouts.app')

@section('title', 'My Attendance')
@section('header', 'My Attendance')
@section('description', 'Track your daily attendance')

@section('content')
<div class="space-y-6">
    <!-- Check In/Out Card -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-sm p-8 text-white">
        <div class="max-w-4xl mx-auto">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="text-center md:text-left">
                    <h2 class="text-2xl font-bold mb-2">{{ now()->format('l, F d, Y') }}</h2>
                    <p class="text-blue-100 text-lg" x-data x-text="new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' })" x-init="setInterval(() => { $el.textContent = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) }, 1000)"></p>
                </div>

                <div class="flex items-center gap-4">
                    @if($todayAttendance && $todayAttendance->check_in)
                        <div class="text-center">
                            <p class="text-sm text-blue-100 mb-1">Checked In</p>
                            <p class="text-2xl font-bold">{{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') }}</p>
                        </div>
                    @endif

                    @if($todayAttendance && $todayAttendance->check_out)
                        <div class="text-center">
                            <p class="text-sm text-blue-100 mb-1">Checked Out</p>
                            <p class="text-2xl font-bold">{{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A') }}</p>
                        </div>
                    @endif

                    @if(!$todayAttendance || !$todayAttendance->check_in)
                        <form action="{{ route('employee.attendance.check-in') }}" method="POST">
                            @csrf
                            <button type="submit" 
                                class="px-8 py-4 bg-white text-blue-600 rounded-lg font-semibold text-lg hover:bg-blue-50 transition-colors shadow-lg">
                                Check In
                            </button>
                        </form>
                    @elseif(!$todayAttendance->check_out)
                        <form action="{{ route('employee.attendance.check-out') }}" method="POST">
                            @csrf
                            <button type="submit" 
                                class="px-8 py-4 bg-white text-blue-600 rounded-lg font-semibold text-lg hover:bg-blue-50 transition-colors shadow-lg">
                                Check Out
                            </button>
                        </form>
                    @else
                        <div class="px-8 py-4 bg-white bg-opacity-20 rounded-lg">
                            <p class="text-sm">Work Hours</p>
                            <p class="text-2xl font-bold">{{ number_format($todayAttendance->work_hours, 1) }} hrs</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Days</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_days'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Present</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['present_days'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Absent</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['absent_days'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Hours</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_hours'], 1) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attendance -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Recent Attendance ({{ now()->format('F Y') }})</h3>
            <a href="{{ route('employee.attendance.history') }}" 
               class="text-sm font-medium text-blue-600 hover:text-blue-700">View All →</a>
        </div>

        <div class="p-6">
            @if($monthAttendance->isNotEmpty())
                <div class="space-y-2">
                    @foreach($monthAttendance->take(10) as $attendance)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="text-center">
                                    <p class="text-xs text-gray-500">{{ $attendance->date->format('D') }}</p>
                                    <p class="text-lg font-bold text-gray-900">{{ $attendance->date->format('d') }}</p>
                                    <p class="text-xs text-gray-500">{{ $attendance->date->format('M') }}</p>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $attendance->getStatusBadgeClass() }}">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </div>
                                    @if($attendance->check_in)
                                        <p class="text-sm text-gray-600">
                                            {{ \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') }}
                                            @if($attendance->check_out)
                                                - {{ \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') }}
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </div>
                            @if($attendance->work_hours)
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900">{{ number_format($attendance->work_hours, 1) }} hrs</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No attendance records this month</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection