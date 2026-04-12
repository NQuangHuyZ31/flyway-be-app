<?php

namespace App\Http\Controllers\API;

use App\Factories\AuthFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWT;

class AuthController extends Controller
{
    // Login
    public function login(LoginRequest $request) {
        $authInplement = AuthFactory::authImplement($request->input('type'));

        if (!$token = $authInplement->login($request->only('email', 'password'))) {
            return $this->errorResponse('Email hoặc mật khẩu không đúng', 401);
        }

        return $this->successResponse(['token' => $token], 'Đăng nhập thành công');
    }

    // logout
    public function logout() {
        auth()->logout();

        return $this->successResponse(null, 'Đăng xuất thành công');
    }

    // refresh token
    public function refresh() {
        $token = JWTAuth::getToken();
        return $this->successResponse(['token' => JWTAuth::refresh($token)], 'Làm mới token thành công');
    }

    // get current user
    public function me() {
        return $this->successResponse(new UserResource(auth()->user()));
    }
}
