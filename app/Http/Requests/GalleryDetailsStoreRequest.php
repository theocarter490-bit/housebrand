<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GalleryDetailsStoreRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gallery_id' => 'required|integer',
            'data' => 'nullable|array',
//            'data.*.id' => 'required|integer',
            'data.*.title' => 'nullable|string',
//            'data.*.image' => 'required|file|image|mimes:jpeg,png,jpg,gif,svg|max:1048',
        ];
    }

    public function messages()
    {
        return [
            'data.*.image.required' => 'An image is required for each item.',
            'data.*.image.image' => 'The file must be an image.',
        ];
    }
}
