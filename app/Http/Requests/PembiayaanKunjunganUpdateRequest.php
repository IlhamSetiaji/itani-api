<?php

namespace App\Http\Requests;

use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;

class PembiayaanKunjunganUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'status_kunjungan' => 'required|string',
            'analisis_penyebab' => 'required|string',
            'luas_terdampak' => 'required|numeric',
            'penyakit' => 'required|string',
            'hama' => 'required|string',
            'bencana' => 'required|string',
            'hasil_pengamatan' => 'required|string',
            'rekomendasi' => 'nullable|string',
            'mdb' => 'required|string',
            'mdb_name' => 'required|string',
        ];
    }

    protected function failedValidation(ValidationValidator $validator)
    {
        throw new HttpResponseException(ResponseFormatter::error($validator, $validator->messages(), 417));
    }
}
