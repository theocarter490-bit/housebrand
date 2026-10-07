<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class GeneralSettingSystemInfoStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'siteName' => 'nullable|string|max:255',
            'siteTitle' => 'nullable|string',
            'copy_right' => 'nullable|string',
            'timeZone' => 'required|exists:time_zones,id',
            'currence' => 'required|exists:currencies,id',
            'dateFormat' => 'required|exists:date_formats,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = response()->json(['errors' => $validator->errors(), 'status' => 403], 200);
        throw new ValidationException($validator, $response);
    }
}
