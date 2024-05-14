<?php

use App\Http\Controllers\Api\Hierarchy\HierarchyController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'hierarchy'
], function () {
    Route::group([
        'middleware' => ['auth:api', 'language']
    ], function () {
        Route::get('index', [HierarchyController::class, 'getListUnits'])->name('get-list-hierarchy');
        Route::get('detail', [HierarchyController::class, 'getUnitDetail'])->name('get-one-hierarchy');
        Route::post('create', [HierarchyController::class, 'createUnit'])->name('create-hierarchy');
        Route::put('update', [HierarchyController::class, 'updateUnit'])->name('update-hierarchy');
        Route::delete('delete', [HierarchyController::class, 'deleteUnit'])->name('delete-hierarchy');
        Route::get('get-by-level', [HierarchyController::class, 'getListUnitsByLevel'])->name('get-list-by-level-hierarchy');
        Route::get('get-by-code', [HierarchyController::class, 'getListUnitsByCode'])->name('get-list-by-code-hierarchy');
    });
});
