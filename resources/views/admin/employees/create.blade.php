{{-- resources/views/admin/employees/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Add Employee')
@section('header', 'Add New Employee')
@section('description', 'Create employee account - Employee will complete their profile after login')

@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ route('admin.employees.store') }}" method="POST">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Employee Account Information</h3>
            
            {{-- Info Banner --}}
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">Employee Self-Service Setup</p>
                        <p>Just provide basic account details. The employee will complete their full profile (personal info, education, bank details, etc.) after their first login.</p>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                {{-- First Name --}}
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="first_name" id="first_name" 
                           value="{{ old('first_name') }}" required autofocus
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                           placeholder="e.g., John">
                    @error('first_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Last Name --}}
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="last_name" id="last_name" 
                           value="{{ old('last_name') }}" required
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                           placeholder="e.g., Mensah">
                    @error('last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Work Email --}}
                <div>
                    <label for="work_email" class="block text-sm font-medium text-gray-700 mb-2">
                        Work Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="work_email" id="work_email" 
                           value="{{ old('work_email') }}" required
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                           placeholder="e.g., john.mensah@jggl.com">
                    <p class="mt-1 text-xs text-gray-500">This will be used for login</p>
                    @error('work_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Department --}}
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Department <span class="text-red-500">*</span>
                    </label>
                    <select name="department_id" id="department_id" required
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

                {{-- Default Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Default Password <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="password" id="password" 
                           value="{{ old('password', 'password123') }}" required
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Employee can change this later from their profile</p>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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

            {{-- Summary Box --}}
            <div class="mt-8 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-green-800">
                        <p class="font-medium mb-1">What happens next?</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>Employee account will be created with JGGL employee number</li>
                            <li>Employee can login immediately with provided email and password</li>
                            <li>They will see an alert to complete their profile</li>
                            <li>Profile includes: Personal info, Education, Bank details, Emergency contact, Family info</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.employees.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Cancel
                </a>

                <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Create Employee Account
                </button>
            </div>
        </div>
    </form>
</div>
@endsection