<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\AuthLogin;
use App\Models\PembiayaanRab;
use App\Interfaces\AuthInterface;
use App\Interfaces\LoginInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\LoginResource;

class AuthRepository implements AuthInterface
{
    public function getDataUser($user)
    {
        if ($user->com_role_user_sf()->first()->role_id == '02005') {
            $data = AuthLogin::UserAgronomis($user);
        } elseif ($user->com_role_user_sf()->first()->role_id == '02003') {
            $data = AuthLogin::UserAccountOfficer($user);
        } elseif ($user->com_role_user_sf()->first()->role_id == '02001' || $user->com_role_user_sf()->first()->role_id == '02015') {
            $data = AuthLogin::UserPetaniAndSupir($user);
        }
        $result = [
            'User' => $user->roles()->first() == null ? $user->load('com_role_user_sf') : $user->load('roles'),
            'result' => new LoginResource($user, $data),
        ];
        return $result;
    }
}
