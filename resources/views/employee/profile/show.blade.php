@extends('layouts.app')

@section('title', 'My Profile')
@section('header', 'My Profile')
@section('description', 'View and manage your personal information.')

@section('content')
<div class="max-w-4xl">
    <x-profile-completion-alert :employee="$employee" />

    <!-- Profile Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="h-32 bg-gradient-to-r from-blue-500 to-blue-600"></div>
        <div class="px-6 pb-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-end gap-6 -mt-16">
                <div class="relative">
                    @if($employee->profile_photo)
                    <img class="w-32 h-32 rounded-xl ring-4 ring-white shadow-lg object-cover" src="{{ Storage::url($employee->profile_photo) }}" alt="{{ $employee->full_name }}">
                    @else
                    <div class="w-32 h-32 rounded-xl ring-4 ring-white shadow-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-4xl">
                        {{ $employee->initials }}
                    </div>
                    @endif
                </div>
                <div class="flex-1 pt-16 sm:pt-0">
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                        <div>
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
                        <a href="{{ route('employee.profile.edit') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

        <!-- Personal Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Personal Information
            </h3>
            <dl class="space-y-4">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Date of Birth</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->date_of_birth?->format('F d, Y') ?? 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($employee->gender ?? 'Not provided') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Marital Status</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($employee->marital_status ?? 'Not provided') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">SSNIT Number</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->ssnit_number ?? 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Ghana Card Number</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->ghana_card_number ?? 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">TIN Number</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->tin_number ?? 'Not provided' }}</dd>
                </div>
            </dl>
        </div>

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
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->work_email ?? 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Personal Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->email ?? 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->phone ?? 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Address</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($employee->address)
                            {{ implode(', ', array_filter([$employee->address, $employee->city, $employee->state, $employee->country, $employee->postal_code])) }}
                        @else
                            Not provided
                        @endif
                    </dd>
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
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Job Title</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->job_title ?? 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Employment Type</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $employee->employment_type)) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Date of Joining</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->date_of_joining?->format('F d, Y') ?? 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Line Manager</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->lineManager?->full_name ?? 'Not assigned' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Emergency Contact -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Emergency Contact
            </h3>
            <dl class="space-y-4">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->emergency_contact_name ?? 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->emergency_contact_phone ?? 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Relationship</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->emergency_contact_relationship ?? 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Address</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->emergency_contact_address ?? 'Not provided' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Bank Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                Bank Details
            </h3>
            <dl class="space-y-4">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Bank Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->bank_name ?? 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Account Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->account_name ?? 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Account Number</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->account_number ?? 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Bank Branch</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->bank_branch ?? 'Not provided' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Family Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Family Information
            </h3>
            <dl class="space-y-4">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Spouse Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->spouse_name ?? 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Spouse Contact</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->spouse_contact ?? 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Number of Children</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->number_of_children ?? '0' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Next of Kin</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->next_of_kin_name ?? 'Not provided' }}</dd>
                </div>
            </dl>
        </div>

    </div>

    <!-- Education History -->
    @if($employee->education->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
            </svg>
            Education History
        </h3>
        <div class="space-y-4">
            @foreach($employee->education as $edu)
            <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg">
                <div class="flex-1">
                    <p class="font-medium text-gray-900">{{ $edu->degree ?? $edu->qualification }}</p>
                    <p class="text-sm text-gray-600">{{ $edu->institution }}</p>
                    @if($edu->year_completed)
                    <p class="text-xs text-gray-500 mt-1">Completed: {{ $edu->year_completed }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
