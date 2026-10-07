<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskLabelUpdateRequest extends FormRequest
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
            'status_id' => 'required|numeric|exists:task_labels,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('task_labels')->where(function ($query) {
                    return $query->where('user_id', getUserId());
                })->ignore($this->input('status_id')),
            ],
            'color' => 'required|string',
            'status' => 'required|in:0,1',
        ];
    }
}
