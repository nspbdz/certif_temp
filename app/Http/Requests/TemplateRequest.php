<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rule = [
            //
            'name'                  => 'required|max:60',          
            'email_subject'         => 'required|max:60',
            'email_body'            => 'required|max_html:1000',
            'font_type'             => 'nullable',
            'font_color'            => 'nullable',
            'text_position'         => 'nullable',
            'certificate_name'      => 'nullable|max:65|alpha_dash',
            'certificate_author'    => 'nullable|max:15',
            'pdf_title'             => 'nullable|max:65',
            'certificate_image'     => 'nullable|image|mimes:jpeg,jpg|max:300'
        ];

        if (request()->method() == "POST") {
            $rule['campaign']       = 'required';
            $rule['header_image']   = 'required|image|mimes:jpeg,jpg|max:100';
            $rule['email_type']     = 'required';
        } else if (request()->method() == "PUT") {
            $rule['header_image'] = 'nullable|image|mimes:jpeg,jpg|max:100';
        }
        return $rule;
    }
}
