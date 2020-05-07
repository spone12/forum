<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/', 'HomeController@index')->name('home');

Route::get('/about', function () {
    return view('about');
});

Route::post('/search', 'SearchController@getDataSearch')->name('search');

Route::get('/notation', 'NotationController@Notation')->middleware('auth');
Route::post('/notation', 'NotationController@AjaxReq')->middleware('auth');

// зайти могут только зарегестрированные
Route::get('/profile', 'ProfileController@view_profile')->middleware('auth');
