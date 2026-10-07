<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
class StoreExpenseRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'expense_type' => 'required|exists:expense_types,id',
            'amount' => 'required|numeric|min:0|max:9999999999',
            'voucher' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,gif,bmp,pdf,doc,docx,xls,xlsx,ppt,pptx',
            'expense_date' => 'required|date',
            'payment_method'=> 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'payment_method' => 'The payment method field is required.'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = response()->json(['errors' => $validator->errors(), 'status' => 403], 200);
        throw new ValidationException($validator, $response);
    }

}
