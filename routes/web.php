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

Auth::routes(['register' => false]);
/* Auth::routes(); */
 	

/* -------------------------------------------------------------------------- */
Route::post('/qrcode','QrController@Crear');
Route::post('/barcode','QrController@Crear_Barcode');
Route::post('/barinterno','QrController@Crear_Barinterno');
Route::get('/etigondola','QrController@Crear_Etiqueta_Gondola');
// HOME 

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/producto', 'ApiController@producto')->name('producto');
Route::get('/catalogo', 'ApiController@catalogo')->name('catalogo');
//Route::apiResource('categorias', 'CategoriaController');

/* -------------------------------------------------------------------------- */

// UTILES 

Route::apiResource('charts', 'ChartController');
Route::apiResource('busquedas', 'BusquedaController');


/* -------------------------------------------------------------------------- */

// CAJAS
 
Route::post('cajaObtener', 'CajaController@obtenerCaja');

/* -------------------------------------------------------------------------- */

// VENTAS 

Route::post('ventas', 'VentaController@mostrar');
Route::get('ventas/periodos', 'VentaController@periodos_superados');
Route::post('venta/guardar', 'VentaController@guardar');
Route::post('venta/numeracion', 'VentaController@numeracion');
Route::post('venta/inicio', 'VentaController@inicio');
Route::post('venta/factura', 'VentaController@factura');
Route::post('venta/ticket', 'VentaController@ticket');
Route::post('venta/resumen', 'VentaController@resumen');
Route::post('venta/datatable', 'VentaController@datatable');
Route::post('ventaFiltrar', 'VentaController@filtrarVenta');
Route::post('ventaAnular', 'VentaController@anularVenta');
Route::post('ventasDatatable', 'VentaController@ventasDatatable');
Route::post('venta/devolucion/productos', 'VentaController@devolucionProductos');
Route::get('ventaMostrarProductos', 'VentaController@mostrarProductos');
Route::post('pdf-generar-rptVale','VentaController@rptVale');
Route::get('ventaValeDatatable', 'VentaController@generarVentaVale');
Route::get('venta/cuenta/datatable', 'VentaController@obtenerCuentas');
Route::post('venta/pago/pe', 'VentaController@pagoEntrega');
Route::post('venta/pago/credito', 'VentaController@pagoCredito');
Route::post('/venta/reporte/unico', 'VentaController@reporteUnico');

/* -------------------------------------------------------------------------- */

// DEV_TRANSFERENCIA
Route::get('transferenciasDev', 'DevTransferenciaController@mostrarDataTable');
Route::get('transferenciasMostrarProductosDev', 'DevTransferenciaController@mostrarProductosDevolucion');
Route::get('transferenciasMostrarProductosDevImp', 'DevTransferenciaController@mostrarProductosDevolucionImp');
Route::post('devTransferenciaEnviar', 'DevTransferenciaController@enviarDevTransferencia');
Route::post('transferenciasDevImportar', 'DevTransferenciaController@mostrarImportar');
Route::post('DevtransferenciaImportar', 'DevTransferenciaController@importarDevTransferencia');
Route::post('devTransferenciaEliminar', 'DevTransferenciaController@eliminarDevTransferencia');
Route::post('devTransferenciaRechazar', 'DevTransferenciaController@rechazarDevTransferencia');
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
Route::post('transferencia/mostrar/importar', 'TransferenciaControler@mostrarImportar');
Route::get('transferenciasMostrarProductos', 'TransferenciaControler@mostrarProductos');
Route::get('transferenciasMostrarProductosDevolucion', 'TransferenciaControler@mostrarProductosDevolucion');		
Route::post('transferenciaRechazar', 'TransferenciaControler@rechazarTransferencia');
Route::post('transferenciaImportar', 'TransferenciaControler@importarTransferencia');
Route::post('transferenciaDetalle', 'TransferenciaControler@detalleTransferencia');
Route::post('pdf-generar-factura-transferencia','TransferenciaControler@getGenerar');
Route::post('pdf-generar-transferencia','TransferenciaControler@getRptTransferencia');
Route::post('pdf-rptTransferencia','TransferenciaControler@rptTransferencia');
Route::get('ventaTransferenciaDatatable', 'TransferenciaControler@generarVentaT');
Route::post('devolver_transferencia','TransferenciaControler@devolverTransferencia');

Route::post('marcar_transferencia_devolucion','TransferenciaControler@marcarTransferenciaDevolucion');


/* -------------------------------------------------------------------------- */

// INVENTARIO

Route::post('inventarioGuardar', 'InventarioController@guardarInventario');
Route::post('inventarioAgregarEditarProducto', 'InventarioController@agregarEditarProducto');
Route::get('inventarioProductos', 'InventarioController@productosDataTable');
Route::get('inventarioMostrar', 'InventarioController@inventarioDataTable');
Route::post('/inventario/eliminar/producto', 'InventarioController@eliminarProducto');
Route::post('inventario/comentario', 'InventarioController@modificarComentario');
Route::post('/inventario/reporte', 'InventarioController@reporte');
Route::post('/inventario/procesar', 'InventarioController@procesar');

/* -------------------------------------------------------------------------- */

// CATEGORIA

Route::get('categoria', 'CategoriaController@obtenerCategorias');
Route::post('categoriaDatatable', 'CategoriaController@categoriasDatatable');
Route::post('CategoriasPorSubCategoriasDatatable', 'CategoriaController@categoriaDatatable');
Route::post('categoriaFiltrar', 'CategoriaController@filtrarCategoria');
Route::post('CategoriaEliminar', 'CategoriaController@CategoriaEliminar');
Route::get('nuevaCategoria','CategoriaController@obtenerCodigo');
Route::post('categoriaGuardar', 'CategoriaController@categoriaGuardar');

/* -------------------------------------------------------------------------- */

// SUB CATEGORIA

Route::post('subCategoria', 'SubCategoriaController@obtenerSubCategorias');
Route::post('subCategoriaFiltrar', 'SubCategoriaController@filtrarSubCategoria');
Route::get('nuevaSubCategoria','SubCategoriaController@obtenerCodigo');
Route::post('subCategoriaEliminar', 'SubCategoriaController@subCategoriaEliminar');
Route::post('subCategoriaGuardar', 'SubCategoriaController@subCategoriaGuardar');
Route::post('subCategoriaDatatable', 'SubCategoriaController@subCategoriasDatatable');
Route::post('subCategoriasDatatable', 'SubCategoriaController@subCategoriaDatatable');

/* -------------------------------------------------------------------------- */

// SUB CATEGORIA DETALLE

Route::post('subCategoriaDetalle', 'SubCategoriaDetalleController@obtenerSubCategoriasDetalle');
Route::post('subCategoriaDetalle/datatable', 'SubCategoriaDetalleController@datatableSubCategoriaDetalle');
Route::post('subCategoriaDetalle/filtrar', 'SubCategoriaDetalleController@filtrarSubCategoriaDetalle');
Route::get('subCategoriaDetalle/nuevo', 'SubCategoriaDetalleController@nuevoSubCategoriaDetalle');
Route::post('subCategoriaDetalle/guardar', 'SubCategoriaDetalleController@guardarSubCategoriaDetalle');
Route::post('subCategoriaDetalle/eliminar', 'SubCategoriaDetalleController@eliminarSubCategoriaDetalle');

/* -------------------------------------------------------------------------- */

// COLOR

Route::get('nuevoColor','ColorController@obtenerCodigo');
Route::get('color', 'ColorController@obtenerColores');
Route::post('coloresDatatable', 'ColorController@ColoresDatatable');
Route::post('colorFiltrar', 'ColorController@filtrarColor');
Route::post('colorGuardar', 'ColorController@colorGuardar');
Route::post('colorEliminar', 'ColorController@colorEliminar');

/* -------------------------------------------------------------------------- */

// TELAS

Route::get('tela', 'TelaController@obtenerTelas');
Route::post('telasDatatable', 'TelaController@telasDatatable');
Route::post('telaFiltrar', 'TelaController@filtrarTela');
Route::get('nuevaTela','TelaController@obtenerCodigo');
Route::post('telaGuardar', 'TelaController@telaGuardar');
Route::post('telaEliminar', 'TelaController@telaEliminar');

/* -------------------------------------------------------------------------- */

// TALLE

Route::get('talle', 'TalleController@obtenerTalles');
Route::post('tallesDatatable', 'TalleController@tallesDatatable');
Route::post('talleFiltrar', 'TalleController@filtrarTalle');
Route::post('talleEliminar', 'TalleController@talleEliminar');
Route::get('nuevoTalle','TalleController@obtenerCodigo');
Route::post('talleGuardar', 'TalleController@talleGuardar');

/* -------------------------------------------------------------------------- */

// GENERO

Route::get('genero', 'GeneroController@obtenerGeneros');

/* -------------------------------------------------------------------------- */

// MARCA

Route::post('marca','MarcaController@obtenerMarcas');
Route::get('nuevaMarca','MarcaController@obtenerCodigo');
Route::post('marcaFiltrar', 'MarcaController@filtrarMarca');
Route::post('marcaGuardar', 'MarcaController@marcaGuardar');
Route::post('marcaEliminar', 'MarcaController@marcaEliminar');
Route::post('marcasDatatable', 'MarcaController@MarcasDatatable');
Route::post('MarcasPorCategoriaDatatable', 'MarcaController@MarcasPorCategoriaDatatable');
Route::post('marca/categoria/seleccion','MarcaController@marcaCategoriaSeleccion');

/* -------------------------------------------------------------------------- */

// GONDOLA

Route::get('gondola', 'GondolaController@obtenerGondolas');
Route::post('gondolasDatatable', 'GondolaController@GondolasDatatable');
Route::post('gondolasFiltrar', 'GondolaController@filtrarGondola');
Route::post('gondolaGuardar', 'GondolaController@gondolaGuardar');
Route::post('gondolaEliminar', 'GondolaController@gondolaEliminar');
Route::get('nuevaGondola','GondolaController@obtenerCodigo');
Route::post('gondola/producto', 'GondolaController@obtenerGondolasProducto');

/* -------------------------------------------------------------------------- */

// PROVEEDOR

Route::get('proveedor', 'ProveedorController@obtenerProveedores');
Route::post('proveedores', 'ProveedorController@mostrar');
Route::post('proveedor/pago', 'ProveedorController@pago');
Route::get('proveedor/datatable', 'ProveedorController@datatable');
Route::post('proveedor/lote', 'ProveedorController@loteProducto');
Route::post('proveedor/devolucion', 'ProveedorController@devolucion');
Route::get('proveedor/devolucion/mostrar', 'ProveedorController@devolucionMostrar');
Route::get('proveedor/devolucion/detalle', 'ProveedorController@devolucionDetalle');
Route::post('proveedorFiltrar', 'ProveedorController@filtrarProveedor');
Route::post('proveedorGuardar', 'ProveedorController@proveedorGuardar');
Route::post('proveedorEliminar', 'ProveedorController@proveedorEliminar');
Route::post('proveedorDatatable', 'ProveedorController@proveedorDatatable');

/* -------------------------------------------------------------------------- */

// PAGOS PROVEEDOR

Route::post('pagos_prov/datatable', 'Pagos_ProvController@datatable');
Route::post('pagos_prov/pagoUnico', 'Pagos_ProvController@pagoUnico');

/* -------------------------------------------------------------------------- */

// MONEDA

Route::get('moneda', 'MonedaController@obtenerMonedas');

/* -------------------------------------------------------------------------- */

// TARJETA

Route::get('tarjeta/datatable', 'TarjetaController@datatable');
Route::post('pdf-rptTarjeta', 'TarjetaController@reporteTarjeta');
Route::get('ventaTarjetaDatatable', 'TarjetaController@generarVentaTarjeta');

/* -------------------------------------------------------------------------- */

// BANCO

Route::get('banco/datatable', 'BancoController@datatable');

/* -------------------------------------------------------------------------- */

// GIRO ENTIDADES

Route::get('giro/datatable/entidades', 'GiroController@datatableEntidades');

/* -------------------------------------------------------------------------- */

// VENDEDORES 

Route::post('devendedores', 'DevolucionController@mostrar');
Route::post('vendedores', 'VendedorController@mostrar');
Route::post('/vendedor/datatable', 'VendedorController@datatable');

Route::post('ventaVendedorDatatable', 'VendedorController@generarVentaVendedor');
Route::post('pdf-rptVendedor', 'VendedorController@reporteVendedor');


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
Route::post('sucursal/sucursalDatatable', 'SucursalController@sucursalDatatable');
Route::post('sucursalFiltrar', 'SucursalController@filtrarSucursal');
Route::post('sucursalGuardar', 'SucursalController@guardarSucursal');
Route::post('sucursalEliminar', 'SucursalController@eliminarSucursal');
Route::get('nuevaSucursal', 'SucursalController@sucursalNueva');

/* -------------------------------------------------------------------------- */

// EMPLEADOS

Route::get('empleado', 'EmpleadoController@mostrar');
Route::post('empleado', 'EmpleadoController@encontrar');

/* -------------------------------------------------------------------------- */

// PRODUCTOS

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
Route::post('productoCompra', 'ProductoController@obtenerProductoCompra');
Route::post('producto/minimo', 'ProductoController@minimo');
Route::post('producto/baja', 'ProductoController@baja');
Route::post('producto/POS', 'ProductoController@obtenerProductoPOS');
Route::post('productoImportar', 'ProductoController@importar');
Route::post('producto/ubicacion', 'ProductoController@ubicacion');
Route::post('producto/existe', 'ProductoController@existe');
Route::post('producto/mostrar_new', 'ProductoController@mostrar_new');
Route::post('producto/catalogo', 'ProductoController@catalogo_cliente');

/* -------------------------------------------------------------------------- */

// SERVICIOS 

Route::post('servicio/POS', 'ServicioController@obtenerServicioPOS');
Route::post('pdf-rptDelivery', 'ServicioController@reporteDelivery');
Route::get('generarDeliveryDatatable', 'ServicioController@generarRptDelivery');

/* -------------------------------------------------------------------------- */

//	PARAMETROS

Route::get('parametro', 'ParametroController@mostrar');

/* -------------------------------------------------------------------------- */

//	LOTES

Route::post('lotesConCantidad', 'StockController@obtenerLotesConCantidad');
Route::post('lote/vencidos', 'StockController@vencidos');
Route::post('lote/terminados', 'StockController@terminados');
Route::post('lote/terminados/reporte', 'StockController@terminados_reporte');

/* -------------------------------------------------------------------------- */

// COTIZACION

Route::post('cotizacion', 'CotizacionController@cotizar');
Route::get('cotizacion', 'CotizacionController@cotizacionDia');
Route::get('cotizacion/compra-dia', 'CotizacionController@cotizacionCompraDia');
Route::post('cotizacionGuardar', 'CotizacionController@guardarCotizacion');
Route::post('cotizacionFiltrar', 'CotizacionController@filtrarCotizacion');
Route::post('eliminarCotizacion', 'CotizacionController@eliminarCotizacion');

/* -------------------------------------------------------------------------- */

// COMPRAS

Route::post('comprasDetProductos', 'ComprasDetController@obtenerProductosNroCaja');
Route::post('compra/guardar-modificar', 'CompraController@guardarModificarCompra');
Route::get('compra/codigo', 'CompraController@obtenerCodigo');
Route::get('compra/datatable', 'CompraController@mostrarDataTable');
Route::post('compra/eliminar', 'CompraController@eliminarCompra');
Route::get('compra/mostrar_productos', 'CompraController@mostrarProductos');
Route::post('compra/pdf', 'CompraController@getPdf');
Route::post('compra/cabecera', 'CompraController@obtenerCabecera');
Route::post('compra/cuerpo', 'CompraController@obtenerCuerpo');

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

// PERMISOS

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

Route::post('clientes', 'ClienteController@mostrar');
Route::post('/cliente/datatable', 'ClienteController@datatable');
Route::post('clienteFiltrar', 'ClienteController@filtrarCliente');
Route::post('cliente/clienteDatatable', 'ClienteController@clienteDatatable');
Route::post('clienteGuardar', 'ClienteController@guardarCliente');
Route::post('clienteEliminar', 'ClienteController@eliminarCliente');
Route::get('nuevoCliente', 'ClienteController@nuevoCliente');
Route::post('cliente/credito', 'ClienteController@creditoCliente');
Route::post('cliente/credito/datatable', 'ClienteController@creditoClienteDatatable');
Route::post('empresasDatatable', 'ClienteController@datatableEmpresa');
Route::post('/cliente/credito/detalle/datatable', 'ClienteController@creditoClienteDatatableDetalle');
Route::post('/cliente/credito/detalle/abono/datatable', 'ClienteController@creditoClienteAbonoDatatable');

/* -------------------------------------------------------------------------- */

// DESCUENTO

Route::post('descuento', 'DescuentoController@mostrar');

/* -------------------------------------------------------------------------- */

// DEUDA

Route::post('/deuda/deudaDatatable', 'DeudaController@deuda_datatable');
Route::post('/deuda/deudaCompraDatatable', 'DeudaController@deuda_compra_datatable');
Route::post('/deuda/datosNota', 'DeudaController@datos_nota');

/* -------------------------------------------------------------------------- */

// EXCEL REPORTES 

Route::post('exportProveedor', 'ProveedorController@descargar');
Route::post('exportransferencia', 'TransferenciaControler@descargar');
Route::post('export', 'ExportController@mostrar');
Route::post('stock', 'Stock@mostrar');
Route::post('exportdescuento', 'ExportDescuentoController@mostrar');
Route::post('ExportInventario', 'InventarioController@Inventario_Cerrado');
Route::post('exportCompra', 'CompraController@Descargar');
Route::post('export/Stock', 'StockController@descargar');

/* -------------------------------------------------------------------------- */

// TRASNSPORTE 

Route::post('transporteFiltrar', 'TransporteController@filtrarTransporte');
Route::post('transporteDatatable', 'TransporteController@transporteDatatable');
Route::get('nuevoTransporte','TransporteController@obtenerCodigo');
Route::post('transporteGuardar', 'TransporteController@transporteGuardar');
Route::post('transporteEliminar', 'TransporteController@transporteEliminar');

/* -------------------------------------------------------------------------- */

// CONTAINER

Route::post('containerFiltrar', 'ContainerController@filtrarContainer');
Route::post('containerDatatable', 'ContainerController@containerDatatable');
Route::get('nuevoContainer','ContainerController@obtenerCodigo');
Route::post('containerGuardar', 'ContainerController@containerGuardar');
Route::post('containerEliminar', 'ContainerController@containerEliminar');

/* -------------------------------------------------------------------------- */

// NOTA DE REMISION 

Route::post('remisionGuardar', 'RemisionController@guardarRemision');
Route::post('remisionFiltrar', 'RemisionController@filtrarRemision');
Route::post('remisionEliminar', 'RemisionController@eliminarRemision');
Route::get('nuevaNota', 'RemisionController@notaNueva');
Route::post('remision/NotaDeRemisionDatatable', 'RemisionController@remisionDatatable');
Route::post('pdf-generar-remision','RemisionController@remision_pdf');
Route::post('remisionCuerpo', 'RemisionController@mostrarCuerpo');
Route::post('remisionModificar', 'RemisionController@modificarRemision');

/* -------------------------------------------------------------------------- */

//ORDEN 

Route::post('orden/datatable', 'OrdenController@datatable');
Route::get('ordenMostrarProductos', 'OrdenController@mostrarProductos');
Route::post('pdf-generar-factura', 'OrdenController@factura');
Route::post('pdf-generar-direccion','OrdenController@direccionPDF');
Route::post('ordenCabecera', 'OrdenController@obtenerCabecera');
Route::post('ordenEnviar', 'OrdenController@enviarOrden');
Route::get('orden/datatablePendiente', 'OrdenController@datatable_pendiente');
Route::get('orden/datatableProcesando', 'OrdenController@datatable_procesando');
Route::post('ordenPendienteCabecera', 'OrdenController@obtenerCabeceraPendiente');
Route::get('ordenPendienteMostrarProductos', 'OrdenController@mostrarProductosPendiente');
Route::post('pdf-generar-factura-pendiente', 'OrdenController@facturaPendiente');
Route::post('pdf-generar-direccion-pendiente','OrdenController@direccionPendientePDF');

/* -------------------------------------------------------------------------- */

// PEDIDO

Route::post('pedido/producto/agregar', 'PedidoController@agregarProducto');
Route::post('pedido/producto/obtener', 'PedidoController@obtenerProductos');
Route::post('pedido/confirmar', 'PedidoController@confirmar');
Route::post('pedido/inicio_mostrar_new', 'PedidoController@inicio_mostrar_new');
Route::post('pedido/cambiar/cantidad', 'PedidoController@cambiar_cantidad');
Route::post('pedido/producto/eliminar', 'PedidoController@eliminar_producto');
Route::post('pedido/mostrar/datatable', 'PedidoController@mostrar_datatable');
Route::post('pedido/cambiar/estatus', 'PedidoController@cambiar_estatus');
Route::post('pedido/reporte', 'PedidoController@reporte');
Route::post('pedido/inicio_catalogo', 'PedidoController@inicio_catalogo');

/* -------------------------------------------------------------------------- */

/* -------------------------------------------------------------------------- */

// REPORTES GENERAR
/* -------------------------------------------------------------------------- */

// REPORTES VENTAS
Route::post('reporte_ventas', 'VentaController@reporteVenta');

Route::post('export_marca_categoria', 'ExportController@descargarMarcaCategoria');
/* -------------------------------------------------------------------------- */

// REPORTES TRANSFERENCIAS
Route::post('reporte_transferencias_ventas', 'VentaController@reporteVenta');

Route::post('export-transferencia-consignacion', 'ExportController@descargarTransferenciaVentas');

/* -------------------------------------------------------------------------- */

// CUPONES
Route::post('/cupon/datatable', 'CuponController@datatable');
Route::post('/cupon/aplicar', 'CuponController@cuponAplicar');
Route::post('cuponDeshabilitar', 'CuponController@cuponDeshabilitar');
Route::post('cuponHabilitar', 'CuponController@cuponHabilitar');
Route::get('obtenerCupon', 'CuponController@CrearCupon');
Route::post('cuponGuardar', 'CuponController@cuponGuardar');
Route::post('conseguirCupon', 'CuponController@ConseguirCupon');
Route::post('cuponModificar', 'CuponController@cuponModificar');
Route::post('conseguirCupon', 'CuponController@ConseguirCupon');
Route::get('arreglar', 'TransferenciaControler@arreglar');

// PERMITE QUE SE PUEDA USAR LOS LINK DE VUE-ROUTER A LA HORA DE RECARGAR 

/* -------------------------------------------------------------------------- */

// NOTA DE CREDITO

Route::post('/nota/credito/generar_cuerpo', 'NotaCreditoController@generar_cuerpo');
Route::post('/nota/credito/guardar', 'NotaCreditoController@guardar');
Route::post('/nota/credito/generar/pdf', 'NotaCreditoController@pdf');

/* -------------------------------------------------------------------------- */


// CONTROL DE CODIGO DE SUPERVISOR

Route::post('/obtener/autorizacion', 'User_SupervisorController@obtener_autorizacion');


/* -------------------------------------------------------------------------- */

Route::get('{any}', function () {
    return view('home');
})->where('any','.*');

/* -------------------------------------------------------------------------- */
