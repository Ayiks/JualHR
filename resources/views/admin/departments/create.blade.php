{{-- resources/views/admin/departments/create.blade.php --}}

@extends('layouts.app')

@section('title', 'Add Department')
@section('header', 'Add New Department')
@section('description', 'Create a new department in the organization')

@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ route('admin.departments.store') }}" method="POST">
        @csrf
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="space-y-6">
                <!-- Department Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Department Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           placeholder="e.g., Human Resources"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="4"
                              placeholder="Brief description of the department's responsibilities..."
                              class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Maximum 1000 characters</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.departments.index') }}" 
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                    class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Create Department
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
