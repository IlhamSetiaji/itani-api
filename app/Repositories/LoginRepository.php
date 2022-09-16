<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\PembiayaanRab;
use App\Interfaces\LoginInterface;
use Illuminate\Support\Facades\DB;

class LoginRepository implements LoginInterface
{
    public function createResponse($user)
    {
        $token = $user->createToken('authToken')->plainTextToken;
        if ($user->com_role_user_sf()->first()->role_id == '02005') {
            $data = collect(DB::connection('mysql_second')->select("SELECT a.*, c.pendamping_kd, d.cluster_nama, e.subcluster_nama
                    FROM pendamping_agronomis a
                        INNER JOIN com_user_pendamping b ON a.pendamping_id = b.pendamping_id
                        INNER JOIN pendamping c ON a.pendamping_id = c.pendamping_id
                        LEFT JOIN master_cluster d ON a.cluster_id = d.cluster_id
                        LEFT JOIN master_subcluster e ON a.subcluster_id = e.subcluster_id
                    WHERE b.user_id = :user_id", [
                'user_id' => $user->user_id,
            ]))->first();
        } elseif ($user->com_role_user_sf()->first()->role_id == '02003') {
            $data = collect(DB::connection('mysql_second')->select("SELECT a.*, c.pendamping_kd, d.cluster_nama, e.subcluster_nama
                    FROM pendamping_account_officer a
                        INNER JOIN com_user_pendamping b ON a.pendamping_id = b.pendamping_id
                        INNER JOIN pendamping c ON a.pendamping_id = c.pendamping_id
                        LEFT JOIN master_cluster d ON a.cluster_id = d.cluster_id
                        LEFT JOIN master_subcluster e ON a.subcluster_id = e.subcluster_id
                    WHERE b.user_id = :user_id", [
                'user_id' => $user->user_id,
            ]))->first();
        } elseif ($user->com_role_user_sf()->first()->role_id == '02001' || $user->com_role_user_sf()->first()->role_id == '02015') {
            // $data = collect(DB::connection('mysql_second')->select("SELECT c.pendamping_kd, d.cluster_nama, e.subcluster_nama
            $data = collect(DB::connection('mysql_second')->select("SELECT c.pendamping_kd
                    FROM com_user_pendamping b
                        INNER JOIN pendamping c ON b.pendamping_id = c.pendamping_id
                        -- LEFT JOIN master_cluster d ON a.cluster_id = d.cluster_id
                        -- LEFT JOIN master_subcluster e ON a.subcluster_id = e.subcluster_id
                    WHERE b.user_id = :user_id", [
                'user_id' => $user->user_id,
            ]))->first();
        }
        $result = array(
            'user_id'        => $user->user_id,
            'user_alias'     => $user->user_alias,
            'user_name'      => $user->user_name,
            // 'user_key'       => $user['user_key'],
            'user_mail'      => $user->user_mail,
            'is_super_user'  => $user->com_role_user_sf()->first()->group_id == 01 ? true : false,
            'nama_lengkap'   => $user->com_user_pendamping()->exists() ? $user->com_user_pendamping()->first()->nama_lengkap : '',
            'no_telp'        => $user->com_user_pendamping()->exists() ? $user->com_user_pendamping()->first()->no_telp : '',
            'user_img'       => $user->com_user_pendamping()->exists() ? 'http://ftp.itani.id/images/pendamping/' . $user->com_user_pendamping()->first()->image_file_name : '',
            'alamat_tinggal' => $user->com_user_pendamping()->exists() ? $user->com_user_pendamping()->first()->tempat_lahir : '',
            'petani_id'     => $user->com_user_petani_sf()->exists() ? $user->com_user_petani_sf()->first()->petani_id : null,
            'pendamping_id'  => $data != null ? $data->pendamping_id : '',
            'pendamping_kd'  => $data != null ? $data->pendamping_kd : '',
            'cluster_id'     => ($user->com_role_user_sf()->first()->role_id == '02001') ? $user->com_user_petani_sf()->first()->cluster_id : (($data != null) ? $data->cluster_id : ''),
            'cluster_nama'   => $data != null ? $data->cluster_nama : '',
            'subcluster_id'  => ($user->com_role_user_sf()->first()->role_id == '02001') ? $user->com_user_petani_sf()->first()->subcluster_id : (($data != null) ? $data->subcluster_id : ''),
            'subcluster_nama' => $data != null ? $data->subcluster_nama : '',
        );
        $access = [
            'Access Token' => $token,
            'Token Type' => 'Bearer Token',
            'User' => $user->roles()->first() == null ? $user->load('com_role_user_sf') : $user->load('roles'),
            'result' => $result,
        ];
        return $access;
    }
}
