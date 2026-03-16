@extends('layouts.app')

@section('title', 'Leave Balances')
@section('header', 'Leave Balances')
@section('description', 'View and manage employee leave balances for the year.')

@section('header-actions')
<form method="GET" action="{{ route('admin.leave-balances.index') }}" class="flex items-center gap-2">
    <select name="year" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
        @foreach(range(now()->year + 1, now()->year - 3) as $y)
        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
        @endforeach
    </select>
</form>
<a href="{{ route('admin.leaves.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
    ← Leave Requests
</a>
@endsection

@section('content')

<!-- Update Balance Modal -->
<div x-data="{ open: false, employeeId: '', employeeName: '', leaveTypeId: '', leaveTypeName: '', totalDays: '' }"
     @open-balance-modal.window="
        open = true;
        employeeId = $event.detail.employeeId;
        employeeName = $event.detail.employeeName;
        leaveTypeId = $event.detail.leaveTypeId;
        leaveTypeName = $event.detail.leaveTypeName;
        totalDays = $event.detail.totalDays;
     ">
    <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display:none">
        <div class="flex min-h-screen items-center justify-center px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="open = false"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6" @click.stop>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Update Leave Balance</h3>
                <p class="text-sm text-gray-500 mb-4">
                    <span x-text="employeeName"></span> — <span x-text="leaveTypeName"></span>
                </p>
                <form method="POST" action="{{ route('admin.leave-balances.update') }}">
                    @csrf
                    <input type="hidden" name="employee_id" :value="employeeId">
                    <input type="hidden" name="leave_type_id" :value="leaveTypeId">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">
                            Total Days Allowed
                        </label>
                        <input type="number" name="total_days" :value="totalDays" min="0" step="0.5"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                               required>
                    </div>
                    <div class="flex items-center justify-end gap-3">
                        <button type="button" @click="open = false" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            Update Balance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($balances->isEmpty())
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
    </svg>
    <p class="text-gray-500 font-medium">No leave balances found for {{ $year }}</p>
    <p class="text-sm text-gray-400 mt-1">Leave balances are created automatically when employees submit leave requests.</p>
</div>
@else

<div class="space-y-4">
    @foreach($balances as $employeeId => $employeeBalances)
    @php $emp = $employeeBalances->first()->employee; @endphp
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Employee Header -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center gap-4">
            <div class="flex-shrink-0">
                @if($emp->profile_photo)
                <img class="w-10 h-10 rounded-full object-cover" src="{{ Storage::url($emp->profile_photo) }}" alt="{{ $emp->full_name }}">
                @else
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-sm">
                    {{ $emp->initials }}
                </div>
                @endif
            </div>
            <div>
                <p class="font-semibold text-gray-900 text-sm">{{ $emp->full_name }}</p>
                <p class="text-xs text-gray-500">{{ $emp->employee_number }} · {{ $emp->department?->name ?? 'No Department' }}</p>
            </div>
        </div>

        <!-- Balance Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-gray-500 uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3 text-left font-medium">Leave Type</th>
                        <th class="px-6 py-3 text-center font-medium">Total Days</th>
                        <th class="px-6 py-3 text-center font-medium">Used</th>
                        <th class="px-6 py-3 text-center font-medium">Remaining</th>
                        <th class="px-6 py-3 text-center font-medium">Usage</th>
                        <th class="px-6 py-3 text-right font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($employeeBalances as $balance)
                    @php
                        $pct = $balance->total_days > 0 ? ($balance->used_days / $balance->total_days) * 100 : 0;
                        $barColor = $pct >= 90 ? 'bg-red-500' : ($pct >= 70 ? 'bg-yellow-500' : 'bg-green-500');
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-900">{{ $balance->leaveType->name }}</span>
                            @if($balance->leaveType->code)
                            <span class="ml-1 text-xs text-gray-400">({{ $balance->leaveType->code }})</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center font-medium text-gray-700">{{ number_format($balance->total_days, 1) }}</td>
                        <td class="px-6 py-4 text-center text-gray-600">{{ number_format($balance->used_days, 1) }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-semibold {{ $balance->remaining_days <= 0 ? 'text-red-600' : ($balance->remaining_days <= 2 ? 'text-yellow-600' : 'text-green-600') }}">
                                {{ number_format($balance->remaining_days, 1) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-200 rounded-full h-1.5 min-w-[60px]">
                                    <div class="{{ $barColor }} h-1.5 rounded-full" style="width: {{ min($pct, 100) }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 w-8 text-right">{{ round($pct) }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button type="button"
                                @click="$dispatch('open-balance-modal', {
                                    employeeId: '{{ $emp->id }}',
                                    employeeName: '{{ addslashes($emp->full_name) }}',
                                    leaveTypeId: '{{ $balance->leave_type_id }}',
                                    leaveTypeName: '{{ addslashes($balance->leaveType->name) }}',
                                    totalDays: '{{ $balance->total_days }}'
                                })"
                                class="text-xs text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                Edit
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>

@endif
@endsection
