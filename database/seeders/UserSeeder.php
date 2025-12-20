<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin
        $superAdminUser = User::create([
            'name' => 'System Administrator', // Add this line
            'email' => 'admin@hrms.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $superAdmin = Employee::create([
            'user_id' => $superAdminUser->id,
            'employee_number' => 'EMP001',
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'email' => 'admin@hrms.com',
            'phone' => '+1234567890',
            'date_of_birth' => '1985-01-01',
            'gender' => 'male',
            'department_id' => Department::where('name', 'Information Technology')->first()->id,
            'job_title' => 'System Administrator',
            'employment_type' => 'full_time',
            'employment_status' => 'active',
            'date_of_joining' => now()->subYears(5),
        ]);

        $superAdminUser->assignRole('super_admin');

        // Create HR Admin
        $hrAdminUser = User::create([
            'name' => 'HR Manager', // Add this line
            'email' => 'hr@hrms.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $hrAdmin = Employee::create([
            'user_id' => $hrAdminUser->id,
            'employee_number' => 'EMP002',
            'first_name' => 'HR',
            'last_name' => 'Manager',
            'email' => 'hr@hrms.com',
            'phone' => '+1234567891',
            'date_of_birth' => '1988-05-15',
            'gender' => 'female',
            'department_id' => Department::where('name', 'Human Resources')->first()->id,
            'job_title' => 'HR Manager',
            'employment_type' => 'full_time',
            'employment_status' => 'active',
            'date_of_joining' => now()->subYears(3),
        ]);

        $hrAdminUser->assignRole('hr_admin');

        // Create Line Manager
        $managerUser = User::create([
            'name' => 'John Manager', // Add this line
            'email' => 'manager@hrms.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $manager = Employee::create([
            'user_id' => $managerUser->id,
            'employee_number' => 'EMP003',
            'first_name' => 'John',
            'last_name' => 'Manager',
            'email' => 'manager@hrms.com',
            'phone' => '+1234567892',
            'date_of_birth' => '1982-08-20',
            'gender' => 'male',
            'department_id' => Department::where('name', 'Sales')->first()->id,
            'job_title' => 'Sales Manager',
            'employment_type' => 'full_time',
            'employment_status' => 'active',
            'date_of_joining' => now()->subYears(4),
        ]);

        $managerUser->assignRole('line_manager');

        // Create Regular Employee
        $employeeUser = User::create([
            'name' => 'Jane Doe', // Add this line
            'email' => 'employee@hrms.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $employee = Employee::create([
            'user_id' => $employeeUser->id,
            'employee_number' => 'EMP004',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'employee@hrms.com',
            'phone' => '+1234567893',
            'date_of_birth' => '1995-03-10',
            'gender' => 'female',
            'department_id' => Department::where('name', 'Sales')->first()->id,
            'line_manager_id' => $manager->id,
            'job_title' => 'Sales Executive',
            'employment_type' => 'full_time',
            'employment_status' => 'active',
            'date_of_joining' => now()->subYear(),
        ]);

        $employeeUser->assignRole('employee');

        $this->command->info('Users seeded successfully!');
        $this->command->info('');
        $this->command->info('Login Credentials:');
        $this->command->info('Super Admin: admin@hrms.com / password');
        $this->command->info('HR Admin: hr@hrms.com / password');
        $this->command->info('Manager: manager@hrms.com / password');
        $this->command->info('Employee: employee@hrms.com / password');
    }
}