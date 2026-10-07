<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class BrandUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get brand id either from request input or route param
        $brandId = $this->brand_id;

        return [
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,webp',
            'name' => 'required|string|max:100|unique:brands,name,' . $brandId,
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:0,1',
            'brand_id' => 'required|exists:brands,id|integer'
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
