<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;


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

Route::post('/generate_token', 'Api\ApiController@generateToken')->name('generateToken')->middleware('api.logger');

Route::group(['prefix' => 'v1', 'middleware' => ['auth.api', 'api.logger']], function () {

    Route::group(['prefix' => 'notation'], function () {

        Route::get('/list', 'Api\v1\ApiNotationController@list')->name('notationList');
        Route::get('/get_notation', 'Api\v1\ApiNotationController@getNotationById')->name('notationById');
        Route::put('/update_notation', 'Api\v1\ApiNotationController@updateNotation')->name('updateNotation');
    });
});
