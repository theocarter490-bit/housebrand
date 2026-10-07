<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ColorThemeStoreRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'theme_name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('color_themes','name')->where(function ($query) {
                    return $query->where('user_id', getUserId());
                }),
            ],
        ];
    }


}
