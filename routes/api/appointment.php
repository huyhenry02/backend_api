<?php

use App\Http\Controllers\Api\Appointment\AppointmentController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'appointment',
    'middleware' => ['auth:api', 'language']
], static function () {
//    Route::post('create', [AppointmentController::class, 'createAppointment'])->name('create-appointment');
    Route::get('get{employee_id?}{status?}{per_page?}{page?}', [AppointmentController::class, 'getAppointments'])->name('list-appointment');
    Route::get('detail{id?}', [AppointmentController::class, 'getAppointment'])->name('get-one-appointment');
    Route::put('update-status', [AppointmentController::class, 'updateStatusAppointment'])->name('update-status-appointment');
});
Route::post('appointment/create', [AppointmentController::class, 'createAppointment'])->name('create-appointment');
