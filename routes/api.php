<?php

use App\Http\Controllers\QuestionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('questions', [QuestionController::class, 'get']);
});
