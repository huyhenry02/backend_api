<?php

use App\Http\Controllers\Api\FileUpload\FileController;
use Illuminate\Support\Facades\Route;

Route::post('upload', [FileController::class, 'upload'])->middleware('language');
Route::post('test', [FileController::class, 'test']);
