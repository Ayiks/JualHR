<?php
// app/Http/Requests/LeaveType/UpdateLeaveTypeRequest.php

namespace App\Http\Requests\LeaveType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeaveTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('manage_leave_types');
    }

    public function rules(): array
    {
        $leaveTypeId = $this->route('leave_type')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required', 
                'string', 
                'max:50', 
                Rule::unique('leave_types', 'code')->ignore($leaveTypeId)
            ],
            'days_per_year' => ['required', 'integer', 'min:0', 'max:365'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Leave type name is required.',
            'code.required' => 'Leave type code is required.',
            'code.unique' => 'This leave type code already exists.',
            'days_per_year.required' => 'Days per year is required.',
        ];
    }
}