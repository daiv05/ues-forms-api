<?php

use App\Http\Controllers\Catalogo\EstadosController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seguridad\Auth\AuthController;
use Orion\Facades\Orion;
use App\Http\Controllers\Catalogo\TiposPreguntasController;
use App\Http\Controllers\Seguridad\UsuarioController;

Route::middleware('auth:api', 'validate.user')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);


    Route::prefix('catalogo')->group(function () {
        Orion::resource('/tipos-preguntas', TiposPreguntasController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
        Route::get('/estados', [EstadosController::class, 'index'])->name('estados.index');
    });
});

Route::prefix('auth')->middleware('auth:api')->group(function () {
    Route::post('/request-unlocking', [UsuarioController::class, 'requestUnlocking'])->name('auth.request-unlocking');
});

require __DIR__ . '/auth/_base.php';
