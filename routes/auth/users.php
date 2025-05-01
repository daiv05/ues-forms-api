<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seguridad\RoleController;
use App\Http\Controllers\Seguridad\UsuarioController;

// Roles y permisos
Route::get('/', [RoleController::class, 'index'])->middleware('permissions:ROLES_VER')->name('roles.index');
Route::post('/', [RoleController::class, 'store'])->middleware('permissions:ROLES_CREAR')->name('roles.store');
Route::put('/{id}', [RoleController::class, 'update'])->middleware('permissions:ROLES_EDITAR')->name('roles.update');
Route::get('/{id}', [RoleController::class, 'show'])->middleware('permissions:ROLES_ACTUALIZAR')->name('roles.show');

// Usuarios
Route::get('/', [UsuarioController::class, 'index'])->middleware('permissions:USUARIO_VER')->name('users.index');
Route::post('/', [UsuarioController::class, 'store'])->middleware('permissions:USUARIO_CREAR')->name('users.store');
Route::get('/{id}', [UsuarioController::class, 'show'])->middleware('permissions:USUARIO_VER')->name('users.show');
Route::put('/{id}', [UsuarioController::class, 'update'])->middleware('permissions:USUARIO_ACTUALIZAR')->name('users.update');
