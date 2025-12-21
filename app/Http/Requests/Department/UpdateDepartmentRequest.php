<?php

// app/Http/Requests/Department/UpdateDepartmentRequest.php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('manage_departments');
    }

    public function rules(): array
    {
        $departmentId = $this->route('department')->id;

        return [
            'name' => [
                'required', 
                'string', 
                'max:255', 
                Rule::unique('departments', 'name')->ignore($departmentId)
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Department name is required.',
            'name.unique' => 'A department with this name already exists.',
        ];
    }
}