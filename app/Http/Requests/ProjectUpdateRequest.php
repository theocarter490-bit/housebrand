<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectUpdateRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg',
            'description' => 'nullable|string',
            'date' => 'nullable|string',
            'status' => 'required|exists:project_statuses,id',
            'customer' => 'required|exists:users,id',
            'category' => 'required|exists:project_categories,id',
            'manager' => 'required|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'budget' => 'required|numeric|min:0',
            'tax_type' => 'nullable|integer|in:1,2,0', // Modify as per your tax types
            'tax' => 'nullable|numeric|min:0',
            'total_cost' => 'required|numeric|min:0',
            'active_status' => 'required|boolean',
            'address' => 'nullable|string',
            'map_location' => 'nullable|string',
            'tags' => 'nullable|json', // You may use 'json' if storing as JSON
        ];
    }
}
