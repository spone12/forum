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

Route::match(['get', 'post'], '/search', 'SearchController@getDataSearch')->name('search');

//NOTATION
Route::prefix('notation')->group(function()
{
    Route::get('/', 'NotationController@Notation')->name('notation')->middleware('auth');
    Route::post('/', 'NotationController@AjaxReq')->middleware('auth');
    Route::get('/view/{notation_id}', 'NotationController@NotationView')
        ->name('notation_view_id')->where('notation_id','[0-9]{1,11}');
    Route::get('/edit/{notation_id}', 'NotationController@NotationEditAccess')
        ->name('notation_edit_id')->where('notation_id','[0-9]{1,11}')->middleware('auth');
    Route::post('/rating/{notation_id}', 'NotationController@NotationRating')
        ->where('id','[0-9]{1,11}')->middleware('auth');
    Route::put('/edit_upd/{notation_id}', 'NotationController@NotationEdit')
        ->where('id','[0-9]{1,11}')->middleware('auth');
    Route::delete('/delete/{notation_id}', 'NotationController@NotationDelete')
    ->where('id','[0-9]{1,11}')->middleware('auth');
});

//END NOTATION


// зайти могут только зарегестрированные
Route::get('/profile', 'ProfileController@view_profile')->name('profile')->middleware('auth');

//с использованием where
 Route::get('/profile/{id}', 'ProfileController@view_another_profile')->where('id','[0-9]{1,11}')
    ->name('profile_id')->middleware('auth');
 Route::get('/change_profile/{id}', 'ProfileController@change_profile')->where('id','[0-9]{1,11}')
    ->name('change_profile')->middleware('auth');
 Route::put('/change_profile_confirm/{id}', 'ProfileController@change_profile_confirm')->where('id','[0-9]{1,11}')
    ->middleware('auth');
Route::post('/avatar-change', 'ProfileController@change_avatar')->name('avatar_change')->middleware('auth');

//Localization
Route::get('locale/{locale}', function ($locale) 
{
    Session::put('locale', $locale);

    return redirect()->back();
})->name('locale');
//END Localization