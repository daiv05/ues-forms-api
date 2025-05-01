<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailController;
use App\Http\Controllers\Seguridad\Auth\AuthController;

Route::prefix('auth')->group(function () {
  Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
  // Route::post('/registrar-usuario', [AuthController::class, 'registrarUsuario']);
  // Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
});
