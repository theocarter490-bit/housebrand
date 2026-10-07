<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskStoreRequest extends FormRequest
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
            'create_task_status_id' => 'required',
            'title'=> 'required|string|max:255',
            'due_date' => 'required',
            'label' => 'required',
            'priority' => 'required',
        ];
    }
}
