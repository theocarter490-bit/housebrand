<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskLabelStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('task_labels')->where(function ($query) {
                    return $query->where('user_id', getUserId());
                }),
            ],
            'color' => 'required|string',
            'status' => 'required|in:0,1',
        ];
    }
}
