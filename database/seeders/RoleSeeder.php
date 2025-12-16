<?php
// database/seeders/RoleSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // Employee Management
            'view_employees',
            'create_employees',
            'edit_employees',
            'delete_employees',
            'export_employees',

            // Leave Management
            'view_all_leaves',
            'approve_leaves',
            'reject_leaves',
            'manage_leave_types',
            'adjust_leave_balance',

            // Policy Management
            'view_policies',
            'create_policies',
            'edit_policies',
            'delete_policies',

            // Query Management
            'issue_queries',
            'view_all_queries',
            'respond_to_queries',
            'close_queries',

            // Complaint Management
            'view_all_complaints',
            'assign_complaints',
            'resolve_complaints',

            // Survey Management
            'create_surveys',
            'view_survey_responses',
            'delete_surveys',

            // Attendance Management
            'view_all_attendance',
            'edit_attendance',
            'export_attendance',

            // Reports
            'view_reports',
            'export_reports',

            // Department Management
            'manage_departments',

            // Onboarding
            'manage_onboarding',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles and Assign Permissions

        // Super Admin - All Permissions
        $superAdmin = Role::create(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // HR Admin - HR Operations
        $hrAdmin = Role::create(['name' => 'hr_admin']);
        $hrAdmin->givePermissionTo([
            'view_employees',
            'create_employees',
            'edit_employees',
            'delete_employees',
            'export_employees',
            'view_all_leaves',
            'approve_leaves',
            'reject_leaves',
            'manage_leave_types',
            'adjust_leave_balance',
            'view_policies',
            'create_policies',
            'edit_policies',
            'delete_policies',
            'issue_queries',
            'view_all_queries',
            'close_queries',
            'view_all_complaints',
            'assign_complaints',
            'resolve_complaints',
            'create_surveys',
            'view_survey_responses',
            'delete_surveys',
            'view_all_attendance',
            'edit_attendance',
            'export_attendance',
            'view_reports',
            'export_reports',
            'manage_departments',
            'manage_onboarding',
        ]);

        // Line Manager - Team Management
        $lineManager = Role::create(['name' => 'line_manager']);
        $lineManager->givePermissionTo([
            'view_employees',
            'approve_leaves',
            'reject_leaves',
            'view_policies',
            'view_reports',
        ]);

        // Employee - Self Service
        $employee = Role::create(['name' => 'employee']);
        $employee->givePermissionTo([
            'view_policies',
            'respond_to_queries',
        ]);

        $this->command->info('Roles and permissions seeded successfully!');
    }
}
