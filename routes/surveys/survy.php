<?php

use App\Http\Controllers\Encuesta\EncuestaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [EncuestaController::class, 'index'])->middleware('permissions:encuesta_ver')->name('encuestas.index');
Route::post('/init-survey', [EncuestaController::class, 'initNewSurvey'])->middleware('permissions:encuesta_editor')->name('encuestas.initNewSurvey');

Route::get('/editor/internal-data/{id}', [EncuestaController::class, 'showInternalData'])->middleware('permissions:encuesta_editor')->name('encuestas.showInternalData');
Route::get('/editor/general-info/{id}', [EncuestaController::class, 'showGeneralInfo'])->middleware('permissions:encuesta_editor')->name('encuestas.showGeneralInfo');
// En curso
Route::get('/editor/form/{id}', [EncuestaController::class, 'showForm'])->middleware('permissions:encuesta_editor')->name('encuestas.showForm');

Route::put('/editor/internal-data/{id}', [EncuestaController::class, 'updateInternalData'])->middleware('permissions:encuesta_editor')->name('encuestas.updateInternalData');
Route::put('/editor/general-info/{id}', [EncuestaController::class, 'updateGeneralInfo'])->middleware('permissions:encuesta_editor')->name('encuestas.updateGeneralInfo');
// En curso
Route::put('/editor/form/{id}', [EncuestaController::class, 'updateForm'])->middleware('permissions:encuesta_editor')->name('encuestas.updateForm');

Route::delete('/{id}', [EncuestaController::class, 'destroy'])->middleware('permissions:encuesta_ver')->name('encuestas.destroy');