<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class AssignUserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_id' => 'required|exists:project_services,id',
            'service_name' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'service_fee' => 'required|numeric|min:0'
        ];
    }


    public function messages(): array
    {
        return [
            'service_id.required' => 'The service selection is required.',
            'service_id.exists' => 'The selected service does not exist.',
            'service_name.required' => 'The service name is required.',
            'service_name.string' => 'The service name must be a valid string.',
            'user_id.required' => 'The user is required.', // Custom message
            'user_id.exists' => 'The selected user does not exist.',
            'service_fee.required' => 'The service fee is required.',
            'service_fee.numeric' => 'The service fee must be a valid number.',
            'service_fee.min' => 'The service fee must be at least 0.'
        ];
    }

}
