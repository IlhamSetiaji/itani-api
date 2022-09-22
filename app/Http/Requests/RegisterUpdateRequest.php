<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;

class RegisterUpdateRequest extends FormRequest
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
        $userID = request('userID');
        return [
            'nama_lengkap' => 'required|string|max:100',
            'user_mail' => 'required|email|max:50|unique:mysql_third.com_user,user_mail,' . $userID . ',user_id',
            'nomor_telepon' => 'required|string|max:50',
            'alamat_tinggal' => 'required|string|max:100',
            'user_name' => 'required|string',
            'user_pass' => 'nullable|string',
            'user_st' => 'required',
            'petani_id' => 'required',
        ];
    }

    protected function failedValidation(ValidationValidator $validator)
    {
        throw new HttpResponseException(ResponseFormatter::error($validator, $validator->messages(), 417));
    }
}
