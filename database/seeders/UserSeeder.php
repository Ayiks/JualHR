<?php

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
        // =====================================================================
        // 1. SUPER ADMIN
        // =====================================================================
        $superAdminUser = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@jggl.com', // Using work email
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'force_password_reset' => false, // Admin doesn't need to reset
        ]);

        $superAdmin = Employee::create([
            'name' => 'System Administrator',
            'user_id' => $superAdminUser->id,
            'employee_number' => 'JGGL001', // Using JGGL format
            
            // Personal Information
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'middle_name' => null,
            'full_name' => 'System Administrator',
            'email' => 'admin.personal@gmail.com', // Personal email (optional)
            'phone' => '+233201234567',
            'ssnit_number' => 'C001234567890',
            'ghana_card_number' => 'GHA-000000001-1',
            'tin_number' => 'TIN001234567',
            'date_of_birth' => '1985-01-01',
            'gender' => 'male',
            'marital_status' => 'married',
            
            // Address
            'address' => 'P.O. Box 123, Kumasi',
            'city' => 'Kumasi',
            'state' => 'Ashanti',
            'country' => 'Ghana',
            'postal_code' => '00233',
            
            // Job Information
            'department_id' => Department::where('name', 'Information Technology')->first()?->id,
            'line_manager_id' => null, // Super admin has no manager
            'job_title' => 'System Administrator',
            'work_email' => 'admin@jggl.com', // Same as login email
            'work_phone' => '+233302000001',
            'cell_phone' => '+233201234567',
            'employment_type' => 'full_time',
            'employment_status' => 'active',
            'date_of_joining' => now()->subYears(5),
            
            // Emergency Contact
            'emergency_contact_name' => 'Mary Administrator',
            'emergency_contact_address' => 'Kumasi, Ashanti Region',
            'emergency_contact_phone' => '+233209876543',
            'emergency_contact_relationship' => 'Spouse',
            
            // Bank Details
            'bank_name' => 'GCB Bank',
            'bank_branch' => 'Kumasi Main',
            'account_name' => 'System Administrator',
            'account_number' => '1234567890',
            
            // Family
            'spouse_name' => 'Mary Administrator',
            'spouse_contact' => '+233209876543',
            'number_of_children' => 0,
            'next_of_kin_name' => 'Mary Administrator',
            'next_of_kin_dob' => '1987-05-15',
            'next_of_kin_sex' => 'female',
        ]);

        $superAdminUser->assignRole('super_admin');

        // =====================================================================
        // 2. HR ADMIN
        // =====================================================================
        $hrAdminUser = User::create([
            'name' => 'HR Manager',
            'email' => 'hr@jggl.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'force_password_reset' => false,
        ]);

        $hrAdmin = Employee::create([
            'name' => 'HR Manager',
            'user_id' => $hrAdminUser->id,
            'employee_number' => 'JGGL002',
            
            // Personal Information
            'first_name' => 'Sarah',
            'last_name' => 'Mensah',
            'middle_name' => 'Akosua',
            'full_name' => 'Sarah Akosua Mensah',
            'email' => 'sarah.mensah@gmail.com',
            'phone' => '+233202345678',
            'ssnit_number' => 'C002345678901',
            'ghana_card_number' => 'GHA-000000002-2',
            'tin_number' => 'TIN002345678',
            'date_of_birth' => '1988-05-15',
            'gender' => 'female',
            'marital_status' => 'married',
            
            // Address
            'address' => 'Ahodwo Estate, Plot 45',
            'city' => 'Kumasi',
            'state' => 'Ashanti',
            'country' => 'Ghana',
            'postal_code' => '00233',
            
            // Job Information
            'department_id' => Department::where('name', 'Human Resources')->first()?->id,
            'line_manager_id' => $superAdmin->id, // Reports to super admin
            'job_title' => 'HR Manager',
            'work_email' => 'hr@jggl.com',
            'work_phone' => '+233302000002',
            'cell_phone' => '+233202345678',
            'employment_type' => 'full_time',
            'employment_status' => 'active',
            'date_of_joining' => now()->subYears(3),
            
            // Emergency Contact
            'emergency_contact_name' => 'Kwame Mensah',
            'emergency_contact_address' => 'Ahodwo, Kumasi',
            'emergency_contact_phone' => '+233203456789',
            'emergency_contact_relationship' => 'Spouse',
            
            // Bank Details
            'bank_name' => 'Ecobank Ghana',
            'bank_branch' => 'Adum Branch',
            'account_name' => 'Sarah Akosua Mensah',
            'account_number' => '2345678901',
            
            // Family
            'spouse_name' => 'Kwame Mensah',
            'spouse_contact' => '+233203456789',
            'number_of_children' => 2,
            'next_of_kin_name' => 'Kwame Mensah',
            'next_of_kin_dob' => '1985-03-20',
            'next_of_kin_sex' => 'male',
        ]);

        $hrAdminUser->assignRole('hr_admin');

        // =====================================================================
        // 3. LINE MANAGER
        // =====================================================================
        $managerUser = User::create([
            'name' => 'John Asante',
            'email' => 'john.asante@jggl.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'force_password_reset' => false,
        ]);

        $manager = Employee::create([
             'name' => 'John Asante',
            'user_id' => $managerUser->id,
            'employee_number' => 'JGGL003',
            
            // Personal Information
            'first_name' => 'John',
            'last_name' => 'Asante',
            'middle_name' => 'Kwame',
            'full_name' => 'John Kwame Asante',
            'email' => 'john.asante@gmail.com',
            'phone' => '+233203456789',
            'ssnit_number' => 'C003456789012',
            'ghana_card_number' => 'GHA-000000003-3',
            'tin_number' => 'TIN003456789',
            'date_of_birth' => '1982-08-20',
            'gender' => 'male',
            'marital_status' => 'single',
            
            // Address
            'address' => 'Asokwa, House No. 23',
            'city' => 'Kumasi',
            'state' => 'Ashanti',
            'country' => 'Ghana',
            'postal_code' => '00233',
            
            // Job Information
            'department_id' => Department::where('name', 'Sales')->first()?->id,
            'line_manager_id' => $hrAdmin->id, // Reports to HR Manager
            'job_title' => 'Sales Manager',
            'work_email' => 'john.asante@jggl.com',
            'work_phone' => '+233302000003',
            'cell_phone' => '+233203456789',
            'employment_type' => 'full_time',
            'employment_status' => 'active',
            'date_of_joining' => now()->subYears(4),
            
            // Emergency Contact
            'emergency_contact_name' => 'Mary Asante',
            'emergency_contact_address' => 'Asokwa, Kumasi',
            'emergency_contact_phone' => '+233204567890',
            'emergency_contact_relationship' => 'Sister',
            
            // Bank Details
            'bank_name' => 'Absa Bank',
            'bank_branch' => 'Adum',
            'account_name' => 'John Kwame Asante',
            'account_number' => '3456789012',
            
            // Family
            'spouse_name' => null,
            'spouse_contact' => null,
            'number_of_children' => 0,
            'next_of_kin_name' => 'Mary Asante',
            'next_of_kin_dob' => '1990-12-10',
            'next_of_kin_sex' => 'female',
        ]);

        $managerUser->assignRole('line_manager');

        // =====================================================================
        // 4. REGULAR EMPLOYEE
        // =====================================================================
        $employeeUser = User::create([
            'name' => 'Jane Osei',
            'email' => 'jane.osei@jggl.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'force_password_reset' => false,
        ]);

        $employee = Employee::create([
            'name' => 'Jane Osei',
            'user_id' => $employeeUser->id,
            'employee_number' => 'JGGL004',
            
            // Personal Information
            'first_name' => 'Jane',
            'last_name' => 'Osei',
            'middle_name' => 'Abena',
            'full_name' => 'Jane Abena Osei',
            'email' => 'jane.osei@gmail.com',
            'phone' => '+233204567890',
            'ssnit_number' => 'C004567890123',
            'ghana_card_number' => 'GHA-000000004-4',
            'tin_number' => null, // Optional
            'date_of_birth' => '1995-03-10',
            'gender' => 'female',
            'marital_status' => 'single',
            
            // Address
            'address' => 'Tech Junction, Flat 12',
            'city' => 'Kumasi',
            'state' => 'Ashanti',
            'country' => 'Ghana',
            'postal_code' => '00233',
            
            // Job Information
            'department_id' => Department::where('name', 'Sales')->first()?->id,
            'line_manager_id' => $manager->id, // Reports to Sales Manager
            'job_title' => 'Sales Executive',
            'work_email' => 'jane.osei@jggl.com',
            'work_phone' => '+233302000004',
            'cell_phone' => '+233204567890',
            'employment_type' => 'full_time',
            'employment_status' => 'active',
            'date_of_joining' => now()->subYear(),
            
            // Emergency Contact
            'emergency_contact_name' => 'Peter Osei',
            'emergency_contact_address' => 'Bantama, Kumasi',
            'emergency_contact_phone' => '+233205678901',
            'emergency_contact_relationship' => 'Father',
            
            // Bank Details
            'bank_name' => 'Fidelity Bank',
            'bank_branch' => 'Tech Junction',
            'account_name' => 'Jane Abena Osei',
            'account_number' => '4567890123',
            
            // Family
            'spouse_name' => null,
            'spouse_contact' => null,
            'number_of_children' => 0,
            'next_of_kin_name' => 'Peter Osei',
            'next_of_kin_dob' => '1965-07-15',
            'next_of_kin_sex' => 'male',
        ]);

        $employeeUser->assignRole('employee');

        // =====================================================================
        // OUTPUT
        // =====================================================================
        $this->command->info('✅ Users seeded successfully with new structure!');
        $this->command->info('');
        $this->command->info('🔐 Login Credentials:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('Super Admin:  admin@jggl.com / password');
        $this->command->info('HR Admin:     hr@jggl.com / password');
        $this->command->info('Manager:      john.asante@jggl.com / password');
        $this->command->info('Employee:     jane.osei@jggl.com / password');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('');
        $this->command->info('📊 Employee Numbers:');
        $this->command->info('JGGL001 - System Administrator');
        $this->command->info('JGGL002 - Sarah Akosua Mensah (HR Manager)');
        $this->command->info('JGGL003 - John Kwame Asante (Sales Manager)');
        $this->command->info('JGGL004 - Jane Abena Osei (Sales Executive)');
        $this->command->info('');
        $this->command->info('✨ All employees have complete data including:');
        $this->command->info('   • Personal info (SSNIT, Ghana Card, TIN)');
        $this->command->info('   • Job details (work email, phones)');
        $this->command->info('   • Emergency contacts (with address)');
        $this->command->info('   • Bank details');
        $this->command->info('   • Family information');
    }
}