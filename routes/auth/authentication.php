<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seguridad\Auth\AuthController;

Route::post('/logout', [AuthController::class, 'logout']);
