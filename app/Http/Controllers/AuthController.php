<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\ComUser;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
        if ($validator->fails()) {
            return ResponseFormatter::error($validator, $validator->messages(), 403);
        }
        $user = ComUser::where('user_mail', request('email'))->first();
        if (!$user) {
            return ResponseFormatter::error(null, 'Email tidak ditemukan', 404);
        }
        // if (!Hash::check(request('password'), $user->password)) {
        if ($user->user_pass != md5(request('password'))) {
            return ResponseFormatter::error(null, 'Password anda salah', 417);
        }
        try {
            Auth::login($user);
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
            } elseif ($user->com_role_user_sf()->first()->role_id == '02001') {
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
            return ResponseFormatter::success($access, 'Login sukses');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), 400);
        }
    }
}
