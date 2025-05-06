<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seguridad\RoleController;
use App\Http\Controllers\Seguridad\UsuarioController;

// Roles y permisos
Route::prefix('roles')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->middleware('permissions:rol_ver')->name('roles.index');
    Route::post('/', [RoleController::class, 'store'])->middleware('permissions:rol_crear')->name('roles.store');
    Route::get('/{id}', [RoleController::class, 'show'])->middleware('permissions:rol_ver')->name('roles.show');
    Route::put('/{id}', [RoleController::class, 'update'])->middleware('permissions:rol_actualizar')->name('roles.update');
});

// Usuarios
Route::prefix('users')->group(function () {
    Route::get('/', [UsuarioController::class, 'index'])->middleware('permissions:usuario_ver')->name('users.index');
    Route::post('/', [UsuarioController::class, 'store'])->middleware('permissions:usuario_crear')->name('users.store');
    Route::get('/{id}', [UsuarioController::class, 'show'])->middleware('permissions:usuario_ver')->name('users.show');
    Route::put('/{id}', [UsuarioController::class, 'update'])->middleware('permissions:usuario_actualizar')->name('users.update');
    Route::put('/update-by-admin/{id}', [UsuarioController::class, 'updateByAdmin'])->middleware('permissions:usuario_actualizar')->name('users.updateByAdmin');
    
});
