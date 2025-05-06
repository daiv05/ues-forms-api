<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailController;
use App\Http\Controllers\Seguridad\Auth\AuthController;
use App\Http\Controllers\Seguridad\UsuarioController;

Route::prefix('auth')->group(function () {
  Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
  Route::post('/request-registration', [UsuarioController::class, 'requestRegistration'])->name('auth.request-registration');
});
