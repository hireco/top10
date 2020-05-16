<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//http request website routes
Route::get('/','IndexController@index');
Route::get('index','IndexController@index');
Route::post('attitude/post','PostController@attitude');
Route::post('thumb/reply','PostController@thumb');
Route::post('hit/post','PostController@post_hit');
Route::post('hit/reply','PostController@reply_hit');
Route::get('post/{uid}','PostController@post');
Route::get('reply/{uid}','PostController@reply');
Route::get('forum/{forum}','ForumController@index');
Route::get('about/{id?}','AboutController@show');


//ajax for the local visiting on server
Route::group(['prefix' => 'myApp'],function(){
	Route::get('/','MyApp\IndexController@layout');
	Route::get('index','MyApp\IndexController@index');
	Route::get('post/{uid}', 'MyApp\IndexController@post');
	Route::get('reply/{uid}','MyApp\IndexController@reply');
	Route::get('forum/{forum}','MyApp\IndexController@forum');
	Route::get('about/{id?}','MyApp\IndexController@about');
	Route::get('forums','MyApp\IndexController@forum_list');
	Route::get('emotions','MyApp\IndexController@emotion_list');
	Route::get('hit/post','MyApp\IndexController@post_hit');
	Route::get('hit/reply','MyApp\IndexController@reply_hit');
	Route::get('attitude/post','MyApp\IndexController@attitude');
	Route::get('thumb/reply','MyApp\IndexController@thumb');
});


//ajax for the cross-domain visiting from client
Route::group(['prefix' => 'myApi'],function(){
	Route::get('index','MyApp\ApiController@index');
	Route::get('forum/{forum}','MyApp\ApiController@forum');
	Route::get('forums','MyApp\ApiController@forum_list');
	Route::get('emotions','MyApp\ApiController@emotion_list');
	Route::get('post/{uid}', 'MyApp\ApiController@post');
	Route::get('reply/{uid}','MyApp\ApiController@reply');
	Route::get('about/{id?}','MyApp\ApiController@about');
	Route::get('hit/post','MyApp\ApiController@post_hit');
	Route::get('hit/reply','MyApp\ApiController@reply_hit');
	Route::get('attitude/post','MyApp\ApiController@attitude');
	Route::get('thumb/reply','MyApp\ApiController@thumb');
});

//for iframe using
Route::get('duoshuo/{uid}','DuoShuoController@index');

//caiji action
Route::Post('capture/multi/post','MultiPostController@post');

// Authentication routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Registration routes...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');

// 发送密码重置链接路由
Route::get('password/email', 'Auth\PasswordController@getEmail');
Route::post('password/email', 'Auth\PasswordController@postEmail');

// 密码重置路由
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');

//ajax checking if logged
Route::get('logged', 'CheckController@isLogged');

//user home
Route::match(['get','post'],'home','UserController@index');

//admin user
Route::filter('user', function() {
	if (!\App\Libs\MyCheck::IhaveRight('admin')) {
		return Redirect::to('home');
	}
});
Route::get('admin',['before' => 'user', 'uses' => 'AdminController@index']);
Route::post('admin',['before' => 'user', 'uses' => 'AdminController@submit']);