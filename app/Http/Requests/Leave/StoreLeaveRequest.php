<?php
// app/Http/Requests/Leave/StoreLeaveRequest.php

namespace App\Http\Requests\Leave;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // All authenticated employees can apply for leave
    }

    public function rules(): array
    {
        return [
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'leave_type_id.required' => 'Please select a leave type.',
            'start_date.required' => 'Start date is required.',
            'start_date.after_or_equal' => 'Start date cannot be in the past.',
            'end_date.required' => 'End date is required.',
            'end_date.after_or_equal' => 'End date must be on or after start date.',
            'reason.required' => 'Please provide a reason for your leave.',
            'reason.min' => 'Reason must be at least 10 characters.',
            'attachment.mimes' => 'Attachment must be a PDF or image file.',
            'attachment.max' => 'Attachment size must not exceed 2MB.',
        ];
    }
}