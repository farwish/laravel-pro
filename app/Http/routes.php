<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// 访问 $_SERVER['HTTP_HOST'] 时
Route::get('/', 'Member\MemberController@index');

// 访问 $_SERVER['HTTP_HOST']/member 时
Route::get('member', 'Member\MemberController@index');

// 访问 $_SERVER['HTTP_HOST']/admin/member 时
Route::group(['prefix' => 'admin'], function() {
	Route::get('member', 'Member\MemberController@index');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});


