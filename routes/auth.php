<?php

use App\Http\Controllers\Auth\VerifyEmailController as AuthVerifyEmailController;
use App\Http\Controllers\Seguridad\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Seguridad\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Seguridad\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Seguridad\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Seguridad\Auth\NewPasswordController;
use App\Http\Controllers\Seguridad\Auth\PasswordController;
use App\Http\Controllers\Seguridad\Auth\PasswordResetLinkController;
use App\Http\Controllers\Seguridad\Auth\RegisteredUserController;
use App\Http\Controllers\Seguridad\Auth\TwoFactorController;
use App\Http\Controllers\Seguridad\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('registrarse', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('registrarse', [RegisteredUserController::class, 'store']);

    Route::get('iniciar-sesion', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('iniciar-sesion', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    // Verificación de email
    Route::get('verificar-email', EmailVerificationPromptController::class)
        ->name('verificacion-email.comprobacion');

    Route::post('verificar-email/reenviar-codigo', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verificacion-email.reenviar');

    Route::post('verificar-email/confirmar', VerifyEmailController::class)
        ->middleware('throttle:6,1')
        ->name('verificacion-email.confirmar');

    // Verificación de doble factor
    Route::get('two-factor', TwoFactorController::class)
        ->name('two-factor.comprobacion');

    Route::post('two-factor/reenviar-codigo', [TwoFactorController::class, 'sendTwoFactorCode'])
        ->middleware('throttle:6,1')
        ->name('two-factor.reenviar');

    Route::post('two-factor/confirmar', [TwoFactorController::class, 'confirmTwoFactorCode'])
        ->middleware('throttle:6,1')
        ->name('two-factor.confirmar');

    // Actualización de contraseña
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // Cierre de sesión
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
