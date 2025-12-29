{{-- resources/views/admin/policies/create.blade.php --}}

@extends('layouts.app')

@section('title', 'Create Policy')
@section('header', 'Create New Policy')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.policies.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Policy Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title') }}"
                           required
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('title') border-red-500 @enderror"
                           placeholder="e.g., Code of Conduct">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        Policy Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="code" 
                           name="code" 
                           value="{{ old('code') }}"
                           required
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('code') border-red-500 @enderror"
                           placeholder="e.g., POL-001">
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Unique identifier for this policy</p>
                </div>

                <!-- Version -->
                <div>
                    <label for="version" class="block text-sm font-medium text-gray-700 mb-2">
                        Version <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="version" 
                           name="version" 
                           value="{{ old('version', '1.0') }}"
                           required
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('version') border-red-500 @enderror"
                           placeholder="e.g., 1.0">
                    @error('version')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select id="category" 
                            name="category" 
                            required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('category') border-red-500 @enderror">
                        <option value="">Select Category</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" 
                            name="status" 
                            required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Effective Date -->
                <div>
                    <label for="effective_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Effective Date
                    </label>
                    <input type="date" 
                           id="effective_date" 
                           name="effective_date" 
                           value="{{ old('effective_date') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('effective_date') border-red-500 @enderror">
                    @error('effective_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Review Date -->
                <div>
                    <label for="review_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Review Date
                    </label>
                    <input type="date" 
                           id="review_date" 
                           name="review_date" 
                           value="{{ old('review_date') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('review_date') border-red-500 @enderror">
                    @error('review_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">When this policy should be reviewed</p>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                              placeholder="Brief description of the policy...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Document Upload -->
                <div class="md:col-span-2">
                    <label for="document" class="block text-sm font-medium text-gray-700 mb-2">
                        Policy Document (PDF) <span class="text-red-500">*</span>
                    </label>
                    <input type="file" 
                           id="document" 
                           name="document" 
                           accept=".pdf"
                           required
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('document') border-red-500 @enderror">
                    @error('document')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Maximum file size: 10MB</p>
                </div>

                <!-- Requires Acknowledgment -->
                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="requires_acknowledgment" 
                               value="1"
                               {{ old('requires_acknowledgment') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">
                            Require employee acknowledgment
                        </span>
                    </label>
                    <p class="mt-1 ml-6 text-xs text-gray-500">
                        If checked, employees will be required to acknowledge they have read this policy
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.policies.index') }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Create Policy
                </button>
            </div>
        </form>
    </div>
</div>
@endsection