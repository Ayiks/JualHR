{{-- resources/views/employee/queries/index.blade.php --}}

@extends('layouts.app')

@section('title', 'My Queries')
@section('header', 'My Queries & Warnings')
@section('description', 'View and respond to queries issued to you')

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Open</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['open'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Responded</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['responded'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Overdue</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['overdue'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <select name="status" 
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="responded" {{ request('status') == 'responded' ? 'selected' : '' }}>Responded</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    <option value="escalated" {{ request('status') == 'escalated' ? 'selected' : '' }}>Escalated</option>
                </select>
            </div>

            <div class="flex-1">
                <select name="type" 
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach(\App\Models\Query::getTypes() as $key => $label)
                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
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
                <a href="{{ route('employee.queries.index') }}" 
                   class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Queries List -->
    <div class="space-y-4">
        @forelse($queries as $query)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow {{ $query->isOverdue() ? 'border-l-4 border-l-red-500' : '' }}">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $query->subject }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $query->reference_number }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $query->getStatusBadgeClass() }}">
                            {{ ucfirst($query->status) }}
                        </span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $query->getTypeBadgeClass() }}">
                            {{ \App\Models\Query::getTypes()[$query->type] }}
                        </span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $query->getSeverityBadgeClass() }}">
                            {{ ucfirst($query->severity) }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 text-sm">
                    <div>
                        <p class="text-gray-500">Issued By</p>
                        <p class="font-medium text-gray-900">{{ $query->issuer->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Issued Date</p>
                        <p class="font-medium text-gray-900">{{ $query->issued_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Response Deadline</p>
                        <p class="font-medium text-gray-900">
                            {{ $query->response_deadline ? $query->response_deadline->format('M d, Y') : 'Not set' }}
                            @if($query->isOverdue())
                                <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Overdue!
                                </span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="mb-4">
                    <p class="text-gray-700">{{ Str::limit($query->description, 150) }}</p>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        @if($query->responses->isNotEmpty())
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            <span>{{ $query->responses->count() }} Response(s)</span>
                        @endif
                    </div>

                    <a href="{{ route('employee.queries.show', $query) }}" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                        View Details
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-500 text-lg font-medium">No queries found</p>
                <p class="text-gray-400 text-sm mt-1">You don't have any queries at this time</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($queries->hasPages())
        <div class="mt-6">
            {{ $queries->links() }}
        </div>
    @endif
</div>
@endsection