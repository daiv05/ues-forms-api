<?php

use App\Http\Controllers\Catalogo\EstadosController;
use Illuminate\Support\Facades\Route;
use Orion\Facades\Orion;
use App\Http\Controllers\Catalogo\TiposPreguntasController;


Orion::resource('/tipos-preguntas', TiposPreguntasController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
Route::get('/estados', [EstadosController::class, 'index'])->name('estados.index');
