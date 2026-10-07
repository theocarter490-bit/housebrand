<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProjectServiceUpdateRequest extends FormRequest
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
    public function rules()
    {
        return [
            'service_id' => ['required', Rule::exists('project_services', 'id')],
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('project_services', 'title')->where(function ($query) {
                    return $query->where('user_id', getUserId());
                })->ignore($this->service_id)
            ],
            'cost' => ['required', 'numeric', 'min:0'], // Ensure cost is a valid number
            'service_category_id' => ['required', Rule::exists('service_categories', 'id')],
        ];
    }
}
