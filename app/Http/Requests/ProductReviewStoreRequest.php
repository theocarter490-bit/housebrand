<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductReviewStoreRequest extends FormRequest
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
            'product_id' => 'required|integer|exists:products,id',
            'review_type_id' => 'required|integer|exists:review_types,id',
            'review' => 'required|string|max:800',
            'rating' => 'required|numeric|min:1|max:5',
        ];
    }
}
