<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingAddressUpdateRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required|email',
            'country' => 'required',
            'state' => 'required',
            'street_address' => 'required',
            'zip_code' => 'string',
            'shipping_address_id' => 'required|exists:shipping_addresses,id|integer',
        ];
    }
}
