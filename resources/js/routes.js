window.Vue = require('vue');
import Router from 'vue-router';

Vue.use(Router);

/* ********************************************* */

// AGREGAR LOS COMPONENTES AL ROUTER 

export default new Router({
	routes: [
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
		}
	],
	mode: 'history'
});

/* ********************************************* */