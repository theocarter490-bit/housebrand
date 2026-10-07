<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class AttributeUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Grab attribute id either from input or from route param
        $attributeId = $this->attribute_id;

        return [
            'name' => 'required|string|unique:attributes,name,' . $attributeId,
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg|max:1024',
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:0,1',
            'attribute_id' => 'required|exists:attributes,id|integer'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = response()->json(['errors' => $validator->errors(), 'status' => 403], 200);
        throw new ValidationException($validator, $response);
    }
}
