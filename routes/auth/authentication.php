<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seguridad\Auth\AuthController;

Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
Route::post('/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
