{{-- resources/views/employee/policies/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Company Policies')
@section('header', 'Company Policies')
@section('description', 'View and acknowledge company policies')

@section('content')
<div class="space-y-6">
    <!-- Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Policies</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Acknowledged</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['acknowledged'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-orange-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Pending</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['pending'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Search policies..."
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="flex-1">
                <select name="category" 
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Filter
                </button>
                <a href="{{ route('employee.policies.index') }}" 
                   class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Policies Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($policies as $policy)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $policy->getCategoryBadgeClass() }}">
                            {{ \App\Models\Policy::getCategories()[$policy->category] }}
                        </span>
                        @if($policy->is_acknowledged)
                            <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $policy->title }}</h3>
                    <p class="text-sm text-gray-500 mb-4">{{ $policy->code }} • v{{ $policy->version }}</p>

                    @if($policy->description)
                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $policy->description }}</p>
                    @endif

                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <span class="text-xs text-gray-500">
                            {{ $policy->effective_date ? $policy->effective_date->format('M d, Y') : 'No date' }}
                        </span>
                        <a href="{{ route('employee.policies.show', $policy) }}" 
                           class="text-sm font-medium text-blue-600 hover:text-blue-700">
                            View Policy →
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-500 text-lg font-medium">No policies available</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($policies->hasPages())
        <div class="mt-6">
            {{ $policies->links() }}
        </div>
    @endif
</div>
@endsection