{{-- resources/views/components/profile-completion-alert.blade.php --}}
@props(['employee'])

@if($employee && !$employee->profile_completed)
<div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg shadow-sm">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="ml-3 flex-1">
            <h3 class="text-sm font-medium text-yellow-800">
                Complete Your Profile
            </h3>
            <div class="mt-2 text-sm text-yellow-700">
                <p>Your profile is incomplete. Please provide your complete information including:</p>
                <ul class="list-disc list-inside mt-2 space-y-1">
                    <li>Personal details (SSNIT, Ghana Card, TIN)</li>
                    <li>Contact information and address</li>
                    <li>Education & qualifications</li>
                    <li>Bank account details</li>
                    <li>Emergency contact</li>
                    <li>Family information</li>
                </ul>
            </div>
            <div class="mt-4">
                <a href="{{ route('employee.profile.edit') }}" 
                   class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Complete Profile Now
                </a>
            </div>
        </div>
    </div>
</div>
@endif