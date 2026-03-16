@extends('layouts.app')

@section('title', 'Edit Survey')
@section('header', 'Edit Survey')
@section('description', 'Update survey settings. Note: existing questions cannot be changed to avoid corrupting responses.')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.surveys.update', $survey) }}">
        @csrf @method('PUT')

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $survey->title) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 @error('title') border-red-300 @enderror">
                @error('title')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Description</label>
                <textarea name="description" rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">{{ old('description', $survey->description) }}</textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Type</label>
                    <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="satisfaction" @selected(old('type', $survey->type) === 'satisfaction')>Satisfaction Survey</option>
                        <option value="general_review" @selected(old('type', $survey->type) === 'general_review')>General Review</option>
                        <option value="exit_interview" @selected(old('type', $survey->type) === 'exit_interview')>Exit Interview</option>
                        <option value="other" @selected(old('type', $survey->type) === 'other')>Other</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $survey->start_date?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $survey->end_date?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="flex flex-wrap gap-6 pt-2 border-t border-gray-100">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $survey->getRawOriginal('is_active')))
                           class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Active (visible to employees)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_anonymous" value="1" @checked(old('is_anonymous', $survey->is_anonymous))
                           class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Anonymous responses</span>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-between mt-4">
            <a href="{{ route('admin.surveys.show', $survey) }}" class="text-sm text-gray-600 hover:text-gray-900">← Cancel</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">Save Changes</button>
        </div>
    </form>
</div>
@endsection
