<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ApiAuthMiddleware;


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

Route::get('/', function () {
    return view('welcome');
});

Route::post('api/register','App\Http\Controllers\UserController@register');

Route::post('api/login','App\Http\Controllers\UserController@login');

Route::put('api/user/update','App\Http\Controllers\UserController@update');

Route::post('api/user/upload','App\Http\Controllers\UserController@upload')->middleware(ApiAuthMiddleware::class);