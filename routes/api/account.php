<?php

use App\Http\Controllers\Api\Account\AuthController;
use App\Http\Controllers\Api\Account\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'auth',
    'middleware' => 'language'
], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('signup', [AuthController::class, 'signup']);
    Route::put('/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
    Route::post('/send-reset-password-email', [UserController::class, 'sendResetPasswordEmail'])->name('send-reset-password-email');
    Route::group([
        'middleware' => ['auth:api']
    ], function () {
        Route::get('/get-user-info', [UserController::class, 'getUserInfo'])->name('get-user-info');
        Route::post('logout', [AuthController::class, 'logout']);
    });
    Route::group([
        'middleware' => ['auth:api', 'check.access']
    ], function () {
        Route::put('/change-pass', [UserController::class, 'changePassUser'])->name('change-pass');
        Route::get('/get-user{user_id?}', [UserController::class, 'getUserByUserId'])->name('get-user-by-userId');
        Route::put('/update', [UserController::class, 'updateUser'])->name('update-user');
        Route::delete('/delete', [UserController::class, 'delete'])->name('delete-user');
        Route::get('/list{per_page?}{page?}', [UserController::class, 'getList'])->name('get-list-user');
    });
});
