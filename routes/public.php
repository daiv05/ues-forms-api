<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seguridad\Auth\AuthController;
use App\Http\Controllers\Seguridad\Auth\AuthRegistrationController;
use App\Http\Controllers\Seguridad\Auth\AuthVerifiedEmailController;

Route::prefix('auth')->group(function () {
  Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
  Route::post('/send-verify-email', [AuthVerifiedEmailController::class, 'sendVerificationCode'])->name('auth.verify-email');
  Route::post('/verify-email', [AuthVerifiedEmailController::class, 'verifyEmail'])->name('auth.verify-email.verify');
  Route::post('/request-registration', [AuthRegistrationController::class, 'requestRegistration'])->name('auth.request-registration');
});
