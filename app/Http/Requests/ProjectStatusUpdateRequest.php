<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectStatusUpdateRequest extends FormRequest
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
            'status_id' => 'required|numeric|exists:project_statuses,id',
            'name' => 'required|string|max:26',
            'color' => 'required|string',
            'status' => 'required|in:0,1',
        ];
    }

}
