<?php

use App\Http\Controllers\PredictionController;
use Illuminate\Support\Facades\Route;



Route::get('/', [PredictionController::class, 'index'])->name('predictions.index');
Route::post('/predict', [PredictionController::class, 'store'])->name('predictions.store');
Route::get('/predictions/{prediction}', [PredictionController::class, 'show'])->name('predictions.show');
Route::delete('/predictions/{prediction}', [PredictionController::class, 'destroy'])->name('predictions.destroy');