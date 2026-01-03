{{-- resources/views/employee/queries/show.blade.php --}}

@extends('layouts.app')

@section('title', 'Query Details')
@section('header', $query->reference_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Query Details Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @if($query->isOverdue())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold text-red-900">Response Overdue</h4>
                        <p class="text-sm text-red-700 mt-1">This query required a response by {{ $query->response_deadline->format('F d, Y') }}. Please respond as soon as possible.</p>
                    </div>
                </div>
            </div>
        @endif

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
                </p>
            </div>

            @if($query->responded_at)
                <div>
                    <p class="text-sm font-medium text-gray-500">Responded On</p>
                    <p class="text-base text-gray-900 mt-2">{{ $query->responded_at->format('F d, Y') }}</p>
                </div>
            @endif
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
                <a href="{{ route('employee.queries.download', $query) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Document
                </a>
            </div>
        @endif

        <div class="pt-6 border-t border-gray-200">
            <a href="{{ route('employee.queries.index') }}" 
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Back to List
            </a>
        </div>
    </div>

    <!-- My Responses -->
    @if($query->responses->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">My Response(s)</h3>

            <div class="space-y-4">
                @foreach($query->responses as $response)
                    <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                        <p class="text-xs text-gray-500 mb-2">Submitted on {{ $response->created_at->format('F d, Y h:i A') }}</p>
                        <p class="text-gray-700 whitespace-pre-line">{{ $response->response }}</p>
                        
                        @if($response->document_path)
                            <div class="mt-3">
                                <a href="{{ route('employee.queries.response.download', $response) }}" 
                                   class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Download My Attachment
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Response Form -->
    @if($query->canRespond())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Submit Your Response</h3>
            
            <form action="{{ route('employee.queries.respond', $query) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label for="response" class="block text-sm font-medium text-gray-700 mb-2">
                        Your Response <span class="text-red-500">*</span>
                    </label>
                    <textarea id="response" 
                              name="response" 
                              rows="6"
                              required
                              minlength="10"
                              class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('response') border-red-500 @enderror"
                              placeholder="Provide your detailed response...">{{ old('response') }}</textarea>
                    @error('response')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Minimum 10 characters required</p>
                </div>

                <div class="mb-4">
                    <label for="document" class="block text-sm font-medium text-gray-700 mb-2">
                        Supporting Document (Optional)
                    </label>
                    <input type="file" 
                           id="document" 
                           name="document" 
                           accept=".pdf,.doc,.docx"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-1 text-xs text-gray-500">PDF or Word document (max 5MB)</p>
                </div>

                <button type="submit" 
                        class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                    Submit Response
                </button>
            </form>
        </div>
    @elseif($query->status === 'closed')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Query Closed</h3>
                <p class="text-sm text-gray-600">
                    This query was closed on {{ $query->closed_at ? $query->closed_at->format('F d, Y') : 'N/A' }}.
                    No further responses can be submitted.
                </p>
            </div>
        </div>
    @endif
</div>
@endsection