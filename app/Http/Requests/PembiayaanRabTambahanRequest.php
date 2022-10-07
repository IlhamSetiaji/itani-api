<?php

namespace App\Http\Requests;

use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;

class PembiayaanRabTambahanRequest extends FormRequest
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
            'item_rab_id' => 'required',
            'jumlah' => 'required|numeric|min:1',
            'harga' => 'required|numeric',
            'pembiayaan_rab_tambahan_id' => 'required|string',
            'pembiayaan_id' => 'required|string',
            'pembiayaan_kunjungan_id' => 'required|string',
            'pengajuan_id' => 'required|string',
            'nilai_tambahan' => 'required|numeric',
            'mdb' => 'required|string',
            'mdb_name' => 'required|string',
        ];
    }

    protected function failedValidation(ValidationValidator $validator)
    {
        throw new HttpResponseException(ResponseFormatter::error($validator, $validator->messages(), 417));
    }
}
