<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Display employee's own profile
     */
    public function show()
    {
        $employee = Auth::user()->employee;
        
        if (!$employee) {
            return redirect()->route('employee.dashboard')
                ->with('error', 'Employee profile not found');
        }

        // Load relationships
        $employee->load([
            'department',
            'lineManager',
            'user.roles',
            'education',
            'children',
        ]);

        return view('employee.profile.show', compact('employee'));
    }

    /**
     * Show edit form for employee's own profile
     */
    public function edit()
    {
        $employee = Auth::user()->employee;
        
        if (!$employee) {
            return redirect()->route('employee.dashboard')
                ->with('error', 'Employee profile not found');
        }

        // Load relationships needed for form
        $employee->load(['education', 'children']);

        return view('employee.profile.edit', compact('employee'));
    }

    /**
     * Update employee's own profile (limited fields)
     */
    public function update(Request $request)
    {
        $employee = Auth::user()->employee;
        
        if (!$employee) {
            return redirect()->route('employee.dashboard')
                ->with('error', 'Employee profile not found');
        }

        // Validate - employees can only update certain fields
        $validated = $request->validate([
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'unique:employees,email,' . $employee->id],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'marital_status' => ['nullable', 'in:single,married,divorced,widowed'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_address' => ['nullable', 'string'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:100'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_branch' => ['nullable', 'string', 'max:255'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'spouse_name' => ['nullable', 'string', 'max:255'],
            'spouse_contact' => ['nullable', 'string', 'max:20'],
            'number_of_children' => ['nullable', 'integer', 'min:0'],
            'next_of_kin_name' => ['nullable', 'string', 'max:255'],
            'next_of_kin_dob' => ['nullable', 'date'],
            'next_of_kin_sex' => ['nullable', 'in:male,female,other'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo
            if ($employee->profile_photo) {
                Storage::disk('public')->delete($employee->profile_photo);
            }

            $validated['profile_photo'] = $request->file('profile_photo')
                ->store('profile-photos', 'public');
        }

        // Update employee
        $employee->update($validated);

        return redirect()->route('employee.profile.show')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Upload/update profile photo
     */
    public function uploadPhoto(Request $request)
    {
        $employee = Auth::user()->employee;
        
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        try {
            // Delete old photo
            if ($employee->profile_photo) {
                Storage::disk('public')->delete($employee->profile_photo);
            }

            // Store new photo
            $path = $request->file('photo')->store('profile-photos', 'public');
            
            $employee->update(['profile_photo' => $path]);

            return back()->with('success', 'Profile photo updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to upload photo: ' . $e->getMessage());
        }
    }
}