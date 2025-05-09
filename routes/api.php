<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seguridad\Auth\AuthUnlockingController;

/**
 * AUTH MIDDLEWARE
 * Rutas con validación de token y
 * autenticación de usuario.
 */
Route::middleware('auth:api')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::prefix('solicitudes-desbloqueo')->group(function () {
            Route::post('/request-unlocking', [AuthUnlockingController::class, 'requestUnlocking'])->name('auth.request-unlocking');
        });
    });

    /**
     * VALID USER MIDDLEWARE
     * Rutas con validación de token y
     * restricción de acceso a usuarios validos.
     */
    Route::middleware('validate.user')->group(function () {

        // Seguridad y autenticación
        Route::prefix('auth')->group(function () {
            require __DIR__ . '/auth/users.php';
            require __DIR__ . '/auth/authentication.php';
        });

        // Catalogos
        Route::prefix('catalogo')->group(function () {
            require __DIR__ . '/catalogues/catalogs.php';
        });
    });
});
