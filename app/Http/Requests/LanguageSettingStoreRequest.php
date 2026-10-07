<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LanguageSettingStoreRequest extends FormRequest
{

    public function rules(): array
    {
        /*
          "name" => "fasdfasdf"
          "code" => "fr"
          "native" => "French"
          "rtl_support" => "0"
          "is_default" => "0"
          "status" => "1"
         */
        return [
            'name'     => 'required|string',
            'code'     => 'required|string',
            'native'   => 'required|string',
            'rtl_support'      => 'required|integer',
            'is_default'      => 'required|integer',
            'status'      => 'required|integer'
        ];
    }
}
