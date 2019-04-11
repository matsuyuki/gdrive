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

Route::get('', 'HomeController@index');

Route::get('sync', 'HomeController@sync')->name('sync');

Route::get('fail', 'HomeController@fail')->name('fail');
Route::post('folder', 'HomeController@json');

Route::post('list_file', 'HomeController@list_file')->name('list_file');
Route::post('curl_api', 'HomeController@curl_api')->name('curl_api');

Route::get('folder', 'HomeController@page_folder')->name('folder');
Route::post('save_folder', 'HomeController@save_folder')->name('save_folder');
