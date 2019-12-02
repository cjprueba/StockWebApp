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

// VUESAX
import Vuesax from 'vuesax'
import { vsDivider, vsTabs, vsTab } from 'vuesax'
import 'vuesax/dist/vuesax.css' //Vuesax styles
Vue.use(vsDivider);
Vue.use(vsTabs);
Vue.use(Vuesax, {
  theme:{
    colors:{
      primary:'#5b3cc4',
      success:'rgb(23, 201, 100)',
      danger:'rgb(242, 19, 93)',
      warning:'rgb(255, 130, 0)',
      dark:'rgb(36, 33, 69)'
    }
  }
});


/* ********************************************* */

window.Swal = require('sweetalert2');
// import Swal from 'sweetalert2'
// Vue.use(Swal)

/* ********************************************* */

// DATATABLE JS

window.dt = require('datatables.net');
import 'datatables.net-bs4';
import jsZip from 'jszip';
import 'datatables.net-buttons';
import 'datatables.net-buttons/js/dataTables.buttons.js';
import 'datatables.net-buttons/js/buttons.flash.js';

import 'datatables.net-buttons/js/buttons.html5.js';
import 'pdfmake';
import pdfFonts from "pdfmake/build/vfs_fonts";
pdfMake.vfs = pdfFonts.pdfMake.vfs;
window.JSZip = jsZip;
import'datatables.net-responsive';
// 
import 'datatables.net-bs4/css/dataTables.bootstrap4.css';
import 'datatables.net-buttons/js/buttons.print.js';


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

import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.min.css';
Vue.component('multiselect', Multiselect);

/* ********************************************* */

/* FONT AWESOME */ 

import { library } from '@fortawesome/fontawesome-svg-core'
import { faPlus, faAmbulance, faCog, faChartArea, faTv, faStickyNote, faBell, faEnvelope, faSearch, faDownload, faCaretUp, faCaretDown, faInfoCircle, faBan, faTruck, faHome, faShoppingBasket, faBarcode, faListAlt, faCheck, faExclamationTriangle, faTags, faSave, faFile, faCopy } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(faPlus, faAmbulance, faCog, faChartArea, faTv, faStickyNote, faBell, faEnvelope, faSearch, faDownload, faCaretUp, faCaretDown, faInfoCircle, faBan, faTruck, faHome, faShoppingBasket, faBarcode, faListAlt, faCheck, faExclamationTriangle, faTags, faSave, faFile, faCopy)

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

// VENDEDOR 

Vue.component('vendedor-general', require('./components/vendedor/busqueda/VendedorGeneral.vue').default);
Vue.component('devolucion-vendedor', require('./components/vendedor/busqueda/DevolucionVendedor.vue').default);

/* ********************************************* */

// ARTICULO

Vue.component('articulo-marca', require('./components/Articulo/busqueda/ArticuloMarca.vue').default);

/* ********************************************* */

// INVENTARIO

Vue.component('Inventario-General', require('./components/Inventario/busqueda/InventarioGeneral.vue').default);

/* ********************************************* */

// PROVEEDOR

Vue.component('producto-proveedor', require('./components/Proveedor/busqueda/ProveedorGeneral.vue').default);

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

// DESCUENTO 

Vue.component('descuento-marca-categoria', require('./components/descuento/busqueda/DescuentoMarcaCategoria.vue').default);

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
