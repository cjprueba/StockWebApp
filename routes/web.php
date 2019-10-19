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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => true]);
/* Auth::routes(); */
 	


Route::get('/home', 'HomeController@index')->name('home');
Route::apiResource('categorias', 'CategoriaController');
Route::apiResource('ventas', 'VentaController');
Route::apiResource('charts', 'ChartController');
Route::apiResource('busquedas', 'BusquedaController');
Route::post('ventas', 'VentaController@mostrar');

/* -------------------------------------------------------------------------- */

// TRANSFERENCIA

Route::get('transferencias', 'TransferenciaControler@mostrarDataTable');
Route::post('transferencias', 'TransferenciaControler@mostrar');
Route::post('transferenciaGuardar', 'TransferenciaControler@guardarTransferencia');
Route::post('transferenciaModificar', 'TransferenciaControler@modificarTransferencia');
Route::post('transferenciaGuardar', 'TransferenciaControler@eliminarTransferencia');
Route::post('transferenciaCabecera', 'TransferenciaControler@mostrarCabecera');
Route::post('transferenciaCuerpo', 'TransferenciaControler@mostrarCuerpo');

/* -------------------------------------------------------------------------- */

/* LARAVEL EXCEL */

// use App\Exports\VentasMarca;
// use Maatwebsite\Excel\Facades\Excel;

// Route::get('/download', function(){
// 	return Excel::download(new VentasMarca, 'ventasMarca.xlsx');
// });

// Route::post('/downloadVentaMarca', function(){
// 	return Excel::download(new VentasMarca(), 'ventasMarca.xlsx');
// });

/* -------------------------------------------------------------------------- */

Route::post('export', 'ExportController@mostrar');

/* -------------------------------------------------------------------------- */

// SUCURSAL

Route::get('sucursal', 'SucursalController@mostrar');
Route::post('sucursal', 'SucursalController@encontrar');

/* -------------------------------------------------------------------------- */

//	EMPLEADOS

Route::get('empleado', 'EmpleadoController@mostrar');
Route::post('empleado', 'EmpleadoController@encontrar');

/* -------------------------------------------------------------------------- */

//	PRODUCTOS

Route::get('producto', 'ProductoController@mostrar');
Route::post('producto', 'ProductoController@encontrar');

/* -------------------------------------------------------------------------- */

//	PARAMETROS

Route::get('parametro', 'ParametroController@mostrar');

/* -------------------------------------------------------------------------- */

// COTIZACION

Route::post('cotizacion', 'CotizacionController@cotizar');
Route::get('cotizacion', 'CotizacionController@cotizacionDia');

/* -------------------------------------------------------------------------- */

// PERMITE QUE SE PUEDA USAR LOS LINK DE VUE-ROUTER A LA HORA DE RECARGAR 

Route::get('{any}', function () {
    return view('home');
})->where('any','.*');

/* -------------------------------------------------------------------------- */
