@extends('layouts.app')

@section('title', 'Create Survey')
@section('header', 'Create Survey')
@section('description', 'Build a new survey with custom questions.')

@section('content')
<div class="max-w-3xl" x-data="surveyBuilder()">

    <form method="POST" action="{{ route('admin.surveys.store') }}">
        @csrf

        <!-- Survey Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Survey Details</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 @error('title') border-red-300 @enderror"
                           placeholder="e.g. Employee Satisfaction Q1 2025">
                    @error('title')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Description</label>
                    <textarea name="description" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                              placeholder="Optional context for employees">{{ old('description') }}</textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Type <span class="text-red-500">*</span></label>
                        <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 @error('type') border-red-300 @enderror">
                            <option value="">Select type</option>
                            <option value="satisfaction" @selected(old('type') === 'satisfaction')>Satisfaction Survey</option>
                            <option value="general_review" @selected(old('type') === 'general_review')>General Review</option>
                            <option value="exit_interview" @selected(old('type') === 'exit_interview')>Exit Interview</option>
                            <option value="other" @selected(old('type') === 'other')>Other</option>
                        </select>
                        @error('type')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="flex flex-wrap gap-6 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active'))
                               class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Publish immediately</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_anonymous" value="1" @checked(old('is_anonymous'))
                               class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Collect responses anonymously</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Questions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900">Questions <span class="text-red-500">*</span></h3>
                <button type="button" @click="addQuestion()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-100 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Question
                </button>
            </div>
            @error('questions')<p class="text-xs text-red-600 mb-3">{{ $message }}</p>@enderror

            <div class="space-y-4">
                <template x-for="(q, index) in questions" :key="q.id">
                    <div class="border border-gray-200 rounded-xl p-4">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold" x-text="index + 1"></span>
                            <button type="button" @click="removeQuestion(index)" class="text-gray-400 hover:text-red-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <input type="text" :name="`questions[${index}][question]`" x-model="q.question"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                                       placeholder="Question text" required>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <select :name="`questions[${index}][type]`" x-model="q.type"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                        <option value="text">Text answer</option>
                                        <option value="multiple_choice">Multiple choice (one)</option>
                                        <option value="checkbox">Multiple choice (many)</option>
                                        <option value="rating">Rating (1–5)</option>
                                    </select>
                                </div>
                                <div class="flex items-center">
                                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                                        <input type="checkbox" :name="`questions[${index}][required]`" value="1" x-model="q.required"
                                               class="w-4 h-4 rounded border-gray-300 text-blue-600">
                                        Required
                                    </label>
                                </div>
                            </div>

                            <!-- Options for multiple choice -->
                            <div x-show="q.type === 'multiple_choice' || q.type === 'checkbox'" class="space-y-2">
                                <p class="text-xs text-gray-500 font-medium">Answer options:</p>
                                <template x-for="(opt, oi) in q.options" :key="oi">
                                    <div class="flex items-center gap-2">
                                        <input type="text" :name="`questions[${index}][options][]`" x-model="q.options[oi]"
                                               class="flex-1 px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                                               :placeholder="`Option ${oi + 1}`">
                                        <button type="button" @click="q.options.splice(oi, 1)" class="text-gray-400 hover:text-red-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                                <button type="button" @click="q.options.push('')" class="text-xs text-blue-600 hover:text-blue-800 font-medium">+ Add option</button>
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="questions.length === 0" class="text-center py-6 text-gray-400 text-sm border border-dashed border-gray-200 rounded-xl">
                    No questions yet. Click "Add Question" to start.
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.surveys.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Back</a>
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                Create Survey
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function surveyBuilder() {
    return {
        questions: [],
        nextId: 1,
        addQuestion() {
            this.questions.push({ id: this.nextId++, question: '', type: 'text', required: false, options: ['', ''] });
        },
        removeQuestion(index) {
            this.questions.splice(index, 1);
        }
    };
}
</script>
@endpush
@endsection
