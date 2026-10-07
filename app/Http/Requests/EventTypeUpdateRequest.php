<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventTypeUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'event_type_id' => ['required', 'exists:event_types,id'], // Validate that the ID exists
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('event_types')->where(function ($query) {
                    return $query->where('user_id', getUserId());
                })->ignore($this->input('event_type_id')),
            ],
            'active_status' => 'required',
        ];
    }
}
