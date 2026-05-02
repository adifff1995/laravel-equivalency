<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEquivalencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        $type = $this->input('type');

        $rules = [
            'name'         => ['required', 'string', 'max:255'],
            'student_id'   => ['required', 'string', 'max:50'],
            'type'         => ['required', 'in:special,internal,external_bridge,external_other'],
            'major'        => ['required', 'string', 'max:255'],
            'courses'      => ['required', 'string'],
            'attachments'  => ['nullable', 'array'],
            'attachments.*'=> ['file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
        ];

        if (in_array($type, ['internal'])) {
            $rules['old_student_id'] = ['required', 'string', 'max:50'];
            $rules['new_student_id'] = ['required', 'string', 'max:50'];
        }

        if (in_array($type, ['external_bridge', 'external_other'])) {
            $rules['university'] = ['required', 'string', 'max:255'];
        }

        return $rules;
    }
}
