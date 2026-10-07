<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class SubscriberMailStoreRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'email' => 'required|string|email:rfc,dns|unique:subscribers,email'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'Already Subscribed With This Email',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = sendError($validator->errors(), [], 422);
        throw new ValidationException($validator, $response);
    }
}
