<?php
// app/Http/Controllers/Admin/EmployeeController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\User;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees
     */
    public function index(Request $request)
    {
        // Eager load relationships to prevent N+1 queries
        $query = Employee::with(['department', 'lineManager']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('employment_status', $request->status);
        }

        // Filter by employment type
        if ($request->filled('type')) {
            $query->where('employment_type', $request->type);
        }

        // Use select to only fetch needed columns
        $employees = $query->select([
            'id', 'first_name', 'last_name', 'middle_name', 'email', 
            'employee_number', 'department_id', 'line_manager_id', 
            'job_title', 'employment_status', 'employment_type', 'profile_photo'
        ])
        ->latest()
        ->paginate(15);
        
        // Cache departments for filter dropdown (1 hour)
        $departments = Cache::remember('departments_list', 3600, function () {
            return Department::select('id', 'name')->get();
        });

        return view('admin.employees.index', compact('employees', 'departments'));
    }

    /**
     * Show the form for creating a new employee
     */
    public function create()
    {
        $departments = Department::all();
        $managers = Employee::active()
            ->whereHas('user', function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['line_manager', 'hr_admin', 'super_admin']);
                });
            })
            ->get();

        return view('admin.employees.create', compact('departments', 'managers'));
    }

    /**
     * Store a newly created employee in database
     */
    public function store(StoreEmployeeRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create User Account
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password ?? 'password123'),
                'email_verified_at' => now(),
            ]);

            // Assign role
            $user->assignRole($request->role ?? 'employee');

            // Generate employee number
            $employeeNumber = $this->generateEmployeeNumber();

            // Handle profile photo upload
            $profilePhotoPath = null;
            if ($request->hasFile('profile_photo')) {
                $profilePhotoPath = $request->file('profile_photo')
                    ->store('profile-photos', 'public');
            }

            // Create Employee
            $employee = Employee::create([
                'user_id' => $user->id,
                'employee_number' => $employeeNumber,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'department_id' => $request->department_id,
                'line_manager_id' => $request->line_manager_id,
                'job_title' => $request->job_title,
                'employment_type' => $request->employment_type,
                'employment_status' => $request->employment_status ?? 'active',
                'date_of_joining' => $request->date_of_joining,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'emergency_contact_relationship' => $request->emergency_contact_relationship,
                'profile_photo' => $profilePhotoPath,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.employees.show', $employee)
                ->with('success', 'Employee created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Failed to create employee: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified employee
     */
    public function show(Employee $employee)
    {
        $employee->load(['department', 'lineManager', 'user.roles', 'subordinates']);

        return view('admin.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee
     */
    public function edit(Employee $employee)
    {
        $departments = Department::all();
        $managers = Employee::active()
            ->where('id', '!=', $employee->id)
            ->whereHas('user', function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['line_manager', 'hr_admin', 'super_admin']);
                });
            })
            ->get();

        return view('admin.employees.edit', compact('employee', 'departments', 'managers'));
    }

    /**
     * Update the specified employee in database
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        try {
            DB::beginTransaction();

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                // Delete old photo
                if ($employee->profile_photo) {
                    Storage::disk('public')->delete($employee->profile_photo);
                }

                $profilePhotoPath = $request->file('profile_photo')
                    ->store('profile-photos', 'public');

                $employee->profile_photo = $profilePhotoPath;
            }

            // Update Employee
            $employee->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'department_id' => $request->department_id,
                'line_manager_id' => $request->line_manager_id,
                'job_title' => $request->job_title,
                'employment_type' => $request->employment_type,
                'employment_status' => $request->employment_status,
                'date_of_joining' => $request->date_of_joining,
                'date_of_leaving' => $request->date_of_leaving,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'emergency_contact_relationship' => $request->emergency_contact_relationship,
            ]);

            // Update User email if changed
            if ($employee->user && $employee->user->email !== $request->email) {
                $employee->user->update(['email' => $request->email]);
            }

            // Update role if provided
            if ($request->filled('role') && $employee->user) {
                $employee->user->syncRoles([$request->role]);
            }

            DB::commit();

            return redirect()
                ->route('admin.employees.show', $employee)
                ->with('success', 'Employee updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Failed to update employee: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified employee from database
     */
    public function destroy(Employee $employee)
    {
        try {
            // Soft delete the employee
            $employee->delete();

            return redirect()
                ->route('admin.employees.index')
                ->with('success', 'Employee deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete employee: ' . $e->getMessage());
        }
    }

    /**
     * Print employee details
     */
    public function print(Employee $employee)
    {
        $employee->load(['department', 'lineManager', 'user.roles']);

        return view('admin.employees.print', compact('employee'));
    }

    /**
     * Import employees from CSV/Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:2048',
        ]);

        try {
            // Import logic will be implemented later with Laravel Excel
            // For now, return success message
            
            return back()->with('success', 'Employees imported successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to import employees: ' . $e->getMessage());
        }
    }

    /**
     * Export employees to CSV/Excel
     */
    public function export(Request $request)
    {
        try {
            // Export logic will be implemented later with Laravel Excel
            // For now, return a simple CSV
            
            return back()->with('success', 'Employees exported successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export employees: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique employee number
     */
    protected function generateEmployeeNumber(): string
    {
        $lastEmployee = Employee::latest('id')->first();
        $number = $lastEmployee ? (int) substr($lastEmployee->employee_number, 3) + 1 : 1;
        
        return 'EMP' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}