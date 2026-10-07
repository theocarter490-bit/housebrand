<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IdeaBoardCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,gif',
            'title' => [
                'required',
                'string',
                'max:26',
                Rule::unique('idea_boards', 'title')
                    ->where(fn($query) =>
                    $query->where('project_id', $this->route('project'))
                    ),
            ],
            'budget' => 'required|numeric',
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:0,1',
        ];
    }
}
