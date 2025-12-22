<?php
// app/Http/Requests/LeaveType/StoreLeaveTypeRequest.php

namespace App\Http\Requests\LeaveType;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('manage_leave_types');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:leave_types,code'],
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
            'days_per_year.min' => 'Days per year cannot be negative.',
        ];
    }
}