<?php

namespace App\Http\Requests;

use App\Models\AttributeValue;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AttributeValueUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $attribute_id = AttributeValue::find($this->value_id)->attribute_id;
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('attribute_values', 'name')
                    ->where(fn($q) => $q->where('attribute_id', $attribute_id))
                    ->ignore($this->value_id), // 👈 ignore current row when updating
            ],
            'aditional_value' => 'nullable|string|max:255',
            'value_id' => 'required|exists:attribute_values,id|integer',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = response()->json(['errors' => $validator->errors(), 'status' => 403], 200);
        throw new ValidationException($validator, $response);
    }
}
