<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventTypeStoreRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('event_types')->where(function ($query) {
                    return $query->where('user_id', getUserId());
                }),
            ],
            'active_status' => 'required',
        ];
    }
}
