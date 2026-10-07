<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SectionCategoryUpdateRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $id = $this->special_sections_category_id;
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('special_section_categories', 'name')
                    ->where('user_id', auth()->id())
                    ->ignore($id),
            ],
            'status' => 'required|in:0,1',
            'special_sections_category_id' => 'required|exists:special_section_categories,id|integer'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = response()->json(['errors' => $validator->errors(), 'status' => 403], 200);
        throw new ValidationException($validator, $response);
    }
}
