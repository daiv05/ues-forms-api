<?php

use App\Http\Controllers\Encuesta\EncuestaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [EncuestaController::class, 'index'])->middleware('permissions:encuesta_ver')->name('encuestas.index');
Route::post('/init-survey', [EncuestaController::class, 'initNewSurvey'])->middleware('permissions:encuesta_editor')->name('encuestas.initNewSurvey');

Route::get('/editor/internal-data/{id}', [EncuestaController::class, 'showInternalData'])->middleware('permissions:encuesta_editor')->name('encuestas.showInternalData');
Route::get('/editor/general-info/{id}', [EncuestaController::class, 'showGeneralInfo'])->middleware('permissions:encuesta_editor')->name('encuestas.showGeneralInfo');
Route::get('/editor/form/{id}', [EncuestaController::class, 'showForm'])->middleware('permissions:encuesta_editor')->name('encuestas.showForm');

Route::put('/editor/internal-data/{id}', [EncuestaController::class, 'updateInternalData'])->middleware('permissions:encuesta_editor')->name('encuestas.updateInternalData');
Route::put('/editor/general-info/{id}', [EncuestaController::class, 'updateGeneralInfo'])->middleware('permissions:encuesta_editor')->name('encuestas.updateGeneralInfo');
Route::put('/editor/form/{id}', [EncuestaController::class, 'updateForm'])->middleware('permissions:encuesta_editor')->name('encuestas.updateForm');

Route::put('publish/{id}', [EncuestaController::class, 'publishSurvey'])->middleware('permissions:encuesta_editor')->name('encuestas.publishSurvey');

Route::get('/statistics/{id}', [EncuestaController::class, 'surveyStatistics'])->middleware('permissions:encuesta_editor')->name('encuestas.surveyStatistics');

Route::delete('/{id}', [EncuestaController::class, 'destroy'])->middleware('permissions:encuesta_ver')->name('encuestas.destroy');
