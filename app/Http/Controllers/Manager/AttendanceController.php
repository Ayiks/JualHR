<?php

// app/Http/Controllers/Manager/AttendanceController.php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display team attendance overview
     */
    public function index(Request $request)
    {

          // Check if user is authenticated
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }

        $user = Auth::user();
        
        // Check if user has employee relationship
        if (!$user->employee) {
            abort(403, 'User does not have an employee record');
        }

        $manager = $user->employee;
        
        // Get manager's team
        $teamMembers = Employee::where('line_manager_id', $manager->id)
            ->where('employment_status', 'active')
            ->get();

        $date = $request->input('date', today());

        // Get today's attendance for team
        $attendances = Attendance::whereIn('employee_id', $teamMembers->pluck('id'))
            ->whereDate('date', $date)
            ->with('employee')
            ->get();

        // Calculate stats
        $stats = [
            'team_size' => $teamMembers->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'on_leave' => $attendances->where('status', 'on_leave')->count(),
        ];

        return view('manager.attendance.index', compact('attendances', 'teamMembers', 'stats', 'date'));
    }

    /**
     * Display team attendance table view
     */
    public function team(Request $request)
    {
         // Check if user is authenticated
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }

        $user = Auth::user();
        
        // Check if user has employee relationship
        if (!$user->employee) {
            abort(403, 'User does not have an employee record');
        }
        $manager = $user->employee;
        
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // Get manager's team
        $teamMembers = Employee::where('line_manager_id', $manager->id)
            ->where('employment_status', 'active')
            ->get();

        // Get attendance records for the month
        $attendances = Attendance::whereIn('employee_id', $teamMembers->pluck('id'))
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->with('employee')
            ->orderBy('date', 'desc')
            ->get();

        // Group by employee
        $attendanceByEmployee = $attendances->groupBy('employee_id');

        // Calculate stats per employee
        $employeeStats = [];
        foreach ($teamMembers as $employee) {
            $empAttendances = $attendanceByEmployee->get($employee->id, collect());
            
            $employeeStats[$employee->id] = [
                'total_days' => $empAttendances->count(),
                'present' => $empAttendances->where('status', 'present')->count(),
                'absent' => $empAttendances->where('status', 'absent')->count(),
                'late' => $empAttendances->where('status', 'late')->count(),
                'total_hours' => $empAttendances->sum('work_hours'),
            ];
        }

        return view('manager.attendance.team', compact('teamMembers', 'employeeStats', 'month', 'year'));
    }

    /**
     * Display attendance report
     */
    public function report(Request $request)
    {
          // Check if user is authenticated
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }

        $user = Auth::user();
        
        // Check if user has employee relationship
        if (!$user->employee) {
            abort(403, 'User does not have an employee record');
        }

        $manager = $user->employee;
        
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Get manager's team
        $teamMembers = Employee::where('line_manager_id', $manager->id)
            ->where('employment_status', 'active')
            ->get();

        // Get attendance records for the period
        $attendances = Attendance::whereIn('employee_id', $teamMembers->pluck('id'))
            ->whereBetween('date', [$startDate, $endDate])
            ->with('employee')
            ->get();

        // Overall stats
        $overallStats = [
            'total_records' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'total_hours' => $attendances->sum('work_hours'),
            'avg_hours' => $attendances->avg('work_hours'),
        ];

        // Per employee stats
        $employeeStats = [];
        foreach ($teamMembers as $employee) {
            $empAttendances = $attendances->where('employee_id', $employee->id);
            
            $employeeStats[] = [
                'employee' => $employee,
                'total_days' => $empAttendances->count(),
                'present' => $empAttendances->where('status', 'present')->count(),
                'absent' => $empAttendances->where('status', 'absent')->count(),
                'late' => $empAttendances->where('status', 'late')->count(),
                'total_hours' => $empAttendances->sum('work_hours'),
                'avg_hours' => $empAttendances->avg('work_hours') ?? 0,
                'attendance_rate' => $empAttendances->count() > 0 
                    ? round(($empAttendances->where('status', 'present')->count() / $empAttendances->count()) * 100, 2)
                    : 0,
            ];
        }

        return view('manager.attendance.report', compact('employeeStats', 'overallStats', 'startDate', 'endDate'));
    }
}