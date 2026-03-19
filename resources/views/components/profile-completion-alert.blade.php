{{-- resources/views/components/profile-completion-alert.blade.php --}}
@props(['employee'])

@if($employee && !$employee->profile_completed)
@php
    $percentage = $employee->profile_completion_percentage;
    $missing = $employee->missing_profile_fields;
@endphp
<div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-xl shadow-sm overflow-hidden">
    <div class="p-4">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 mt-0.5">
                <svg class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-4">
                    <h3 class="text-sm font-semibold text-yellow-800">
                        Profile {{ $percentage }}% Complete
                    </h3>
                    <a href="{{ route('employee.profile.edit') }}"
                       class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 border border-red-500 text-red-600 text-xs font-medium rounded-lg hover:bg-red-50 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Complete Profile
                    </a>
                </div>

                <!-- Progress bar -->
                <div class="mt-2 w-full bg-yellow-200 rounded-full h-2">
                    <div class="bg-yellow-500 h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                </div>

                @if(count($missing) > 0)
                <p class="mt-2 text-xs text-yellow-700">
                    Still needed:
                    <span class="font-medium">{{ implode(', ', array_slice($missing, 0, 4)) }}{{ count($missing) > 4 ? ' and ' . (count($missing) - 4) . ' more' : '' }}</span>
                </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
