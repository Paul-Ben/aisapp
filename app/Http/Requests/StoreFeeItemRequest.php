<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeeItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
            'classes' => ['nullable', 'array'],
            'classes.*' => ['exists:classes,id'],
            'class_categories' => ['nullable', 'array'],
            'class_categories.*' => ['exists:class_categories,id'],
        ];
    }
}
