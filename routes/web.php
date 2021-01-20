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
Route::resource('clientes.carros', 'CarroClienteController')->middleware('auth');
Route::resource('carros', 'CarroController')->middleware('auth');
Route::resource('modelos', 'ModeloController')->middleware('auth');
Route::resource('marcas', 'MarcaController')->middleware('auth');
Route::resource('regras', 'RegraController')->middleware('auth');
Route::resource('estoques', 'EstoqueController')->middleware('auth');
Route::resource('interesses', 'InteresseController')->middleware('auth');
Route::resource('caracteristicas', 'CaracteristicaController')->middleware('auth');
Route::resource('usuarios', 'UserController')->middleware('auth')->name('index', 'users');
Route::resource('fipe', 'FipeController')->middleware('auth');

// download
Route::get('estoques/{id}/matches/download', 'EstoqueController@downloadMatches')->middleware('auth');

// search
Route::get('/clientes/search/{search}', 'ClienteController@search')->middleware('auth');
Route::get('/carros/search/{search}', 'CarroController@search')->middleware('auth');
Route::get('/marcas/search/{search}', 'MarcaController@search')->middleware('auth');
Route::get('/estoques/search/{search}', 'EstoqueController@search')->middleware('auth');
Route::get('/interesses/search/{search}', 'InteresseController@search')->middleware('auth');
