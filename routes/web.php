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

Route::get('/configtype', 'ConfigTypeController@index');
Route::post('/configtype', 'ConfigTypeController@store');
Route::post('/configtype/update', 'ConfigTypeController@update');
Route::post('/configtype/delete', 'ConfigTypeController@delete');

Route::get('/configtype/index', function () {
    return view('configtype', ['controllername' => 'ConfigTypeController']);
});

Route::get('/translatemean', 'TranslateMeanController@index');
Route::post('/translatemean', 'TranslateMeanController@store');
Route::post('/translatemean/update', 'TranslateMeanController@update');
Route::post('/translatemean/delete', 'TranslateMeanController@delete');
Route::get('/translatemean/dvtquydoi/{id}', 'TranslateMeanController@dvtquydoi');

Route::get('/translatemean/index', function () {
    return view('translatemean', ['controllername' => 'TranslateMeanController']);
});
