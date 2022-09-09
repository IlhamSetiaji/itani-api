<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'pengangkutan_id' => 'required|numeric',
            'truk_nopol' => 'required|string',
            'supir_id' => 'required|string',
            'pengangkutan_st' => 'required|in:yes,no',
            'jumlah_lahan' => 'required|numeric',
            'jumlah_karung' => 'required|numeric',
            'total_berat' => 'required|numeric',
            'mdb' => 'required|string',
            'mdb_name' => 'required|string',
        ];
    }
}
