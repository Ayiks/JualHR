<?php
// database/seeders/LeaveTypeSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Annual Leave',
                'code' => 'ANNUAL',
                'days_per_year' => 21,
                'description' => 'Regular annual vacation leave',
                'is_active' => true,
            ],
            [
                'name' => 'Sick Leave',
                'code' => 'SICK',
                'days_per_year' => 10,
                'description' => 'Medical or health-related leave',
                'is_active' => true,
            ],
            [
                'name' => 'Casual Leave',
                'code' => 'CASUAL',
                'days_per_year' => 7,
                'description' => 'Short-term casual leave for personal matters',
                'is_active' => true,
            ],
            [
                'name' => 'Maternity Leave',
                'code' => 'MATERNITY',
                'days_per_year' => 90,
                'description' => 'Leave for childbirth and maternity',
                'is_active' => true,
            ],
            [
                'name' => 'Paternity Leave',
                'code' => 'PATERNITY',
                'days_per_year' => 10,
                'description' => 'Leave for new fathers',
                'is_active' => true,
            ],
            [
                'name' => 'Bereavement Leave',
                'code' => 'BEREAVEMENT',
                'days_per_year' => 5,
                'description' => 'Leave for loss of a family member',
                'is_active' => true,
            ],
            [
                'name' => 'Study Leave',
                'code' => 'STUDY',
                'days_per_year' => 10,
                'description' => 'Leave for educational purposes',
                'is_active' => true,
            ],
            [
                'name' => 'Unpaid Leave',
                'code' => 'UNPAID',
                'days_per_year' => 0,
                'description' => 'Leave without pay',
                'is_active' => true,
            ],
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::create($leaveType);
        }

        $this->command->info('Leave types seeded successfully!');
    }
}
