<?php

use App\Http\Controllers\Api\MasterData\MasterDataController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'master-data'
], function () {
    Route::group([
        'middleware' => ['auth:api', 'language']
    ], function () {
        Route::get('list', [MasterDataController::class, 'getListMasterData'])->name('get-list-master-data');
        Route::get('list-multi-key', [MasterDataController::class, 'getListMultiKeyMasterData'])->name('get-list-multi-key-master-data');
    });
});
