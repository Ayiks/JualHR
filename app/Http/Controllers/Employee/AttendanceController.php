<?php
// app/Http/Controllers/Employee/AttendanceController.php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display attendance dashboard
     */
    public function index()
    {
        $employee = Auth::user()->employee;

        // Get today's attendance
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->forDate(today())
            ->first();

        // Get this month's attendance
        $monthAttendance = Attendance::where('employee_id', $employee->id)
            ->forMonth(now()->month, now()->year)
            ->orderBy('date', 'desc')
            ->get();

        // Calculate statistics
        $stats = [
            'total_days' => $monthAttendance->count(),
            'present_days' => $monthAttendance->where('status', 'present')->count(),
            'absent_days' => $monthAttendance->where('status', 'absent')->count(),
            'late_days' => $monthAttendance->where('status', 'late')->count(),
            'total_hours' => $monthAttendance->sum('work_hours'),
        ];

        return view('employee.attendance.index', compact('todayAttendance', 'monthAttendance', 'stats'));
    }

    /**
     * Check in
     */
    public function checkIn(Request $request)
    {
        try {
            $employee = Auth::user()->employee;

            // Check if already checked in today
            $existingAttendance = Attendance::where('employee_id', $employee->id)
                ->forDate(today())
                ->first();

            if ($existingAttendance && $existingAttendance->check_in) {
                return back()->with('error', 'You have already checked in today.');
            }

            // Create or update attendance
            $attendance = Attendance::firstOrCreate(
                [
                    'employee_id' => $employee->id,
                    'date' => today(),
                ],
                [
                    'status' => 'present',
                ]
            );

            $attendance->checkIn();

            // Check if late (assuming 9:00 AM is standard time)
            if ($attendance->isLate()) {
                $attendance->update(['status' => 'late']);
            }

            return back()->with('success', 'Checked in successfully at ' . now()->format('h:i A'));

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to check in: ' . $e->getMessage());
        }
    }

    /**
     * Check out
     */
    public function checkOut(Request $request)
    {
        try {
            $employee = Auth::user()->employee;

            $attendance = Attendance::where('employee_id', $employee->id)
                ->forDate(today())
                ->first();

            if (!$attendance || !$attendance->check_in) {
                return back()->with('error', 'Please check in first before checking out.');
            }

            if ($attendance->check_out) {
                return back()->with('error', 'You have already checked out today.');
            }

            $attendance->checkOut();

            return back()->with('success', 'Checked out successfully at ' . now()->format('h:i A'));

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to check out: ' . $e->getMessage());
        }
    }

    /**
     * Display attendance history
     */
    public function history(Request $request)
    {
        $employee = Auth::user()->employee;

        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->paginate(31);

        // Calculate statistics
        $stats = [
            'total_days' => $attendances->count(),
            'present_days' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->where('status', 'present')
                ->count(),
            'absent_days' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->where('status', 'absent')
                ->count(),
            'late_days' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->where('status', 'late')
                ->count(),
            'total_hours' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->sum('work_hours'),
        ];

        return view('employee.attendance.history', compact('attendances', 'stats', 'month', 'year'));
    }
}