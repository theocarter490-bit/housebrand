<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailStoreRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email_engine_type' => 'required|in:smtp,mailgun,ses,postmark,sendmail',
            'from_name' => 'required|string|min:2|max:255',
            'from_email' => 'required|email:rfc,dns',
            'mail_driver' => 'required|string|in:smtp,sendmail,mail',
            'mail_host' => 'required|string|regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'mail_port' => 'required|integer|between:1,65535',
            'mail_username' => 'required|string|min:1',
            'mail_password' => 'required|string|min:1',
            'mail_encryption' => 'required|string|in:tls,ssl,none',
        ];
    }

}
