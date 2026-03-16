@extends('layouts.app')

@section('title', 'Complaints')
@section('header', 'Complaint Management')
@section('description', 'Review and manage employee complaints.')

@section('content')
<div class="space-y-6">

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Total</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Open</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['open'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-sm text-gray-500">In Progress</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['in_progress'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Resolved</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['resolved'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    <option value="open" @selected(request('status') === 'open')>Open</option>
                    <option value="in_progress" @selected(request('status') === 'in_progress')>In Progress</option>
                    <option value="resolved" @selected(request('status') === 'resolved')>Resolved</option>
                    <option value="closed" @selected(request('status') === 'closed')>Closed</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Priority</label>
                <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">All Priorities</option>
                    <option value="low" @selected(request('priority') === 'low')>Low</option>
                    <option value="medium" @selected(request('priority') === 'medium')>Medium</option>
                    <option value="high" @selected(request('priority') === 'high')>High</option>
                    <option value="urgent" @selected(request('priority') === 'urgent')>Urgent</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Category</label>
                <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">All Categories</option>
                    <option value="harassment" @selected(request('category') === 'harassment')>Harassment</option>
                    <option value="discrimination" @selected(request('category') === 'discrimination')>Discrimination</option>
                    <option value="workplace_safety" @selected(request('category') === 'workplace_safety')>Workplace Safety</option>
                    <option value="misconduct" @selected(request('category') === 'misconduct')>Misconduct</option>
                    <option value="policy_violation" @selected(request('category') === 'policy_violation')>Policy Violation</option>
                    <option value="management" @selected(request('category') === 'management')>Management</option>
                    <option value="compensation" @selected(request('category') === 'compensation')>Compensation</option>
                    <option value="other" @selected(request('category') === 'other')>Other</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">Filter</button>
                <a href="{{ route('admin.complaints.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">Clear</a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($complaints as $complaint)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            @if($complaint->is_anonymous)
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700 italic">Anonymous</p>
                                </div>
                            </div>
                            @else
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-semibold text-xs">
                                    {{ $complaint->employee->initials }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $complaint->employee->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $complaint->employee->employee_number }}</p>
                                </div>
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-900 font-medium">{{ Str::limit($complaint->subject, 45) }}</p>
                            @if($complaint->assignedTo)
                            <p class="text-xs text-gray-400 mt-0.5">Assigned: {{ $complaint->assignedTo->full_name }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ ucwords(str_replace('_', ' ', $complaint->category)) }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $complaint->getPriorityBadgeClass() }}">
                                {{ ucfirst($complaint->priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $complaint->getStatusBadgeClass() }}">
                                {{ ucwords(str_replace('_', ' ', $complaint->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $complaint->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.complaints.show', $complaint) }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <p class="text-gray-500">No complaints found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($complaints->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $complaints->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
