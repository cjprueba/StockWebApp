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

// BANDERAS 

import FlagIcon from 'vue-flag-icon'
Vue.use(FlagIcon);

/* ********************************************* */

// HOTKEYS
/* ********************************************* */
import hotkeys from 'hotkeys-js';
import Mousetrap from 'mousetrap';
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

// QUAGGA

import adapter from 'webrtc-adapter';
window.Quagga = require('quagga');

/* ********************************************* */

// import * as qz from 'qz-tray';
// Vue.use(qz)

window.shajs = require('sha.js');
window.qz = require('qz-tray');
window.RSVP = require('rsvp');
window.sha256 = require('js-sha256');

qz.api.setSha256Type(function (data) {
    return shajs('sha256').update(data).digest('hex')
});

/* ********************************************* */

// VUE SELECT 

import Vue from 'vue'
import vSelect from 'vue-select'

Vue.component('v-select', vSelect)
import 'vue-select/dist/vue-select.css';


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

import { faPlus, faAmbulance, faCog, faChartArea, faTv, faStickyNote, faBell, faEnvelope, faSearch, faDownload, faCaretUp, faCaretDown, faInfoCircle, faBan, faTruck, faHome, faShoppingBasket, faBarcode, faListAlt, faCheck, faExclamationTriangle, faTags, faSave, faFile, faCopy, faCartPlus,faUserCircle, faCalendar, faListOl, faCreditCard, faMoneyCheckAlt, faMoneyBillAlt, faAddressCard, faSyncAlt, faExternalLinkAlt, faInfo, faShoppingBag, faCamera, faUniversity, faCoins, faTruckMoving, faBuilding, faComments, faCheckSquare, faPrint, faTrash, faUser, faUserPlus, faCashRegister, faFileAlt, faSortAmountDown, faThLarge, faSign, faGlobe, faUserTie, faUserCog, faWalking, faCalendarAlt, faCalendarDay, faBoxOpen,faQrcode } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(faPlus, faAmbulance, faCog, faChartArea, faTv, faStickyNote, faBell, faEnvelope, faSearch, faDownload, faCaretUp, faCaretDown, faInfoCircle, faBan, faTruck, faHome, faShoppingBasket, faBarcode, faListAlt, faCheck, faExclamationTriangle, faTags, faSave, faFile, faCopy, faCartPlus,faUserCircle, faCalendar, faListOl, faCreditCard, faMoneyCheckAlt, faMoneyBillAlt, faAddressCard, faSyncAlt, faExternalLinkAlt, faInfo, faShoppingBag, faCamera, faUniversity, faCoins, faTruckMoving, faBuilding, faComments, faCheckSquare, faPrint, faTrash, faUser, faUserPlus, faCashRegister, faFileAlt, faSortAmountDown, faThLarge, faSign, faGlobe, faUserTie, faUserCog, faWalking, faCalendarAlt, faCalendarDay, faBoxOpen,faQrcode )



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

Vue.component('producto-proveedor', require('./components/proveedor/busqueda/ProveedorGeneral.vue').default);
Vue.component('modal-detalle-proveedor-devolucion', require('./components/proveedor/modal/ModalDetalleDevolucion.vue').default);

/* ********************************************* */

// MARCA

Vue.component('marca', require('./components/marca/Inicio.vue').default);
Vue.component('venta-marca-categoria', require('./components/marca/busquedas/VentaMarcaCategoria.vue').default);
Vue.component('select-marca', require('./components/textboxs/MarcaCategoria.vue').default);


/* ********************************************* */

// TRANSFERENCIA

Vue.component('transferencia',  require('./components/transferencia/Inicio.vue').default);
Vue.component('transferencia-marca-categoria', require('./components/transferencia/busquedas/TransferenciaMarcaCategoria.vue').default);
Vue.component('realizarTransferencia', require('./components/transferencia/RealizarTransferencia.vue').default);
Vue.component('modal-detalle-transferencia', require('./components/transferencia/modal/ModalDetalleTransferencia.vue').default);
Vue.component('modal-detalle-transferencia-dev', require('./components/transferencia/modal/ModalDetalleTransferenciaDev.vue').default);
Vue.component('modal-detalle-transferencia-dev-imp', require('./components/transferencia/modal/ModalDetalleTransferenciaDevImp.vue').default);
Vue.component('modal-devolucion-transferencia', require('./components/transferencia/modal/ModalDevolucionTransferencia.vue').default);

/* ********************************************* */

// TABLAS

Vue.component('table-marcas', require('./components/tables/tableMarca.vue').default);
Vue.component('table-categorias', require('./components/tables/tableCategoria.vue').default);

/* ********************************************* */

// CAJAS

Vue.component('cajas', require('./components/cajas/Cajas.vue').default);
Vue.component('caja-lote', require('./components/cajas/LoteCaja.vue').default);
Vue.component('caja-periodo', require('./components/cajas/PeriodoCaja.vue').default);
Vue.component('caja-minimo', require('./components/cajas/MinimoCaja.vue').default);
Vue.component('caja-lote-cero', require('./components/cajas/LoteVacio.vue').default);
Vue.component('caja-aviso', require('./components/cajas/Aviso.vue').default);

/* ********************************************* */

// COTIZACIONES

Vue.component('cotizacionEnviarTransferencia', require('./components/cotizacion/cotizacionEnviarTransferencia.vue').default);

/* ********************************************* */

// TEXTBOX

Vue.component('rol-nombre', require('./components/textboxs/RolTextbox.vue').default);
Vue.component('permiso-nombre', require('./components/textboxs/PermisoTextbox.vue').default);
Vue.component('selected-sucursal', require('./components/textboxs/Sucursal.vue').default);
Vue.component('codigo-producto', require('./components/textboxs/CodigoProducto.vue').default);
Vue.component('usuario-nombre', require('./components/textboxs/UsuarioTextbox.vue').default);
Vue.component('selected-categoria', require('./components/textboxs/CategoriaTextbox.vue').default);
Vue.component('selected-sub-categoria', require('./components/textboxs/SubCategoriaTextbox.vue').default);
Vue.component('selected-sub-categoria-detalle', require('./components/textboxs/SubCategoriaDetalleTextbox.vue').default);
Vue.component('precio-textbox', require('./components/textboxs/PrecioTextbox.vue').default);
Vue.component('select-color', require('./components/textboxs/ColorCategoria.vue').default);
Vue.component('talle-nombre', require('./components/textboxs/Atributos/TalleTextbox.vue').default);
Vue.component('marca-nombre', require('./components/textboxs/Atributos/MarcaTextbox.vue').default);
Vue.component('color-nombre', require('./components/textboxs/Atributos/ColorTextbox.vue').default);
Vue.component('tela-nombre', require('./components/textboxs/Atributos/TelaTextbox.vue').default);
Vue.component('gondola-nombre', require('./components/textboxs/Gondolas/GondolasTextbox.vue').default);
Vue.component('categoria-nombre', require('./components/textboxs/Atributos/CategoriaTextbox.vue').default);
Vue.component('subcategoria-nombre', require('./components/textboxs/Atributos/SubCategoriaTextbox.vue').default);
Vue.component('nombre-textbox', require('./components/textboxs/Atributos/NombreTextbox.vue').default);
Vue.component('forma-pago-textbox', require('./components/textboxs/FormaPagoTextbox.vue').default);
Vue.component('lote-proveedor', require('./components/textboxs/LotesProveedor.vue').default);
Vue.component('lote-general', require('./components/textboxs/LotesGeneral.vue').default);
Vue.component('search-marca', require('./components/textboxs/MarcaSearch.vue').default);
Vue.component('ventas-id', require('./components/textboxs/Ventas/VentasTextbox.vue').default);
Vue.component('ventas-global-textbox', require('./components/textboxs/Ventas/VentasGlobalTextbox.vue').default);
Vue.component('transporte-nombre', require('./components/textboxs/Transporte/TransporteTextbox.vue').default);
Vue.component('container-nombre', require('./components/textboxs/Container/ContainerTextbox.vue').default);
Vue.component('sucursal-filtrar', require('./components/textboxs/SucursalTextbox.vue').default);
Vue.component('remision-filtrar', require('./components/textboxs/NotaDeRemisionTextbox.vue').default);
Vue.component('proveedor-nombre', require('./components/textboxs/Proveedor/ProveedorTextbox.vue').default);
Vue.component('forma-pago-credito-textbox', require('./components/textboxs/FormaPagoCreditoTextbox.vue').default);
Vue.component('nropiso-textbox', require('./components/textboxs/PisoTextbox.vue').default);
Vue.component('sector-textbox', require('./components/textboxs/SectorTextbox.vue').default);

/* ********************************************* */

// SUCURSAL RM

Vue.component('sucursal-rm', require('./components/maquinas/textboxs/SucursalRmTextbox.vue').default);

/* ********************************************* */

// SECTOR RM

Vue.component('sector-rm', require('./components/maquinas/textboxs/SectorRmTextbox.vue').default);

/* ********************************************* */

// REGISTROS DE MAQUINAS

Vue.component('registro-maquinas', require('./components/maquinas/textboxs/RegistroMaquinasTextbox.vue').default);

/* ********************************************* */

// CAMARA

Vue.component('camara-bardcode', require('./components/camara/camara-barcode.vue').default);

/* ********************************************* */

// COLOR 

/* ********************************************* */

// ORDEN

Vue.component('modal-detalle-orden', require('./components/orden/modal/ModalDetalleOrden.vue').default);
Vue.component('modal-detalle-orden-pendiente', require('./components/orden/modal/ModalDetalleOrdenPendiente.vue').default);

/* ********************************************* */
// VENTAS

Vue.component('modal-detalle-venta', require('./components/venta/modal/ModalDetalleVenta.vue').default);

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

// COLOR


/* ********************************************* */

// CATEGORIA



/* ********************************************* */

// REPORTES

Vue.component('venta-seccion-rpt', require('./components/reportes/venta/Seccion/ReporteVentaPorSeccion.vue').default);
Vue.component('top-venta-rpt', require('./components/reportes/venta/TopVenta/ReporteTopVenta.vue').default);
Vue.component('periodo-venta', require('./components/reportes/venta/PeriodoDeVenta/PeriodoDeVentaDelProducto.vue').default);
Vue.component('venta-marca-categoria-rpt', require('./components/reportes/venta/Marca_Categoria/Venta_Marca_Categoria_Rpt.vue').default);
Vue.component('transferencia-consignacion-rpt', require('./components/reportes/transferencia/Consignacion/TransferenciaConsignacionRpt.vue').default);
Vue.component('venta-diaria-rpt', require('./components/reportes/venta/Diario/ReporteVentaDiario.vue').default);
Vue.component('transferencia-consignacion-rpt', require('./components/reportes/transferencia/Consignacion/TransferenciaConsignacionRpt.vue').default);
Vue.component('venta-vendedor-rpt', require('./components/reportes/venta/Vendedor/VentaVendedorRpt.vue').default);
Vue.component('venta-proveedor-rpt', require('./components/reportes/venta/Proveedor/ReporteVentaProveedor.vue').default);
Vue.component('productos-vencidos-rpt', require('./components/reportes/producto/Vencido/VencimientoDeProductoRpt.vue').default);
Vue.component('productos-stock-rpt', require('./components/reportes/stock/ReporteStock.vue').default);
Vue.component('venta-gondola-rpt', require('./components/reportes/venta/Gondola/ReporteVentaPorGondola.vue').default);
Vue.component('venta-vales-rpt' , require('./components/reportes/vales/Vales.vue').default);
Vue.component('venta-transferencia-rpt' , require('./components/reportes/VentaTransferencia.vue').default);
Vue.component('venta-delivery-rpt' , require('./components/reportes/ServicioDelivery.vue').default);
Vue.component('venta-tarjeta-rpt' , require('./components/reportes/VentaTarjeta.vue').default);
Vue.component('productos-salida-rpt', require('./components/reportes/producto/Salida/SalidaDeProductoRpt.vue').default);


/* ********************************************* */

// GONDOLA

Vue.component('select-gondola', require('./components/textboxs/GondolaTextbox.vue').default);

/* ********************************************* */

Vue.component('select-sucursal', require('./components/textboxs/UsuarioSucursalTextbox.vue').default);

/* ********************************************* */

// DESCUENTO 

Vue.component('descuento-marca-categoria', require('./components/descuento/busqueda/DescuentoMarcaCategoria.vue').default);

/* ********************************************* */

// PROVEEDOR

Vue.component('select-proveedor', require('./components/textboxs/ProveedorTextbox.vue').default);

/* ********************************************* */

// PRODUCTO

Vue.component('producto-detalle', require('./components/producto/modal/DetalleProducto.vue').default);
Vue.component('producto-detalle-venta', require('./components/producto/modal/VentaProducto.vue').default);

/* ********************************************* */

// MONEDA

Vue.component('select-moneda', require('./components/textboxs/MonedaTextbox.vue').default);

/* ********************************************* */

// TARJETA

Vue.component('tarjeta-modal', require('./components/tarjeta/modal/ModalTarjeta.vue').default);

/* ********************************************* */

// BANCO

Vue.component('banco-modal', require('./components/banco/modal/ModalBanco.vue').default);

/* ********************************************* */

// BANCO

Vue.component('giro-modal', require('./components/giro/modal/ModalEntidades.vue').default);

/* ********************************************* */

// CHEQUE

Vue.component('cheque-modal', require('./components/cheque/modal/ChequeModal.vue').default);

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

// COMPRAS

Vue.component('compras-marca', require('./components/compra/busqueda/CompraGeneral.vue').default);
Vue.component('modal-detalle-compra', require('./components/compra/modal/ModalDetalleCompra.vue').default);

/* ********************************************* */

// CLIENTES

Vue.component('busqueda-cliente-modal', require('./components/cliente/BusquedaCliente.vue').default);
Vue.component('cliente-filtrar', require('./components/textboxs/ClienteTextbox.vue').default);
Vue.component('crear-cliente', require('./components/cliente/CrearCliente.vue').default);

/* ********************************************* */

// COMBOS DE PRODUCTOS

Vue.component('combo-filtrar', require('./components/textboxs/Combos/ComboProductoTextbox.vue').default);

/* ********************************************* */

// EMPRESAS

Vue.component('empresa-mostrar', require('./components/textboxs/EmpresaTextbox.vue').default);


/* ********************************************* */

// VENDEDORES

Vue.component('busqueda-vendedor-modal', require('./components/vendedor/BusquedaVendedor.vue').default);

/* ********************************************* */

Vue.component('miscomponentes', require('./components/MisComponentes.vue').default);
Vue.component('categoria', require('./components/Categoria.vue').default);
Vue.component('formv', require('./components/Form.vue').default);
Vue.component('sidebar', require('./components/Sidebar.vue').default);
Vue.component('dashboard', require('./components/Dashboard.vue').default);
Vue.component('home', require('./components/Home.vue').default);
Vue.component('producto', require('./components/producto/MostrarProductoUno.vue').default);
Vue.component('productoqr', require('./components/producto/MostrarProductoQR.vue').default);
Vue.component('cajacompraqr', require('./components/compra/MostrarCajaCompraQr.vue').default);


/* ********************************************* */

// CATALOGO

Vue.component('catalogo', require('./components/producto/Catalogo.vue').default);

/* ********************************************* */

// NOTA CREDITO

Vue.component('nota-credito-cliente-datatable', require('./components/movimientos/CreditoNotaDatatableTextbox.vue').default);
Vue.component('modal-detalle-salida-productos', require('./components/movimientos/modal/ModalDetalleSalidaProductos.vue').default);
Vue.component('modal-detalle-nota-credito', require('./components/movimientos/modal/ModalDetalleCreditoNota.vue').default);


/* ********************************************* */

// PRESTAMO DE PRODUCTOS.

Vue.component('modal-detalle-prestamo-productos', require('./components/movimientos/Prestamo/modal/ModalDetallePrestamo.vue').default);

/* ********************************************* */

// AGENCIA

Vue.component('agencia-datatable-textbox', require('./components/agencia/AgenciaDatatable.vue').default);

/* ********************************************* */

// AUTORIZACION

Vue.component('autorizacion', require('./components/autorizacion/Autorizacion.vue').default);

//EMPLEADO
Vue.component('empleado-textbox', require('./components/textboxs/EmpleadoTextbox.vue').default);

//SECCION
Vue.component('seccion-textbox', require('./components/textboxs/SeccionTextbox.vue').default);

/* ********************************************* */

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
