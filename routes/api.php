<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seguridad\Auth\AuthController;
use Orion\Facades\Orion;
use App\Http\Controllers\Catalogo\TiposPreguntasController;



Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Orion::resource('catalogo/tipos-preguntas', TiposPreguntasController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
});

require __DIR__ . '/auth/_base.php';
