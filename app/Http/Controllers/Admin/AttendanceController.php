<?php

// app/Http/Controllers/Admin/AttendanceController.php (Enhanced Version)

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance records
     */
    public function index(Request $request)
    {
        $query = Attendance::with('employee.department');

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        } else {
            // Default to today
            $query->whereDate('date', today());
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->latest('date')->latest('check_in')->paginate(20);

        // Get all employees for filter
        $employees = Employee::active()
            ->select('id', 'first_name', 'last_name', 'employee_number')
            ->orderBy('first_name')
            ->get();

        // Get departments for filter
        $departments = Department::select('id', 'name')->orderBy('name')->get();

        // Calculate statistics for the filtered date/range
        $date = $request->input('date', today());
        $stats = [
            'total' => Attendance::whereDate('date', $date)->count(),
            'present' => Attendance::whereDate('date', $date)->where('status', 'present')->count(),
            'absent' => Attendance::whereDate('date', $date)->where('status', 'absent')->count(),
            'late' => Attendance::whereDate('date', $date)->where('status', 'late')->count(),
            'on_leave' => Attendance::whereDate('date', $date)->where('status', 'on_leave')->count(),
        ];

        return view('admin.attendance.index', compact('attendances', 'employees', 'departments', 'stats'));
    }

    /**
     * Show the form for creating a new attendance record
     */
    public function create()
    {
        $employees = Employee::active()
            ->select('id', 'first_name', 'last_name', 'employee_number')
            ->orderBy('first_name')
            ->get();

        return view('admin.attendance.create', compact('employees'));
    }

    /**
     * Store a newly created attendance record
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date|before_or_equal:today',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:present,absent,half_day,late,on_leave',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            // Check if attendance already exists
            $exists = Attendance::where('employee_id', $request->employee_id)
                ->where('date', $request->date)
                ->exists();

            if ($exists) {
                return back()
                    ->withInput()
                    ->with('error', 'Attendance record already exists for this employee on this date.');
            }

            // Calculate work hours if both check in and check out are provided
            $workHours = null;
            if ($request->check_in && $request->check_out) {
                $checkIn = Carbon::parse($request->check_in);
                $checkOut = Carbon::parse($request->check_out);
                $workHours = $checkOut->diffInHours($checkIn, true);
            }

            Attendance::create([
                'employee_id' => $request->employee_id,
                'date' => $request->date,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'status' => $request->status,
                'work_hours' => $workHours,
                'notes' => $request->notes,
            ]);

            return redirect()
                ->route('admin.attendance.index')
                ->with('success', 'Attendance record created successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create attendance record: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified attendance
     */
    public function edit(Attendance $attendance)
    {
        $attendance->load('employee');
        return view('admin.attendance.edit', compact('attendance'));
    }

    /**
     * Update the specified attendance record
     */
    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:present,absent,half_day,late,on_leave',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            // Calculate work hours if both check in and check out are provided
            $workHours = null;
            if ($request->check_in && $request->check_out) {
                $checkIn = Carbon::parse($request->check_in);
                $checkOut = Carbon::parse($request->check_out);
                $workHours = $checkOut->diffInHours($checkIn, true);
            }

            $attendance->update([
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'status' => $request->status,
                'work_hours' => $workHours,
                'notes' => $request->notes,
            ]);

            return redirect()
                ->route('admin.attendance.index')
                ->with('success', 'Attendance record updated successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update attendance record: ' . $e->getMessage());
        }
    }

    /**
     * Delete attendance record
     */
    public function destroy(Attendance $attendance)
    {
        try {
            $attendance->delete();

            return redirect()
                ->route('admin.attendance.index')
                ->with('success', 'Attendance record deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete attendance record: ' . $e->getMessage());
        }
    }

    /**
     * Bulk create attendance for multiple employees
     */
    public function bulkCreate(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'status' => 'required|in:present,absent,half_day,late,on_leave',
        ]);

        try {
            DB::beginTransaction();

            $created = 0;
            $skipped = 0;

            foreach ($request->employee_ids as $employeeId) {
                // Check if already exists
                $exists = Attendance::where('employee_id', $employeeId)
                    ->where('date', $request->date)
                    ->exists();

                if (!$exists) {
                    Attendance::create([
                        'employee_id' => $employeeId,
                        'date' => $request->date,
                        'status' => $request->status,
                    ]);
                    $created++;
                } else {
                    $skipped++;
                }
            }

            DB::commit();

            $message = "Created {$created} attendance record(s).";
            if ($skipped > 0) {
                $message .= " Skipped {$skipped} duplicate(s).";
            }

            return redirect()
                ->route('admin.attendance.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create attendance records: ' . $e->getMessage());
        }
    }

    /**
     * Display attendance report
     */
    public function report(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $departmentId = $request->input('department_id');

        // Base query
        $query = Attendance::with('employee.department')
            ->whereBetween('date', [$startDate, $endDate]);

        // Filter by department
        if ($departmentId) {
            $query->whereHas('employee', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $attendances = $query->get();

        // Overall statistics
        $overallStats = [
            'total_records' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'half_day' => $attendances->where('status', 'half_day')->count(),
            'on_leave' => $attendances->where('status', 'on_leave')->count(),
            'total_hours' => $attendances->sum('work_hours'),
            'avg_hours' => round($attendances->avg('work_hours'), 2),
        ];

        // Department-wise statistics
        $departmentStats = $attendances->groupBy('employee.department_id')->map(function($deptAttendances) {
            return [
                'department' => $deptAttendances->first()->employee->department->name ?? 'N/A',
                'total' => $deptAttendances->count(),
                'present' => $deptAttendances->where('status', 'present')->count(),
                'absent' => $deptAttendances->where('status', 'absent')->count(),
                'late' => $deptAttendances->where('status', 'late')->count(),
                'avg_hours' => round($deptAttendances->avg('work_hours'), 2),
            ];
        });

        // Get all departments for filter
        $departments = Department::select('id', 'name')->orderBy('name')->get();

        return view('admin.attendance.report', compact('overallStats', 'departmentStats', 'departments', 'startDate', 'endDate'));
    }

    /**
     * Export attendance records to CSV
     */
    public function export(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $attendances = Attendance::with('employee.department')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        $filename = "attendance_report_{$startDate}_to_{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($attendances) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Date',
                'Employee Number',
                'Employee Name',
                'Department',
                'Check In',
                'Check Out',
                'Status',
                'Work Hours',
                'Notes'
            ]);

            // Data rows
            foreach ($attendances as $attendance) {
                fputcsv($file, [
                    $attendance->date->format('Y-m-d'),
                    $attendance->employee->employee_number,
                    $attendance->employee->full_name,
                    $attendance->employee->department->name ?? 'N/A',
                    $attendance->check_in ?? 'N/A',
                    $attendance->check_out ?? 'N/A',
                    ucfirst($attendance->status),
                    $attendance->work_hours ?? 'N/A',
                    $attendance->notes ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}