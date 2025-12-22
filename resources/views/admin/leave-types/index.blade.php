{{-- resources/views/admin/leave-types/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Leave Types')
@section('header', 'Leave Types')
@section('description', 'Manage leave types and allocations')

@section('header-actions')
    <a href="{{ route('admin.leave-types.create') }}" 
        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Add Leave Type
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($leaveTypes as $leaveType)
        <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $leaveType->name }}</h3>
                        @if($leaveType->is_active)
                            <span class="px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span>
                        @else
                            <span class="px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Inactive</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 mb-2">Code: <span class="font-mono">{{ $leaveType->code }}</span></p>
                    <p class="text-sm text-gray-600 line-clamp-2">
                        {{ $leaveType->description ?? 'No description' }}
                    </p>
                </div>
            </div>

            <!-- Stats -->
            <div class="flex items-center gap-4 mb-4 pb-4 border-b border-gray-100">
                <div>
                    <p class="text-xs text-gray-500">Days/Year</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $leaveType->days_per_year }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Total Requests</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $leaveType->leaves_count }}</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.leave-types.edit', $leaveType) }}" 
                    class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-50 border border-blue-200 rounded-lg text-sm font-medium text-blue-700 hover:bg-blue-100">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                <form action="{{ route('admin.leave-types.destroy', $leaveType) }}" method="POST" class="inline"
                    onsubmit="return confirm('Are you sure you want to delete this leave type?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center p-2 bg-red-50 border border-red-200 rounded-lg text-red-600 hover:bg-red-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="col-span-3 text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No leave types</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new leave type.</p>
        </div>
    @endforelse
</div>
@endsection
