<?php

namespace App\Http\Requests;

use App\Models\ShopSetting;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class SystemInfoStoreRequest extends FormRequest
{

    protected $shopSetting;

    public function prepareForValidation()
    {
        $this->shopSetting = ShopSetting::where('user_id', getUserId())->first();
    }

    public function rules(): array
    {
        return [
            'shop_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('shop_settings', 'shop_name')->ignore($this->shopSetting ? $this->shopSetting->id : null),
            ],
            'address' => 'required|string',
            'phone' => [
                'required',
                'numeric',
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('shop_settings', 'email')->ignore($this->shopSetting ? $this->shopSetting->id : null),
            ],
            'map_location' => 'nullable|string',
            'copy_right' => 'nullable|string',
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        $response = response()->json(['errors' => $validator->errors(), 'status' => 403], 200);
        throw new ValidationException($validator, $response);
    }
}
