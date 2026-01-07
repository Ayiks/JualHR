@extends('layouts.app')

@section('title', 'Employee Details')
@section('header', $employee->full_name)
@section('description', $employee->job_title ?? 'Employee')

@section('header-actions')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.employees.edit', $employee) }}" 
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit
        </a>
        <a href="{{ route('admin.employees.print', $employee) }}" target="_blank"
            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-gray-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Print
        </a>
    </div>
@endsection

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Profile Header Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="h-32 bg-gradient-to-r from-blue-500 to-blue-600"></div>
        <div class="px-6 pb-6">
            <div class="flex flex-col md:flex-row items-start md:items-end gap-6 -mt-16">
                <div class="relative">
                    @if($employee->profile_photo)
                        <img class="w-32 h-32 rounded-xl ring-4 ring-white shadow-lg" src="{{ Storage::url($employee->profile_photo) }}" alt="{{ $employee->full_name }}">
                    @else
                        <div class="w-32 h-32 rounded-xl ring-4 ring-white shadow-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-4xl">
                            {{ $employee->initials }}
                        </div>
                    @endif
                    <div class="absolute -bottom-2 -right-2 w-10 h-10 rounded-full {{ $employee->is_active ? 'bg-green-500' : 'bg-gray-400' }} ring-4 ring-white flex items-center justify-center">
                        @if($employee->is_active)
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        @endif
                    </div>
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $employee->full_name }}</h2>
                    <p class="text-sm text-gray-600 mt-1">{{ $employee->job_title ?? 'No title assigned' }}</p>
                    <div class="flex flex-wrap items-center gap-2 mt-3">
                        <span class="px-3 py-1 text-xs font-medium bg-blue-50 text-blue-700 rounded-full">
                            {{ $employee->employee_number }}
                        </span>
                        <span class="px-3 py-1 text-xs font-medium rounded-full {{ $employee->employment_status === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-50 text-gray-700' }}">
                            {{ ucfirst(str_replace('_', ' ', $employee->employment_status)) }}
                        </span>
                        <span class="px-3 py-1 text-xs font-medium bg-purple-50 text-purple-700 rounded-full">
                            {{ ucfirst(str_replace('_', ' ', $employee->employment_type)) }}
                        </span>
                        @if($employee->user && $employee->user->roles->isNotEmpty())
                            <span class="px-3 py-1 text-xs font-medium bg-indigo-50 text-indigo-700 rounded-full">
                                {{ ucfirst(str_replace('_', ' ', $employee->user->roles->first()->name)) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Joined</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $employee->date_of_joining?->format('M d, Y') ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Tenure</p>
                    <p class="text-sm font-semibold text-gray-900">
                        @if($employee->date_of_joining)
                            {{ $employee->date_of_joining->diffForHumans(null, true) }}
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Reports To</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $employee->lineManager?->full_name ?? 'None' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Direct Reports</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $employee->subordinates->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100" x-data="{ activeTab: 'personal' }">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200">
            <nav class="flex gap-4 px-6" aria-label="Tabs">
                <button @click="activeTab = 'personal'"
                        :class="activeTab === 'personal' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Personal Info
                </button>
                <button @click="activeTab = 'employment'"
                        :class="activeTab === 'employment' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Employment
                </button>
                <button @click="activeTab = 'contact'"
                        :class="activeTab === 'contact' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Contact
                </button>
                <button @click="activeTab = 'bank'"
                        :class="activeTab === 'bank' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Bank Details
                </button>
                <button @click="activeTab = 'education'"
                        :class="activeTab === 'education' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Education Details
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Personal Info Tab -->
            <div x-show="activeTab === 'personal'" x-transition>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Full Name</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Email</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Phone</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->phone ?? 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Date of Birth</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->date_of_birth?->format('F d, Y') ?? 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Gender</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->gender ? ucfirst($employee->gender) : 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Ghana Card Number</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->ghana_card_number ?? 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">SSNIT Number</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->ssnit_number ?? 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">TIN</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->tin ?? 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Marital Status</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->marital_status ?? 'Not provided' }}</dd>
                    </div>
                    <div class="">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Address</dt>
                        <dd class="text-sm text-gray-900">
                            @if($employee->address || $employee->city || $employee->state || $employee->country)
                                {{ $employee->address }}<br>
                                {{ collect([$employee->city, $employee->state, $employee->postal_code])->filter()->implode(', ') }}<br>
                                {{ $employee->country }}
                            @else
                                Not provided
                            @endif
                        </dd>
                    </div>
                    
                </div>
            </div>

            <!-- Employment Tab -->
            <div x-show="activeTab === 'employment'" x-transition>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Employee Number</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->employee_number }}</dd>
                    </div>
                     <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Work Email</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->work_email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Department</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->department?->name ?? 'Not assigned' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Job Title</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->job_title ?? 'Not assigned' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Line Manager</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->lineManager?->full_name ?? 'None' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Employment Type</dt>
                        <dd class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $employee->employment_type)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Employment Status</dt>
                        <dd class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $employee->employment_status)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Date of Joining</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->date_of_joining?->format('F d, Y') ?? 'Not provided' }}</dd>
                    </div>
                    @if($employee->date_of_leaving)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Date of Leaving</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->date_of_leaving->format('F d, Y') }}</dd>
                    </div>
                    @endif
                </div>

                <!-- Direct Reports -->
                @if($employee->subordinates->isNotEmpty())
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-900 mb-4">Direct Reports ({{ $employee->subordinates->count() }})</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($employee->subordinates as $subordinate)
                        <a href="{{ route('admin.employees.show', $subordinate) }}" 
                           class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors">
                            @if($subordinate->profile_photo)
                                <img class="w-10 h-10 rounded-full" src="{{ Storage::url($subordinate->profile_photo) }}" alt="">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-semibold text-sm">
                                    {{ $subordinate->initials }}
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $subordinate->full_name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $subordinate->job_title }}</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Contact Tab -->
            <div x-show="activeTab === 'contact'" x-transition>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <h4 class="text-sm font-semibold text-gray-900 mb-4">Emergency Contact</h4>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Contact Name</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->emergency_contact_name ?? 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Contact Phone</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->emergency_contact_phone ?? 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Relationship</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->emergency_contact_relationship ?? 'Not provided' }}</dd>
                    </div>
                </div>
            </div>

            <!-- Bank Tab -->
            <div x-show="activeTab === 'bank'" x-transition>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Bank Name</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->bank_name ?? 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Account Name</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->account_name ?? 'Not provided' }}</dd>
                    </div>
                     <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Account Number</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->account_number ?? 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Branch</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->bank_branch ?? 'Not assigned' }}</dd>
                    </div>
                   
                </div>

                <!-- Direct Reports -->
                @if($employee->subordinates->isNotEmpty())
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-900 mb-4">Direct Reports ({{ $employee->subordinates->count() }})</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($employee->subordinates as $subordinate)
                        <a href="{{ route('admin.employees.show', $subordinate) }}" 
                           class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors">
                            @if($subordinate->profile_photo)
                                <img class="w-10 h-10 rounded-full" src="{{ Storage::url($subordinate->profile_photo) }}" alt="">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-semibold text-sm">
                                    {{ $subordinate->initials }}
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $subordinate->full_name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $subordinate->job_title }}</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Education Tab -->
            <div x-show="activeTab === 'education'" x-transition>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Institution Name</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->institution_name ?? 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Program</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->program ?? 'Not provided' }}</dd>
                    </div>
                     <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Certificate</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->certificate ?? 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Branch</dt>
                        <dd class="text-sm text-gray-900">{{ $employee->bank_branch ?? 'Not assigned' }}</dd>
                    </div>
                   
                </div>

                <!-- Direct Reports -->
                @if($employee->subordinates->isNotEmpty())
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-900 mb-4">Direct Reports ({{ $employee->subordinates->count() }})</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($employee->subordinates as $subordinate)
                        <a href="{{ route('admin.employees.show', $subordinate) }}" 
                           class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors">
                            @if($subordinate->profile_photo)
                                <img class="w-10 h-10 rounded-full" src="{{ Storage::url($subordinate->profile_photo) }}" alt="">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-semibold text-sm">
                                    {{ $subordinate->initials }}
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $subordinate->full_name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $subordinate->job_title }}</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection