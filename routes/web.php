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

Route::get('/', 'PageController@index');

Route::group(['prefix' => 'api/v1'], function () {
    Route::any('/units/key/{key}', 'ApiController@getData')->name('api.units');
});

Auth::routes();

