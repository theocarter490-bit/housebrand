<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class BlogPostRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        switch ($this->route()->getName()) {
            case 'blog.post.update':
                $rules = [
                    'title' => 'required|string|max:255',
                    'category_id' => 'required',
                ];
                break;
            default:
                $rules = [
                    'title' => 'required|string|max:255',
                    'category_id' => 'required',
                    'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
                ];
                break;
            }

            return $rules;
    }
    protected function failedValidation(Validator $validator)
    {
        $response = response()->json(['errors' => $validator->errors(), 'status' => 403], 200);
        throw new ValidationException($validator, $response);
    }

}

