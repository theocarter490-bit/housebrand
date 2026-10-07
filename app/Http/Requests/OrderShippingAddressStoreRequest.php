<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderShippingAddressStoreRequest extends FormRequest
{


    public function rules(): array
    {
        return [
            'fullName' => 'required',
            'phone' => 'required|numeric',
            'email' => 'required|email',
            'street' => 'required',
            'state' => 'required',
            'zipCode' => 'required|numeric',
            'country' => 'required',
            'userID' => 'required',
        ];
    }
}
