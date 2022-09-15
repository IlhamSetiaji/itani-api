<?php

namespace App\Http\Requests;

use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;

class PostLahanRequest extends FormRequest
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
            // 'lahan_id' => 'required',
            // 'lahan_kd' => 'required',
            'nama_pemilik' => 'required',
            'luas_lahan' => 'required',
            'luas_sppt' => 'required',
            'lahan_st' => 'nullable',
            'koordinat' => 'required',
            'geojson' => 'required',
            'alamat' => 'required',
            'rt' => 'required',
            'rw' => 'required',
            'prov_id' => 'required',
            'kab_id' => 'required',
            'kec_id' => 'required',
            'kel_id' => 'required',
            // 'kelompok_id' => 'required',
            'blok_lahan_id' => 'nullable',
            'cluster_id' => 'required',
            'subcluster_id' => 'required',
            'user_id' => 'required',
            'nama_lengkap' => 'required',
        ];
    }

    protected function failedValidation(ValidationValidator $validator)
    {
        throw new HttpResponseException(ResponseFormatter::error($validator, $validator->messages(), 417));
    }
}
