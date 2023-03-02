<?php

use App\Http\Controllers\Integrations\ServerHandler;
use App\User as User;

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
Route::get('/', 'HomeController@index');
Route::put('/generate_api_key', [User::class, 'generateApiKey'])->name('generateApiKey')->middleware('auth');

Route::match(['get', 'post'], '/search', 'SearchController@getDataSearch')->name('search');

/**
 * Notation
 */
Route::prefix('notation')->group(function()
{
    Route::get('/', 'NotationController@Notation')->name('notation')->middleware('auth');
    Route::post('/', 'NotationController@createNotation')->middleware('auth');
    Route::get('/view/{notation_id}', 'NotationController@NotationView')
        ->name('notation_view_id')->where('notation_id','[0-9]{1,11}');
    Route::get('/edit/{notation_id}', 'NotationController@NotationEditAccess')
        ->name('notation_edit_id')->where('notation_id','[0-9]{1,11}')->middleware('auth');
    Route::post('/rating/{notation_id}', 'NotationController@NotationRating')
        ->where('notation_id','[0-9]{1,11}')->middleware('auth');
    Route::post('/add_photos/{notation_id}', 'NotationController@NotationAddPhotos')
        ->name('notationAddPhotos')->where('notation_id','[0-9]{1,11}')->middleware('auth');
    Route::put('/edit_upd/{notation_id}', 'NotationController@NotationEdit')
        ->where('notation_id','[0-9]{1,11}')->middleware('auth');
    Route::delete('/delete/{notation_id}', 'NotationController@NotationDelete')
    ->where('notation_id','[0-9]{1,11}')->middleware('auth');
    Route::delete('/delete_photo/{photo_id}', 'NotationController@NotationPhotoDelete')
    ->where('photo_id','[0-9]{1,11}')->middleware('auth');
});

Route::get('/map', 'MapController@viewMap')->name('map')->middleware('auth');

//Integrations
Route::match(['get', 'post'], "/vk_bot_callback", function (Request $request)
{
    $handler = new ServerHandler();
    $data = json_decode(file_get_contents('php://input'));
    $handler->parse($data);
});

/*Route::group(['namespace' => 'Integrations', 'middleware' => ['auth'], 'prefix' => '/integration'], function () {
    Route::get('/vk', 'vkController@confirmation')->name('vk');

});
*/

/**
 * Chat
 */
Route::group(['middleware' => ['auth'], 'prefix' => '/chat'], function ()
{
    Route::get('/', 'chatController@chat')->name('chat');
    Route::get('/dialog/{dialogId}', 'chatController@dialog')->name('dialog')->where('dialogId','[0-9]{1,11}');
    Route::post('/search/', 'chatController@searchChat')->name('searchChat')->where('word','[а-яА-Яa-zA-Z0-9 ]+');
    Route::post('/send_message/', 'chatController@sendMessage')->name('sendMessage');
});

/**
 * Profile
 */
Route::get('/profile', 'ProfileController@viewProfile')->name('profile')->middleware('auth');
 Route::get('/profile/{id}', 'ProfileController@viewAnotherProfile')->where('id','[0-9]{1,11}')
    ->name('profile_id')->middleware('auth');
 Route::get('/change_profile/{id}', 'ProfileController@changeProfile')->where('id','[0-9]{1,11}')
    ->name('change_profile')->middleware('auth');
 Route::put('/change_profile_confirm/{id}', 'ProfileController@changeProfileConfirm')->where('id','[0-9]{1,11}')
    ->middleware('auth');
Route::post('/avatar-change', 'ProfileController@changeAvatar')->name('avatar_change')->middleware('auth');

/**
 * Localization
 *
 * @return lluminate\Http\RedirectResponse
 */
Route::get('locale/{locale}', function ($locale)
{
    Session::put('locale', $locale);
    return redirect()->back();
})->name('locale');

Route::get('/test_http', 'TestHttpController@http')->name('testHttp');
