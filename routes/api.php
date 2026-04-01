<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\RoleController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');

Route::middleware('auth:api')->group(function () {
	Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
	Route::get('/me', [AuthController::class, 'me'])->name('auth.me');

	// Role Routes	
	Route::apiResource('roles', RoleController::class);
});

