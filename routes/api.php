<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\serviceController;

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
        Route::get('search_by_cid/{cid}', 'searchbycid');
        Route::get('create_by_id/{cid}/{owner}', 'create_by_id');
        Route::post('update', 'update');
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
        Route::get('getuserdata', 'getuserdata');

    });
});





});

