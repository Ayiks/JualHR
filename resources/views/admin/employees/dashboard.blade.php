
@extends('layouts.app')

@section('title', 'My Dashboard')
@section('header', 'Welcome back, ' . $employee->first_name)
@section('description', 'Your personal workspace and quick access to important features.')

@section('content')
<!-- Profile Overview -->
<div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-sm p-6 text-white mb-6">
    <div class="flex items-center gap-6">
        <div class="flex-shrink-0">
            @if($employee->profile_photo)
                <img class="w-20 h-20 rounded-full ring-4 ring-white ring-opacity-50" src="{{ Storage::url($employee->profile_photo) }}" alt="{{ $employee->full_name }}">
            @else
                <div class="w-20 h-20 rounded-full bg-white bg-opacity-20 ring-4 ring-white ring-opacity-50 flex items-center justify-center text-white font-bold text-2xl">
                    {{ $employee->initials }}
                </div>
            @endif
        </div>
        <div class="flex-1">
            <h2 class="text-2xl font-bold">{{ $employee->full_name }}</h2>
            <p class="text-blue-100 mt-1">{{ $employee->job_title ?? 'Employee' }}</p>
            <div class="flex items-center gap-4 mt-3 text-sm">
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    {{ $employee->department?->name ?? 'No Department' }}
                </span>
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ $employee->employee_number }}
                </span>
            </div>
        </div>
        <a href="{{ route('employee.profile.show') }}" 
            class="px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg text-sm font-medium transition-colors">
            View Profile
        </a>
    </div>
</div>

<!-- Quick Links Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <a href="{{ route('employee.profile.show') }}" 
        class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-blue-200 transition-all group">
        <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center mb-4 group-hover:bg-blue-100 transition-colors">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
        <h3 class="text-sm font-semibold text-gray-900 mb-1">My Profile</h3>
        <p class="text-xs text-gray-500">View and update your information</p>
    </a>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 opacity-60 cursor-not-allowed">
        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h3 class="text-sm font-semibold text-gray-900 mb-1">My Leaves</h3>
        <p class="text-xs text-gray-500">Coming Soon</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 opacity-60 cursor-not-allowed">
        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h3 class="text-sm font-semibold text-gray-900 mb-1">Attendance</h3>
        <p class="text-xs text-gray-500">Coming Soon</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 opacity-60 cursor-not-allowed">
        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>
        <h3 class="text-sm font-semibold text-gray-900 mb-1">Policies</h3>
        <p class="text-xs text-gray-500">Coming Soon</p>
    </div>
</div>
@endsection