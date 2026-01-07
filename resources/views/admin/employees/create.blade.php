{{-- resources/views/admin/employees/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Add Employee')
@section('header', 'Add New Employee')
@section('description', 'Register a new employee in the system (7-step process)')

@section('content')
<div class="max-w-5xl mx-auto">
    <form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data"
        x-data="employeeForm()" @submit.prevent="submitForm">
        @csrf

        {{-- Stepper --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="flex items-center justify-between overflow-x-auto">
                <template x-for="(step, index) in steps" :key="index">
                    <div class="flex items-center" :class="index < steps.length - 1 ? 'flex-1' : ''">
                        <div class="flex flex-col items-center min-w-[80px]">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full transition-all"
                                :class="currentStep > index ? 'bg-green-500 text-white' : 
                                         currentStep === index ? 'bg-blue-600 text-white' : 
                                         'bg-gray-200 text-gray-600'">
                                <span x-show="currentStep <= index" x-text="index + 1" class="font-semibold text-sm"></span>
                                <svg x-show="currentStep > index" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-xs mt-2 font-medium text-center"
                                :class="currentStep >= index ? 'text-gray-900' : 'text-gray-500'"
                                x-text="step.title"></span>
                        </div>
                        <div x-show="index < steps.length - 1"
                            class="flex-1 h-0.5 mx-2 transition-all min-w-[20px]"
                            :class="currentStep > index ? 'bg-green-500' : 'bg-gray-200'"></div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Form Steps --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

            {{-- Step 1: Personal Information --}}
            <div x-show="currentStep === 0" x-transition>
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Personal Information</h3>

                {{-- Profile Photo --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
                    <div class="flex items-center gap-6">
                        <div class="relative">
                            <div class="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden">
                                <img x-show="photoPreview" :src="photoPreview" class="w-full h-full object-cover">
                                <svg x-show="!photoPreview" class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <input type="file" name="profile_photo" id="profile_photo" accept="image/*"
                                class="hidden" @change="previewPhoto($event)">
                            <label for="profile_photo"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Choose Photo
                            </label>
                            <p class="text-xs text-gray-500 mt-1">JPG, PNG. Max 2MB</p>
                        </div>
                    </div>
                    @error('profile_photo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Full Name --}}
                    <!-- <div class="md:col-span-2">
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('full_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div> -->

                    {{-- First Name --}}
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('first_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Last Name --}}
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Middle Name --}}
                    <div>
                        <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
                        <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    {{-- Address --}}
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <textarea name="address" id="address" rows="2" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('address') }}</textarea>
                        @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Mobile Number --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Mobile Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- SSNIT Number --}}
                    <div>
                        <label for="ssnit_number" class="block text-sm font-medium text-gray-700 mb-2">
                            SSNIT Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="ssnit_number" id="ssnit_number" value="{{ old('ssnit_number') }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('ssnit_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Ghana Card Number --}}
                    <div>
                        <label for="ghana_card_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Ghana Card Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="ghana_card_number" id="ghana_card_number" value="{{ old('ghana_card_number') }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('ghana_card_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- TIN Number --}}
                    <div>
                        <label for="tin_number" class="block text-sm font-medium text-gray-700 mb-2">TIN Number (Optional)</label>
                        <input type="text" name="tin_number" id="tin_number" value="{{ old('tin_number') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    {{-- Date of Birth --}}
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                            Date of Birth <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('date_of_birth')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Gender --}}
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <select name="gender" id="gender" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('gender')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Marital Status --}}
                    <div>
                        <label for="marital_status" class="block text-sm font-medium text-gray-700 mb-2">
                            Marital Status <span class="text-red-500">*</span>
                        </label>
                        <select name="marital_status" id="marital_status" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Status</option>
                            <option value="single" {{ old('marital_status') === 'single' ? 'selected' : '' }}>Single</option>
                            <option value="married" {{ old('marital_status') === 'married' ? 'selected' : '' }}>Married</option>
                            <option value="divorced" {{ old('marital_status') === 'divorced' ? 'selected' : '' }}>Divorced</option>
                            <option value="widowed" {{ old('marital_status') === 'widowed' ? 'selected' : '' }}>Widowed</option>
                        </select>
                        @error('marital_status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Step 2: Job Information --}}
            <div x-show="currentStep === 1" x-transition>
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Job Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Job Title --}}
                    <div>
                        <label for="job_title" class="block text-sm font-medium text-gray-700 mb-2">
                            Job Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="job_title" id="job_title" value="{{ old('job_title') }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('job_title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Department --}}
                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Department <span class="text-red-500">*</span>
                        </label>
                        <select name="department_id" id="department_id" required
                            @change="fetchDepartmentHead($event.target.value)"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('department_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Work Email --}}
                    <div>
                        <label for="work_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Work Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="work_email" id="work_email" value="{{ old('work_email') }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('work_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Personal Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Personal Email<span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('personal_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Work Phone --}}
                    <div>
                        <label for="work_phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Work Phone <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="work_phone" id="work_phone" value="{{ old('work_phone') }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('work_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Cell Phone --}}
                    <div>
                        <label for="cell_phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Cell Phone <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="cell_phone" id="cell_phone" value="{{ old('cell_phone') }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('cell_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Employment Type --}}
                    <div>
                        <label for="employment_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Employment Type <span class="text-red-500">*</span>
                        </label>
                        <select name="employment_type" id="employment_type" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Type</option>
                            <option value="full_time" {{ old('employment_type') === 'full_time' ? 'selected' : '' }}>Full Time</option>
                            <option value="part_time" {{ old('employment_type') === 'part_time' ? 'selected' : '' }}>Part Time</option>
                            <option value="contract" {{ old('employment_type') === 'contract' ? 'selected' : '' }}>Contract</option>
                            <option value="intern" {{ old('employment_type') === 'intern' ? 'selected' : '' }}>Intern</option>
                        </select>
                        @error('employment_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Date of Joining --}}
                    <div>
                        <label for="date_of_joining" class="block text-sm font-medium text-gray-700 mb-2">
                            Date of Joining <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_of_joining" id="date_of_joining" value="{{ old('date_of_joining') }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('date_of_joining')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Line Manager --}}
                    <div class="md:col-span-2">
                        <label for="line_manager_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Line Manager
                            <span class="text-xs text-gray-500">(Auto-filled from department head)</span>
                        </label>
                        <div class="flex gap-2">
                            <select name="line_manager_id" id="line_manager_id" x-model="lineManagerId"
                                class="flex-1 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Manager</option>
                                @foreach($managers as $manager)
                                <option value="{{ $manager->id }}" {{ old('line_manager_id') == $manager->id ? 'selected' : '' }}>
                                    {{ $manager->full_name }} - {{ $manager->job_title ?? 'N/A' }}
                                </option>
                                @endforeach
                            </select>
                            <button type="button" @click="clearLineManager" x-show="lineManagerId"
                                class="px-3 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200"
                                title="Clear selection">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1" x-show="autoFilledManager">
                            ✓ Auto-filled with department head. You can change if needed.
                        </p>
                    </div>

                    {{-- User Role --}}
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                            User Role <span class="text-red-500">*</span>
                        </label>
                        <select name="role" id="role" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="employee" {{ old('role') === 'employee' ? 'selected' : '' }}>Employee</option>
                            <option value="line_manager" {{ old('role') === 'line_manager' ? 'selected' : '' }}>Line Manager</option>
                            <option value="hr_admin" {{ old('role') === 'hr_admin' ? 'selected' : '' }}>HR Admin</option>
                            <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        </select>
                        @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Step 3: Education/Qualifications --}}
            <div x-show="currentStep === 2" x-transition>
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Education & Qualifications</h3>

                <div x-data="educationManager()">
                    <template x-for="(education, index) in educations" :key="index">
                        <div class="mb-6 p-4 border border-gray-200 rounded-lg">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-medium text-gray-900">Education Record <span x-text="index + 1"></span></h4>
                                <button type="button" @click="removeEducation(index)" x-show="educations.length > 1"
                                    class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Name of Institution <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" :name="'education[' + index + '][institution_name]'" required
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Program <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" :name="'education[' + index + '][program]'" required
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Certificate Obtained <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" :name="'education[' + index + '][certificate_obtained]'" required
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Attach Certificates (Max 2 files, 10MB each)
                                    </label>
                                    <input type="file" :name="'education[' + index + '][certificates][]'" multiple accept=".pdf"
                                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <p class="text-xs text-gray-500 mt-1">PDF only, max 2 files per record</p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <button type="button" @click="addEducation"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Another Education
                    </button>
                </div>
            </div>

            {{-- Step 4: Emergency Contact --}}
            <div x-show="currentStep === 3" x-transition>
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Emergency Contact</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="emergency_contact_name" id="emergency_contact_name"
                            value="{{ old('emergency_contact_name') }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('emergency_contact_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="emergency_contact_address" class="block text-sm font-medium text-gray-700 mb-2">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <textarea name="emergency_contact_address" id="emergency_contact_address" rows="2" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('emergency_contact_address') }}</textarea>
                        @error('emergency_contact_address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="emergency_contact_phone" id="emergency_contact_phone"
                            value="{{ old('emergency_contact_phone') }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('emergency_contact_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="emergency_contact_relationship" class="block text-sm font-medium text-gray-700 mb-2">
                            Relationship <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="emergency_contact_relationship" id="emergency_contact_relationship"
                            value="{{ old('emergency_contact_relationship') }}" required
                            placeholder="e.g., Spouse, Parent, Sibling"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('emergency_contact_relationship')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Step 5: Bank Details --}}
            <div x-show="currentStep === 4" x-transition>
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Bank Details</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Bank Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name') }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('bank_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bank_branch" class="block text-sm font-medium text-gray-700 mb-2">
                            Branch <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="bank_branch" id="bank_branch" value="{{ old('bank_branch') }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('bank_branch')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="account_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Account Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="account_name" id="account_name" value="{{ old('account_name') }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('account_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="account_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Account Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="account_number" id="account_number" value="{{ old('account_number') }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('account_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Step 6: Family --}}
            <div x-show="currentStep === 5" x-transition>
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Family Information</h3>

                {{-- Spouse Information --}}
                <div class="mb-8">
                    <h4 class="font-medium text-gray-900 mb-4">Spouse Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="spouse_name" class="block text-sm font-medium text-gray-700 mb-2">Spouse Name</label>
                            <input type="text" name="spouse_name" id="spouse_name" value="{{ old('spouse_name') }}"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="spouse_contact" class="block text-sm font-medium text-gray-700 mb-2">Spouse Contact</label>
                            <input type="text" name="spouse_contact" id="spouse_contact" value="{{ old('spouse_contact') }}"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                {{-- Children Information --}}
                <div class="mb-8" x-data="childrenManager()">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-medium text-gray-900">Children Information</h4>
                        <div>
                            <label class="text-sm text-gray-600 mr-2">Number of Children:</label>
                            <input type="number" name="number_of_children" x-model="numberOfChildren" min="0" max="20"
                                @input="updateChildrenCount"
                                class="w-20 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <template x-if="numberOfChildren > 0">
                        <div>
                            <template x-for="(child, index) in children" :key="index">
                                <div class="mb-4 p-4 border border-gray-200 rounded-lg">
                                    <h5 class="font-medium text-sm text-gray-700 mb-3">Child <span x-text="index + 1"></span></h5>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Name <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" :name="'children[' + index + '][name]'" required
                                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Date of Birth <span class="text-red-500">*</span>
                                            </label>
                                            <input type="date" :name="'children[' + index + '][date_of_birth]'" required
                                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Sex <span class="text-red-500">*</span>
                                            </label>
                                            <select :name="'children[' + index + '][sex]'" required
                                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                                <option value="">Select</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Next of Kin --}}
                <div>
                    <h4 class="font-medium text-gray-900 mb-4">Next of Kin</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="next_of_kin_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="next_of_kin_name" id="next_of_kin_name"
                                value="{{ old('next_of_kin_name') }}" required
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('next_of_kin_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="next_of_kin_dob" class="block text-sm font-medium text-gray-700 mb-2">
                                Date of Birth <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="next_of_kin_dob" id="next_of_kin_dob"
                                value="{{ old('next_of_kin_dob') }}" required
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('next_of_kin_dob')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="next_of_kin_sex" class="block text-sm font-medium text-gray-700 mb-2">
                                Sex <span class="text-red-500">*</span>
                            </label>
                            <select name="next_of_kin_sex" id="next_of_kin_sex" required
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Sex</option>
                                <option value="male" {{ old('next_of_kin_sex') === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('next_of_kin_sex') === 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('next_of_kin_sex')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 7: Review & Submit --}}
            <div x-show="currentStep === 6" x-transition>
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Review & Submit</h3>

                <div class="space-y-6">
                    {{-- Summary Info Box --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <div class="flex gap-3">
                            <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="flex-1">
                                <h4 class="font-semibold text-blue-900 mb-2">Ready to Create Employee</h4>
                                <div class="text-sm text-blue-800 space-y-1">
                                    <p>✓ Personal information collected</p>
                                    <p>✓ Job details configured</p>
                                    <p>✓ Education records added</p>
                                    <p>✓ Emergency contact provided</p>
                                    <p>✓ Bank details entered</p>
                                    <p>✓ Family information complete</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Important Notes --}}
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div class="text-sm text-yellow-800">
                                <p class="font-semibold mb-1">Important Notes:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Employee number will be auto-generated (Format: JGGL + 3 digits)</li>
                                    <li>Default password: <strong>JGGLDefault@2025</strong></li>
                                    <li>Employee must change password on first login</li>
                                    <li>Login credentials will be sent to work email</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Data Privacy Notice --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" id="privacy_consent" required
                                class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="privacy_consent" class="text-sm text-gray-700">
                                I confirm that all information provided is accurate and complete. I understand that this data will be stored securely and used in accordance with company policies and data protection regulations.
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Navigation Buttons --}}
            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                <button type="button" @click="previousStep" x-show="currentStep > 0"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Previous
                </button>

                <div class="flex-1"></div>

                <button type="button" @click="nextStep" x-show="currentStep < steps.length - 1"
                    class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700">
                    Next
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <button type="submit" x-show="currentStep === steps.length - 1"
                    class="inline-flex items-center px-6 py-2 bg-green-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Create Employee
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function employeeForm() {
        return {
            currentStep: 0,
            photoPreview: null,
            lineManagerId: '',
            autoFilledManager: false,
            steps: [{
                    title: 'Personal'
                },
                {
                    title: 'Job Info'
                },
                {
                    title: 'Education'
                },
                {
                    title: 'Emergency'
                },
                {
                    title: 'Bank'
                },
                {
                    title: 'Family'
                },
                {
                    title: 'Review'
                }
            ],

            // Validate current step before moving to next
            async nextStep() {
                if (await this.validateCurrentStep()) {
                    if (this.currentStep < this.steps.length - 1) {
                        this.currentStep++;
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }
                }
            },

            previousStep() {
                if (this.currentStep > 0) {
                    this.currentStep--;
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            },

            // Validate fields for current step
            async validateCurrentStep() {
                let isValid = true;
                let errorMessages = [];

                switch (this.currentStep) {
                    case 0: // Personal Information
                        isValid = this.validateStep1();
                        if (!isValid) errorMessages.push('Please fill all required personal information fields');
                        break;

                    case 1: // Job Information
                        isValid = this.validateStep2();
                        if (!isValid) errorMessages.push('Please fill all required job information fields');
                        break;

                    case 2: // Education (optional but if filled, validate)
                        isValid = this.validateStep3();
                        if (!isValid) errorMessages.push('Please complete all education fields or remove incomplete entries');
                        break;

                    case 3: // Emergency Contact
                        isValid = this.validateStep4();
                        if (!isValid) errorMessages.push('Please fill all required emergency contact fields');
                        break;

                    case 4: // Bank Details
                        isValid = this.validateStep5();
                        if (!isValid) errorMessages.push('Please fill all required bank details');
                        break;

                    case 5: // Family
                        isValid = this.validateStep6();
                        if (!isValid) errorMessages.push('Please complete all required family information');
                        break;
                }

                if (!isValid) {
                    this.showNotification(errorMessages[0], 'error');
                }

                return isValid;
            },

            // Step 1: Personal Information Validation
            validateStep1() {
                const requiredFields = [,
                    'first_name',
                    'last_name',
                    'address',
                    'phone',
                    'ssnit_number',
                    'ghana_card_number',
                    'date_of_birth',
                    'gender',
                    'marital_status'
                ];

                return this.validateFields(requiredFields);
            },

            // Step 2: Job Information Validation
            validateStep2() {
                const requiredFields = [
                    'job_title',
                    'department_id',
                    'work_email',
                    'personal_email',
                    'work_phone',
                    'cell_phone',
                    'employment_type',
                    'date_of_joining',
                    'role'
                ];

                const isValid = this.validateFields(requiredFields);

                // Additional email validation
                if (isValid) {
                    const workEmail = document.getElementById('work_email').value;
                    if (!this.isValidEmail(workEmail)) {
                        this.showNotification('Please enter a valid work email address', 'error');
                        return false;
                    }

                    const personalEmail = document.getElementById('email').value;
                    if (personalEmail && !this.isValidEmail(personalEmail)) {
                        this.showNotification('Please enter a valid personal email address', 'error');
                        return false;
                    }
                }

                return isValid;
            },

            // Step 3: Education Validation (optional, but if started, must complete)
            validateStep3() {
                const institutionInputs = document.querySelectorAll('input[name*="[institution_name]"]');

                for (let input of institutionInputs) {
                    if (input.value.trim()) {
                        // If institution name is filled, check other required education fields
                        const index = input.name.match(/\[(\d+)\]/)[1];
                        const program = document.querySelector(`input[name="education[${index}][program]"]`);
                        const certificate = document.querySelector(`input[name="education[${index}][certificate_obtained]"]`);

                        if (!program.value.trim() || !certificate.value.trim()) {
                            this.showNotification(`Please complete all fields for education entry ${parseInt(index) + 1}`, 'error');
                            return false;
                        }
                    }
                }

                return true;
            },

            // Step 4: Emergency Contact Validation
            validateStep4() {
                const requiredFields = [
                    'emergency_contact_name',
                    'emergency_contact_address',
                    'emergency_contact_phone',
                    'emergency_contact_relationship'
                ];

                return this.validateFields(requiredFields);
            },

            // Step 5: Bank Details Validation
            validateStep5() {
                const requiredFields = [
                    'bank_name',
                    'bank_branch',
                    'account_name',
                    'account_number'
                ];

                return this.validateFields(requiredFields);
            },

            // Step 6: Family Validation
            validateStep6() {
                const requiredFields = [
                    'next_of_kin_name',
                    'next_of_kin_dob',
                    'next_of_kin_sex'
                ];

                const isValid = this.validateFields(requiredFields);

                // Validate children if number_of_children > 0
                if (isValid) {
                    const numberOfChildren = parseInt(document.querySelector('input[name="number_of_children"]').value) || 0;

                    if (numberOfChildren > 0) {
                        const childNameInputs = document.querySelectorAll('input[name*="children"][name*="[name]"]');

                        if (childNameInputs.length < numberOfChildren) {
                            this.showNotification(`Please add information for all ${numberOfChildren} children`, 'error');
                            return false;
                        }

                        // Validate each child entry
                        for (let i = 0; i < numberOfChildren; i++) {
                            const name = document.querySelector(`input[name="children[${i}][name]"]`);
                            const dob = document.querySelector(`input[name="children[${i}][date_of_birth]"]`);
                            const sex = document.querySelector(`select[name="children[${i}][sex]"]`);

                            if (!name || !name.value.trim() || !dob || !dob.value || !sex || !sex.value) {
                                this.showNotification(`Please complete all fields for child ${i + 1}`, 'error');
                                return false;
                            }
                        }
                    }
                }

                return isValid;
            },

            // Generic field validation helper
            validateFields(fieldNames) {
                for (let fieldName of fieldNames) {
                    const field = document.getElementById(fieldName) ||
                        document.querySelector(`[name="${fieldName}"]`);

                    if (!field) {
                        console.warn(`Field ${fieldName} not found`);
                        continue;
                    }

                    const value = field.value.trim();

                    if (!value) {
                        // Highlight the field
                        field.classList.add('border-red-500', 'ring-red-500');
                        field.focus();

                        // Get field label
                        const label = document.querySelector(`label[for="${fieldName}"]`);
                        const fieldLabel = label ? label.textContent.replace('*', '').trim() : fieldName;

                        this.showNotification(`${fieldLabel} is required`, 'error');

                        // Remove highlight after 3 seconds
                        setTimeout(() => {
                            field.classList.remove('border-red-500', 'ring-red-500');
                        }, 3000);

                        return false;
                    }
                }

                return true;
            },

            // Email validation helper
            isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            },

            previewPhoto(event) {
                const file = event.target.files[0];
                if (file) {
                    // Validate file size (2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        this.showNotification('Profile photo must be less than 2MB', 'error');
                        event.target.value = '';
                        return;
                    }

                    // Validate file type
                    if (!file.type.match('image/(jpg|jpeg|png)')) {
                        this.showNotification('Profile photo must be JPG or PNG', 'error');
                        event.target.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.photoPreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },

            async fetchDepartmentHead(departmentId) {
                if (!departmentId) {
                    this.lineManagerId = '';
                    this.autoFilledManager = false;
                    return;
                }

                try {
                    const response = await fetch(`/admin/employees/department/${departmentId}/head`);
                    const data = await response.json();

                    if (data.success && data.head) {
                        this.lineManagerId = data.head.id;
                        this.autoFilledManager = true;
                        this.showNotification('Line manager auto-filled with department head: ' + data.head.name, 'success');
                    } else {
                        this.autoFilledManager = false;
                        this.showNotification('No department head assigned. Please select a line manager manually.', 'info');
                    }
                } catch (error) {
                    console.error('Error fetching department head:', error);
                    this.autoFilledManager = false;
                }
            },

            clearLineManager() {
                this.lineManagerId = '';
                this.autoFilledManager = false;
            },

            showNotification(message, type = 'success') {
                const colors = {
                    success: {
                        bg: 'bg-green-100',
                        border: 'border-green-400',
                        text: 'text-green-700'
                    },
                    error: {
                        bg: 'bg-red-100',
                        border: 'border-red-400',
                        text: 'text-red-700'
                    },
                    info: {
                        bg: 'bg-blue-100',
                        border: 'border-blue-400',
                        text: 'text-blue-700'
                    }
                };

                const color = colors[type] || colors.info;

                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 ${color.bg} border ${color.border} ${color.text} px-4 py-3 rounded-lg shadow-lg z-50 max-w-md`;
                notification.innerHTML = `
                <div class="flex items-start gap-2">
                    <span class="flex-1">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-current opacity-70 hover:opacity-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 5000);
            },

            submitForm(event) {
                // Final validation before submit
                if (!document.getElementById('privacy_consent')?.checked) {
                    event.preventDefault();
                    this.showNotification('Please accept the privacy consent to continue', 'error');
                    return false;
                }

                event.target.submit();
            }
        }
    }

    function educationManager() {
        return {
            educations: [{}],
            addEducation() {
                this.educations.push({});
            },
            removeEducation(index) {
                this.educations.splice(index, 1);
            }
        }
    }

    function childrenManager() {
        return {
            numberOfChildren: 0,
            children: [],
            updateChildrenCount() {
                const count = parseInt(this.numberOfChildren) || 0;
                if (count > this.children.length) {
                    // Add children
                    for (let i = this.children.length; i < count; i++) {
                        this.children.push({});
                    }
                } else if (count < this.children.length) {
                    // Remove children
                    this.children.splice(count);
                }
            }
        }
    }
</script>
@endpush
@endsection