<?php

use App\Http\Controllers\Seguridad\ProfileController;
use App\Http\Controllers\Seguridad\RoleController;
use App\Http\Controllers\Seguridad\UsuarioController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auditorias\UserAuditController;
use App\Http\Controllers\InicioController;

Route::get('/', function () {
    return view('welcome');
})->name('landing');

Route::middleware('auth', 'verified', 'two_factor')->group(function () {

    Route::get('/inicio', [InicioController::class, 'inicio'])->name('dashboard');

    Route::get('/forbidden', function () {
        return view('errors.forbidden');
    })->name('errors.forbidden');

    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::prefix('seguridad')->group(function () {
        // Rutas para los roles
        Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:ROLES_VER')->name('roles.index');
        Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:ROLES_CREAR')->name('roles.store');
        Route::get('/roles/create', [RoleController::class, 'create'])->middleware('permission:ROLES_CREAR')->name('roles.create');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->middleware('permission:ROLES_EDITAR')->name('roles.update');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->middleware('permission:ROLES_EDITAR')->name('roles.edit');
        Route::get('/roles/{role}', [RoleController::class, 'show'])->middleware('permission:ROLES_VER')->name('roles.show');

        // Rutas para los usuarios
        Route::get('/usuarios', [UsuarioController::class, 'index'])->middleware('permission:USUARIOS_VER')->name('usuarios.index');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->middleware('permission:USUARIOS_CREAR')->name('usuarios.store');
        Route::get('/usuarios/create', [UsuarioController::class, 'create'])->middleware('permission:USUARIOS_CREAR')->name('usuarios.create');
        Route::get('/usuarios/{usuario}', [UsuarioController::class, 'show'])->middleware('permission:USUARIOS_VER')->name('usuarios.show');
        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->middleware('permission:USUARIOS_EDITAR')->name('usuarios.update');
        Route::get('/usuarios/{usuario}/edit', [UsuarioController::class, 'edit'])->middleware('permission:USUARIOS_EDITAR')->name('usuarios.edit');
    });

    Route::prefix('bitacora')->group(function () {
        Route::get('/', [UserAuditController::class, 'index'])->middleware('permission:BITACORA_VER')->name('general.index');
        Route::get('/get-events', [UserAuditController::class, 'getEvents'])->middleware('permission:BITACORA_VER');
    });

});


require __DIR__ . '/auth.php';
