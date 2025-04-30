<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailController;
use App\Http\Controllers\Seguridad\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth')->group(function () {
  Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
  // Route::post('/registrar-usuario', [AuthController::class, 'registrarUsuario']);
  // Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
});
