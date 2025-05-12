<?php

use Orion\Facades\Orion;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Catalogo\EstadosController;
use App\Http\Controllers\Catalogo\GrupoMetaController;
use App\Http\Controllers\Catalogo\TiposPreguntasController;

Orion::resource('/tipos-preguntas', TiposPreguntasController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
Route::get('/estados', [EstadosController::class, 'index'])->name('estados.index');
Orion::resource('/grupos-meta', GrupoMetaController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
