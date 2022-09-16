<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\ComUser;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Requests\LoginRequest;
use App\Repositories\LoginRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private LoginRepository $loginRepository;

    public function __construct(LoginRepository $loginRepository)
    {
        $this->loginRepository = $loginRepository;
    }

    public function login(LoginRequest $request)
    {
        $payload = $request->validated();
        $user = ComUser::where('user_mail', $payload['email'])->first();
        if (!$user) {
            return ResponseFormatter::error(null, 'Email tidak ditemukan', 404);
        }
        // if (!Hash::check(request('password'), $user->password)) {
        if ($user->user_pass != md5($payload['password'])) {
            return ResponseFormatter::error(null, 'Password anda salah', 417);
        }
        try {
            Auth::login($user);
            return ResponseFormatter::success($this->loginRepository->createResponse($user), 'Login sukses');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), 400);
        }
    }
}
