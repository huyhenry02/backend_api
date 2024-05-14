<?php

use App\Http\Controllers\Api\RolePermission\PermissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RolePermission\RoleController;

Route::group([
    'prefix' => 'role'
], function () {
    Route::group([
        'middleware' => ['auth:api', 'check.access', 'language']
    ], function () {
        Route::get('index', [RoleController::class, 'getListRoles'])->name('get-list-roles');
        Route::get('get', [RoleController::class, 'getRole'])->name('get-role');
        Route::post('create', [RoleController::class, 'createRole'])->name('create-role');
        Route::delete('delete', [RoleController::class, 'deleteRole'])->name('delete-role');
        Route::patch('change-status', [RoleController::class, 'changeRoleStatus'])->name('change-status-role');
        Route::patch('update', [RoleController::class, 'updateRole'])->name('update-role');
        Route::post('give-permissions', [RoleController::class, 'givePermissionsToRole'])->name('give-permissions-to-role');
    });
});
Route::group([
    'prefix' => 'permission'
], function () {
    Route::group([
        'middleware' => ['auth:api']
    ], function () {
        Route::get('index', [PermissionController::class, 'getListPermissions'])->name('get-list-permissions');
    });
});
