<?php
// app/Http/Controllers/Employee/LeaveRequestController.php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Http\Requests\Leave\StoreLeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of employee's leave requests
     */
    public function index()
    {
        $employee = Auth::user()->employee;

        $leaves = Leave::with(['leaveType', 'approvedBy'])
            ->where('employee_id', $employee->id)
            ->latest()
            ->paginate(15);

        return view('employee.leaves.index', compact('leaves'));
    }

    /**
     * Show the form for creating a new leave request
     */
    public function create()
    {
        $employee = Auth::user()->employee;
        $leaveTypes = LeaveType::active()->get();
        
        // Get current year leave balances
        $leaveBalances = LeaveBalance::where('employee_id', $employee->id)
            ->where('year', now()->year)
            ->with('leaveType')
            ->get()
            ->keyBy('leave_type_id');

        return view('employee.leaves.create', compact('leaveTypes', 'leaveBalances'));
    }

    /**
     * Store a newly created leave request
     */
    public function store(StoreLeaveRequest $request)
    {
        try {
            $employee = Auth::user()->employee;

            // Calculate number of days
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $numberOfDays = $startDate->diffInDays($endDate) + 1;

            // Check leave balance
            $balance = LeaveBalance::firstOrCreate(
                [
                    'employee_id' => $employee->id,
                    'leave_type_id' => $request->leave_type_id,
                    'year' => $startDate->year,
                ],
                [
                    'total_days' => LeaveType::find($request->leave_type_id)->days_per_year,
                    'used_days' => 0,
                    'remaining_days' => LeaveType::find($request->leave_type_id)->days_per_year,
                ]
            );

            if (!$balance->hasEnoughBalance($numberOfDays)) {
                return back()
                    ->withInput()
                    ->with('error', 'Insufficient leave balance. You have ' . $balance->remaining_days . ' days remaining.');
            }

            // Handle file upload
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')
                    ->store('leave-attachments/' . $employee->id, 'private');
            }

            // Create leave request
            $leave = Leave::create([
                'employee_id' => $employee->id,
                'leave_type_id' => $request->leave_type_id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'number_of_days' => $numberOfDays,
                'reason' => $request->reason,
                'status' => 'pending',
                'attachment' => $attachmentPath,
            ]);

            return redirect()
                ->route('employee.leaves.show', $leave)
                ->with('success', 'Leave request submitted successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to submit leave request: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified leave request
     */
    public function show(Leave $leave)
    {
        // Ensure employee can only view their own leaves
        if ($leave->employee_id !== Auth::user()->employee->id) {
            abort(403, 'Unauthorized access.');
        }

        $leave->load(['leaveType', 'approvedBy']);

        return view('employee.leaves.show', compact('leave'));
    }

    /**
     * Cancel a pending leave request
     */
    public function cancel(Leave $leave)
    {
        // Ensure employee can only cancel their own leaves
        if ($leave->employee_id !== Auth::user()->employee->id) {
            abort(403, 'Unauthorized access.');
        }

        if ($leave->status !== 'pending') {
            return back()->with('error', 'Only pending leave requests can be cancelled.');
        }

        try {
            $leave->cancel();

            return back()->with('success', 'Leave request cancelled successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel leave: ' . $e->getMessage());
        }
    }

    /**
     * Display leave balance
     */
    public function balance()
    {
        $employee = Auth::user()->employee;
        $year = now()->year;

        $leaveBalances = LeaveBalance::where('employee_id', $employee->id)
            ->where('year', $year)
            ->with('leaveType')
            ->get();

        // Get leave history
        $leaveHistory = Leave::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereYear('start_date', $year)
            ->with('leaveType')
            ->get()
            ->groupBy('leave_type_id');

        return view('employee.leaves.balance', compact('leaveBalances', 'leaveHistory', 'year'));
    }
}