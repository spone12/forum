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

/*Route::get('/about', function () {
    return view('about');
});*/

Route::post('/search', 'SearchController@getDataSearch')->name('search');

//NOTATION
Route::get('/notation', 'NotationController@Notation')->name('notation')->middleware('auth');
Route::post('/notation', 'NotationController@AjaxReq')->middleware('auth');
Route::get('/notation/view/{notation_id}', 'NotationController@NotationView')
    ->name('notation_view_id')->where('notation_id','[0-9]{1,11}');
Route::get('/notation/edit/{notation_id}', 'NotationController@NotationEditAccess')
    ->name('notation_edit_id')->where('notation_id','[0-9]{1,11}')->middleware('auth');
Route::post('/notation/rating/{notation_id}', 'NotationController@NotationRating')->middleware('auth');
Route::post('/notation/edit_upd/{notation_id}', 'NotationController@NotationEdit')->middleware('auth');
Route::post('/notation/delete/{notation_id}', 'NotationController@NotationDelete')->middleware('auth');
//END NOTATION


// зайти могут только зарегестрированные
Route::get('/profile', 'ProfileController@view_profile')->name('profile')->middleware('auth');

//с использованием where
Route::get('/profile/{id}', 'ProfileController@view_another_profile')->where('id','[0-9]{1,11}')
    ->name('profile_id')->middleware('auth');
