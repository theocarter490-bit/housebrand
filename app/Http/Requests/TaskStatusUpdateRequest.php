<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskStatusUpdateRequest extends FormRequest
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
            'status_id'=> 'required|numeric|exists:task_statuses,id',
            'name' => 'required|string|max:26',
            'color'=> 'required|string',
            'status' => 'required|in:0,1',
            'project_id' => 'required|numeric|exists:projects,id',
        ];
    }
}
