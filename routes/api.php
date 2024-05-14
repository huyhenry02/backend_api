<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

require ('api/example.php');
require ('api/employee.php');
require ('api/appointment.php');
require ('api/account.php');
require ('api/upload.php');
require('api/role.php');
require('api/module.php');
require('api/hierarchy.php');
require('api/masterData.php');
require('api/log.php');
require('api/globalSearch.php');
require('api/asset.php');
