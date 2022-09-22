<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\ComUser;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use App\Helpers\MyEncryption;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Repositories\LoginRepository;
use Illuminate\Support\Facades\Crypt;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Config;
use App\Http\Resources\RegisterResource;
use App\Repositories\RegisterRepository;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\RegisterUpdateRequest;
use App\Http\Resources\PetaniUpdateResource;

class AuthController extends Controller
{
    private LoginRepository $loginRepository;
    private RegisterRepository $registerRepository;

    public function __construct(LoginRepository $loginRepository, RegisterRepository $registerRepository)
    {
        $this->loginRepository = $loginRepository;
        $this->registerRepository = $registerRepository;
    }

    public function login(LoginRequest $request)
    {
        $payload = $request->validated();
        $user = ComUser::where('user_mail', $payload['email'])->first();
        if (!$user) {
            return ResponseFormatter::error(null, 'Email tidak ditemukan', 404);
        }
        if ($user->user_pass == md5($payload['password']) || Hash::check($payload['password'] . $user->user_key, $user->user_pass)) {
            try {
                Auth::login($user);
                return ResponseFormatter::success($this->loginRepository->createResponse($user), 'Login sukses');
            } catch (Exception $e) {
                return ResponseFormatter::error(null, $e->getMessage(), 400);
            }
        }
        return ResponseFormatter::error(null, 'Password anda salah', 417);
    }

    public function register(RegisterRequest $request)
    {
        $payload = $request->validated();
        if (ComUser::where('user_name', $payload['user_name'])->exists()) {
            return ResponseFormatter::error(null, 'Username ' . $payload['user_name'] . ' sudah terpakai', 409);
        }
        if (ComUser::where('user_mail', $payload['user_mail'])->exists()) {
            return ResponseFormatter::error(null, 'Email ' . $payload['user_mail'] . ' sudah terpakai', 409);
        }
        return ResponseFormatter::success(new RegisterResource($this->registerRepository->createResponse($payload)), 'Data user berhasil didaftarkan');
    }

    public function update(RegisterUpdateRequest $request, $userID)
    {
        $user = ComUser::find($userID);
        if (!$user) {
            return ResponseFormatter::error(null, 'Data user tidak ditemukan', 404);
        }
        $pegawai = Pegawai::find($userID);
        if (!$pegawai) {
            return ResponseFormatter::error(null, 'Data pegawai tidak ditemukan', 404);
        }
        $payload = $request->validated();
        return ResponseFormatter::success(new PetaniUpdateResource($this->registerRepository->updateResponse($payload, $user, $pegawai)), 'Data user berhasil diupdate');
    }
}
