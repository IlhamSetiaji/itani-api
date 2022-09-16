<?php

namespace App\Repositories;

use App\Http\Resources\LoginResource;
use Carbon\Carbon;
use App\Models\PembiayaanRab;
use App\Interfaces\LoginInterface;
use App\Models\AuthLogin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginRepository implements LoginInterface
{
    public function createResponse($user)
    {
        $token = $user->createToken('authToken')->plainTextToken;
        if ($user->com_role_user_sf()->first()->role_id == '02005') {
            $data = AuthLogin::UserAgronomis($user);
        } elseif ($user->com_role_user_sf()->first()->role_id == '02003') {
            $data = AuthLogin::UserAccountOfficer($user);
        } elseif ($user->com_role_user_sf()->first()->role_id == '02001' || $user->com_role_user_sf()->first()->role_id == '02015') {
            $data = AuthLogin::UserPetaniAndSupir($user);
        }
        $access = [
            'Access Token' => $token,
            'Token Type' => 'Bearer Token',
            'User' => $user->roles()->first() == null ? $user->load('com_role_user_sf') : $user->load('roles'),
            'result' => new LoginResource($user, $data),
        ];
        return $access;
    }
}
