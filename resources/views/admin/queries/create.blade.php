{{-- resources/views/admin/queries/create.blade.php --}}

@extends('layouts.app')

@section('title', 'Issue Query')
@section('header', 'Issue Query/Warning')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.queries.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Employee -->
                <div class="md:col-span-2">
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Employee <span class="text-red-500">*</span>
                    </label>
                    <select id="employee_id" 
                            name="employee_id" 
                            required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('employee_id') border-red-500 @enderror">
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->full_name }} ({{ $employee->employee_number }}) - {{ $employee->department->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Query Type <span class="text-red-500">*</span>
                    </label>
                    <select id="type" 
                            name="type" 
                            required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('type') border-red-500 @enderror">
                        <option value="">Select Type</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Severity -->
                <div>
                    <label for="severity" class="block text-sm font-medium text-gray-700 mb-2">
                        Severity <span class="text-red-500">*</span>
                    </label>
                    <select id="severity" 
                            name="severity" 
                            required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('severity') border-red-500 @enderror">
                        @foreach($severities as $key => $label)
                            <option value="{{ $key }}" {{ old('severity', 'medium') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('severity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Issued Date -->
                <div>
                    <label for="issued_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Issued Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="issued_date" 
                           name="issued_date" 
                           value="{{ old('issued_date', today()->format('Y-m-d')) }}"
                           required
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('issued_date') border-red-500 @enderror">
                    @error('issued_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Response Deadline -->
                <div>
                    <label for="response_deadline" class="block text-sm font-medium text-gray-700 mb-2">
                        Response Deadline
                    </label>
                    <input type="date" 
                           id="response_deadline" 
                           name="response_deadline" 
                           value="{{ old('response_deadline') }}"
                           min="{{ today()->format('Y-m-d') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('response_deadline') border-red-500 @enderror">
                    @error('response_deadline')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Optional - Set if employee must respond by specific date</p>
                </div>

                <!-- Subject -->
                <div class="md:col-span-2">
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="subject" 
                           name="subject" 
                           value="{{ old('subject') }}"
                           required
                           maxlength="255"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('subject') border-red-500 @enderror"
                           placeholder="e.g., Unauthorized Absence">
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="6"
                              required
                              class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                              placeholder="Provide detailed description of the issue...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Required -->
                <div class="md:col-span-2">
                    <label for="action_required" class="block text-sm font-medium text-gray-700 mb-2">
                        Action Required
                    </label>
                    <textarea id="action_required" 
                              name="action_required" 
                              rows="3"
                              class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('action_required') border-red-500 @enderror"
                              placeholder="What action should the employee take?">{{ old('action_required') }}</textarea>
                    @error('action_required')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Optional - Specify any required corrective actions</p>
                </div>

                <!-- Document Upload -->
                <div class="md:col-span-2">
                    <label for="document" class="block text-sm font-medium text-gray-700 mb-2">
                        Supporting Document
                    </label>
                    <input type="file" 
                           id="document" 
                           name="document" 
                           accept=".pdf,.doc,.docx"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('document') border-red-500 @enderror">
                    @error('document')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Optional - PDF or Word document (max 5MB)</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.queries.index') }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Issue Query
                </button>
            </div>
        </form>
    </div>
</div>
@endsection