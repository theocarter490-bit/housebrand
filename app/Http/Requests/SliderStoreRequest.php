<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SliderStoreRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'title' => 'nullable|string',
            'image' => [
                'required',
                function ($attribute, $value, $fail) {
                    $fileType = request()->input('file_type');
                    $image = request()->file('image');

                    if ($fileType == 0 && $image && !in_array($image->getMimeType(), ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])) {
                        $fail('The File must be a valid image file (jpeg, png, jpg, webp) when file_type is image.');
                    }

                    if ($fileType == 1 && $image && !in_array($image->getMimeType(), ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska'])) {
                        $fail('The File must be a valid video file (mp4, mov, avi, mkv) when file_type is video.');
                    }
                },
            ],
            'description' => 'string|nullable',
            'slider_type' => 'required',
            'file_type' => 'required|in:0,1',
            'active_status' => 'required',
        ];

    }
}
