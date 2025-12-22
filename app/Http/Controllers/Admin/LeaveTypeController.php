<?php
// app/Http/Controllers/Admin/LeaveTypeController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use App\Http\Requests\LeaveType\StoreLeaveTypeRequest;
use App\Http\Requests\LeaveType\UpdateLeaveTypeRequest;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    /**
     * Display a listing of leave types
     */
    public function index()
    {
        $leaveTypes = LeaveType::withCount('leaves')->latest()->get();

        return view('admin.leave-types.index', compact('leaveTypes'));
    }

    /**
     * Show the form for creating a new leave type
     */
    public function create()
    {
        return view('admin.leave-types.create');
    }

    /**
     * Store a newly created leave type
     */
    public function store(StoreLeaveTypeRequest $request)
    {
        try {
            LeaveType::create([
                'name' => $request->name,
                'code' => $request->code,
                'days_per_year' => $request->days_per_year,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true),
            ]);

            return redirect()
                ->route('admin.leave-types.index')
                ->with('success', 'Leave type created successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create leave type: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified leave type
     */
    public function edit(LeaveType $leaveType)
    {
        return view('admin.leave-types.edit', compact('leaveType'));
    }

    /**
     * Update the specified leave type
     */
    public function update(UpdateLeaveTypeRequest $request, LeaveType $leaveType)
    {
        try {
            $leaveType->update([
                'name' => $request->name,
                'code' => $request->code,
                'days_per_year' => $request->days_per_year,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active'),
            ]);

            return redirect()
                ->route('admin.leave-types.index')
                ->with('success', 'Leave type updated successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update leave type: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified leave type
     */
    public function destroy(LeaveType $leaveType)
    {
        try {
            // Check if leave type has any leave requests
            if ($leaveType->leaves()->count() > 0) {
                return back()->with('error', 'Cannot delete leave type with existing leave requests.');
            }

            $leaveType->delete();

            return redirect()
                ->route('admin.leave-types.index')
                ->with('success', 'Leave type deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete leave type: ' . $e->getMessage());
        }
    }
} 