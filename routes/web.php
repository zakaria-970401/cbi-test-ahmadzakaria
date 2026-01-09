<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::post('/login', 'App\Http\Controllers\Auth\LoginController@doLogin')->name('doLogin');
Route::GET('/home', 'App\Http\Controllers\Auth\LoginController@home')->name('home');
Route::GET('/getListItem', 'App\Http\Controllers\Auth\LoginController@getListItem')->name('getListItem');
