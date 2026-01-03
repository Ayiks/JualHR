{{-- resources/views/admin/queries/show.blade.php --}}

@extends('layouts.app')

@section('title', 'Query Details')
@section('header', $query->reference_number)

@section('content')
<div class="space-y-6">
    <!-- Query Details Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $query->subject }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $query->reference_number }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $query->getStatusBadgeClass() }}">
                    {{ ucfirst($query->status) }}
                </span>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $query->getTypeBadgeClass() }}">
                    {{ \App\Models\Query::getTypes()[$query->type] }}
                </span>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $query->getSeverityBadgeClass() }}">
                    {{ ucfirst($query->severity) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-sm font-medium text-gray-500">Employee</p>
                <div class="flex items-center gap-3 mt-2">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="text-blue-600 font-semibold text-sm">{{ $query->employee->initials }}</span>
                    </div>
                    <div>
                        <p class="text-base font-medium text-gray-900">{{ $query->employee->full_name }}</p>
                        <p class="text-xs text-gray-500">{{ $query->employee->employee_number }} • {{ $query->employee->department->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Issued By</p>
                <p class="text-base text-gray-900 mt-2">{{ $query->issuer->name ?? 'N/A' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Issued Date</p>
                <p class="text-base text-gray-900 mt-2">{{ $query->issued_date->format('F d, Y') }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Response Deadline</p>
                <p class="text-base text-gray-900 mt-2">
                    {{ $query->response_deadline ? $query->response_deadline->format('F d, Y') : 'Not set' }}
                    @if($query->isOverdue())
                        <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                            Overdue
                        </span>
                    @endif
                </p>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Description</h3>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-gray-700 whitespace-pre-line">{{ $query->description }}</p>
            </div>
        </div>

        @if($query->action_required)
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Action Required</h3>
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-gray-700 whitespace-pre-line">{{ $query->action_required }}</p>
                </div>
            </div>
        @endif

        @if($query->document_path)
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Attached Document</h3>
                <a href="{{ route('admin.queries.download', $query) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Document
                </a>
            </div>
        @endif

        <div class="pt-6 border-t border-gray-200 flex gap-3">
            <a href="{{ route('admin.queries.edit', $query) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Edit Query
            </a>
            <a href="{{ route('admin.queries.index') }}" 
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Back to List
            </a>
        </div>
    </div>

    <!-- Employee Responses -->
    @if($query->responses->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                Employee Response{{ $query->responses->count() > 1 ? 's' : '' }} ({{ $query->responses->count() }})
            </h3>

            <div class="space-y-4">
                @foreach($query->responses as $response)
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-start gap-4 mb-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 font-semibold text-sm">{{ $response->employee->initials }}</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $response->employee->full_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $response->created_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pl-14">
                            <p class="text-gray-700 whitespace-pre-line">{{ $response->response }}</p>
                            
                            @if($response->document_path)
                                <div class="mt-3">
                                    <a href="{{ route('admin.queries.response.download', $response) }}" 
                                       class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Download Attachment
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Close Query Form -->
    @if($query->status !== 'closed')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Close Query</h3>
            
            <form action="{{ route('admin.queries.close', $query) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="closure_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Closure Notes <span class="text-red-500">*</span>
                    </label>
                    <textarea id="closure_notes" 
                              name="closure_notes" 
                              rows="4"
                              required
                              class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Provide notes on how this query was resolved..."></textarea>
                </div>

                <button type="submit" 
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Close Query
                </button>
            </form>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Query Closed</h3>
            
            <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-gray-600 mb-2">
                    <strong>Closed by:</strong> {{ $query->closer->name ?? 'N/A' }}
                </p>
                <p class="text-sm text-gray-600 mb-2">
                    <strong>Closed on:</strong> {{ $query->closed_at ? $query->closed_at->format('F d, Y h:i A') : 'N/A' }}
                </p>
                @if($query->closure_notes)
                    <p class="text-sm text-gray-600">
                        <strong>Notes:</strong><br>
                        <span class="whitespace-pre-line">{{ $query->closure_notes }}</span>
                    </p>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection