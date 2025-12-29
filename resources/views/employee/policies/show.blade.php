
{{-- resources/views/employee/policies/show.blade.php --}}

@extends('layouts.app')

@section('title', $policy->title)
@section('header', $policy->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Policy Details -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $policy->title }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $policy->code }} • Version {{ $policy->version }}</p>
            </div>
            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $policy->getCategoryBadgeClass() }}">
                {{ \App\Models\Policy::getCategories()[$policy->category] }}
            </span>
        </div>

        @if($policy->description)
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Description</h3>
                <p class="text-gray-700">{{ $policy->description }}</p>
            </div>
        @endif>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-sm font-medium text-gray-500">Effective Date</p>
                <p class="text-base text-gray-900">
                    {{ $policy->effective_date ? $policy->effective_date->format('F d, Y') : 'Not set' }}
                </p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Last Updated</p>
                <p class="text-base text-gray-900">{{ $policy->updated_at->format('F d, Y') }}</p>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('employee.policies.download', $policy) }}" 
               class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download PDF
            </a>
            <a href="{{ route('employee.policies.index') }}" 
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Back
            </a>
        </div>
    </div>

    <!-- Acknowledgment Section -->
    @if($policy->requires_acknowledgment)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            @if($isAcknowledged)
                <div class="flex items-center gap-4 p-4 bg-green-50 rounded-lg">
                    <svg class="w-12 h-12 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-green-900">Policy Acknowledged</h3>
                        <p class="text-sm text-green-700 mt-1">
                            You acknowledged this policy on {{ $acknowledgment->acknowledged_at->format('F d, Y \a\t h:i A') }}
                        </p>
                    </div>
                </div>
            @else
                <div class="space-y-4">
                    <div class="flex items-start gap-4 p-4 bg-orange-50 rounded-lg">
                        <svg class="w-6 h-6 text-orange-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-orange-900">Acknowledgment Required</h3>
                            <p class="text-sm text-orange-700 mt-1">
                                Please read this policy carefully and acknowledge that you have understood its contents.
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('employee.policies.acknowledge', $policy) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="flex items-start">
                                <input type="checkbox" 
                                       required
                                       class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-3 text-sm text-gray-700">
                                    I confirm that I have read and understood this policy and agree to comply with its provisions.
                                </span>
                            </label>
                        </div>

                        <button type="submit" 
                                class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                            Acknowledge Policy
                        </button>
                    </form>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection