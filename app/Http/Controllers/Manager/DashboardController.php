<?php
// app/Http/Controllers/Manager/DashboardController.php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $manager = Auth::user()->employee;

        if (!$manager) {
            return redirect()->route('employee.dashboard');
        }

        // Team Statistics
        $teamSize = $manager->subordinates()->count();
        $teamOnLeaveToday = $manager->subordinates()
            ->whereHas('leaves', function ($query) {
                $query->approved()
                    ->where('start_date', '<=', today())
                    ->where('end_date', '>=', today());
            })
            ->count();

        $pendingLeaveApprovals = Leave::whereHas('employee', function ($query) use ($manager) {
                $query->where('line_manager_id', $manager->id);
            })
            ->pending()
            ->count();

        // Team Members
        $teamMembers = $manager->subordinates()
            ->with(['department', 'leaves' => function ($query) {
                $query->approved()
                    ->where('start_date', '<=', today())
                    ->where('end_date', '>=', today());
            }])
            ->get();

        // Pending Leave Requests
        $pendingLeaves = Leave::with(['employee', 'leaveType'])
            ->whereHas('employee', function ($query) use ($manager) {
                $query->where('line_manager_id', $manager->id);
            })
            ->pending()
            ->latest()
            ->take(5)
            ->get();

        // Team Attendance Today
        $teamAttendance = Attendance::whereHas('employee', function ($query) use ($manager) {
                $query->where('line_manager_id', $manager->id);
            })
            ->forDate(today())
            ->with('employee')
            ->get();

        return view('manager.dashboard', compact(
            'teamSize',
            'teamOnLeaveToday',
            'pendingLeaveApprovals',
            'teamMembers',
            'pendingLeaves',
            'teamAttendance'
        ));
    }
}