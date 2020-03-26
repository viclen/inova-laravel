<?php

use App\Carro;
use App\Cliente;
use App\Estoque;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->resource('marcas', 'Api\MarcaController');
Route::middleware('auth:api')->resource('carros', 'Api\CarroController');
Route::middleware('auth:api')->resource('clientes', 'Api\ClienteController');
Route::middleware('auth:api')->resource('estoques', 'Api\EstoqueController');
Route::middleware('auth:api')->resource('interesses', 'Api\InteresseController');
Route::middleware('auth:api')->resource('categorias', 'Api\CategoriaController');

Route::middleware('api')->post('/login', 'Api\ApiTokenController@login');
