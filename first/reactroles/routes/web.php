<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
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

Route::group(['middleware' => 'role:user'], function() {
    Route::get('/1', function() {
        return 'Добро пожаловать, Юзер';
    });
    Route::get('index', 'UserController@index')->name('index');
});

Route::group(['middleware' => 'role:guest'], function() {
    Route::get('/1', function() {
        return 'Добро пожаловать, guest';
    });
});
Route::group(['middleware' => 'role:admin'])->group(function () {
    Route::get('index', 'UserController@index')->name('index');


});
Route::group(['middleware' => 'role:redactor'], function() {
    Route::get('index', 'UserController@index')->name('index');
    Route::get('/2', function() {
        return 'Добро пожаловать, redactor';
    });
});
Route::group(['middleware' => 'role:superadmin'], function() {
    Route::get('/1', function() {
        return 'Добро пожаловать, superadmin';
    });
    Route::get('index', 'UserController@index')->name('index');
});

Route::get('/', function() {
    return view('welcome');
});

Route::get('register', 'RegisterController@register')->name('register');
Route::post('register', 'RegisterController@create');
Route::get('login', 'LoginController@login')->name('login');
Route::post('login', 'LoginController@auth')->name('auth');
Route::get('logout', 'LoginController@logout')->name('logout');
Route::get('index', 'UserController@index')->name('index');

