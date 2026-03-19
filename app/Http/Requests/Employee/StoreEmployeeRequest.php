<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Required: core identity & login
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'work_email' => ['required', 'email', 'unique:users,email', 'unique:employees,work_email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:employee,line_manager,hr_admin,super_admin'],

            // Required: job details
            'job_title' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'exists:departments,id'],
            'employment_type' => ['required', 'in:full_time,part_time,contract,intern'],
            'date_of_joining' => ['required', 'date'],

            // Optional: employee fills in themselves later
            'employment_status' => ['nullable', 'in:active,inactive,terminated,resigned'],
            'line_manager_id' => ['nullable', 'exists:employees,id'],
            'email' => ['nullable', 'email', 'unique:employees,email'],
            'work_phone' => ['nullable', 'string', 'max:20'],
            'cell_phone' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:20'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],

            // Personal — employee fills in
            'ssnit_number' => ['nullable', 'string', 'unique:employees,ssnit_number'],
            'ghana_card_number' => ['nullable', 'string', 'unique:employees,ghana_card_number'],
            'tin_number' => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female'],
            'marital_status' => ['nullable', 'in:single,married,divorced,widowed'],

            // Address — employee fills in
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],

            // Emergency Contact — employee fills in
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_address' => ['nullable', 'string'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:100'],

            // Bank Details — employee fills in
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_branch' => ['nullable', 'string', 'max:255'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:50'],

            // Family — employee fills in
            'spouse_name' => ['nullable', 'string', 'max:255'],
            'spouse_contact' => ['nullable', 'string', 'max:20'],
            'number_of_children' => ['nullable', 'integer', 'min:0', 'max:20'],
            'next_of_kin_name' => ['nullable', 'string', 'max:255'],
            'next_of_kin_dob' => ['nullable', 'date', 'before:today'],
            'next_of_kin_sex' => ['nullable', 'in:male,female'],

            // Education — optional
            'education' => ['nullable', 'array'],
            'education.*.institution_name' => ['required_with:education.*', 'string', 'max:255'],
            'education.*.program' => ['required_with:education.*', 'string', 'max:255'],
            'education.*.certificate_obtained' => ['required_with:education.*', 'string', 'max:255'],
            'education.*.certificates.*' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],

            // Children — optional
            'children' => ['nullable', 'array'],
            'children.*.name' => ['required_with:children.*', 'string', 'max:255'],
            'children.*.date_of_birth' => ['required_with:children.*', 'date', 'before:today'],
            'children.*.sex' => ['required_with:children.*', 'in:male,female'],
        ];
    }

    public function messages(): array
    {
        return [
            // 'full_name.required' => 'Full name is required',
            'ssnit_number.required' => 'SSNIT number is required',
            'ssnit_number.unique' => 'This SSNIT number is already registered',
            'ghana_card_number.required' => 'Ghana Card number is required',
            'ghana_card_number.unique' => 'This Ghana Card number is already registered',
            'work_email.required' => 'Work email is required',
            'work_email.unique' => 'This work email is already in use',
            'education.*.certificates.*.max' => 'Certificate file size must not exceed 10MB',
            'education.*.certificates.*.mimes' => 'Certificate must be a PDF file',
        ];
    }
}