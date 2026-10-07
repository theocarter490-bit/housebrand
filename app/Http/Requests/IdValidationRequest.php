<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class IdValidationRequest extends FormRequest
{

    public function rules(): array
    {
        $possibleFields = [
            'id',
            'brand_id',
            'order_id',
            'user_id',
            'unit_id',
            'gallery_id',
            'special_sections_category_id',
            'category_id',
            'section_id',
            'product_id',
            'attribute_id',
            'vendor_id',
            'role_id',
            'value_id',
            'product_image_id',
            'shipping_addresses_id',
        ];

        $field = $this->getDynamicField($possibleFields);

        return [
            $field  => 'required|integer',
        ];
    }

    private function getDynamicField(array $possibleFields): string
    {
        foreach ($possibleFields as $field) {
            if ($this->has($field)) {
                return $field;
            }
        }

        return 'id';
    }

    protected function failedValidation(Validator $validator)
    {
        $response = response()->json(['errors' => $validator->errors(), 'status' => 403], 200);
        throw new ValidationException($validator, $response);
    }
}
