<?php
// app/Http/Controllers/Admin/DepartmentController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments
     */
    public function index(Request $request)
    {
        $query = Department::withCount('employees');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $departments = $query->latest()->paginate(15);

        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department
     */
    public function create()
    {
        return view('admin.departments.create');
    }

    /**
     * Store a newly created department
     */
    public function store(StoreDepartmentRequest $request)
    {
        try {
            $department = Department::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            // Clear cache
            Cache::forget('departments_list');

            return redirect()
                ->route('admin.departments.index')
                ->with('success', 'Department created successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create department: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified department
     */
    public function show(Department $department)
    {
        $department->load(['employees' => function($query) {
            $query->select('id', 'first_name', 'last_name', 'middle_name', 'email', 'job_title', 'employment_status', 'profile_photo', 'department_id')
                  ->orderBy('first_name');
        }]);

        return view('admin.departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified department
     */
    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Update the specified department
     */
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        try {
            $department->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            // Clear cache
            Cache::forget('departments_list');

            return redirect()
                ->route('admin.departments.index')
                ->with('success', 'Department updated successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update department: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified department
     */
    public function destroy(Department $department)
    {
        try {
            // Check if department has employees
            if ($department->employees()->count() > 0) {
                return back()->with('error', 'Cannot delete department with employees. Please reassign employees first.');
            }

            $department->delete();

            // Clear cache
            Cache::forget('departments_list');

            return redirect()
                ->route('admin.departments.index')
                ->with('success', 'Department deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete department: ' . $e->getMessage());
        }
    }
}