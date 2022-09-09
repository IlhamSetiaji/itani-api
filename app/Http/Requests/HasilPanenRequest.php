<?php

namespace App\Http\Requests;

use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;

class HasilPanenRequest extends FormRequest
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
            "pengangkutan_hasil_id" => 'required|string',
            "pengangkutan_id" => 'required|string',
            "pembiayaan_id" => 'required|string',
            "petani_id" => 'required|string',
            "petani_nama" => 'required|string',
            "lahan_id" => 'required|string',
            "lahan_kd" => 'required|string',
            "berat_gkp" => 'required|numeric',
            "karung_gkp" => 'required|numeric',
            "mdb" => 'required|string',
            "mdb_name" => 'required|string',
        ];
    }

    protected function failedValidation(ValidationValidator $validator)
    {
        throw new HttpResponseException(ResponseFormatter::error($validator, $validator->messages(), 417));
    }
}
