@extends('layouts.app')

@section('title', 'Edit Profile')
@section('header', 'Edit Profile')
@section('description', 'Complete and update your personal information.')

@section('content')
<div class="max-w-4xl">

    <x-profile-completion-alert :employee="$employee" />

    <form method="POST" action="{{ route('employee.profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Profile Photo -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Profile Photo
            </h3>
            <div class="flex items-center gap-6">
                <div class="flex-shrink-0">
                    @if($employee->profile_photo)
                    <img id="photo-preview" class="w-24 h-24 rounded-xl object-cover ring-2 ring-gray-200" src="{{ Storage::url($employee->profile_photo) }}" alt="{{ $employee->full_name }}">
                    @else
                    <div id="photo-initials" class="w-24 h-24 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-3xl">
                        {{ $employee->initials }}
                    </div>
                    <img id="photo-preview" class="w-24 h-24 rounded-xl object-cover ring-2 ring-gray-200 hidden" alt="Preview">
                    @endif
                </div>
                <div>
                    <label class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Choose Photo
                        <input type="file" name="profile_photo" accept="image/jpg,image/jpeg,image/png" class="sr-only" onchange="previewPhoto(this)">
                    </label>
                    <p class="text-xs text-gray-500 mt-2">JPG or PNG, max 2MB</p>
                    @error('profile_photo')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Personal Information
                @if(!$employee->date_of_birth || !$employee->gender)
                <span class="ml-auto text-xs font-normal px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full">Incomplete</span>
                @else
                <span class="ml-auto text-xs font-normal px-2 py-0.5 bg-green-100 text-green-700 rounded-full">Complete</span>
                @endif
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Date of Birth <span class="text-red-500">*</span></label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $employee->date_of_birth?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('date_of_birth') border-red-300 @enderror">
                    @error('date_of_birth')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Gender <span class="text-red-500">*</span></label>
                    <select name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('gender') border-red-300 @enderror">
                        <option value="">Select gender</option>
                        <option value="male" @selected(old('gender', $employee->gender) === 'male')>Male</option>
                        <option value="female" @selected(old('gender', $employee->gender) === 'female')>Female</option>
                        <option value="other" @selected(old('gender', $employee->gender) === 'other')>Other</option>
                    </select>
                    @error('gender')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Marital Status</label>
                    <select name="marital_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select status</option>
                        <option value="single" @selected(old('marital_status', $employee->marital_status) === 'single')>Single</option>
                        <option value="married" @selected(old('marital_status', $employee->marital_status) === 'married')>Married</option>
                        <option value="divorced" @selected(old('marital_status', $employee->marital_status) === 'divorced')>Divorced</option>
                        <option value="widowed" @selected(old('marital_status', $employee->marital_status) === 'widowed')>Widowed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Personal Email</label>
                    <input type="email" name="email" value="{{ old('email', $employee->email) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-300 @enderror">
                    @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Contact & Address -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Contact & Address
                @if(!$employee->phone || !$employee->address || !$employee->city || !$employee->state || !$employee->country || !$employee->postal_code)
                <span class="ml-auto text-xs font-normal px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full">Incomplete</span>
                @else
                <span class="ml-auto text-xs font-normal px-2 py-0.5 bg-green-100 text-green-700 rounded-full">Complete</span>
                @endif
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Phone Number <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-300 @enderror"
                           placeholder="+233 XX XXX XXXX">
                    @error('phone')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Street Address <span class="text-red-500">*</span></label>
                    <input type="text" name="address" value="{{ old('address', $employee->address) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-300 @enderror"
                           placeholder="House / flat number, street name">
                    @error('address')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">City <span class="text-red-500">*</span></label>
                    <input type="text" name="city" value="{{ old('city', $employee->city) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('city')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">State / Region <span class="text-red-500">*</span></label>
                    <input type="text" name="state" value="{{ old('state', $employee->state) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Country <span class="text-red-500">*</span></label>
                    <input type="text" name="country" value="{{ old('country', $employee->country) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Ghana">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Postal Code <span class="text-red-500">*</span></label>
                    <input type="text" name="postal_code" value="{{ old('postal_code', $employee->postal_code) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- Emergency Contact -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Emergency Contact
                @if(!$employee->emergency_contact_name || !$employee->emergency_contact_phone)
                <span class="ml-auto text-xs font-normal px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full">Incomplete</span>
                @else
                <span class="ml-auto text-xs font-normal px-2 py-0.5 bg-green-100 text-green-700 rounded-full">Complete</span>
                @endif
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('emergency_contact_name') border-red-300 @enderror"
                           placeholder="Contact person's full name">
                    @error('emergency_contact_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Phone <span class="text-red-500">*</span></label>
                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('emergency_contact_phone') border-red-300 @enderror">
                    @error('emergency_contact_phone')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Relationship</label>
                    <input type="text" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g. Spouse, Parent, Sibling">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Address</label>
                    <input type="text" name="emergency_contact_address" value="{{ old('emergency_contact_address', $employee->emergency_contact_address) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- Bank Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                Bank Details
                @if(!$employee->bank_name || !$employee->account_number || !$employee->bank_branch)
                <span class="ml-auto text-xs font-normal px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full">Incomplete</span>
                @else
                <span class="ml-auto text-xs font-normal px-2 py-0.5 bg-green-100 text-green-700 rounded-full">Complete</span>
                @endif
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Bank Name <span class="text-red-500">*</span></label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $employee->bank_name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('bank_name') border-red-300 @enderror"
                           placeholder="e.g. GCB Bank">
                    @error('bank_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Account Name</label>
                    <input type="text" name="account_name" value="{{ old('account_name', $employee->account_name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Account Number <span class="text-red-500">*</span></label>
                    <input type="text" name="account_number" value="{{ old('account_number', $employee->account_number) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('account_number') border-red-300 @enderror">
                    @error('account_number')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Bank Branch <span class="text-red-500">*</span></label>
                    <input type="text" name="bank_branch" value="{{ old('bank_branch', $employee->bank_branch) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('bank_branch') border-red-300 @enderror"
                           placeholder="e.g. Accra Main Branch">
                    @error('bank_branch')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Family Information (optional) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-1 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Family Information
                <span class="ml-auto text-xs font-normal text-gray-400">Optional</span>
            </h3>
            <p class="text-xs text-gray-500 mb-4">This information is kept confidential and used only for HR records.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Spouse Name</label>
                    <input type="text" name="spouse_name" value="{{ old('spouse_name', $employee->spouse_name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Spouse Contact</label>
                    <input type="text" name="spouse_contact" value="{{ old('spouse_contact', $employee->spouse_contact) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Number of Children</label>
                    <input type="number" name="number_of_children" value="{{ old('number_of_children', $employee->number_of_children ?? 0) }}" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Next of Kin Name</label>
                    <input type="text" name="next_of_kin_name" value="{{ old('next_of_kin_name', $employee->next_of_kin_name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Next of Kin Date of Birth</label>
                    <input type="date" name="next_of_kin_dob" value="{{ old('next_of_kin_dob', $employee->next_of_kin_dob?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Next of Kin Gender</label>
                    <select name="next_of_kin_sex" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select</option>
                        <option value="male" @selected(old('next_of_kin_sex', $employee->next_of_kin_sex) === 'male')>Male</option>
                        <option value="female" @selected(old('next_of_kin_sex', $employee->next_of_kin_sex) === 'female')>Female</option>
                        <option value="other" @selected(old('next_of_kin_sex', $employee->next_of_kin_sex) === 'other')>Other</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex items-center justify-between bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <a href="{{ route('employee.profile.show') }}" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">
                ← Back to Profile
            </a>
            <div class="flex items-center gap-3">
                <p class="text-xs text-gray-500">You can save partial progress and return later.</p>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Save Changes
                </button>
            </div>
        </div>

    </form>
</div>

@push('scripts')
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('photo-preview');
            const initials = document.getElementById('photo-initials');
            if (preview) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            }
            if (initials) {
                initials.classList.add('hidden');
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
