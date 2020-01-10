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
 	

/* -------------------------------------------------------------------------- */

// HOME 

Route::get('/home', 'HomeController@index')->name('home');
//Route::apiResource('categorias', 'CategoriaController');

/* -------------------------------------------------------------------------- */

// UTILES 

Route::apiResource('charts', 'ChartController');
Route::apiResource('busquedas', 'BusquedaController');

/* -------------------------------------------------------------------------- */

// VENTAS 

Route::post('ventas', 'VentaController@mostrar');
// Route::apiResource('ventas', 'VentaController');

/* -------------------------------------------------------------------------- */

// TRANSFERENCIA

Route::get('transferencias', 'TransferenciaControler@mostrarDataTable');
Route::post('transferencias', 'TransferenciaControler@mostrar');
Route::post('transferenciaG', 'TransferenciaControler@guardarTransferencia');
Route::post('transferenciaModificar', 'TransferenciaControler@modificarTransferencia');
Route::post('transferenciaEliminar', 'TransferenciaControler@eliminarTransferencia');
Route::post('transferenciaEnviar', 'TransferenciaControler@enviarTransferencia');
Route::post('transferenciaCabecera', 'TransferenciaControler@mostrarCabecera');
Route::post('transferenciaCuerpo', 'TransferenciaControler@mostrarCuerpo');
Route::get('transferenciaMostrarImportar', 'TransferenciaControler@mostrarImportar');
Route::get('transferenciasMostrarProductos', 'TransferenciaControler@mostrarProductos');
Route::post('transferenciaRechazar', 'TransferenciaControler@rechazarTransferencia');
Route::post('transferenciaImportar', 'TransferenciaControler@importarTransferencia');
Route::post('transferenciaDetalle', 'TransferenciaControler@detalleTransferencia');
Route::post('pdf-generar-factura','TransferenciaControler@getGenerar');
Route::post('pdf-generar-transferencia','TransferenciaControler@getRptTransferencia');

/* -------------------------------------------------------------------------- */

// INVENTARIO

Route::post('inventarioGuardar', 'InventarioController@guardarInventario');
Route::post('inventarioAgregarEditarProducto', 'InventarioController@agregarEditarProducto');
Route::get('inventarioProductos', 'InventarioController@productosDataTable');
Route::get('inventarioMostrar', 'InventarioController@inventarioDataTable');

/* -------------------------------------------------------------------------- */

// CATEGORIA

Route::get('categoria', 'CategoriaController@obtenerCategorias');

/* -------------------------------------------------------------------------- */

// SUB CATEGORIA

Route::get('subCategoria', 'SubCategoriaController@obtenerSubCategorias');

/* -------------------------------------------------------------------------- */

// COLOR

Route::get('color', 'ColorController@obtenerColores');

/* -------------------------------------------------------------------------- */

// TELAS

Route::get('tela', 'TelaController@obtenerTelas');

/* -------------------------------------------------------------------------- */

// TALLE

Route::get('talle', 'TalleController@obtenerTalles');

/* -------------------------------------------------------------------------- */

// GENERO

Route::get('genero', 'GeneroController@obtenerGeneros');

/* -------------------------------------------------------------------------- */

// MARCA

Route::get('marca', 'MarcaController@obtenerMarcas');

/* -------------------------------------------------------------------------- */

// GONDOLA

Route::get('gondola', 'GondolaController@obtenerGondolas');
Route::post('gondola/producto', 'GondolaController@obtenerGondolasProducto');

/* -------------------------------------------------------------------------- */

// PROVEEDOR

Route::get('proveedor', 'ProveedorController@obtenerProveedores');
Route::post('proveedores', 'ProveedorController@mostrar');

/* -------------------------------------------------------------------------- */

// MONEDA

Route::get('moneda', 'MonedaController@obtenerMonedas');

/* -------------------------------------------------------------------------- */

// VENDEDORES 

Route::post('devendedores', 'DevolucionController@mostrar');
Route::post('vendedores', 'VendedorController@mostrar');

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

Route::post('productoDatatable', 'ProductoController@mostrar');
Route::post('producto', 'ProductoController@encontrar');
Route::get('productoCodigoInterno', 'ProductoController@generarCI');
Route::get('productoCodigo', 'ProductoController@generarCodigo');
Route::post('productoGuardar', 'ProductoController@guardar');
Route::post('productoObtener', 'ProductoController@obtener');
Route::post('productoDatatableGeneral', 'ProductoController@mostrarDataTableGeneral');
Route::post('productoDetalle', 'ProductoController@productoDetalle');
Route::post('productoConProveedor', 'ProductoController@productoProveedor');
Route::post('producto/transferencia', 'ProductoController@productoTransferencia');
Route::post('producto/eliminar', 'ProductoController@eliminar');

/* -------------------------------------------------------------------------- */

//	PARAMETROS

Route::get('parametro', 'ParametroController@mostrar');

/* -------------------------------------------------------------------------- */

//	LOTES

Route::post('lotesConCantidad', 'StockController@obtenerLotesConCantidad');

/* -------------------------------------------------------------------------- */

// COTIZACION

Route::post('cotizacion', 'CotizacionController@cotizar');
Route::get('cotizacion', 'CotizacionController@cotizacionDia');

/* -------------------------------------------------------------------------- */

// COMPRAS

Route::post('comprasDetProductos', 'ComprasDetController@obtenerProductosNroCaja');

/* -------------------------------------------------------------------------- */

// USUARIOS

Route::post('usuariosDatatable', 'UserController@mostrar');
Route::post('usuarioGuardar', 'UserController@guardarUsuario');


/* -------------------------------------------------------------------------- */


// ROLES

Route::get('rolTraer', 'UserController@obtenerRoles');
Route::post('rolesDatatable', 'UserController@rolesDatatable');
Route::post('rolFiltrar', 'UserController@filtrarPermisos');
Route::post('rolUsuarioTraer', 'UserController@obtenerRolesUsuario');
Route::post('rolGuardar', 'UserController@guardarRol');
Route::post('rolAsignar', 'UserController@asignarRol');

/* -------------------------------------------------------------------------- */

// Permiso
Route::get('permisoTraer', 'UserController@obtenerPermisos');
Route::post('permisoFiltrar', 'UserController@filtrarPermiso');
Route::post('permisosDatatable', 'UserController@permisosDatatable');
Route::post('PermisoGuardar', 'UserController@guardarPermiso');
Route::post('permisoAsignar', 'UserController@asignarPermiso');
/* -------------------------------------------------------------------------- */
// ARTICULOS 

Route::post('MontoArticulos', 'ArticulosController@PorMonto');
Route::post('CantidadArticulos', 'ArticulosController@PorCantidad');

/* -------------------------------------------------------------------------- */

// CLIENTES 

Route::post('clientes', 'ClientesController@mostrar');

/* -------------------------------------------------------------------------- */

// DESCUENTO

Route::post('descuento', 'DescuentoController@mostrar');

/* -------------------------------------------------------------------------- */

// EXCEL REPORTES 

Route::post('exportProveedor', 'ProveedorController@descargar');
Route::post('exportransferencia', 'TransferenciaControler@descargar');
Route::post('export', 'ExportController@mostrar');
Route::post('stock', 'Stock@mostrar');
Route::post('exportdescuento', 'ExportDescuentoController@mostrar');
Route::post('ExportInventario', 'InventarioController@Inventario_Cerrado');
Route::post('exportCompra', 'CompraController@Descargar');
/* -------------------------------------------------------------------------- */

// PERMITE QUE SE PUEDA USAR LOS LINK DE VUE-ROUTER A LA HORA DE RECARGAR 

Route::get('{any}', function () {
    return view('home');
})->where('any','.*');

/* -------------------------------------------------------------------------- */
