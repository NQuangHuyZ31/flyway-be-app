<?php

namespace App\Services\AuthService;

use Tymon\JWTAuth\Facades\JWTAuth;

class LoginEmailService {
	public function login($credentials) {

		$token = JWTAuth::attempt($credentials);

		return $token;
	}
}