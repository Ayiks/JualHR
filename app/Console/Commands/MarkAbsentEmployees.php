<?php

// app/Console/Commands/MarkAbsentEmployees.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Leave;
use Carbon\Carbon;

class MarkAbsentEmployees extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'attendance:mark-absent {date?}';

    /**
     * The console command description.
     */
    protected $description = 'Automatically mark employees as absent if they have not checked in';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : Carbon::yesterday();

        $this->info("Marking absent employees for date: {$date->format('Y-m-d')}");

        // Get all active employees
        $employees = Employee::where('employment_status', 'active')->get();

        $markedAbsent = 0;
        $skipped = 0;

        foreach ($employees as $employee) {
            // Check if attendance record already exists
            $existingAttendance = Attendance::where('employee_id', $employee->id)
                ->where('date', $date)
                ->first();

            if ($existingAttendance) {
                $skipped++;
                continue;
            }

            // Check if employee is on approved leave
            $onLeave = Leave::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->exists();

            if ($onLeave) {
                // Mark as on leave
                Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $date,
                    'status' => 'on_leave',
                    'notes' => 'Automatically marked - Employee on approved leave',
                ]);
                $this->line("  - {$employee->full_name}: Marked as on_leave");
                $markedAbsent++;
            } else {
                // Mark as absent
                Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $date,
                    'status' => 'absent',
                    'notes' => 'Automatically marked absent - No check-in record',
                ]);
                $this->line("  - {$employee->full_name}: Marked as absent");
                $markedAbsent++;
            }
        }

        $this->info("\nCompleted!");
        $this->info("Marked: {$markedAbsent} employees");
        $this->info("Skipped: {$skipped} employees (already have records)");

        return Command::SUCCESS;
    }
}