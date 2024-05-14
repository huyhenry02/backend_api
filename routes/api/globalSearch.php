<?php

use App\Http\Controllers\Api\GlobalSearch\SearchController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['auth:api', 'language']
], function () {
    Route::get('search', [SearchController::class, 'search'])->name('global-search');
});
