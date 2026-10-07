<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'review_type_id' => 'required|integer|exists:review_types,id',
            'review' => 'required|string',
            'rating' => 'required|numeric|min:1|max:5',
        ];
    }
}
