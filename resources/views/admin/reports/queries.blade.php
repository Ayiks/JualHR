@extends('layouts.app')

@section('title', 'Queries & Complaints Report')
@section('header', 'Queries & Complaints Report')
@section('description', 'Status overview of all queries and complaints.')

@section('header-actions')
<a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
    ← Reports
</a>
@endsection

@section('content')
<div class="space-y-5">

    {{-- Query Summary --}}
    <div>
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Queries</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <p class="text-2xl font-bold text-gray-900">{{ $summary['total'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Total</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <p class="text-2xl font-bold text-yellow-600">{{ $summary['open'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Open</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $summary['responded'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Responded</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <p class="text-2xl font-bold text-green-600">{{ $summary['closed'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Closed</p>
            </div>
        </div>
    </div>

    {{-- Complaint Summary --}}
    <div>
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Complaints</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <p class="text-2xl font-bold text-yellow-600">{{ $summary['complaints']['open'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Open</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $summary['complaints']['in_progress'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">In Progress</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <p class="text-2xl font-bold text-green-600">{{ $summary['complaints']['resolved'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Resolved</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <p class="text-2xl font-bold text-gray-600">{{ $summary['complaints']['closed'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Closed</p>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('admin.reports.queries') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">All</option>
                    @foreach(['open','responded','closed'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">Filter</button>
            <a href="{{ route('admin.reports.queries') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Reset</a>
        </form>
    </div>

    {{-- Queries Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Queries</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issued By</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($queries as $query)
                    @php
                    $badge = match($query->status) {
                        'open'      => 'bg-yellow-100 text-yellow-700',
                        'responded' => 'bg-blue-100 text-blue-700',
                        'closed'    => 'bg-green-100 text-green-700',
                        default     => 'bg-gray-100 text-gray-600',
                    };
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $query->employee?->full_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ Str::limit($query->subject, 50) }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $query->issuedBy?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $query->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                {{ ucfirst($query->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">No queries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($queries->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $queries->links() }}</div>
        @endif
    </div>

</div>
@endsection
