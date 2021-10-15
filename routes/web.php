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
Route::post('/PdfQrCajaCompra','QrController@Crear_Pdf_Compra_Caja_Qr');

Route::post('/PdfQrCajaTransferencia','QrController@Crear_Pdf_Transferencia_Caja_Qr');
// HOME 

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/producto', 'ApiController@producto')->name('producto');
Route::get('/productoqr', 'ApiController@productoqr')->name('productoqr');
Route::get('/catalogo', 'ApiController@catalogo')->name('catalogo');
Route::get('/cajaqr', 'ApiController@Cajaqr')->name('cajaqr');
Route::get('/bannerCotizacion', 'ApiController@bannerCotizacion')->name('bannerCotizacion');
//Route::apiResource('categorias', 'CategoriaController');

/* -------------------------------------------------------------------------- */

// UTILES 

Route::apiResource('charts', 'ChartController');
Route::apiResource('busquedas', 'BusquedaController');
Route::apiResource('busqueda-sector-sucursal', 'BusquedaRmController');


/* -------------------------------------------------------------------------- */

// CAJAS
 
Route::post('cajaObtener', 'CajaController@obtenerCaja');
Route::post('asignarCaja', 'CajaController@cajaAsignar');
Route::post('existeCaja', 'CajaController@cajaExiste');
Route::post('quitarCaja', 'CajaController@cajaQuitar');


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
Route::post('/venta/nota/credito/asingar/credito/cliente', 'VentaController@asignarNotaCreditoCreditoCliente');

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
Route::get('transferencia/cobrar', 'TransferenciaControler@cobrarDataTable');
Route::post('transferenciaModificarUbicacion', 'TransferenciaControler@modificarTransferenciaUbicacion');
Route::post('transferencia/obtener/caja/numero', 'TransferenciaControler@obtenerCajaNumero');


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
Route::post('reporte_inventario_seccion', 'InventarioController@generar_reporte_inventario_seccion');
Route::post('export_inventario_seccion_gondola', 'InventarioController@ExportInventarioSeccion');

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
Route::get('configuracion/gondola','GondolaController@ConfiguracionInicioGondola');
Route::get('gondola/encargada/seccion', 'GondolaController@obtenerGondolasEncargada');



/* -------------------------------------------------------------------------- */

// PISO
Route::get('piso', 'PisoController@mostrar');
Route::post('piso', 'PisoController@encontrar');
Route::post('pisoDatatable', 'PisoController@datatable');
Route::post('pisoFiltrar', 'PisoController@filtrarPiso');
Route::get('nuevoPiso', 'PisoController@PisoNuevo');
Route::post('guardarPiso', 'PisoController@PisoGuardar');
Route::post('eliminarPiso', 'PisoController@PisoEliminar');

/* -------------------------------------------------------------------------- */

// SECTOR

Route::get('sector', 'SectorController@mostrar');
Route::post('sector', 'SectorController@encontrar');
Route::post('sectorDatatable', 'SectorController@datatable');

Route::get('nuevoSector', 'SectorController@SectorNuevo');
Route::post('sectorFiltrar', 'SectorController@filtrarSector');
Route::post('guardarSector', 'SectorController@SectorGuardar');
Route::post('eliminarSector', 'SectorController@SectorEliminar');



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
Route::get('nuevoProveedor', 'ProveedorController@nuevoProveedor');


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

 use App\Exports\StockImageExport;
 use Maatwebsite\Excel\Facades\Excel;

Route::get('/download', function(){
	return Excel::download(new StockImageExport(), 'STOCKIMAGE.xlsx');
});

// Route::post('/downloadVentaMarca', function(){
// 	return Excel::download(new VentasMarca(), 'ventasMarca.xlsx');
// });

/* -------------------------------------------------------------------------- */

Route::post('export', 'ExportController@mostrar');

/* -------------------------------------------------------------------------- */

// SUCURSAL

Route::get('sucursal', 'SucursalController@mostrar');
Route::post('sucursal', 'SucursalController@encontrar');
Route::get('sucursalInventario', 'SucursalController@mostrarSucursal');
Route::post('sucursal/sucursalDatatable', 'SucursalController@sucursalDatatable');
Route::post('sucursalFiltrar', 'SucursalController@filtrarSucursal');
Route::post('sucursalGuardar', 'SucursalController@guardarSucursal');
Route::post('sucursalEliminar', 'SucursalController@eliminarSucursal');
Route::get('nuevaSucursal', 'SucursalController@sucursalNueva');

/* -------------------------------------------------------------------------- */

// EMPLEADOS

Route::get('empleado', 'EmpleadoController@mostrar');
Route::post('empleado', 'EmpleadoController@encontrar');
Route::post('empleadoDatatable', 'EmpleadoController@datatable');
Route::get('nuevoEmpleado', 'EmpleadoController@empleadoNuevo');
Route::post('empleadoFiltrar', 'EmpleadoController@filtrarEmpleado');
Route::post('guardarEmpleado', 'EmpleadoController@empleadoGuardar');
Route::post('eliminarEmpleado', 'EmpleadoController@empleadoEliminar');
Route::post('empleado/recibe', 'EmpleadoController@datatable_recibe');


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
Route::get('producto/minimo', 'ProductoController@minimo');
Route::post('producto/baja', 'ProductoController@baja');
Route::post('producto/POS', 'ProductoController@obtenerProductoPOS');
Route::post('productoImportar', 'ProductoController@importar');
Route::post('producto/ubicacion', 'ProductoController@ubicacion');
Route::post('producto/existe', 'ProductoController@existe');
Route::post('producto/mostrar_new', 'ProductoController@mostrar_new');
Route::post('producto/catalogo', 'ProductoController@catalogo_cliente');
Route::post('producto/movimiento', 'ProductoController@productoMovimiento');
Route::post('detalleProductoVentasDatatable', 'ProductoController@detalleProductoVenta');
Route::post('barcodeFiltrar', 'ProductoController@filtrarBarcode');
Route::post('productoBuscar', 'ProductoController@buscarProducto');
Route::post('producto/inventario', 'ProductoController@inventario');

/* -------------------------------------------------------------------------- */

// SERVICIOS 

Route::post('servicio/POS', 'ServicioController@obtenerServicioPOS');
Route::post('pdf-rptDelivery', 'ServicioController@reporteDelivery');
Route::get('generarDeliveryDatatable', 'ServicioController@generarRptDelivery');

/* -------------------------------------------------------------------------- */

// CAJERO
Route::post('cajeros', 'CajeroController@mostrar');
Route::post('/cajero/datatable', 'CajeroController@datatable');
Route::post('busquedaCajero', 'CajeroController@cajeroBusqueda');
Route::post('ventaCajeroDatatable', 'CajeroController@generarVentaCajero');

Route::post('pdf-rptCajero', 'CajeroController@reporteCajero');

/* -------------------------------------------------------------------------- */

//	PARAMETROS

Route::get('parametro', 'ParametroController@mostrar');

/* -------------------------------------------------------------------------- */

//	LOTES

Route::post('lotesConCantidad', 'StockController@obtenerLotesConCantidad');
Route::get('lote/vencidos', 'StockController@vencidos');
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
Route::post('/obtener/Cotizaciones/Venta', 'CotizacionController@obtenerVentaCotizacion');
Route::post('cotizacion/compra-venta', 'CotizacionController@cotizacionCompraDia');
Route::post('cotizacionBanner', 'CotizacionController@cotizacionDiaBanner');

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
Route::post('compraModificarUbicacion', 'CompraController@ubicacionModificarCompra');
Route::post('reporte_entrada_compra_seccion', 'CompraController@generar_reporte_entrada_seccion');
Route::post('reporte_venta_compra_seccion', 'CompraController@generar_reporte_venta_compra_seccion');



/* -------------------------------------------------------------------------- */

// USUARIOS
Route::post('cambiarSucursal', 'UsuarioTieneSucursalesController@cambiar_Sucursal');
Route::get('obtenerUsuarioSucursales', 'UsuarioTieneSucursalesController@obtenerSucursal');
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
Route::post('/cliente/credito/detalle/nc/datatable', 'ClienteController@notaCreditoDatatable');

/* -------------------------------------------------------------------------- */

// DESCUENTO

Route::post('descuento', 'DescuentoController@mostrar');
Route::post('descuentoLoteProducto', 'LoteTieneDescuentoController@guardarDescuentoLote');

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
Route::post('export/Stock/Image', 'StockController@descargarImageStock');
Route::post('export_venta_periodo', 'StockController@descargarPeriodoStock');
Route::post('export_producto_vencimiento', 'StockController@descagrarVencimientoProducto');
Route::post('export_venta_proveedor', 'ProveedorController@descargar_excel');
Route::post('vencimientoProductoDatatable', 'StockController@vencimientoDatatable');
Route::post('ventaProductoVencidoDatatable', 'StockController@productoVencidoVentaDatatable');
Route::post('export_venta_gondola', 'VentaController@descargarVentaGondola');
Route::post('export_producto_salida', 'SalidaProductoController@descargarSalida');
Route::get('export_inventario_gonodola', 'ExportController@descargarInventario');
Route::post('export_venta_producto_vencido', 'StockController@descagrarVentaProductoVencido');
Route::post('export_productos_en_gondola', 'GondolaController@descagrarProductosEnGondola');
Route::post('export_venta_seccion_gondola', 'VentaController@descargarVentaGondolaSeccion');


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

// CONTROL DE MAQUINAS

	// SUCURSALES
Route::post('sucursalRmGuardar', 'Sucursal_RmController@guardarSucursalRm');
Route::get('sucursalesRmDatatable', 'Sucursal_RmController@sucursalRmDatatable');
Route::post('sucursalRmFiltrar', 'Sucursal_RmController@filtrarSucursalRm');
Route::post('sucursalEliminarRM', 'Sucursal_RmController@eliminarSucursalRm');
Route::get('nuevaSucursalRm', 'Sucursal_RmController@sucursalNuevaRm');

	//SECTORES
Route::post('sectorEliminarRM', 'Sector_RmController@eliminarSectorRm');
Route::post('sectorRmGuardar', 'Sector_RmController@guardarSectorRm');
Route::post('sectorRmFiltrar', 'Sector_RmController@filtrarSectorRm');
Route::get('sectoresRmDatatable', 'Sector_RmController@sectorRmDatatable');
Route::get('nuevoSectorRm', 'Sector_RmController@sectorNuevoRm');

	//REGISTRO DE MAQUINAS 
Route::post('ultimoSectorRm', 'Registro_MaquinaController@ultimoRegistroSectorRm');
Route::post('ultimaSucursalRm', 'Registro_MaquinaController@ultimoRegistroSucursalRm');
Route::post('guardarRegistroM', 'Registro_MaquinaController@registroMaquinaGuardar');
Route::post('maquinaDatatable', 'Registro_MaquinaController@registrosDatatable');
Route::get('nuevoRegistroM', 'Registro_MaquinaController@nuevoRegistroMaquina');
Route::post('filtrarRm', 'Registro_MaquinaController@registroMaquinaFiltrar');
Route::post('registroMaEliminar', 'Registro_MaquinaController@eliminarRegistroMaquina');

/* -------------------------------------------------------------------------- */

// REPORTES GENERAR
/* -------------------------------------------------------------------------- */

// REPORTES VENTAS
Route::post('export-top-venta', 'ExportController@exportTopArticulos');
Route::post('reporte_ventas', 'VentaController@reporteVenta');
Route::post('ventaTopDatatable', 'VentaController@reporteTopArticulos');
Route::post('pdf-generar-rptDiario', 'VentaController@reporteDiario');
Route::post('export_marca_categoria', 'ExportController@descargarMarcaCategoria');
Route::post('ventaPeriodoDatatable', 'VentaController@reportePeriodoVenta');
Route::post('reporte_ventas_Proveedor', 'VentaController@reporteVentaProveedor');
Route::post('reporte_ventas_Gondola', 'VentaController@reporteVentaGondola');

/* -------------------------------------------------------------------------- */

// REPORTES TRANSFERENCIAS
Route::post('reporte_transferencias_ventas', 'VentaController@reporteVenta');

Route::post('export-transferencia-consignacion', 'ExportController@descargarTransferenciaVentas');

/* -------------------------------------------------------------------------- */
//REPORTES SALIDA DE PRODUCTOS

Route::post('reporte_salida_productos', 'SalidaProductoController@reporteSalidaProductos');

/* -------------------------------------------------------------------------- */
//REPORTE DE COMPRAS
Route::post('export_entrada_seccion_proveedor', 'CompraController@reporteEntradaSeccion');


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
Route::get('nota/credito/obtener/credito/cliente', 'NotaCreditoController@obtenerCreditoCliente');
Route::get('nota/credito/mostrar', 'NotaCreditoController@mostrarNotaCredito');
Route::post('nota/credito/cancelar', 'NotaCreditoController@cancelarNota');
Route::get('credito/nota/producto/detalle', 'NotaCreditoController@CreditoNotaDetalle');
Route::post('notaCreditoRptDatatable', 'NotaCreditoController@generarRptNota');


/* -------------------------------------------------------------------------- */

// AGENCIA

Route::get('/agencia/datatable', 'AgenciaController@datatable');

/* -------------------------------------------------------------------------- */


// CONTROL DE CODIGO DE SUPERVISOR

Route::post('/obtener/autorizacion', 'User_SupervisorController@obtener_autorizacion');

/* -------------------------------------------------------------------------- */

//SALIDA DE PRODUCTOS

Route::post('lote/salida', 'StockController@loteProducto');
Route::post('salida/producto', 'SalidaProductoController@salida');
Route::get('salida/mostrar', 'SalidaProductoController@salidaMostrar');
Route::get('salida/producto/detalle', 'SalidaProductoController@salidaProductoDetalle');
Route::post('/salida/reporte', 'SalidaProductoController@reporte');
Route::post('/salida/devolver', 'SalidaProductoController@devolver');

//AVISOS

/* -------------------------------------------------------------------------- */
Route::get('aviso/obtener', 'EspecificacionController@obtenerAviso');
Route::post('aviso/confirmar', 'EspecificacionController@aceptarTerminos');


//MOVIMIENTO DE CAJA

/* -------------------------------------------------------------------------- */
Route::post('/movimiento/caja/guardar', 'Movimiento_CajaController@guardarMovimiento');

/* -------------------------------------------------------------------------- */

//NEW COTIZACION

Route::get('cotizacionDatatable', 'NewCotizacionController@obtenerCotizaciones');
Route::post('cotizacion/guardar', 'NewCotizacionController@guardarCotizacion');

/* -------------------------------------------------------------------------- */

// SECCION 

Route::post('export_venta_seccion', 'VentaController@seccion_excel');
Route::get('seccion', 'SeccionController@mostrar');
Route::post('seccion', 'SeccionController@encontrar');
Route::post('seccionDatatable', 'SeccionController@datatable');
Route::get('nuevaSeccion', 'SeccionController@seccionNuevo');
Route::post('seccionFiltrar', 'SeccionController@filtrarSeccion');
Route::post('guardarSeccion', 'SeccionController@SeccionGuardar');
Route::post('eliminarSeccion', 'SeccionController@SeccionEliminar');
Route::post('export_seccion_proveedor', 'SeccionController@reporteSeccionProveedor');
Route::post('export_venta_seccion_proveedor', 'SeccionController@reporteSeccionProveedorVenta');

/* -------------------------------------------------------------------------- */
// 	PRESTAMOS DE PRODUCTOS

Route::post('prestamoProductoGuardar', 'PrestamoProductoController@prestar');
Route::get('prestamo/mostrar', 'PrestamoProductoController@prestamoMostrar');
Route::get('prestamo/producto/detalle', 'PrestamoProductoController@prestamoProductoDetalle');
Route::post('prestamo/devolver', 'PrestamoProductoController@devolver');


/* -------------------------------------------------------------------------- */


Route::get('{any}', function () {
    return view('home');
})->where('any','.*');

/* -------------------------------------------------------------------------- */
