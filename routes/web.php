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

Route::get('/', function () {
    return view('home');
});


Route::get('/home', 'HomeController@index')->name('home');

Route::get('/about', function () {
    return view('about');
});

Route::post('/search', 'SearchController@getDataSearch')->name('search');

Route::get('/notation', 'NotationController@Notation')->middleware('auth');
Route::post('/notation', 'NotationController@AjaxReq')->middleware('auth');
/*Route::post('/search', function () {
    return view('search');
});*/

Route::get('/profile', function () {
    // зайти могут только зарегестрированные
    return view('menu.profile');
})->middleware('auth');
