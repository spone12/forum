<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::put('/update_token', 'Api\ApiController@updateToken')->middleware('api')->name('updateToken');

Route::group(['prefix' => 'v1/notation', 'middleware' => 'api'], function () {

    Route::post('/list', 'Api\v1\ApiNotationController@list')->name('list');

});
