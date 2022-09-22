<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PetaniUpdateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if (empty($this)) {
            return null;
        }
        // get user img
        $image_path = empty($this->user_img_path) ? '' : trim($this->user_img_path, '/') . '/' . trim($this->user_img_name, '/');
        $user_img   = is_file($image_path) ? $image_path : 'resource/doc/images/users/default.png';
        return array(
            'user_id'       => $this->user_id,
            'user_alias'    => $this->user_alias,
            'user_name'     => $this->user_name,
            'user_pass'     => $this->user_pass,
            'user_key'      => $this->user_key,
            'user_mail'     => $this->user_mail,
            'is_locked'     => $this->user_st == '0',
            'is_super_user' => (bool) $this->super_user_id,
            'nama_lengkap'  => $this->nama_lengkap,
            'alamat'        => $this->alamat_ktp,
            'no_telp'       => $this->nomor_telepon,
            'user_img'      => 'http://ftp.itani.id/images/pendamping/' . $user_img,
            'user_img_name'  => $this->user_img_name != null ? $this->user_img_name : '',
            'user_img_path'  => $this->user_img_path != null ? $this->user_img_path : '',
            'user_st'        => $this->user_st,
            'nomor_telepon'  => $this->nomor_telepon,
            'alamat_tinggal' => $this->alamat_tinggal,
        );
    }
}
