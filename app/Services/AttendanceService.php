<?php

// app/Services/AttendanceService.php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    /**
     * Record employee check-in
     */
    public function checkIn(Employee $employee, ?Carbon $time = null): Attendance
    {
        $time = $time ?? now();
        $date = $time->toDateString();

        // Get or create attendance record
        $attendance = Attendance::firstOrCreate(
            [
                'employee_id' => $employee->id,
                'date' => $date,
            ],
            [
                'status' => 'present',
            ]
        );

        // Set check-in time
        $attendance->check_in = $time->format('H:i:s');

        // Determine if late (assuming 9:00 AM is standard)
        $standardTime = Carbon::parse($date . ' 09:00:00');
        if ($time->greaterThan($standardTime)) {
            $attendance->status = 'late';
        } else {
            $attendance->status = 'present';
        }

        $attendance->save();

        return $attendance;
    }

    /**
     * Record employee check-out
     */
    public function checkOut(Attendance $attendance, ?Carbon $time = null): Attendance
    {
        $time = $time ?? now();

        $attendance->check_out = $time->format('H:i:s');
        $attendance->calculateWorkHours();
        $attendance->save();

        return $attendance;
    }

    /**
     * Get attendance statistics for a date range
     */
    public function getStatistics(Carbon $startDate, Carbon $endDate, ?int $employeeId = null, ?int $departmentId = null): array
    {
        $query = Attendance::whereBetween('date', [$startDate, $endDate]);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        if ($departmentId) {
            $query->whereHas('employee', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $attendances = $query->get();

        return [
            'total_records' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'half_day' => $attendances->where('status', 'half_day')->count(),
            'on_leave' => $attendances->where('status', 'on_leave')->count(),
            'total_hours' => $attendances->sum('work_hours'),
            'avg_hours' => round($attendances->avg('work_hours') ?? 0, 2),
            'attendance_rate' => $attendances->count() > 0 
                ? round((($attendances->where('status', 'present')->count() + $attendances->where('status', 'late')->count()) / $attendances->count()) * 100, 2)
                : 0,
        ];
    }

    /**
     * Get employee attendance summary for a period
     */
    public function getEmployeeSummary(Employee $employee, Carbon $startDate, Carbon $endDate): array
    {
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $totalWorkingDays = $startDate->diffInDaysFiltered(function(Carbon $date) {
            return !$date->isWeekend();
        }, $endDate) + 1;

        return [
            'total_working_days' => $totalWorkingDays,
            'total_days' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'half_day' => $attendances->where('status', 'half_day')->count(),
            'on_leave' => $attendances->where('status', 'on_leave')->count(),
            'total_hours' => $attendances->sum('work_hours'),
            'avg_hours' => round($attendances->avg('work_hours') ?? 0, 2),
            'attendance_rate' => $totalWorkingDays > 0 
                ? round((($attendances->where('status', 'present')->count() + $attendances->where('status', 'late')->count()) / $totalWorkingDays) * 100, 2)
                : 0,
        ];
    }

    /**
     * Mark employees absent for a specific date
     */
    public function markAbsentEmployees(Carbon $date): array
    {
        $employees = Employee::where('employment_status', 'active')->get();

        $marked = 0;
        $skipped = 0;

        foreach ($employees as $employee) {
            // Check if attendance already exists
            if (Attendance::where('employee_id', $employee->id)->where('date', $date)->exists()) {
                $skipped++;
                continue;
            }

            // Check if on approved leave
            $onLeave = Leave::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->exists();

            Attendance::create([
                'employee_id' => $employee->id,
                'date' => $date,
                'status' => $onLeave ? 'on_leave' : 'absent',
                'notes' => $onLeave 
                    ? 'Automatically marked - Employee on approved leave'
                    : 'Automatically marked absent - No check-in record',
            ]);

            $marked++;
        }

        return [
            'marked' => $marked,
            'skipped' => $skipped,
        ];
    }

    /**
     * Bulk create attendance records
     */
    public function bulkCreate(array $employeeIds, Carbon $date, string $status, ?array $additionalData = []): array
    {
        DB::beginTransaction();

        try {
            $created = 0;
            $skipped = 0;

            foreach ($employeeIds as $employeeId) {
                // Check if already exists
                if (Attendance::where('employee_id', $employeeId)->where('date', $date)->exists()) {
                    $skipped++;
                    continue;
                }

                Attendance::create(array_merge([
                    'employee_id' => $employeeId,
                    'date' => $date,
                    'status' => $status,
                ], $additionalData));

                $created++;
            }

            DB::commit();

            return [
                'created' => $created,
                'skipped' => $skipped,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get department-wise attendance summary
     */
    public function getDepartmentSummary(Carbon $startDate, Carbon $endDate): array
    {
        $attendances = Attendance::with('employee.department')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $summary = [];

        foreach ($attendances->groupBy('employee.department_id') as $departmentId => $deptAttendances) {
            $department = $deptAttendances->first()->employee->department;

            $summary[] = [
                'department_id' => $departmentId,
                'department_name' => $department ? $department->name : 'N/A',
                'total' => $deptAttendances->count(),
                'present' => $deptAttendances->where('status', 'present')->count(),
                'absent' => $deptAttendances->where('status', 'absent')->count(),
                'late' => $deptAttendances->where('status', 'late')->count(),
                'half_day' => $deptAttendances->where('status', 'half_day')->count(),
                'on_leave' => $deptAttendances->where('status', 'on_leave')->count(),
                'total_hours' => $deptAttendances->sum('work_hours'),
                'avg_hours' => round($deptAttendances->avg('work_hours') ?? 0, 2),
                'attendance_rate' => $deptAttendances->count() > 0 
                    ? round((($deptAttendances->where('status', 'present')->count() + $deptAttendances->where('status', 'late')->count()) / $deptAttendances->count()) * 100, 2)
                    : 0,
            ];
        }

        return $summary;
    }

    /**
     * Calculate attendance rate for an employee
     */
    public function calculateAttendanceRate(Employee $employee, Carbon $startDate, Carbon $endDate): float
    {
        $totalWorkingDays = $startDate->diffInDaysFiltered(function(Carbon $date) {
            return !$date->isWeekend();
        }, $endDate) + 1;

        $presentDays = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereIn('status', ['present', 'late'])
            ->count();

        return $totalWorkingDays > 0 
            ? round(($presentDays / $totalWorkingDays) * 100, 2)
            : 0;
    }
}