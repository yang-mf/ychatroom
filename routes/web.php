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




Route::get('/','index\IndexController@index');
Route::get('index/show','index\IndexController@show');
Route::get('index/zhuce','index\IndexController@zhuce');
Route::post('index/login','index\IndexController@login');
Route::post('index/dozhuce','index\IndexController@dozhuce');
Route::get('session','index\IndexController@session');
