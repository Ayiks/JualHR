
{{-- resources/views/admin/attendance/edit.blade.php --}}

@extends('layouts.app')

@section('title', 'Edit Attendance Record')
@section('header', 'Edit Attendance Record')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <!-- Employee Info Card -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <span class="text-blue-600 font-semibold">{{ $attendance->employee->initials }}</span>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $attendance->employee->full_name }}</h3>
                    <p class="text-sm text-gray-500">{{ $attendance->employee->employee_number }} • {{ $attendance->date->format('l, F d, Y') }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.attendance.update', $attendance) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" 
                            name="status" 
                            required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                        <option value="present" {{ old('status', $attendance->status) == 'present' ? 'selected' : '' }}>Present</option>
                        <option value="absent" {{ old('status', $attendance->status) == 'absent' ? 'selected' : '' }}>Absent</option>
                        <option value="late" {{ old('status', $attendance->status) == 'late' ? 'selected' : '' }}>Late</option>
                        <option value="half_day" {{ old('status', $attendance->status) == 'half_day' ? 'selected' : '' }}>Half Day</option>
                        <option value="on_leave" {{ old('status', $attendance->status) == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Work Hours (Read-only info) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Current Work Hours
                    </label>
                    <div class="w-full px-3 py-2 bg-gray-100 rounded-lg text-gray-700">
                        {{ $attendance->work_hours ? number_format($attendance->work_hours, 2) . ' hours' : 'Not calculated' }}
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Automatically calculated from check in/out times</p>
                </div>

                <!-- Check In -->
                <div>
                    <label for="check_in" class="block text-sm font-medium text-gray-700 mb-2">
                        Check In Time
                    </label>
                    <input type="time" 
                           id="check_in" 
                           name="check_in" 
                           value="{{ old('check_in', $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('check_in') border-red-500 @enderror">
                    @error('check_in')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Check Out -->
                <div>
                    <label for="check_out" class="block text-sm font-medium text-gray-700 mb-2">
                        Check Out Time
                    </label>
                    <input type="time" 
                           id="check_out" 
                           name="check_out" 
                           value="{{ old('check_out', $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('check_out') border-red-500 @enderror">
                    @error('check_out')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Must be after check in time</p>
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notes
                    </label>
                    <textarea id="notes" 
                              name="notes" 
                              rows="3"
                              maxlength="500"
                              class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-500 @enderror"
                              placeholder="Any additional notes or remarks...">{{ old('notes', $attendance->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Maximum 500 characters</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <form action="{{ route('admin.attendance.destroy', $attendance) }}" 
                      method="POST" 
                      onsubmit="return confirm('Are you sure you want to delete this attendance record? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Delete Record
                    </button>
                </form>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.attendance.index') }}" 
                       class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Update Record
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection