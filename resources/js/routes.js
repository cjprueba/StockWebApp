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
			path: '/rl1',
			name: 'rolCrear',
			component: require('./components/rol/CrearRol.vue').default
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
		}
	],
	mode: 'history'
});

/* ********************************************* */