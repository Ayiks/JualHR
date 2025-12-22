{{-- resources/views/admin/leave-types/edit.blade.php --}}

@extends('layouts.app')

@section('title', 'Edit Leave Type')
@section('header', 'Edit Leave Type')

@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ route('admin.leave-types.update', $leaveType) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Leave Type Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $leaveType->name) }}" required
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" id="code" value="{{ old('code', $leaveType->code) }}" required
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 uppercase">
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="days_per_year" class="block text-sm font-medium text-gray-700 mb-2">
                        Days per Year <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="days_per_year" id="days_per_year" value="{{ old('days_per_year', $leaveType->days_per_year) }}" required min="0" max="365"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('days_per_year')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('description', $leaveType->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $leaveType->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
                </div>
            </div>

            <div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-200">
                <form action="{{ route('admin.leave-types.destroy', $leaveType) }}" method="POST" class="inline"
                    onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-red-700">
                        Delete
                    </button>
                </form>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.leave-types.index') }}" 
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" 
                        class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700">
                        Update
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection