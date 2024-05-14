<?php

use App\Http\Controllers\Api\Example\ExampleController;
use App\Jobs\SendMailJob;
use App\Mail\CommonMail;
use Illuminate\Support\Facades\Route;

Route::get('example', [ExampleController::class, 'example']);
Route::post('create-example-with-files', [ExampleController::class, 'createExampleWithFiles']);

Route::middleware('auth:api')->group(function () {
    Route::get('example-with-passport', [ExampleController::class, 'exampleWithPassport']);
});


Route::get('email-test', function () {
    $emails = ['longnvd2t@gmail.com', 'test@gmail.com'];
    $cc = ['admin@wishcare.com'];
    $bcc = ['user@wishcare.com'];
    $data['email'] = $emails;
    $mailable = new CommonMail(data: $data, subject: 'Test mail', view: 'mails.common');
    dispatch(job: new SendMailJob(mailable: $mailable, email: $emails, cc: $cc, bcc: $bcc));
});
