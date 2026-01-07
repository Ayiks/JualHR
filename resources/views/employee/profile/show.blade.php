@extends('layouts.app')

@section('title', 'My Profile')
@section('header', 'My Profile')
@section('description', 'View and manage your personal information.')

@section('content')
<div class="max-w-4xl">
    <!-- Profile Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="h-32 bg-gradient-to-r from-blue-500 to-blue-600"></div>
        <div class="px-6 pb-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-end gap-6 -mt-16">
                <div class="relative">
                    @if($employee->profile_photo)
                    <img class="w-32 h-32 rounded-xl ring-4 ring-white shadow-lg" src="{{ Storage::url($employee->profile_photo) }}" alt="{{ $employee->full_name }}">
                    @else
                    <div class="w-32 h-32 rounded-xl ring-4 ring-white shadow-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-4xl">
                        {{ $employee->initials }}
                    </div>
                    @endif
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $employee->full_name }}</h2>
                    <p class="text-sm text-gray-600 mt-1">{{ $employee->job_title ?? 'Employee' }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="px-3 py-1 text-xs font-medium bg-blue-50 text-blue-700 rounded-full">
                            {{ $employee->employee_number }}
                        </span>
                        <span class="px-3 py-1 text-xs font-medium bg-green-50 text-green-700 rounded-full">
                            {{ ucfirst(str_replace('_', ' ', $employee->employment_status)) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <!-- Contact Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Contact Information
            </h3>
            <dl class="space-y-4">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Work Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->work_email }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Personal Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->email }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->phone ?? 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 mb-1">SSNIT Number</dt>
                    <dd class="text-sm text-gray-900">{{ $employee->ssnit_number ?? 'Not provided' }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500 mb-1">Ghana Card Number</dt>
                    <dd class="text-sm text-gray-900">{{ $employee->ghana_card_number ?? 'Not provided' }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500 mb-1">TIN Number</dt>
                    <dd class="text-sm text-gray-900">{{ $employee->tin_number ?? 'Not provided' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Employment Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Employment Details
            </h3>
            <dl class="space-y-4">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Department</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->department?->name ?? 'Not assigned' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Employment Type</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $employee->employment_type)) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Date of Joining</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->date_of_joining?->format('F d, Y') ?? 'Not provided' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Bank Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Other Details
            </h3>
            <dl class="space-y-4">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Bank Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->bank_name ?? 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Account Number</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->account_number ?? 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Bank Branch</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->date_of_joining?->format('F d, Y') ?? 'Not provided' }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection