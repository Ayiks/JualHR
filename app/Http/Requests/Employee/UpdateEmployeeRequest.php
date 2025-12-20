<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('edit_employees');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $employeeId = $this->route('employee')->id;
        $userId = $this->route('employee')->user_id;
        return [
            // Basic Information
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
                Rule::unique('employees', 'email')->ignore($employeeId),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            
            // Address
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            
            // Employment Details
            'department_id' => ['nullable', 'exists:departments,id'],
            'line_manager_id' => [
                'nullable',
                'exists:employees,id',
                Rule::notIn([$employeeId]), // Can't be their own manager
            ],
            'job_title' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['required', 'in:full_time,part_time,contract,intern'],
            'employment_status' => ['required', 'in:active,on_leave,suspended,terminated,resigned'],
            'date_of_joining' => ['nullable', 'date'],
            'date_of_leaving' => ['nullable', 'date', 'after:date_of_joining'],
            
            // Emergency Contact
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:100'],
            
            // Profile Photo
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            
            // Role
            'role' => ['nullable', 'exists:roles,name'],
        ];
    }

     public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email address is already registered.',
            'employment_type.required' => 'Employment type is required.',
            'employment_status.required' => 'Employment status is required.',
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'date_of_leaving.after' => 'Date of leaving must be after date of joining.',
            'line_manager_id.not_in' => 'Employee cannot be their own manager.',
            'profile_photo.image' => 'Profile photo must be an image.',
            'profile_photo.max' => 'Profile photo must not be larger than 2MB.',
        ];
    }
}
