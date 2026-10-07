<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class OrderStoreRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id|integer',
            'shippingAddressId' => 'required|exists:shipping_addresses,id|integer',
            'discount_type' => 'required|in:0,1,2',
            'discount_value' => 'numeric',
            'discount_amount' => 'numeric',
            'tax_value' => 'required|in:0,1,2',
            'tax_value' => 'numeric',
            'tax_amount' => 'numeric',
            'sub_total' => 'required|numeric',
            'total' => 'required|numeric',
            'product' => 'required|array',
            'product.required' => 'There have to have at least one poduct in the cart before place order.',
            'shippingAddressId.required' => 'Please add a shipping address before place order.',
        ];
    }
}
