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

Route::get('/', function () {
    return view('home');
});

/*Route::get('/login', function () {
    return view('auth.login');
});*/

//Route::get('/login', 'SessionController@create');

/*Route::get('/register', function () {
    return view('auth.register');
});*/

//Route::get('/register', 'RegistrationController@create');

Route::get('/footer', function () {
    return view('footer');
});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


