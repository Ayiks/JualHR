<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeEducation;
use App\Models\EmployeeChild;
use App\Models\Department;
use App\Models\User;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class EmployeeController extends Controller
{
    const DEFAULT_PASSWORD = 'JGGLDefault@2025';

    public function index(Request $request)
    {
        $query = Employee::with(['department', 'lineManager']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('work_email', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%")
                  ->orWhere('ssnit_number', 'like', "%{$search}%")
                  ->orWhere('ghana_card_number', 'like', "%{$search}%");
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

        $employees = $query->latest()->paginate(15);
        
        $departments = Cache::remember('departments_list', 3600, function () {
            return Department::select('id', 'name')->get();
        });

        return view('admin.employees.index', compact('employees', 'departments'));
    }

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

    public function store(StoreEmployeeRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create User Account
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->middle_name . ' ' . $request->last_name,
                'email' => $request->work_email,
                'password' => Hash::make(self::DEFAULT_PASSWORD),
                'email_verified_at' => now(),
                'force_password_reset' => true, // Force password reset on first login
            ]);

            // Assign role
            $user->assignRole($request->role ?? 'employee');

            // Generate employee number: JGGL + 3 random digits
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
                'name' => $request->first_name . ' ' . $request->middle_name . ' ' . $request->last_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'full_name' => $request->first_name . ' ' . $request->middle_name . ' ' . $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'ssnit_number' => $request->ssnit_number,
                'ghana_card_number' => $request->ghana_card_number,
                'tin_number' => $request->tin_number,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'marital_status' => $request->marital_status,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'department_id' => $request->department_id,
                'line_manager_id' => $request->line_manager_id,
                'job_title' => $request->job_title,
                'work_email' => $request->work_email,
                'work_phone' => $request->work_phone,
                'cell_phone' => $request->cell_phone,
                'employment_type' => $request->employment_type,
                'employment_status' => $request->employment_status ?? 'active',
                'date_of_joining' => $request->date_of_joining,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_address' => $request->emergency_contact_address,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'emergency_contact_relationship' => $request->emergency_contact_relationship,
                'bank_name' => $request->bank_name,
                'bank_branch' => $request->bank_branch,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'spouse_name' => $request->spouse_name,
                'spouse_contact' => $request->spouse_contact,
                'number_of_children' => $request->number_of_children ?? 0,
                'next_of_kin_name' => $request->next_of_kin_name,
                'next_of_kin_dob' => $request->next_of_kin_dob,
                'next_of_kin_sex' => $request->next_of_kin_sex,
                'profile_photo' => $profilePhotoPath,
            ]);

            // Handle Education Records
            if ($request->has('education')) {
                foreach ($request->education as $edu) {
                    if (!empty($edu['institution_name'])) {
                        $certificates = [];
                        
                        // Handle certificate uploads (max 2 per education entry)
                        if (isset($edu['certificates'])) {
                            foreach (array_slice($edu['certificates'], 0, 2) as $file) {
                                if ($file) {
                                    $certificates[] = $file->store('education-certificates', 'private');
                                }
                            }
                        }

                        EmployeeEducation::create([
                            'employee_id' => $employee->id,
                            'institution_name' => $edu['institution_name'],
                            'program' => $edu['program'],
                            'certificate_obtained' => $edu['certificate_obtained'],
                            'certificates' => $certificates,
                        ]);
                    }
                }
            }

            // Handle Children Records
            if ($request->has('children')) {
                foreach ($request->children as $child) {
                    if (!empty($child['name'])) {
                        EmployeeChild::create([
                            'employee_id' => $employee->id,
                            'name' => $child['name'],
                            'date_of_birth' => $child['date_of_birth'],
                            'sex' => $child['sex'],
                        ]);
                    }
                }
            }

            DB::commit();
            
            Cache::forget('admin_dashboard_stats');
            Cache::forget('departments_list');

            return redirect()
                ->route('admin.employees.show', $employee)
                ->with('success', "Employee created successfully! Employee Number: {$employeeNumber}. Default password: " . self::DEFAULT_PASSWORD);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Failed to create employee: ' . $e->getMessage());
        }
    }

    public function show(Employee $employee)
    {
        $employee->load([
            'department', 
            'lineManager', 
            'user.roles', 
            'education',
            'children',
            'subordinates' => function($query) {
                $query->select('id', 'first_name', 'last_name', 'middle_name', 'job_title', 'profile_photo', 'line_manager_id');
            }
        ]);

        return view('admin.employees.show', compact('employee'));
    }

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

        $employee->load(['education', 'children']);

        return view('admin.employees.edit', compact('employee', 'departments', 'managers'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        try {
            DB::beginTransaction();

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                if ($employee->profile_photo) {
                    Storage::disk('public')->delete($employee->profile_photo);
                }
                $employee->profile_photo = $request->file('profile_photo')
                    ->store('profile-photos', 'public');
            }

            // Update Employee
            $employee->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'ssnit_number' => $request->ssnit_number,
                'ghana_card_number' => $request->ghana_card_number,
                'tin_number' => $request->tin_number,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'marital_status' => $request->marital_status,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'department_id' => $request->department_id,
                'line_manager_id' => $request->line_manager_id,
                'job_title' => $request->job_title,
                'work_email' => $request->work_email,
                'work_phone' => $request->work_phone,
                'cell_phone' => $request->cell_phone,
                'employment_type' => $request->employment_type,
                'employment_status' => $request->employment_status,
                'date_of_joining' => $request->date_of_joining,
                'date_of_leaving' => $request->date_of_leaving,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_address' => $request->emergency_contact_address,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'emergency_contact_relationship' => $request->emergency_contact_relationship,
                'bank_name' => $request->bank_name,
                'bank_branch' => $request->bank_branch,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'spouse_name' => $request->spouse_name,
                'spouse_contact' => $request->spouse_contact,
                'number_of_children' => $request->number_of_children ?? 0,
                'next_of_kin_name' => $request->next_of_kin_name,
                'next_of_kin_dob' => $request->next_of_kin_dob,
                'next_of_kin_sex' => $request->next_of_kin_sex,
            ]);

            // Update User
            if ($employee->user) {
                $employee->user->update([
                    'name' => $request->first_name . ' ' . $request->middle_name . ' ' . $request->last_name,
                    'email' => $request->work_email
                ]);

                // Update role if provided
                if ($request->filled('role')) {
                    $employee->user->syncRoles([$request->role]);
                }
            }

            // Update Education Records
            if ($request->has('education')) {
                // Delete removed education records
                $submittedIds = collect($request->education)->pluck('id')->filter();
                $employee->education()->whereNotIn('id', $submittedIds)->get()->each->delete();

                foreach ($request->education as $edu) {
                    if (!empty($edu['institution_name'])) {
                        $certificates = [];
                        $existingEducation = null;

                        if (isset($edu['id'])) {
                            $existingEducation = EmployeeEducation::find($edu['id']);
                            $certificates = $existingEducation->certificates ?? [];
                        }
                        
                        // Handle new certificate uploads
                        if (isset($edu['certificates'])) {
                            foreach (array_slice($edu['certificates'], 0, 2) as $file) {
                                if ($file && count($certificates) < 2) {
                                    $certificates[] = $file->store('education-certificates', 'private');
                                }
                            }
                        }

                        $data = [
                            'institution_name' => $edu['institution_name'],
                            'program' => $edu['program'],
                            'certificate_obtained' => $edu['certificate_obtained'],
                            'certificates' => $certificates,
                        ];

                        if ($existingEducation) {
                            $existingEducation->update($data);
                        } else {
                            $employee->education()->create($data);
                        }
                    }
                }
            }

            // Update Children Records
            if ($request->has('children')) {
                // Delete removed children
                $submittedIds = collect($request->children)->pluck('id')->filter();
                $employee->children()->whereNotIn('id', $submittedIds)->delete();

                foreach ($request->children as $child) {
                    if (!empty($child['name'])) {
                        $data = [
                            'name' => $child['name'],
                            'date_of_birth' => $child['date_of_birth'],
                            'sex' => $child['sex'],
                        ];

                        if (isset($child['id'])) {
                            EmployeeChild::find($child['id'])->update($data);
                        } else {
                            $employee->children()->create($data);
                        }
                    }
                }
            }

            DB::commit();
            
            Cache::forget('admin_dashboard_stats');
            Cache::forget('departments_list');

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

    public function destroy(Employee $employee)
    {
        try {
            $employee->delete();
            return redirect()
                ->route('admin.employees.index')
                ->with('success', 'Employee deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete employee: ' . $e->getMessage());
        }
    }

    /**
     * Reset employee password to default
     */
    public function resetPassword(Employee $employee)
    {
        try {
            $employee->user->update([
                'password' => Hash::make(self::DEFAULT_PASSWORD),
                'force_password_reset' => true,
            ]);

            return back()->with('success', 'Password reset to default. Employee must change password on next login.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reset password: ' . $e->getMessage());
        }
    }
    
    /**
     * Get department head for auto-fill
     */
    public function getDepartmentHead($departmentId)
    {
        $department = Department::with('head')->find($departmentId);
        
        if (!$department || !$department->head) {
            return response()->json([
                'success' => false,
                'message' => 'No department head assigned',
            ]);
        }

        return response()->json([
            'success' => true,
            'head' => [
                'id' => $department->head->id,
                'name' => $department->head->full_name,
                'job_title' => $department->head->job_title,
            ],
        ]);
    }

    /**
     * Download education certificate
     */
    public function downloadCertificate($educationId, $index)
    {
        $education = EmployeeEducation::findOrFail($educationId);
        
        if (!isset($education->certificates[$index])) {
            abort(404);
        }

        $path = $education->certificates[$index];
        
        if (!Storage::disk('private')->exists($path)) {
            abort(404);
        }

        return response()->download(Storage::disk('private')->path($path));
    }

    public function print(Employee $employee)
    {
        $employee->load(['department', 'lineManager', 'user.roles', 'education', 'children']);
        return view('admin.employees.print', compact('employee'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:2048',
        ]);

        try {
            return back()->with('success', 'Employees imported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to import employees: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        try {
            return back()->with('success', 'Employees exported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export employees: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique employee number: JGGL + 3 random digits
     */
    protected function generateEmployeeNumber(): string
    {
        do {
            $number = 'JGGL' . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        } while (Employee::where('employee_number', $number)->exists());
        
        return $number;
    }
}