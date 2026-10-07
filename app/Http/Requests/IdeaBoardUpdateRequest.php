<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class IdeaBoardUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif',
            'title' => [
                'required',
                'string',
                'max:26',
                Rule::unique('idea_boards', 'title')
                    ->where(fn($query) => $query->where('project_id', $this->route('project'))
                    )
                    ->ignore($this->idea_board_id),
            ],
            'budget' => 'required|numeric',
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:0,1',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'errors' => $validator->errors(),
            'status' => 403,
        ], 200);

        throw new ValidationException($validator, $response);
    }
}
