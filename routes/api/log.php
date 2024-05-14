<?php

use App\Http\Controllers\Api\Log\LogController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'log'
], function () {
    Route::group([
        'middleware' => ['auth:api', 'language']
    ], function () {
        Route::get('get', [LogController::class, 'getLogData'])->name('get-log-audit');
    });
});
