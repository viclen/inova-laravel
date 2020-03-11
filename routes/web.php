<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');

Route::resource('clientes', 'ClienteController')->middleware('auth');
Route::resource('carros', 'CarroController')->middleware('auth');
Route::resource('marcas', 'MarcaController')->middleware('auth');
Route::resource('regras', 'RegraController')->middleware('auth');
Route::resource('interesses', 'InteresseController')->middleware('auth');
