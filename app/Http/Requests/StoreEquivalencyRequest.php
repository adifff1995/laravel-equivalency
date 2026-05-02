<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEquivalencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public form — allow all
    }

    public function rules(): array
    {
        $type = $this->input('type');

        $rules = [
            'name'        => ['required', 'string', 'max:255'],
            'student_id'  => ['required', 'string', 'max:50'],
            'email'       => ['nullable', 'email', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:30'],
            'type'        => ['required', 'in:special,internal,external_bridge,external_other'],
            'major'       => ['required', 'string', 'max:255'],
            'courses'     => ['required', 'string'],
            'attachments' => ['nullable', 'array'],
            'attachments.*'=> ['file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
        ];

        // Internal transfer: requires both old and new student IDs
        if (in_array($type, ['internal'])) {
            $rules['old_student_id'] = ['required', 'string', 'max:50'];
            $rules['new_student_id'] = ['required', 'string', 'max:50'];
        }

        // External: requires university
        if (in_array($type, ['external_bridge', 'external_other'])) {
            $rules['university'] = ['required', 'string', 'max:255'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'Student name is required.',
            'student_id.required' => 'Student ID is required.',
            'type.required'       => 'Request type is required.',
            'major.required'      => 'Major is required.',
            'courses.required'    => 'Course list is required.',
            'university.required' => 'University name is required for external requests.',
        ];
    }
}
