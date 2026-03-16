<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Support\Facades\Auth;

class LeaveApprovalController extends Controller
{
    /**
     * View team leave schedule (read-only — HR approves).
     */
    public function index()
    {
        $manager = Auth::user()->employee;

        $leaves = Leave::whereHas('employee', function ($q) use ($manager) {
                $q->where('line_manager_id', $manager->id);
            })
            ->with(['employee', 'leaveType'])
            ->latest('start_date')
            ->paginate(20);

        return view('manager.leaves.index', compact('leaves'));
    }

    /**
     * View a single leave request from a team member.
     */
    public function show(Leave $leave)
    {
        $manager = Auth::user()->employee;

        // Ensure the leave belongs to a direct report
        if ($leave->employee->line_manager_id !== $manager->id) {
            abort(403);
        }

        $leave->load(['employee.department', 'leaveType', 'approvedBy']);

        return view('manager.leaves.show', compact('leave'));
    }
}
