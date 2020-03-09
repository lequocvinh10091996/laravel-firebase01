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

Route::get('/', 'LoginController@index');
Route::post('/checklogin', 'LoginController@checklogin');
Route::get('/successlogin', 'LoginController@successlogin');
Route::get('/logout', 'LoginController@logout');

Route::get('/account', 'AccountController@index');
Route::post('/account', 'AccountController@store');
Route::post('/account/update', 'AccountController@update');
Route::post('/account/delete', 'AccountController@delete');

Route::get('/account/index', function () {
    return view('account', ['controllername' => 'AccountController']);
});

Route::get('/section', 'SectionController@index');
Route::post('/section', 'SectionController@store');
Route::post('/section/update', 'SectionController@update');
Route::post('/section/delete', 'SectionController@delete');

Route::get('/section/index', function () {
    return view('section', ['controllername' => 'SectionController']);
});

Route::get('/terminology', 'TerminologyController@index');
Route::post('/terminology', 'TerminologyController@store');
Route::post('/terminology/update', 'TerminologyController@update');
Route::post('/terminology/delete', 'TerminologyController@delete');

Route::get('/terminology/index', function () {
    return view('terminology', ['controllername' => 'TerminologyController']);
});

Route::get('/topic', 'TopicController@index');
Route::post('/topic', 'TopicController@store');
Route::post('/topic/update', 'TopicController@update');
Route::post('/topic/delete', 'TopicController@delete');

Route::get('/topic/index', function () {
    return view('topic', ['controllername' => 'TopicController']);
});

Route::get('/googlesheet', 'GoogleSheetController@index');
Route::post('/googlesheet', 'GoogleSheetController@store');
Route::post('/googlesheet/update', 'GoogleSheetController@update');
Route::post('/googlesheet/delete', 'GoogleSheetController@delete');

Route::get('/googlesheet/index', function () {
    return view('googlesheet', ['controllername' => 'GoogleSheetController']);
});
