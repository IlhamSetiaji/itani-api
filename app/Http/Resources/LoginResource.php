<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    private $data;

    public function __construct($resource, $data)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->data = $data;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->com_user_petani_sf()->exists()) {
            return [
                'user_id'        => $this->user_id,
                'user_alias'     => $this->user_alias,
                'user_name'      => $this->user_name,
                // 'user_key'       => $this['user_key'],
                'user_mail'      => $this->user_mail,
                'is_super_user'  => $this->com_role_user_sf()->first()->group_id == 01 ? true : false,
                'nama_lengkap'   => $this->com_user_pendamping()->exists() ? $this->com_user_pendamping()->first()->nama_lengkap : '',
                'no_telp'        => $this->com_user_pendamping()->exists() ? $this->com_user_pendamping()->first()->no_telp : '',
                'user_img'       => $this->com_user_pendamping()->exists() ? 'http://ftp.itani.id/images/pendamping/' . $this->com_user_pendamping()->first()->image_file_name : '',
                'alamat_tinggal' => $this->com_user_pendamping()->exists() ? $this->com_user_pendamping()->first()->tempat_lahir : '',
                'petani_id'     => $this->com_user_petani_sf()->exists() ? $this->com_user_petani_sf()->first()->petani_id : null,
                'pendamping_id'  => $this->data != null ? $this->data->pendamping_id : '',
                'pendamping_kd'  => $this->data != null ? $this->data->pendamping_kd : '',
                'cluster_id'     => ($this->com_role_user_sf()->first()->role_id == '02001') ? $this->com_user_petani_sf()->first()->cluster_id : (($this->data != null) ? $this->data->cluster_id : ''),
                'cluster_nama'   => $this->data != null ? $this->data->cluster_nama : '',
                'subcluster_id'  => ($this->com_role_user_sf()->first()->role_id == '02001') ? $this->com_user_petani_sf()->first()->subcluster_id : (($this->data != null) ? $this->data->subcluster_id : ''),
                'subcluster_nama' => $this->data != null ? $this->data->subcluster_nama : '',
            ];
        }
        return [
            'user_id'        => $this->user_id,
            'user_alias'     => $this->user_alias,
            'user_name'      => $this->user_name,
            // 'user_key'       => $this['user_key'],
            'user_mail'      => $this->user_mail,
            'is_super_user'  => $this->com_role_user_sf()->first()->group_id == 01 ? true : false,
            'nama_lengkap'   => $this->com_user_pendamping()->exists() ? $this->com_user_pendamping()->first()->nama_lengkap : '',
            'no_telp'        => $this->com_user_pendamping()->exists() ? $this->com_user_pendamping()->first()->no_telp : '',
            'user_img'       => $this->com_user_pendamping()->exists() ? 'http://ftp.itani.id/images/pendamping/' . $this->com_user_pendamping()->first()->image_file_name : '',
            'alamat_tinggal' => $this->com_user_pendamping()->exists() ? $this->com_user_pendamping()->first()->tempat_lahir : '',
            'petani_id'     => $this->com_user_petani_sf()->exists() ? $this->com_user_petani_sf()->first()->petani_id : null,
            'pendamping_id'  => $this->data != null ? $this->data->pendamping_id : '',
            'pendamping_kd'  => $this->data != null ? $this->data->pendamping_kd : '',
            'cluster_id'     => ($this->com_role_user_sf()->first()->role_id == '02001') ? '' : (($this->data != null) ? $this->data->cluster_id : ''),
            'cluster_nama'   => $this->data != null ? $this->data->cluster_nama : '',
            'subcluster_id'  => ($this->com_role_user_sf()->first()->role_id == '02001') ? '' : (($this->data != null) ? $this->data->subcluster_id : ''),
            'subcluster_nama' => $this->data != null ? $this->data->subcluster_nama : '',
        ];
    }
}
