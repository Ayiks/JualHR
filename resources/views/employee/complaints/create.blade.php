@extends('layouts.app')

@section('title', 'Submit Complaint')
@section('header', 'Submit a Complaint')
@section('description', 'Your complaint will be handled confidentially by HR.')

@section('content')
<div class="max-w-2xl">

    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-sm text-blue-700">All complaints are treated with the utmost confidentiality. You may also submit anonymously if you prefer.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('employee.complaints.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">

            <div>
                <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Subject <span class="text-red-500">*</span></label>
                <input type="text" name="subject" value="{{ old('subject') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 @error('subject') border-red-300 @enderror"
                       placeholder="Brief summary of the complaint">
                @error('subject')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Category <span class="text-red-500">*</span></label>
                    <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 @error('category') border-red-300 @enderror">
                        <option value="">Select category</option>
                        <option value="harassment" @selected(old('category') === 'harassment')>Harassment</option>
                        <option value="discrimination" @selected(old('category') === 'discrimination')>Discrimination</option>
                        <option value="workplace_safety" @selected(old('category') === 'workplace_safety')>Workplace Safety</option>
                        <option value="misconduct" @selected(old('category') === 'misconduct')>Misconduct</option>
                        <option value="policy_violation" @selected(old('category') === 'policy_violation')>Policy Violation</option>
                        <option value="management" @selected(old('category') === 'management')>Management</option>
                        <option value="compensation" @selected(old('category') === 'compensation')>Compensation</option>
                        <option value="other" @selected(old('category') === 'other')>Other</option>
                    </select>
                    @error('category')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Priority <span class="text-red-500">*</span></label>
                    <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 @error('priority') border-red-300 @enderror">
                        <option value="">Select priority</option>
                        <option value="low" @selected(old('priority') === 'low')>Low</option>
                        <option value="medium" @selected(old('priority') === 'medium')>Medium</option>
                        <option value="high" @selected(old('priority') === 'high')>High</option>
                        <option value="urgent" @selected(old('priority') === 'urgent')>Urgent</option>
                    </select>
                    @error('priority')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Description <span class="text-red-500">*</span></label>
                <textarea name="description" rows="6"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 @error('description') border-red-300 @enderror"
                          placeholder="Describe the complaint in detail. Include dates, names (if applicable), and any supporting context...">{{ old('description') }}</textarea>
                @error('description')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Supporting Document (optional)</label>
                <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                       class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-gray-400 mt-1">PDF, Word, or image — max 5MB</p>
                @error('attachment')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Anonymous toggle -->
            <div class="pt-2 border-t border-gray-100">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="is_anonymous" value="1" @checked(old('is_anonymous'))
                           class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Submit anonymously</p>
                        <p class="text-xs text-gray-500 mt-0.5">Your name will not be visible to HR when reviewing this complaint.</p>
                    </div>
                </label>
            </div>

        </div>

        <div class="flex items-center justify-between mt-4">
            <a href="{{ route('employee.complaints.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Back</a>
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Submit Complaint
            </button>
        </div>
    </form>
</div>
@endsection
