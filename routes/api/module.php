<?php

use App\Http\Controllers\Api\RolePermission\ModuleController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'module'
], function () {
    Route::group([
        'middleware' => ['auth:api', 'language']
    ], function () {
        Route::get('index', [ModuleController::class, 'getListModules'])->name('get-list-modules');
    });
});
