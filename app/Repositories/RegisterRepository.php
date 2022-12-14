<?php

namespace App\Repositories;

use Exception;
use Carbon\Carbon;
use App\Models\ComUser;
use App\Models\Pegawai;
use App\Models\AuthLogin;
use App\Models\PembiayaanRab;
use App\Helpers\ResponseFormatter;
use App\Http\Requests\RegisterUpdateRequest;
use App\Interfaces\LoginInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\LoginResource;
use App\Interfaces\RegisterInterface;

class RegisterRepository implements RegisterInterface
{
    public function generate_id()
    {
        list($usec, $sec) = explode(" ", microtime());
        $microtime = $sec . $usec;
        $microtime = str_replace(array(',', '.'), array('', ''), $microtime);
        $microtime = substr_replace($microtime, rand(10000, 99999), -2);
        return $microtime;
    }

    public function createResponse($payload)
    {
        $params = array();
        $key  = crc32($payload['user_pass']);
        $user_key = abs($key);
        $params['com_user'] = array(
            'user_id'       => $this->generate_id(),
            'user_alias'    => $payload['nama_lengkap'],
            'user_name'     => $payload['user_name'],
            'user_pass'     => Hash::make($payload['user_pass'] . $user_key),
            'user_key'      => $user_key,
            'user_mail'     => $payload['user_mail'],
            'user_st'       => $payload['user_st'],
            'user_completed' => '1',
            'mdd'           => Carbon::now(),
        );
        $params['pegawai'] = array(
            'user_id'           => $this->generate_id(),
            'nama_lengkap'      => $payload['nama_lengkap'],
            'alamat_tinggal'    => $payload['alamat_tinggal'],
            'nomor_telepon'     => $payload['nomor_telepon'],
            'mdd'       => date("Y-m-d H:i:s"),
        );
        try {
            $result = ComUser::create($params['com_user']);
            $pegawai = Pegawai::create($params['pegawai']);
            try {
                $result->com_role_user_sf()->attach('02001', [
                    'role_default' => '1',
                    'role_display' => '1',
                ]);
            } catch (Exception $e) {
                $result->delete();
                $pegawai->delete();
                return ResponseFormatter::error(null, $e->getMessage(), 400);
            }
            try {
                $result->com_user_petani_sf()->attach($payload['petani_id']);
            } catch (Exception $e) {
                $result->delete();
                $pegawai->delete();
                return ResponseFormatter::error(null, $e->getMessage(), 400);
            }
            return $result;
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), 400);
        }
    }

    public function updateResponse($payload, $user, $pegawai)
    {
        $params['com_user'] = array(
            // 'user_id'       => $this->generate_id(),
            'user_alias'    => $payload['nama_lengkap'],
            'user_name'     => $payload['user_name'],
            'user_mail'     => $payload['user_mail'],
            'user_st'       => $payload['user_st'],
            'user_completed' => '1',
            'mdd'           => Carbon::now(),
        );
        $params['pegawai'] = array(
            // 'user_id'           => $this->generate_id(),
            'nama_lengkap'      => $payload['nama_lengkap'],
            'alamat_tinggal'    => $payload['alamat_tinggal'],
            'nomor_telepon'     => $payload['nomor_telepon'],
            'mdd'       => date("Y-m-d H:i:s"),
        );
        if (!empty($payload['user_pass'])) {
            $key  = crc32($payload['user_pass']);
            $user_key = abs($key);
            $params['com_user']['user_pass'] = Hash::make($payload['user_pass'] . $user_key);
            $params['com_user']['user_key'] = $user_key;
        }
        try {
            $user->update($params['com_user']);
            $pegawai->update($params['pegawai']);
            return $user;
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), 400);
        }
    }
}
