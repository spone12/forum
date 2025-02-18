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
Route::get('/', 'HomeController@index')->name('homePage');

Route::match(['get', 'post'], '/search', 'SearchController@getDataSearch')->name('search');

/**
 * Notation
 */
Route::prefix('notation')->group(function()
{
    Route::get('/', 'NotationController@notation')->name('notation')->middleware('auth');
    Route::post('/', 'NotationController@createNotation')->middleware('auth');
    Route::get('/view/{notation_id}', 'NotationController@notationView')
        ->name('notation_view_id')->where('notation_id','[0-9]{1,11}');
    Route::get('/edit/{notation_id}', 'NotationController@notationEditView')
        ->name('notation_edit_id')->where('notation_id','[0-9]{1,11}')->middleware('auth');
    Route::post('/rating/{notation_id}', 'NotationController@notationRating')
        ->where('notation_id','[0-9]{1,11}')->middleware('auth');
    Route::post('/add_photos/{notation_id}', 'NotationController@notationAddPhoto')
        ->name('notationAddPhoto')->where('notation_id','[0-9]{1,11}')->middleware('auth');
    Route::put('/update/{notation_id}', 'NotationController@notationUpdate')
        ->where('notation_id','[0-9]{1,11}')->middleware('auth');
    Route::delete('/delete/{notation_id}', 'NotationController@notationDelete')
        ->where('notation_id','[0-9]{1,11}')->middleware('auth');
    Route::delete('/delete_photo/{photo_id}', 'NotationController@removeNotationPhoto')
        ->where('photo_id','[0-9]{1,11}')->middleware('auth');
});

Route::get('/map', 'MapController@viewMap')->name('map')->middleware('auth');

/**
 * Chat
 */
Route::group(['middleware' => ['auth'], 'prefix' => '/chat'], function ()
{
    Route::get('/', 'ChatController@chat')->name('chat');
    Route::get('/dialog/{dialogId}', 'ChatController@dialog')->name('dialog')->where('dialogId', '[0-9]{1,11}');
    Route::post('/search/', 'ChatController@searchChat')->name('searchChat')->where('word', '[а-яА-Яa-zA-Z0-9 ]+');
    Route::post('/send_message/', 'ChatController@sendMessage')->name('sendMessage');
    Route::put('/edit_message/', 'ChatController@editMessage')->name('editMessage');
    Route::delete('/delete_message/', 'ChatController@deleteMessage')->name('deleteMessage');
    Route::put('/recover_message/', 'ChatController@recoverMessage')->name('recoverMessage');
});

/**
 * Profile
 */
Route::group(['middleware' => ['auth'], 'prefix' => '/profile'], function ()
{
    Route::get('/', 'ProfileController@viewProfile')->name('profile');
    Route::get('/{id}', 'ProfileController@viewAnotherProfile')->where('id','[0-9]{1,11}')
        ->name('profile_id')->withoutMiddleware('auth');
    Route::get('/change/{id}', 'ProfileController@changeProfile')->where('id','[0-9]{1,11}')
        ->name('change_profile');
    Route::put('/confirm_change/{id}', 'ProfileController@changeProfileConfirm')->where('id','[0-9]{1,11}');
    Route::post('/change_avatar', 'ProfileController@changeAvatar')->name('change_avatar');
    Route::post('/generate_token', 'Api\ApiController@generateApiToken')->middleware('auth')->name('generateApiToken');
});

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

//Integrations
Route::match(['get', 'post'], "/vk_bot_callback", function (Request $request)
{
    $handler = new ServerHandler();
    $data = json_decode(file_get_contents('php://input'));
    $handler->parse($data);
});

/*
  Route::group(['namespace' => 'Integrations', 'middleware' => ['auth'], 'prefix' => '/integration'], function () {
    Route::get('/vk', 'vkController@confirmation')->name('vk');
  });
*/
