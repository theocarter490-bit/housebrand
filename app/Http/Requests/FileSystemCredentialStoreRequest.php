<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileSystemCredentialStoreRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'AWS_ACCESS_KEY_ID' => 'required|string',
            'AWS_SECRET_ACCESS_KEY' => 'required|string',
            'AWS_DEFAULT_REGION' => 'required|string',
            'AWS_BUCKET' => 'required|string'
        ];
    }
}
