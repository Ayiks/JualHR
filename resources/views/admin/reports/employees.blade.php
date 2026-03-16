@extends('layouts.app')

@section('title', 'Employee Report')
@section('header', 'Employee Report')
@section('description', 'Headcount and workforce breakdown.')

@section('header-actions')
<a href="{{ route('admin.reports.export', ['type' => 'employees']) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
    Export CSV
</a>
<a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
    ← Reports
</a>
@endsection

@section('content')
<div class="space-y-5">

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $summary['total'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Total</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $summary['active'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Active</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-yellow-600">{{ $summary['on_leave'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">On Leave</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-red-600">{{ $summary['terminated'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Exited</p>
        </div>
    </div>

    {{-- Dept Breakdown --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">By Department</h3>
            <div class="space-y-2">
                @foreach($summary['by_dept'] as $dept)
                @if($dept->employees_count > 0)
                <div class="flex items-center gap-2 text-sm">
                    <span class="flex-1 text-gray-700 truncate">{{ $dept->name }}</span>
                    <span class="font-semibold text-gray-900">{{ $dept->employees_count }}</span>
                    <div class="w-24 bg-gray-100 rounded-full h-1.5">
                        <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $summary['total'] ? round($dept->employees_count / $summary['total'] * 100) : 0 }}%"></div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">By Employment Type</h3>
            <div class="space-y-2">
                @foreach($summary['by_type'] as $type)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-700">{{ ucfirst(str_replace('_', ' ', $type->employment_type)) }}</span>
                    <span class="font-semibold text-gray-900">{{ $type->total }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('admin.reports.employees') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Department</label>
                <select name="department_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="employment_status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    @foreach(['active','on_leave','suspended','terminated','resigned'] as $s)
                    <option value="{{ $s }}" @selected(request('employment_status') === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
                <select name="employment_type" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach(['full_time','part_time','contract','intern'] as $t)
                    <option value="{{ $t }}" @selected(request('employment_type') === $t)>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Gender</label>
                <select name="gender" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">All</option>
                    <option value="male" @selected(request('gender') === 'male')>Male</option>
                    <option value="female" @selected(request('gender') === 'female')>Female</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">Filter</button>
            <a href="{{ route('admin.reports.employees') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Reset</a>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Title</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($employees as $emp)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-semibold text-xs flex-shrink-0">{{ $emp->initials }}</div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $emp->full_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $emp->employee_number }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $emp->department?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $emp->job_title ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ ucfirst(str_replace('_', ' ', $emp->employment_type)) }}</td>
                        <td class="px-4 py-3">
                            @php
                            $badge = match($emp->employment_status) {
                                'active'    => 'bg-green-100 text-green-700',
                                'on_leave'  => 'bg-yellow-100 text-yellow-700',
                                'suspended' => 'bg-orange-100 text-orange-700',
                                default     => 'bg-gray-100 text-gray-600',
                            };
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                {{ ucfirst(str_replace('_', ' ', $emp->employment_status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $emp->date_of_joining?->format('M d, Y') ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-sm text-gray-400">No employees match the selected filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($employees->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $employees->links() }}</div>
        @endif
    </div>

</div>
@endsection
