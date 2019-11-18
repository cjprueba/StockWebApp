/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

/* ------------------------------------------------------------------------------- */
/*window.$ = require('jquery')
window.JQuery = require('jquery')*/


/* ********************************************* */

/* VUE */ 

window.Vue = require('vue');
require('./bootstrap');
require('bootstrap-datepicker');

/* ********************************************* */

// PERMISOS 

import Permissions from './mixins/Permissions';
Vue.mixin(Permissions);

/* ********************************************* */

// Funciones utiles 

window.numeral = require('numeral');
window.Common = require('./common.js');

import {Howl, Howler} from 'howler';

/* ********************************************* */

// BOOSTRAP VUE

import BootstrapVue from 'bootstrap-vue'
Vue.use(BootstrapVue)

/* ********************************************* */

window.Swal = require('sweetalert2');
// import Swal from 'sweetalert2'
// Vue.use(Swal)

/* ********************************************* */

// DATATABLE JS

window.dt = require('datatables.net');
require( 'datatables.net-bs4' );
require('datatables.net-responsive');
require( 'datatables.net-buttons' );

/* ********************************************* */

// VUE SINGLE

import VueSingleSelect from "vue-single-select";

/* ********************************************* */

import { VBPopover } from 'bootstrap-vue';
Vue.directive('b-popover', VBPopover);
// // not sure if you need this at all

/* ********************************************* */

/* APPEX CHARTS */

import VueApexCharts from 'vue-apexcharts'

/* ********************************************* */

/* FONT AWESOME */ 

import { library } from '@fortawesome/fontawesome-svg-core'
import { faPlus, faAmbulance, faCog, faChartArea, faTv, faStickyNote, faBell, faEnvelope, faSearch, faDownload, faCaretUp, faCaretDown, faInfoCircle, faBan, faTruck, faHome, faShoppingBasket, faBarcode, faListAlt, faCheck, faExclamationTriangle, faTags, faSave } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(faPlus, faAmbulance, faCog, faChartArea, faTv, faStickyNote, faBell, faEnvelope, faSearch, faDownload, faCaretUp, faCaretDown, faInfoCircle, faBan, faTruck, faHome, faShoppingBasket, faBarcode, faListAlt, faCheck, faExclamationTriangle, faTags, faSave)

/* ********************************************* */

/* ------------------------------------------------------------------------------- */

/* COMPONENTS */ 

/* ********************************************* */

// VUE SINGLE

Vue.component('vue-single-select', VueSingleSelect);

/* ********************************************* */

// FONT AWESOME

Vue.component('font-awesome-icon', FontAwesomeIcon)

/* ********************************************* */

// APPEX CHARTS

Vue.component('apexchart', VueApexCharts)

Vue.component('bar', require('./components/charts/Bar.vue').default);
Vue.component('donut', require('./components/charts/Donut.vue').default);

/* ********************************************* */

// CHART JS

Vue.component('barChart', require('./components/charts/Barchart.vue').default);

/* ********************************************* */

// MARCA

Vue.component('marca', require('./components/marca/Inicio.vue').default);
Vue.component('venta-marca-categoria', require('./components/marca/busquedas/VentaMarcaCategoria.vue').default);

/* ********************************************* */

// TRANSFERENCIA

Vue.component('transferencia',  require('./components/transferencia/Inicio.vue').default);
Vue.component('transferencia-marca-categoria', require('./components/transferencia/busquedas/TransferenciaMarcaCategoria.vue').default);
Vue.component('realizarTransferencia', require('./components/transferencia/RealizarTransferencia.vue').default);
Vue.component('modal-detalle-transferencia', require('./components/transferencia/modal/ModalDetalleTransferencia.vue').default);

/* ********************************************* */

// TABLAS

Vue.component('table-marcas', require('./components/tables/tableMarca.vue').default);
Vue.component('table-categorias', require('./components/tables/tableCategoria.vue').default);

/* ********************************************* */

// CAJAS

Vue.component('cajas', require('./components/cajas/Cajas.vue').default);

/* ********************************************* */

// COTIZACIONES

Vue.component('cotizacionEnviarTransferencia', require('./components/cotizacion/cotizacionEnviarTransferencia.vue').default);

/* ********************************************* */

// TEXTBOX

Vue.component('selected-sucursal', require('./components/textboxs/Sucursal.vue').default);
Vue.component('codigo-producto', require('./components/textboxs/CodigoProducto.vue').default);
Vue.component('selected-categoria', require('./components/textboxs/CategoriaTextbox.vue').default);
Vue.component('selected-sub-categoria', require('./components/textboxs/SubCategoriaTextbox.vue').default);
Vue.component('precio-textbox', require('./components/textboxs/PrecioTextbox.vue').default);

/* ********************************************* */

// COLOR 

Vue.component('select-color', require('./components/textboxs/ColorCategoria.vue').default);

/* ********************************************* */

// TELA

Vue.component('select-tela', require('./components/textboxs/TelaCategoria.vue').default);

/* ********************************************* */

// TALLE

Vue.component('select-talle', require('./components/textboxs/TalleCategoria.vue').default);

/* ********************************************* */

// GENERO

Vue.component('select-genero', require('./components/textboxs/GeneroCategoria.vue').default);

/* ********************************************* */

// MARCA

Vue.component('select-marca', require('./components/textboxs/MarcaCategoria.vue').default);

/* ********************************************* */

// GONDOLA

Vue.component('select-gondola', require('./components/textboxs/GondolaTextbox.vue').default);

/* ********************************************* */

// PROVEEDOR

Vue.component('select-proveedor', require('./components/textboxs/ProveedorTextbox.vue').default);

/* ********************************************* */

// MONEDA

Vue.component('select-moneda', require('./components/textboxs/MonedaTextbox.vue').default);

/* ********************************************* */

// MENSAJE

Vue.component('mensaje', require('./components/mensajes/Error.vue').default);

/* ********************************************* */

// UTILES

Vue.component('cuatrocientos-cuatro', require('./components/utiles/404.vue').default);

/* ********************************************* */

// FOOTER

Vue.component('pie-pagina', require('./components/utiles/Footer.vue').default);

/* ********************************************* */

Vue.component('miscomponentes', require('./components/MisComponentes.vue').default);
Vue.component('categoria', require('./components/Categoria.vue').default);
Vue.component('formv', require('./components/Form.vue').default);
Vue.component('sidebar', require('./components/Sidebar.vue').default);
Vue.component('dashboard', require('./components/Dashboard.vue').default);
Vue.component('home', require('./components/Home.vue').default);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

/* ********************************************* */

// RUTAS 

import router from './routes';

/* ********************************************* */

const app = new Vue({
    el: '#app',
    router
});
