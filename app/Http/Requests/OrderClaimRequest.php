<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class OrderClaimRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'claim_issue_type_id'=>'required|integer|exists:order_claim_issue_types,id',
            'details'=>'required|string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = response()->json(['errors' => $validator->errors(), 'status' => 403], 200);
        throw new ValidationException($validator, $response);
    }
}
