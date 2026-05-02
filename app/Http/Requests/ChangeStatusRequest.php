<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:under_review,ready_for_entry,entered,approved,rejected'],
            'notes'  => ['nullable', 'string', 'max:2000'],
        ];
    }
}
