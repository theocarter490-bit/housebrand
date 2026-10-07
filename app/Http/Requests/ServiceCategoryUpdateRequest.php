<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceCategoryUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status_id' => ['required', Rule::exists('service_categories', 'id')],
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('service_categories')->where(function ($query) {
                    return $query->where('user_id', getUserId());
                })->ignore($this->status_id)
            ],
            'color' => ['required'],
            'status' => ['required'],
        ];
    }
}
