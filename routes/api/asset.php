<?php

use App\Http\Controllers\Api\Asset\AssetController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'asset'
], function () {
    Route::group([
        'middleware' => ['auth:api', 'check.access', 'language']
    ], function () {
        Route::get('detail', [AssetController::class, 'detail'])->name('get-asset');
        Route::get('list', [AssetController::class, 'getListAsset'])->name('get-list-asset');
        Route::post('create', [AssetController::class, 'createAsset'])->name('create-asset');
        Route::put('update', [AssetController::class, 'updateAsset'])->name('update-asset');
        Route::delete('delete', [AssetController::class, 'deleteAsset'])->name('delete-asset');
        Route::group([
            'prefix' => 'delivery-history'
        ], function () {
            Route::post('create', [AssetController::class, 'createAssetDelivery'])->name('create-asset-delivery-history');
            Route::get('detail', [AssetController::class, 'getAssetDelivery'])->name('get-asset-delivery-history');
            Route::get('list', [AssetController::class, 'getAssetDeliveries'])->name('list-asset-delivery-history');
        });
        Route::group([
            'prefix' => 'maintenance'
        ], function () {
            Route::post('create', [AssetController::class, 'createAssetMaintenance'])->name('create-asset-maintenance');
            Route::get('detail', [AssetController::class, 'getAssetMaintenance'])->name('get-asset-maintenance');
            Route::get('list', [AssetController::class, 'getAssetMaintenances'])->name('list-asset-maintenance');
        });
    });
});
