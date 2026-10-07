<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProjectCategoryUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif',
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('project_categories')->where(function ($query) {
                    return $query->where('user_id', getUserId());
                })->ignore($this->input('category_id')),
            ],
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:0,1',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = response()->json(['errors' => $validator->errors(), 'status' => 403], 200);
        throw new ValidationException($validator, $response);
    }
}
