<?php

namespace App\Http\Requests;

use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;

class PengangkutanUpdateRequest extends FormRequest
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
            'pengangkutan_hasil_id' => 'required',
            'pengangkutan_id' => 'required',
            'pembiayaan_id' => 'required',
            'petani_id' => 'required',
            'petani_nama' => 'required',
            'lahan_id' => 'required',
            'lahan_kd' => 'required',
            'berat_gkp' => 'required',
            'karung_gkp' => 'required',
            'mdb' => 'required|string',
            'mdb_name' => 'required|string',
        ];
    }

    protected function failedValidation(ValidationValidator $validator)
    {
        throw new HttpResponseException(ResponseFormatter::error($validator, $validator->messages(), 417));
    }
}
