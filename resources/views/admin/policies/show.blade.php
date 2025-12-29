{{-- resources/views/admin/policies/show.blade.php --}}

@extends('layouts.app')

@section('title', 'View Policy')
@section('header', $policy->title)

@section('content')
<div class="space-y-6">
    <!-- Policy Details Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $policy->title }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $policy->code }} • Version {{ $policy->version }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $policy->getStatusBadgeClass() }}">
                    {{ ucfirst($policy->status) }}
                </span>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $policy->getCategoryBadgeClass() }}">
                    {{ \App\Models\Policy::getCategories()[$policy->category] }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-500">Effective Date</p>
                <p class="text-base text-gray-900">
                    {{ $policy->effective_date ? $policy->effective_date->format('F d, Y') : 'Not set' }}
                </p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Review Date</p>
                <p class="text-base text-gray-900">
                    {{ $policy->review_date ? $policy->review_date->format('F d, Y') : 'Not set' }}
                </p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Created By</p>
                <p class="text-base text-gray-900">{{ $policy->creator->name ?? 'N/A' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Last Updated</p>
                <p class="text-base text-gray-900">{{ $policy->updated_at->format('F d, Y') }}</p>
            </div>

            @if($policy->description)
                <div class="md:col-span-2">
                    <p class="text-sm font-medium text-gray-500 mb-2">Description</p>
                    <p class="text-base text-gray-700">{{ $policy->description }}</p>
                </div>
            @endif
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 flex gap-3">
            <a href="{{ route('admin.policies.download', $policy) }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download PDF
            </a>
            <a href="{{ route('admin.policies.edit', $policy) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Edit Policy
            </a>
            <a href="{{ route('admin.policies.index') }}" 
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Back to List
            </a>
        </div>
    </div>

    <!-- Acknowledgment Statistics -->
    @if($policy->requires_acknowledgment)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Acknowledgment Status</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Total Employees</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $totalEmployees }}</p>
                </div>

                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Acknowledged</p>
                    <p class="text-3xl font-bold text-green-600">{{ $acknowledgedCount }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ number_format($policy->getAcknowledgmentRate(), 1) }}%</p>
                </div>

                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Pending</p>
                    <p class="text-3xl font-bold text-red-600">{{ $pendingCount }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ number_format(100 - $policy->getAcknowledgmentRate(), 1) }}%</p>
                </div>
            </div>

            <!-- Recent Acknowledgments -->
            @if($policy->acknowledgments->isNotEmpty())
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-3">Recent Acknowledgments</h4>
                    <div class="space-y-2">
                        @foreach($policy->acknowledgments->take(5) as $ack)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-blue-600 font-semibold text-sm">
                                            {{ $ack->employee->initials }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $ack->employee->full_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $ack->employee->employee_number }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-900">{{ $ack->acknowledged_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $ack->acknowledged_at->format('h:i A') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Pending Employees -->
            @if($pendingEmployees->isNotEmpty())
                <div>
                    <h4 class="text-md font-semibold text-gray-900 mb-3">Employees Pending Acknowledgment ({{ $pendingEmployees->count() }})</h4>
                    <div class="max-h-64 overflow-y-auto space-y-2">
                        @foreach($pendingEmployees as $employee)
                            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                        <span class="text-red-600 font-semibold text-sm">
                                            {{ $employee->initials }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $employee->full_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $employee->employee_number }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Version History -->
    @if($policy->versions->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Version History</h3>
            <div class="space-y-3">
                @foreach($policy->versions->sortByDesc('created_at') as $version)
                    <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0 w-16 text-center">
                            <p class="text-lg font-bold text-blue-600">v{{ $version->version }}</p>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $version->changes }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                By {{ $version->creator->name ?? 'System' }} • {{ $version->created_at->format('M d, Y h:i A') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection