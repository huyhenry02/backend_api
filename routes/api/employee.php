<?php

use App\Http\Controllers\Api\Employee\CurriculumVitaeController;
use App\Http\Controllers\Api\Employee\EmployeeController;
use App\Http\Controllers\Api\Employee\HealthController;
use App\Http\Controllers\Api\Employee\ContractController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'employee',
    'middleware' => ['auth:api','check.access', 'language']
], static function () {
    Route::prefix('/user')->group(function () {
        Route::post('/create', [EmployeeController::class, 'createUser'])->name('create-user');
    });

    Route::get('list{per_page?}{page?}', [EmployeeController::class, 'getList'])->name('get-list-employee');
    Route::put('update-status', [EmployeeController::class, 'updateStatus'])->name('update-status-employee');
//    Route::get('history{employee_id?}{per_page?}{page?}', [EmployeeController::class, 'getHistory'])->name('get-history');
////    Route::get('get-log-compare{id?}', [EmployeeContro
//ller::class, 'getLogCompare'])->name('get-compare-log');

    Route::prefix('/curriculum-vitae')->group(function () {
//        Route::get('/list{per_page?}{page?}', [CurriculumVitaeController::class, 'getCurriculumVitaeList'])->name('get-list-curriculum-vitae');
        Route::get('/detail{employee_id?}', [CurriculumVitaeController::class, 'getOneCurriculumVitae'])->name('get-one-curriculum-vitae');
        Route::put('/update', [CurriculumVitaeController::class, 'updateCurriculumVitae'])->name('update-curriculum-vitae');
    });

    Route::prefix('/health')->group(function () {
        Route::get('/get', [HealthController::class, 'getHealth'])->name('get-health');
        Route::put('/update', [HealthController::class, 'updateHealth'])->name('update-health');
    });

    Route::post('electronic-record/create', [EmployeeController::class, 'createElectronicRecord'])->name('create-electronic-record');

    Route::prefix('/contract')->group(function () {
        Route::get('/detail', [ContractController::class, 'getContractDetail'])->name('get-contract');
        Route::put('/update', [ContractController::class, 'updateContract'])->name('update-contract');
    });
});
