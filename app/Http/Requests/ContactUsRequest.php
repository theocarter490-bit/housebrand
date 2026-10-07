<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ContactUsRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'name'          => 'required|string',
            'email'         => 'required|email',
            'phone'         => 'required|numeric',
            'message'       => 'required|string|max:1000',
        ];
    }
}
