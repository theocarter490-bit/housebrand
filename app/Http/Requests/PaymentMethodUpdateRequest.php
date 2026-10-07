<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentMethodUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'logo' => 'sometimes|image|mimes:jpeg,png,jpg,gif',
            'payment_method_id'=>'required|exists:payment_methods,id'
        ];
    }
}
