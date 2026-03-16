@extends('layouts.app')

@section('title', $survey->title)
@section('header', $survey->title)
@section('description', $survey->description ?? 'Please answer all questions honestly.')

@section('content')
<div class="max-w-2xl">

    @if($survey->is_anonymous)
    <div class="bg-purple-50 border border-purple-200 rounded-xl p-4 mb-6 flex items-center gap-3">
        <svg class="w-5 h-5 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
        </svg>
        <p class="text-sm text-purple-700">This survey is anonymous — your identity will not be recorded.</p>
    </div>
    @endif

    <form method="POST" action="{{ route('employee.surveys.submit', $survey) }}">
        @csrf

        <div class="space-y-5">
            @foreach($survey->questions as $q)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <p class="text-sm font-semibold text-gray-900 mb-3">
                    {{ $loop->iteration }}. {{ $q->question }}
                    @if($q->is_required)<span class="text-red-500 ml-1">*</span>@endif
                </p>

                @error('answers.' . $q->id)
                <p class="text-xs text-red-600 mb-2">{{ $message }}</p>
                @enderror

                @if($q->question_type === 'text')
                <textarea name="answers[{{ $q->id }}]" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                          placeholder="Your answer..."
                          @if($q->is_required) required @endif>{{ old('answers.' . $q->id) }}</textarea>

                @elseif($q->question_type === 'multiple_choice')
                <div class="space-y-2">
                    @foreach($q->options ?? [] as $opt)
                    <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors">
                        <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt }}"
                               @checked(old('answers.' . $q->id) === $opt)
                               class="w-4 h-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                               @if($q->is_required) required @endif>
                        <span class="text-sm text-gray-700">{{ $opt }}</span>
                    </label>
                    @endforeach
                </div>

                @elseif($q->question_type === 'checkbox')
                <div class="space-y-2">
                    @foreach($q->options ?? [] as $opt)
                    <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors">
                        <input type="checkbox" name="answers[{{ $q->id }}][]" value="{{ $opt }}"
                               @checked(in_array($opt, (array)(old('answers.' . $q->id) ?? [])))
                               class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">{{ $opt }}</span>
                    </label>
                    @endforeach
                </div>

                @elseif($q->question_type === 'rating')
                <div class="flex items-center gap-3">
                    @for($i = 1; $i <= 5; $i++)
                    <label class="cursor-pointer group" title="{{ $i }} star{{ $i !== 1 ? 's' : '' }}">
                        <input type="radio" name="answers[{{ $q->id }}]" value="{{ $i }}"
                               @checked(old('answers.' . $q->id) == $i)
                               class="sr-only peer"
                               @if($q->is_required) required @endif>
                        <svg class="w-8 h-8 text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-300 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </label>
                    @endfor
                    <span class="text-xs text-gray-400 ml-1">(1 = Poor, 5 = Excellent)</span>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('employee.surveys.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Back</a>
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Submit Response
            </button>
        </div>
    </form>
</div>
@endsection
