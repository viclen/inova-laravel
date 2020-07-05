<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('api')->post('/login', 'Api\ApiTokenController@login');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// search
Route::get('/clientes/search', 'Api\ClienteController@search')->middleware('auth:api');
Route::get('/carros/search', 'Api\CarroController@search')->middleware('auth:api');
Route::get('/marcas/search', 'Api\MarcaController@search')->middleware('auth:api');
Route::get('/estoques/search', 'Api\EstoqueController@search')->middleware('auth:api');
Route::get('/interesses/search', 'Api\InteresseController@search')->middleware('auth:api');

// resources
Route::middleware('auth:api')->resource('marcas', 'Api\MarcaController');
Route::middleware('auth:api')->resource('carros', 'Api\CarroController');
Route::middleware('auth:api')->resource('carros.modelos', 'Api\ModeloController');
Route::middleware('auth:api')->resource('clientes', 'Api\ClienteController');
Route::middleware('auth:api')->resource('carroclientes', 'Api\CarroClienteController');
Route::middleware('auth:api')->resource('estoques', 'Api\EstoqueController');
Route::middleware('auth:api')->resource('interesses', 'Api\InteresseController');
Route::middleware('auth:api')->resource('categorias', 'Api\CategoriaController');
Route::middleware('auth:api')->resource('caracteristicas', 'Api\CaracteristicaController');
Route::middleware('auth:api')->resource('match', 'Api\MatchController');

Route::middleware('auth:api')->get('/estoques/{id}/match', 'Api\EstoqueController@match');
Route::middleware('auth:api')->get('/interesses/{id}/match', 'Api\InteresseController@match');
