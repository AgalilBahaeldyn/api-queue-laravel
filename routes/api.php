<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\serviceController;
use App\Http\Controllers\API\serviceControllerv2;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group([ 'prefix' => 'v1/'], function(){

    Route::controller(serviceController::class)->group(function () {
        Route::group([ 'prefix' => 'service'], function(){
        Route::get('getreport/{sdate}/{edate}', 'getreport');
        Route::get('search_by_cid/{cid}/{age}', 'searchbycid');

        Route::get('create_by_id/{cid}/{owner}/{key}/{age}/{fullname?}', 'create_by_id');

        Route::get('serivceperday/{date}', 'serivceperday');

        Route::get('print_queue/{cid}', 'print_queue');

    });
});

    Route::controller(serviceControllerv2::class)->group(function () {
        Route::group([ 'prefix' => 'servicev2'], function(){
        Route::get('getreport/{sdate}/{edate}', 'getreport');
        Route::get('search_by_cid/{cid}/{age}', 'searchbycid');

        Route::get('create_by_id/{cid}/{owner}/{key}/{age}/{fullname?}', 'create_by_id');
        
        Route::get('serivceperday/{date}', 'serivceperday');

        Route::get('print_queue/{cid}', 'print_queue');

    });
    });





});

