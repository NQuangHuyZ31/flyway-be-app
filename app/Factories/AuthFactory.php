<?php

namespace App\Factories;

use App\Services\AuthService\LoginEmailService;
use App\Services\AuthService\LoginFacebookService;
use App\Services\AuthService\LoginGoogleService;

class AuthFactory
{
	public static function authImplement($type)
	{
		return match ($type) {
			'email' => new LoginEmailService(),
			'google' => new LoginGoogleService(),
			'facebook' => new LoginFacebookService(),
			default => throw new \InvalidArgumentException('Invalid authentication type'),
		};
	}
}
