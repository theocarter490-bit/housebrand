<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateRequest extends FormRequest
{
    public function rules(): array
    {

        return [
            'shippingAddressId' => 'nullable|exists:shipping_addresses,id|integer',
            'discount_type' => 'required|in:0,1,2',
            'discount_value' => 'numeric',
            'discount_amount' => 'numeric',
            'tax_type' => 'required|in:0,1,2',
            'tax_value' => 'numeric',
            'tax_amount' => 'numeric',
            'sub_total' => 'required|numeric',
            'total' => 'required|numeric',
            'product' => 'required|array',
            'product.required' => 'There have to have at least one product in the cart before place order.',
        ];
    }
}
