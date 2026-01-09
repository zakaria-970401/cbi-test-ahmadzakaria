<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::post('/login', 'App\Http\Controllers\Api\LoginController@doLogin')->name('doLogin');
Route::GET('/home', 'App\Http\Controllers\Api\LoginController@home')->name('home');
Route::GET('/getListItem', 'App\Http\Controllers\Api\LoginController@getListItem')->name('getListItem');
