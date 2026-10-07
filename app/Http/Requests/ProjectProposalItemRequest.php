<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ProjectProposalItemRequest extends FormRequest
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
            'product' => 'required|array|min:1',
//            'product.*.product_id' => 'required|integer|exists:products,id',
            'product.*.type' => 'required|integer|in:1,2',
//            'product.*.variant' => 'required|array|min:1',
            'product.*.price' => 'required|numeric|min:0',
            'product.*.quantity' => 'required|integer|min:1',
            'sub_total' => 'required|numeric|min:0',
//            'discount_type' => 'required|integer|in:0,1,2',
//            'discount_value' => 'required|numeric|min:0',
//            'discount_amount' => 'required|numeric|min:0',
//            'tax_type' => 'required|integer|in:0,1,2',
//            'tax_value' => 'required|numeric|min:0',
//            'tax_amount' => 'required|numeric|min:0',
//            'shipping_charge' => 'required|numeric|min:0',
            'deposite_request' => 'required|numeric|min:0',
            'total' => 'required|numeric',
        ];
    }

}
