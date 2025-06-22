<?php

use App\Http\Controllers\PredictionController;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return redirect()->route('predictions.index');
});

Route::resource('predictions', PredictionController::class)->except(['edit', 'update']);
