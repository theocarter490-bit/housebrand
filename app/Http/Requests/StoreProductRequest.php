<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|max:255',
            'category' => 'required|exists:categories,id',
            'unit' => 'required',
            'attributes' => 'nullable|array',
            'attribute_values' => 'nullable|array',
            'attribute_values.*.value' => 'array|required',
            'attribute_values.*.attribute_id' => 'required',
            'variant' => 'nullable|array',
            'weightAndDiamensions' => 'nullable|array',
            'specifications' => 'nullable|array',
            'ecommerce_product_tags' => 'nullable',
            'video_link' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'description' => 'nullable',
            'shipping_policy' => 'nullable',
            'return_policy' => 'nullable',
            'disclaimer' => 'nullable',
            'unit_price' => 'required|numeric|min:0',
            'discount_type' => 'required|in:0,1,2',
            'discount_value' => ['required_if:discount_type,1,2', Rule::prohibitedIf(function () {
                return match ($this->request->get('discount_type')) {
                    '1' => !($this->request->get('discount_value') <= 100),
                    '2' => !($this->request->get('discount_value') <= $this->request->get('unit_price')),
                    default => false,
                };
            })],
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,webp',
            'meta_image' => 'nullable|image|mimes:jpeg,png,jpg,webp',
            'variant.*.discount' => [
                'required_if:discount_type,1,2',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1]; // Extract the index from the attribute (e.g., variant.0.discount -> 0)
                    $price = request("variant.$index.price"); // Get the corresponding price

                    $discountType = request('discount_type');
                    if ($discountType == '1' && $value > 100) {
                        $fail("The discount is percentage can't be greater than 100.");
                    }

                    if ($discountType == '2' && $value > $price) {
                        $fail("The discount cannot be greater than the price for index $index.");
                    }
                }
            ],
            'variant.*.price' => 'required|numeric|min:0',
            'variant.*.quantity' => 'required|numeric|min:0',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'discount_value.required_if' => 'Discount value is required when product has discount type: Fixed or Percentage',
            'unit_price.required' => 'Base Price is required.',
            'discount_value.prohibited' => 'Discount value is invalid or less than base price.',
            'variant.*.discount.required_if' => 'Variant discount value is required when product has discount type: Fixed or Percentage',
            'attribute_values.*.value' => 'Variant attribute value is required when attribute value is provided.',
        ];
    }
}
