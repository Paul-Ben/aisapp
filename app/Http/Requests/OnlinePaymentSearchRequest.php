<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OnlinePaymentSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'admission_number' => ['required', 'string', 'min:2', 'max:50'],
            'email' => ['required', 'email:rfc', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'admission_number.required' => 'Please enter the student admission number.',
            'admission_number.min' => 'The admission number is too short.',
            'email.required' => 'Please enter an email address for the payment receipt.',
            'email.email' => 'Please enter a valid email address.',
        ];
    }
}
