<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UnitUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Use either the request body unit_id or route parameter
        $unitId = $this->unit_id;

        return [
            'name' => 'required|string|unique:units,name,' . $unitId,
            // 'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg|max:1024',
            'status' => 'required|in:0,1',
            'unit_id' => 'required|exists:units,id|integer'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'errors' => $validator->errors(),
            'status' => 403
        ], 200);

        throw new ValidationException($validator, $response);
    }
}
