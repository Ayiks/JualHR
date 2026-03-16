<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Complaint;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Query;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $stats = [
            'total_employees'    => Employee::count(),
            'active_employees'   => Employee::where('employment_status', 'active')->count(),
            'pending_leaves'     => Leave::where('status', 'pending')->count(),
            'approved_leaves'    => Leave::where('status', 'approved')
                                        ->whereMonth('start_date', now()->month)->count(),
            'attendance_today'   => Attendance::whereDate('date', today())->count(),
            'open_queries'       => Query::where('status', 'open')->count(),
            'open_complaints'    => Complaint::whereIn('status', ['open', 'in_progress'])->count(),
            'departments'        => Department::count(),
        ];

        return view('admin.reports.index', compact('stats'));
    }

    public function employees(Request $request)
    {
        $query = Employee::with('department')
            ->when($request->department_id, fn($q) => $q->where('department_id', $request->department_id))
            ->when($request->employment_status, fn($q) => $q->where('employment_status', $request->employment_status))
            ->when($request->employment_type, fn($q) => $q->where('employment_type', $request->employment_type))
            ->when($request->gender, fn($q) => $q->where('gender', $request->gender))
            ->orderBy('first_name');

        $employees   = $query->paginate(50)->withQueryString();
        $departments = Department::orderBy('name')->get();

        $summary = [
            'total'       => Employee::count(),
            'active'      => Employee::where('employment_status', 'active')->count(),
            'on_leave'    => Employee::where('employment_status', 'on_leave')->count(),
            'terminated'  => Employee::whereIn('employment_status', ['terminated', 'resigned'])->count(),
            'by_dept'     => Department::withCount('employees')->orderBy('employees_count', 'desc')->get(),
            'by_type'     => Employee::select('employment_type', DB::raw('count(*) as total'))
                                ->groupBy('employment_type')->get(),
        ];

        return view('admin.reports.employees', compact('employees', 'departments', 'summary'));
    }

    public function leaves(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->format('Y-m-d');
        $to   = $request->to   ?? now()->endOfMonth()->format('Y-m-d');

        $query = Leave::with(['employee.department', 'leaveType'])
            ->whereBetween('start_date', [$from, $to])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->leave_type_id, fn($q) => $q->where('leave_type_id', $request->leave_type_id))
            ->when($request->department_id, fn($q) => $q->whereHas('employee', fn($q) => $q->where('department_id', $request->department_id)))
            ->latest('start_date');

        $leaves     = $query->paginate(50)->withQueryString();
        $leaveTypes = LeaveType::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        $summary = [
            'total'    => $query->count(),
            'pending'  => Leave::whereBetween('start_date', [$from, $to])->where('status', 'pending')->count(),
            'approved' => Leave::whereBetween('start_date', [$from, $to])->where('status', 'approved')->count(),
            'rejected' => Leave::whereBetween('start_date', [$from, $to])->where('status', 'rejected')->count(),
            'by_type'  => Leave::whereBetween('start_date', [$from, $to])
                            ->select('leave_type_id', DB::raw('count(*) as total'))
                            ->with('leaveType')
                            ->groupBy('leave_type_id')->get(),
        ];

        return view('admin.reports.leaves', compact('leaves', 'leaveTypes', 'departments', 'summary', 'from', 'to'));
    }

    public function attendance(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->format('Y-m-d');
        $to   = $request->to   ?? now()->format('Y-m-d');

        $query = Attendance::with('employee.department')
            ->whereBetween('date', [$from, $to])
            ->when($request->department_id, fn($q) => $q->whereHas('employee', fn($q) => $q->where('department_id', $request->department_id)))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest('date');

        $records     = $query->paginate(50)->withQueryString();
        $departments = Department::orderBy('name')->get();

        $summary = [
            'present'   => Attendance::whereBetween('date', [$from, $to])->where('status', 'present')->count(),
            'absent'    => Attendance::whereBetween('date', [$from, $to])->where('status', 'absent')->count(),
            'late'      => Attendance::whereBetween('date', [$from, $to])->where('status', 'late')->count(),
            'half_day'  => Attendance::whereBetween('date', [$from, $to])->where('status', 'half_day')->count(),
            'avg_hours' => round(Attendance::whereBetween('date', [$from, $to])->whereNotNull('work_hours')->avg('work_hours') ?? 0, 1),
        ];

        return view('admin.reports.attendance', compact('records', 'departments', 'summary', 'from', 'to'));
    }

    public function queries(Request $request)
    {
        $query = Query::with(['employee', 'issuedBy'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest();

        $queries = $query->paginate(50)->withQueryString();

        $summary = [
            'open'       => Query::where('status', 'open')->count(),
            'responded'  => Query::where('status', 'responded')->count(),
            'closed'     => Query::where('status', 'closed')->count(),
            'total'      => Query::count(),
            'complaints' => [
                'open'       => Complaint::where('status', 'open')->count(),
                'in_progress'=> Complaint::where('status', 'in_progress')->count(),
                'resolved'   => Complaint::where('status', 'resolved')->count(),
                'closed'     => Complaint::where('status', 'closed')->count(),
            ],
        ];

        return view('admin.reports.queries', compact('queries', 'summary'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => ['required', 'in:employees,leaves,attendance,queries'],
            'from'        => ['nullable', 'date'],
            'to'          => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        return redirect()->route('admin.reports.' . $request->report_type, $request->only('from', 'to', 'department_id', 'status'));
    }

    public function export(Request $request)
    {
        $type = $request->type ?? 'employees';
        $from = $request->from ?? now()->startOfMonth()->format('Y-m-d');
        $to   = $request->to   ?? now()->format('Y-m-d');

        $filename = "{$type}_report_" . now()->format('Y-m-d') . ".csv";

        return response()->streamDownload(function () use ($type, $from, $to) {
            $handle = fopen('php://output', 'w');

            match ($type) {
                'employees'  => $this->exportEmployees($handle),
                'leaves'     => $this->exportLeaves($handle, $from, $to),
                'attendance' => $this->exportAttendance($handle, $from, $to),
                default      => fputcsv($handle, ['No data']),
            };

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function exportEmployees($handle): void
    {
        fputcsv($handle, ['Employee #', 'Name', 'Department', 'Job Title', 'Type', 'Status', 'Joined']);
        Employee::with('department')->orderBy('first_name')->each(function ($e) use ($handle) {
            fputcsv($handle, [
                $e->employee_number,
                $e->full_name,
                $e->department?->name ?? '—',
                $e->job_title ?? '—',
                ucfirst(str_replace('_', ' ', $e->employment_type)),
                ucfirst($e->employment_status),
                $e->date_of_joining?->format('Y-m-d') ?? '—',
            ]);
        });
    }

    private function exportLeaves($handle, $from, $to): void
    {
        fputcsv($handle, ['Employee', 'Department', 'Leave Type', 'From', 'To', 'Days', 'Status']);
        Leave::with(['employee.department', 'leaveType'])
            ->whereBetween('start_date', [$from, $to])
            ->latest('start_date')
            ->each(function ($l) use ($handle) {
                fputcsv($handle, [
                    $l->employee->full_name,
                    $l->employee->department?->name ?? '—',
                    $l->leaveType->name,
                    $l->start_date->format('Y-m-d'),
                    $l->end_date->format('Y-m-d'),
                    $l->number_of_days ?? '—',
                    ucfirst($l->status),
                ]);
            });
    }

    private function exportAttendance($handle, $from, $to): void
    {
        fputcsv($handle, ['Date', 'Employee', 'Department', 'Check In', 'Check Out', 'Work Hours', 'Status']);
        Attendance::with('employee.department')
            ->whereBetween('date', [$from, $to])
            ->latest('date')
            ->each(function ($a) use ($handle) {
                fputcsv($handle, [
                    $a->date->format('Y-m-d'),
                    $a->employee->full_name,
                    $a->employee->department?->name ?? '—',
                    $a->check_in ?? '—',
                    $a->check_out ?? '—',
                    $a->work_hours ?? '—',
                    ucfirst($a->status),
                ]);
            });
    }
}
