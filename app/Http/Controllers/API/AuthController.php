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
            return response()->json([
                'success' => false,
                'message' => 'Email hoặc mật khẩu không đúng',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'token' => $token,
        ]);
    }

    // logout
    public function logout() {
        auth()->logout();

        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công',
        ]);
    }

    // refresh token
    public function refresh() {
        $token = JWTAuth::getToken();
        return response()->json([
            'success' => true,
            'message' => 'Làm mới token thành công',
            'token' => JWTAuth::refresh($token),
        ]);
    }

    // get current user
    public function me() {
        return response()->json([
            'success' => true,
            'data' => new UserResource(auth()->user()),
        ]);
    }
}
