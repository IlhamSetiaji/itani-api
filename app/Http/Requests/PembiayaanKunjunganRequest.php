<?php

namespace App\Http\Requests;

use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;

class PembiayaanKunjunganRequest extends FormRequest
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
            'pembiayaan_kunjungan_id' => 'required|string',
            'pembiayaan_id' => 'required|numeric',
            'lahan_id' => 'required|numeric',
            'jenis_kunjungan' => 'required|string',
            'catatan_kunjungan' => 'required|string',
            'mdb' => 'required|string',
            'mdb_name' => 'required|string',
        ];
    }

    protected function failedValidation(ValidationValidator $validator)
    {
        throw new HttpResponseException(ResponseFormatter::error($validator, $validator->messages(), 417));
    }
}
