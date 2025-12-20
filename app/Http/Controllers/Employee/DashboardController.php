<?php

namespace App\Http\Controllers\Employee;


use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveBalance;
use App\Models\Attendance;
use App\Models\Query;
use App\Models\Complaint;
use App\Models\Survey;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
     public function index()
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            abort(403, 'No employee profile found.');
        }

        // Leave Balance Summary
        $currentYear = now()->year;
        $leaveBalances = LeaveBalance::where('employee_id', $employee->id)
            ->where('year', $currentYear)
            ->with('leaveType')
            ->get();

        // Recent Leaves
        $recentLeaves = Leave::where('employee_id', $employee->id)
            ->with('leaveType')
            ->latest()
            ->take(5)
            ->get();

        // Upcoming Leaves
        $upcomingLeaves = Leave::where('employee_id', $employee->id)
            ->approved()
            ->where('start_date', '>=', today())
            ->orderBy('start_date')
            ->take(3)
            ->get();

        // Pending Queries
        $pendingQueries = Query::where('employee_id', $employee->id)
            ->pending()
            ->count();

        // Open Complaints
        $openComplaints = Complaint::where('employee_id', $employee->id)
            ->whereIn('status', ['open', 'in_progress'])
            ->count();

        // Available Surveys
        $availableSurveys = Survey::active()
            ->available()
            ->whereDoesntHave('responses', function ($query) use ($employee) {
                $query->where('employee_id', $employee->id);
            })
            ->count();

        // Recent Attendance
        $recentAttendance = Attendance::where('employee_id', $employee->id)
            ->latest('date')
            ->take(7)
            ->get();

        // Today's Attendance
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->forDate(today())
            ->first();

        // Monthly Attendance Summary
        $monthlyAttendance = Attendance::where('employee_id', $employee->id)
            ->forMonth(now()->month, now()->year)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Quick Stats
        $stats = [
            'pending_leaves' => Leave::where('employee_id', $employee->id)->pending()->count(),
            'approved_leaves' => Leave::where('employee_id', $employee->id)->approved()->count(),
            'pending_queries' => $pendingQueries,
            'open_complaints' => $openComplaints,
        ];

        return view('employee.dashboard', compact(
            'employee',
            'leaveBalances',
            'recentLeaves',
            'upcomingLeaves',
            'pendingQueries',
            'openComplaints',
            'availableSurveys',
            'recentAttendance',
            'todayAttendance',
            'monthlyAttendance',
            'stats'
        ));
    }
}
