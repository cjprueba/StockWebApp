<?php

use Illuminate\Http\Request;

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

Route::get('tasks', 'TaskController@index');
Route::get('task/{id}', 'TaskController@show');
Route::post('task', 'TaskController@store');
Route::put('task/{id}', 'TaskController@update');
Route::delete('task/{id}', 'TaskController@delete');

/* -------------------------------------------------------------------------- */

// PRODUCTO

Route::post('productos', 'ProductoController@apiListarProductos');
Route::post('producto/ofertas', 'ProductoController@ofertas');

/* -------------------------------------------------------------------------- */

// CATEGORIA 

Route::post('categorias_cantidad', 'CategoriaController@CategoriasCantidad');

/* -------------------------------------------------------------------------- */