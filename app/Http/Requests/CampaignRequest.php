<?php

namespace App\Http\Requests;

use App\Models\Campaign;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class CampaignRequest extends FormRequest
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
        $regexPattern = '/^[a-zA-Z0-9.,\-_"\':&!\s]+$/';
        $max = 50;
        $required = 'required';

        return [
            "name" =>  "{$required}|max:{$max}|regex:{$regexPattern}",
            "sender_email" => "{$required}|not_in:0",
            "sender_name" => "{$required}|max:{$max}|regex:{$regexPattern}",
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) { //digunakan untuk menambahkan logika validasi tambahan setelah validasi utama selesai 
            // membuat menjadi lower case 
            $lowerName = Str::lower($this->input('name'));

            // Cek apakah ada data di database dengan kasus huruf yang sama
            // $existingData = Campaign::whereRaw('LOWER(name) = ?', [$lowerName])->first();
            if ($this->isMethod('put')) { // jika method put 
                $data = $this->route('campaign'); // Ganti 'campaign' dengan nama parameter rute sesuai kebutuhan
                // untuk pengecekan database 
                $existingData = Campaign::whereRaw('LOWER(name) = ?', [$lowerName])
                    ->where('id', '<>', $data->id)
                    ->first();
            } else {
                // untuk pengecekan database 
                $existingData = Campaign::whereRaw('LOWER(name) = ?', [$lowerName])->first();
            }

            if ($existingData) {
                // menampilkan error 
                $validator->errors()->add('name', 'Data sudah ada dalam database.');
            }
        });
    }
}
