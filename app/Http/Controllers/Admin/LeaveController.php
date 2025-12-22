<?php
// app/Http/Controllers/Admin/LeaveController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveBalance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    /**
     * Display a listing of leave requests
     */
    public function index(Request $request)
    {
        $query = Leave::with(['employee', 'leaveType', 'approvedBy']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by leave type
        if ($request->filled('leave_type')) {
            $query->where('leave_type_id', $request->leave_type);
        }

        // Filter by employee
        if ($request->filled('employee')) {
            $query->where('employee_id', $request->employee);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        $leaves = $query->latest()->paginate(15);

        return view('admin.leaves.index', compact('leaves'));
    }

    /**
     * Display the specified leave request
     */
    public function show(Leave $leave)
    {
        $leave->load(['employee.department', 'leaveType', 'approvedBy']);

        return view('admin.leaves.show', compact('leave'));
    }

    /**
     * Approve leave request
     */
    public function approve(Request $request, Leave $leave)
    {
        if ($leave->status !== 'pending') {
            return back()->with('error', 'Only pending leaves can be approved.');
        }

        try {
            $approver = Auth::user()->employee;
            $leave->approve($approver->id);

            return back()->with('success', 'Leave request approved successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to approve leave: ' . $e->getMessage());
        }
    }

    /**
     * Reject leave request
     */
    public function reject(Request $request, Leave $leave)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($leave->status !== 'pending') {
            return back()->with('error', 'Only pending leaves can be rejected.');
        }

        try {
            $approver = Auth::user()->employee;
            $leave->reject($approver->id, $request->rejection_reason);

            return back()->with('success', 'Leave request rejected.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reject leave: ' . $e->getMessage());
        }
    }

    /**
     * Display leave balances for all employees
     */
    public function balances(Request $request)
    {
        $year = $request->input('year', now()->year);
        
        $balances = LeaveBalance::with(['employee', 'leaveType'])
            ->where('year', $year)
            ->get()
            ->groupBy('employee_id');

        return view('admin.leaves.balances', compact('balances', 'year'));
    }

    /**
     * Update leave balance for an employee
     */
    public function updateBalance(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'year' => 'required|integer',
            'total_days' => 'required|numeric|min:0',
        ]);

        try {
            $balance = LeaveBalance::updateOrCreate(
                [
                    'employee_id' => $request->employee_id,
                    'leave_type_id' => $request->leave_type_id,
                    'year' => $request->year,
                ],
                [
                    'total_days' => $request->total_days,
                ]
            );

            $balance->updateBalance();

            return back()->with('success', 'Leave balance updated successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update balance: ' . $e->getMessage());
        }
    }
}