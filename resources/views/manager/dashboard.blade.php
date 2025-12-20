{{-- resources/views/manager/dashboard.blade.php --}}

@extends('layouts.app')

@section('title', 'Manager Dashboard')

@section('header', 'Manager Dashboard')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Team Overview</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-blue-50 rounded-lg p-4">
            <p class="text-sm font-medium text-blue-600">Team Size</p>
            <p class="text-3xl font-bold text-blue-900">{{ $teamSize }}</p>
        </div>
    </div>
</div>
@endsection
