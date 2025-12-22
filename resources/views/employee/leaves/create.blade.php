<div>
    <!-- Nothing in life is to be feared, it is only to be understood. Now is the time to understand more, so that we may fear less. - Marie Curie -->
</div>
{{-- resources/views/employee/leaves/create.blade.php --}}

@extends('layouts.app')

@section('title', 'Apply for Leave')
@section('header', 'Apply for Leave')
@section('description', 'Submit a new leave request')

@section('content')
<div class="max-w-3xl mx-auto">
    <form action="{{ route('employee.leaves.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="space-y-6">
                <!-- Leave Type -->
                <div>
                    <label for="leave_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Leave Type <span class="text-red-500">*</span>
                    </label>
                    <select name="leave_type_id" id="leave_type_id" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Leave Type</option>
                        @foreach($leaveTypes as $type)
                            <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} 
                                @if(isset($leaveBalances[$type->id]))
                                    ({{ $leaveBalances[$type->id]->remaining_days }} days remaining)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('leave_type_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required
                               min="{{ now()->format('Y-m-d') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            End Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required
                               min="{{ now()->format('Y-m-d') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Reason -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Reason <span class="text-red-500">*</span>
                    </label>
                    <textarea name="reason" id="reason" rows="4" required
                              placeholder="Please provide a detailed reason for your leave request..."
                              class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Minimum 10 characters</p>
                </div>

                <!-- Attachment -->
                <div>
                    <label for="attachment" class="block text-sm font-medium text-gray-700 mb-2">
                        Attachment (Optional)
                    </label>
                    <input type="file" name="attachment" id="attachment" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('attachment')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">PDF or image files, max 2MB</p>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">Important Information</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Your leave request will be sent to your line manager for approval</li>
                                <li>You will receive a notification once your request is reviewed</li>
                                <li>Ensure you have sufficient leave balance before applying</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('employee.leaves.index') }}" 
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                    class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Submit Request
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
