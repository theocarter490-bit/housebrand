<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class CategoryUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,webp,gif',
            'name' => [
                'required',
                'string',
                'max:26',
                Rule::unique('categories')->ignore($this->category_id), // Ignore current category's ID
            ],
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:0,1',
            'category_id' => 'required|exists:categories,id|integer',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = response()->json(['errors' => $validator->errors(), 'status' => 403], 200);
        throw new ValidationException($validator, $response);
    }
}
