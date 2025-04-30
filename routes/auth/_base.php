<?php

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->middleware('auth:api')->group(function () {
    require __DIR__ . '/users.php';
    require __DIR__ . '/authentication.php';
});
