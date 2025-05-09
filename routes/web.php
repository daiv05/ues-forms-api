<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('landing');



Route::get('/resp-reg', function () {
    return view('emails.response-registration-request');
})->name('resp-reg');
