window.Vue = require('vue');
import Router from 'vue-router';

Vue.use(Router);

/* ********************************************* */

// AGREGAR LOS COMPONENTES AL ROUTER 

export default new Router({
	routes: [
		{
			path: '/home',
			name: 'Inicio',
			component: require('./components/Dashboard.vue').default
		},
		{
			path: '/tr2/:id',
			name: 'transferencia',
			component: require('./components/transferencia/RealizarTransferencia.vue').default
		},
		{
			path: '/tr1',
			name: 'transferenciaMostrar',
			component: require('./components/transferencia/MostrarTransferencias.vue').default
		},
		{
			path: '/tr3',
			name: 'transferenciaImportar',
			component: require('./components/transferencia/ImportarTransferencia.vue').default
		},

		{
			path: '/in1',
			name: 'inventarioMostrar',
			component: require('./components/inventario/MostrarInventario.vue').default
		},
		{
			path: '/in2/:id',
			name: 'inventarioRealizar',
			component: require('./components/inventario/RealizarInventario.vue').default
		},
		{
			path: '/pr1',
			name: 'productoMostrar',
			component: require('./components/producto/MostrarProducto.vue').default
		},
		{
			path: '/pr4',
			name: 'productoMostrarNew',
			component: require('./components/producto/MostrarProductoNew.vue').default
		},
		{
			path: '/pr5',
			name: 'productoMostrarUno',
			component: require('./components/producto/MostrarProductoUno.vue').default
		},
		{
			path: '/pr2',
			name: 'productoRegistrar',
			component: require('./components/producto/RegistrarProducto.vue').default
		},
	    {
			path: '/pr3',
			name: 'productoImprimir',
			component: require('./components/producto/ImprimirProducto.vue').default
		},
		{
			path: '/us1',
			name: 'userCrear',
			component: require('./components/usuario/Registro.vue').default
		},
		{
			path: '/rl1',
			name: 'rolCrear',
			component: require('./components/usuario/rol/CrearRol.vue').default
		},
		{
			path: '/rl2',
			name: 'rolAsignar',
			component: require('./components/usuario/rol/AsignarRol.vue').default
		},
				{
			path: '/per2',
			name: 'permisoAsignar',
			component: require('./components/usuario/permiso/AsignarPermiso.vue').default
		},
		{
			path: '/per1',
			name: 'permisosCrear',
			component: require('./components/usuario/permiso/CrearPermiso.vue').default
		},
		{
			path: '/rpt1',
			name: 'rptVenta',
			component: require('./components/marca/Inicio.vue').default
		},
		{
			path: '/rpt2',
			name: 'rptTransferencia',
			component: require('./components/transferencia/Inicio.vue').default
		},
		{
			path: '/rpt3',
			name: 'rptDescuento',
			component: require('./components/descuento/Inicio.vue').default
		},
		{
			path: '/rpt4',
			name: 'rptVendedor',
			component: require('./components/vendedor/inicio.vue').default
		},
		// {
		// 	path: '/rpt5',
		// 	name: 'rptCliente',
		// 	component: require('./components/cliente/inicio.vue').default
		// },
		{
			path: '/rpt6',
			name: 'rptArticulo',
			component: require('./components/Articulo/inicio.vue').default
		},
		{
			path: '/rpt7',
			name: 'rptProveedor',
			component: require('./components/Proveedor/inicio.vue').default
		},
		{
			path: '/rpt8',
			name: 'rptInventario',
			component: require('./components/Inventario/inicio.vue').default
		},
		{
			path: '/rpt9',
			name: 'rptCompra',
			component: require('./components/compra/inicio.vue').default
		},
		{
			path: '/cr1',
			name: 'compraMostrar',
			component: require('./components/compra/MostrarCompra.vue').default
		},
		{
			path: '/crc',
			name: 'compraContainer',
			component: require('./components/container/crearContainer.vue').default
		},
		{
			path: '/cr2/:id',
			name: 'compra',
			component: require('./components/compra/RealizarCompra.vue').default
		},
		{
			path: '/mar1',
			name: 'marcaCrear',
			component: require('./components/Atributos/marca/CrearMarca.vue').default
		},
		{
			path: '/col1',
			name: 'colorCrear',
			component: require('./components/Atributos/color/CrearColor.vue').default
		},
		{
			path: '/tall1',
			name: 'talleCrear',
			component: require('./components/Atributos/talle/CrearTalle.vue').default
		},
		{
			path: '/tel1',
			name: 'telaCrear',
			component: require('./components/Atributos/tela/CrearTela.vue').default
		},
		{
			path: '/lin1',
			name: 'categoriaCrear',
			component: require('./components/Atributos/categoria/CrearCategoria.vue').default
		},
		{
			path: '/sublin1',
			name: 'subcategoriaCrear',
			component: require('./components/Atributos/subCategoria/CrearSubCategoria.vue').default
		},
		{
			path: '/gond1',
			name: 'gondolaCrear',
			component: require('./components/gondolas/CrearGondolas.vue').default
		},
		{
			path: '/pro1',
			name: 'proveedorMostrar',
			component: require('./components/proveedor/MostrarProveedor.vue').default
		},
		{
			path: '/pro2',
			name: 'proveedorPago',
			component: require('./components/proveedor/PagoProveedor.vue').default
		},
		{
			path: '/pro3',
			name: 'proveedorDevolucion',
			component: require('./components/proveedor/DevolucionProveedor.vue').default
		},
		{
			path: '/pro4',
			name: 'proveedorDevolucionMostrar',
			component: require('./components/proveedor/DevolucionProveedorMostrar.vue').default
		},
		{
			path: '/pro5',
			name: 'proveedorCrear',
			component: require('./components/proveedor/CrearProveedor.vue').default
		},
		{
			path: '/rpt10',
			name: 'rptStock',
			component: require('./components/reportes/stock/busqueda/stock.vue').default
		},
		{
			path: '/rpt11',
			name: 'rptStockCero',
			component: require('./components/reportes/stock/busqueda/cerado.vue').default
		},
		{
			path: '/vt0',
			name: 'ventaCaja',
			component: require('./components/venta/MostrarVentaCaja.vue').default
		},
		{
			path: '/vt1',
			name: 'ventaMostrar',
			component: require('./components/venta/MostrarVenta.vue').default
		},
		{
			path: '/vt2',
			name: 'ventaRealizar',
			component: require('./components/venta/RealizarVenta.vue').default
		},
		{
			path: '/vt4',
			name: 'ventaCuenta',
			component: require('./components/venta/CobroCuenta.vue').default
		},
		{
			path: '/vt3',
			name: 'ventaAnular',
			component: require('./components/venta/AnularVenta.vue').default
		},
		{
			path: '/nom1',
			name: 'nombreCrear',
			component: require('./components/Atributos/nombre/CrearNombre.vue').default
		},
		{
			path: '/cli1',
			name: 'crearCliente',
			component: require('./components/cliente/CrearCliente.vue').default
		},
		{
			path: '/cli2',
			name: 'creditoCliente',
			component: require('./components/cliente/CreditoCliente.vue').default
		},
		{
			path: '/ct2',
			component: require('./components/cotizacion/CotizacionMostrar.vue').default
		},
		{
			path: '/ct3',
			name: 'cotizacionCrearNuevo',
			component: require('./components/cotizacion/CotizacionCrearNuevo.vue').default
		},
		{
			path: '/ct4',
			name: 'cotizacionMostrarNuevo',
			component: require('./components/cotizacion/MostrarCotizacionNew.vue').default
		},
		{
			path: '/tr1',
			name: 'transporteCrear',
			component: require('./components/transporte/crearTransporte.vue').default
		},
		{
			path: '/suc1',
			name: 'crearSucursal',
			component: require('./components/sucursales/CrearSucursal.vue').default
		},
		{
			path: '/remision',
			component: require('./components/movimientos/RemisionNota.vue').default
		},
		{
			path: '/cup1',
			name: 'cuponMostrar',
			component: require('./components/cupones/MostrarCupones.vue').default
		},
		{
			path: '/cup2/:id',
			name: 'cuponCrear',
			component: require('./components/cupones/CrearCupones.vue').default
		},
		{
			path: '/rpn1',
			name: 'rptVentaWeb',
			component: require('./components/reportes/venta/Inicio.vue').default
		},
		{
			path: '/orden1',
			name: 'ordenesMostrar',
			component: require('./components/orden/MostrarOrden.vue').default
		},
		{
			path: '/orden2',
			name: 'ordenesPendientes',
			component: require('./components/orden/MostrarPendientes.vue').default
		},
		{
			path: '/orden3',
			name: 'ordenesProcesando',
			component: require('./components/orden/MostrarOrdenProcesando.vue').default
		},
		
	    {
			path: '/pd1',
			name: 'pdMostrar',
			component: require('./components/pedido/MostrarPedido.vue').default
		},
		{
			path: '/pd2',
			name: 'pdCheckout',
			component: require('./components/pedido/CheckoutPedido.vue').default
		},
		{

			path: '/tr4',
			name: 'transferenciaMostrarDevolucion',
			component: require('./components/transferencia/MostrarDevolucionTransferencias.vue').default
		},
		{
			path: '/tr5',
			name: 'transferenciaImportarDevolucion',
			component: require('./components/transferencia/ImportarTransferenciaDevolucion.vue').default
		},
		{
			path: '/tr6',
			name: 'transferenciaCobrar',
			component: require('./components/transferencia/CobrarTransferencia.vue').default
		},
    	
		{
			path: '/mov1',
			name: 'notaCredito',
			component: require('./components/movimientos/CreditoNota.vue').default
      	},
		{
			path: '/mov2',
			name: 'notaCreditoMostrar',
			component: require('./components/movimientos/MostrarCreditoNota.vue').default
		},
		{
			path: '/rpn2',
			name: 'rptTransferenciaWeb',
			component: require('./components/reportes/transferencia/Inicio.vue').default
		},
		{
			path: '/mov3',
			name: 'salidaProducto',
			component: require('./components/movimientos/SalidaProductos.vue').default
		},
		{
			path: '/mov4',
			name: 'salidaMostrar',
			component: require('./components/movimientos/SalidaProductosMostrar.vue').default
		},
		{
			path: '/rptvp',
			name: 'rptVencimiento',
			component: require('./components/reportes/producto/Vencido/VencimientoDeProductoRpt.vue').default
		},
		{
			path: '/maq1',
			component: require('./components/maquinas/CrearSucursalSector.vue').default
		},
		{
			path: '/maq2',
			component: require('./components/maquinas/RegistroMaquina.vue').default
		},
		{
			path: '/rptsec',
			name: 'rptSeccion',
			component: require('./components/reportes/venta/Seccion/Inicio.vue').default
		},
		{
			path: '/rptstw',
			name: 'rptStockWeb',
			component: require('./components/reportes/stock/ReporteStock.vue').default
		},
		{
			path: '/mov5',
			name: 'prestarProducto',
			component: require('./components/movimientos/Prestamo/PrestarProductos.vue').default
		},
		{
			path: '/mov6',
			name: 'prestamoMostrar',
			component: require('./components/movimientos/Prestamo/MostrarPrestamos.vue').default
		},
		{
			path: '/pro6',
			component: require('./components/producto/modal/CambiarPrecio.vue').default
		},
		{
			path: '/comb1',
			component: require('./components/producto/RegistrarCombo.vue').default
		},
		{
			path: '/cre',
			name: 'crearEmpleado',
			component: require('./components/empleado/CrearEmpleados.vue').default
		},
		{
			path: '/crs',
			name: 'crearSeccion',
			component: require('./components/seccion/CrearSecciones.vue').default
		},
		{
			path: '/crpiso',
			name: 'crearPiso',
			component: require('./components/gondolas/CrearPiso.vue').default
		},
		{
			path: '/CambioDeSucursal',
			name: 'cambiarSucursal',
			component: require('./components/usuario/CambiarSucursal.vue').default
		},
		{

			path: '/rptProducto',
			name: 'rptProductoWeb',
			component: require('./components/reportes/producto/Inicio.vue').default
		},
    	{
			path: '/AsignacionDeCaja',
			name: 'asignarCaja',
			component: require('./components/cajas/AsignarCaja.vue').default
		},
    	{
			path: '/descLote',
			name: 'desccuentoLote',
			component: require('./components/descuento/busqueda/DescuentoLote.vue').default
		},
    	{
			path: '/rptNotaCre',
			name: 'notaDeCraeditoReporte',
			component: require('./components/reportes/creditoNota/NotaCreditoReporte.vue').default
		},

	],
	mode: 'history'
});

/* ********************************************* */