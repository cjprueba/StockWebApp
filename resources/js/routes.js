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
			path: '/pr2',
			name: 'productoRegistrar',
			component: require('./components/producto/RegistrarProducto.vue').default
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
			path: '/rpt10',
			name: 'rptStock',
			component: require('./components/reportes/stock/busqueda/stock.vue').default
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
			path: '/ct2',
			name: 'cotizacionCrear',
			component: require('./components/cotizacion/CotizacionMostrar.vue').default
		},
		{
			path: '/tr1',
			name: 'transporteCrear',
			component: require('./components/transporte/crearTransporte.vue').default
		}

	],
	mode: 'history'
});

/* ********************************************* */