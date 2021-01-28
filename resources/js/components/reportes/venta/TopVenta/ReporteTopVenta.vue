<template>
	<!-- REPORTE TOP VENTAS  -->
	<div v-if="$can('producto.mostrar')">
		<!-- INICIO DE TARJETA -->
		<div class="card mt-3 shadow border-bottom-primary" >
		  <div class="card-header">TOP DE VENTAS</div>
			<!-- CUERPO DE LA TARJETA -->
			<div class="card-body">
			  <div class="row">

				<div class="col-3">

					<!-- -------------------------------------------SELECCIONAR SUCURSAL---------------------------------------- -->
					
				  	<label for="validationTooltip01">Sucursal</label>
					<select class="custom-select custom-select-sm" v-on:change="habilitar_insert" v-bind:class="{ 'is-invalid': validarSucursal }" v-model="selectedSucursal">
						<option value="null" selected>Seleccionar</option>
						<option v-for="sucursal in sucursales" :value="sucursal.CODIGO">{{ sucursal.DESCRIPCION }}</option>
					</select>
					<div class="invalid-feedback">
						{{messageInvalidSucursal}}
					</div>

					<!-- ----------------------------------------SELECCIONAR FECHA---------------------------------------- -->
					
					<label class="mt-3">Seleccione Top</label>
			    	<select v-model="selectedTop" class="custom-select custom-select-sm">
			  	  		<option value=10>TOP 10</option>
			    		<option value=50>TOP 50</option>
			    		<option value=100>TOP 100</option>
			  		</select>

					<!-- ----------------------------------------SELECCIONAR FECHA---------------------------------------- -->

					<label class="mt-3">Seleccione Intervalo de Tiempo</label>
					<div id="sandbox-container" class="input-daterange input-group">
						<input id='selectedInicialFecha' class="input-sm form-control form-control-sm" v-model="selectedInicialFecha" v-bind:class="{ 'is-invalid': validarInicialFecha }"/>
						<div class="input-group-append form-control-sm">
							<span class="input-group-text">a</span>
						</div>
						<input name='end' id='selectedFinalFecha' class="input-sm form-control form-control-sm" v-model="selectedFinalFecha" v-bind:class="{ 'is-invalid': validarFinalFecha }"/>
						<div class="invalid-feedback">
						    {{messageInvalidFecha}}
						</div>
					</div>
				</div>

				<div class="col-md-3">
					<label>Filtrar Por</label>
			    	<select v-model="selectedFiltro" class="custom-select custom-select-sm">
			  	  		<option value="SECCION">Sección</option>
			    		<option value="PROVEEDOR">Proveedor</option>
			  		</select>

					<label for="validationTooltip01" class="mt-3">Agrupar por:</label> 
					<div class="form-check form-check">
	  		            <input v-model="radioAgrupar" v-on:change="" class="form-check-input" type="radio" name="radioOptions" id="radio0" value=true v-bind:class="{ 'is-invalid': validarRadio }" v-on:change="habilitar_insert">
	  		            <label class="form-check-label" for="radio0">Cantidad</label>
	  		        </div>
	  		        <div class="form-check form-check">
	  		            <input  v-model="radioAgrupar"  v-on:change="" class="form-check-input" type="radio" name="radioOptions" id="radio1" value=false v-bind:class="{ 'is-invalid': validarRadio }" v-on:change="habilitar_insert">
	  		            <label class="form-check-label" for="radio1">Monto</label>
	  		        </div>

					<div class="custom-control custom-switch mr-sm-2 mt-3" >
						<input type="checkbox" class="custom-control-input" id="customControlAutosizing" v-model="switch_stock">
						<label class="custom-control-label" for="customControlAutosizing" data-toggle="tooltip" data-placement="top">Stock</label>
					</div>
				</div>

                <!-- -----------------------------------SELECT SECCION------------------------------------- -->

                <div class="col-md-3" v-if="selectedFiltro === 'SECCION'">
					<label for="validationTooltip01">Seleccione Sección</label>
					<select v-on:change="habilitar_insert" class="custom-select custom-select-sm" v-model="selectedSeccion" v-bind:class="{ 'is-invalid': validarSeccion }">
						<option value="null" selected>Seleccionar</option>
						<option v-for="seccion in secciones" :value="seccion.ID_SECCION">{{ seccion.DESCRIPCION }}</option>
					</select>
					<div class="invalid-feedback">
						{{messageInvalidSeccion}}
					</div>
				</div>

				<!-- ------------------------------------------- PROVEEDOR ----------------------------------------------- -->
				
				<div class="col-md-5" v-if="selectedFiltro === 'PROVEEDOR'">
					<label  for="validationTooltip01">Seleccione Proveedor</label> 
					<div class="container_checkbox mr-2"  v-bind:class="{ 'is-invalid': validarProveedor }">
	                    <div class="mt-2 mb-2 pl-2" v-for="proveedor in proveedores">
	                      <div class="custom-control custom-checkbox">
	                        <input type="checkbox" class="custom-control-input" :value="proveedor.CODIGO" :id="proveedor.CODIGO" v-model="selectedProveedor">
	                        <label class="custom-control-label" :for="proveedor.CODIGO">{{proveedor.NOMBRE}} </label>
	                      </div>
	                    </div>
	                </div>
					<div class="custom-control custom-switch mt-3">
						<input type="checkbox" class="custom-control-input" id="customSwitch2" v-model="onProveedor" v-on:change="habilitar_insert">
						<label class="custom-control-label" for="customSwitch2">Seleccionar todos</label>
					</div>
                </div> 
			  </div>
				<!-- -------------------------------------------MOSTRAR BOTONES----------------------------------------------- -->

				<div class="col-12 mt-3">
					<button class="btn btn-dark btn-sm" type="submit" v-on:click="descargar()"><font-awesome-icon icon="download"/> Descargar</button>
					<button class="btn btn-primary btn-sm" type="submit" v-on:click="llamarDatos">Generar</button> 
				</div>
			  
			<!-- FINAL DEL CUERPO -->
			</div>

		<!-- FINAL DE TARJETA -->
		</div>
		<!-- ------------------------------------------------------------------------ -->
		
		<!-- <div v-else>
			<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div> -->

		<!-- ------------------------------------------------------------------------ -->

		<!-- -------------------------------------------MOSTRAR DOWNLOADING------------------------------------------ -->

		<div class="col-md-12 mt-3">
			<div v-if="descarga" class="ml-5 d-flex justify-content-center">
					Descargando...
				<div class="spinner-grow text-primary" role="status" aria-hidden="true"></div>
			</div>
		</div>

		<!-- -------------------------------------------MOSTRAR CARGANDO--------------------------------------------- -->

		<div class="col-md-12">
			<div v-if="cargado" class="ml-5 d-flex justify-content-center">
				Cargando...
		        <div class="spinner-grow" role="status" aria-hidden="true"></div>
		    </div>
	    </div>

	    <!-- -------------------------------------------TABLAS------------------------------------------- -->

	    <div class="col-xl-6 col-lg-6 mt-3">
	     	<table class="table" v-if="responseVentaTop.length > 0">
				<thead>
				    <tr>
				      <th scope="col">#</th>
				      <th scope="col">Código</th>
				      <th scope="col">Descripción</th>
				      <th scope="col">Cantidad</th>
				      <th scope="col">Precio</th>
				      <th scope="col">Total</th>
				      <th scope="col">Utilidad</th>
				    </tr>
				</thead>
				<tbody>
				    <tr v-for="(venta, index) in responseVentaTop" data-toggle="modal" data-target="#exampleModalCenter" class="cuerpoTabla">
				      <th scope="row">{{index+1}}</th>
				      <td>{{venta.CODIGO}}</td>
				      <td>{{venta.DESCRIPCION}}</td>
				      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(venta.CANTIDAD)}}</td>
				      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(venta.PRECIO)}}</td>
				      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(venta.TOTAL)}}</td>
				      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(venta.UTILIDAD)}}</td>
				    </tr>
				</tbody>
				<tfoot>
					<tr>
					  <th></th>
					  <th></th>
					  <th>TOTALES</th>
					  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseVentaTop.reduce((acc, item) => acc + item.CANTIDAD, 0))}}</th>
					  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseVentaTop.reduce((acc, item) => acc + item.PRECIO, 0))}}</th>
					   <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseVentaTop.reduce((acc, item) => acc + item.TOTAL, 0))}}</th>
					  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseVentaTop.reduce((acc, item) => acc + item.UTILIDAD, 0))}}</th>
					</tr>
				</tfoot>
			</table>
	    </div>

	    <!-- -------------------------------------------FIN TABLA------------------------------------------- -->
	</div>

	<!-- REPORTE TOP VENTAS  -->
</template>

<script >
	export default {
      props: ['candec', 'descripcion'],
        data(){
            return {
              	sucursales: [],
              	proveedores: [],
              	secciones: [],
              	selectedProveedor: [],
              	selectedSeccion: "null",
              	selectedSucursal: 'null',
              	selectedTop: 10,
              	datos: {},
              	messageInvalidSucursal: '',
              	messageInvalidFecha: '',
              	messageInvalidSeccion: '',
              	selectedInicialFecha: '',
              	selectedFinalFecha: '',
              	validarSucursal: false,
              	validarInicialFecha: false,
              	validarFinalFecha: false,
              	validarRadio: false,
              	validarSeccion: false,
              	validarProveedor: false,
              	cargado: false,
              	descarga: false,
              	onProveedor: false,
              	radioAgrupar: '',
              	controlar: true,
              	responseVentaTop: {
	          		CODIGO: '',
              		DESCRIPCION: '',
	          		PRECIO: '',
	          		CANTIDAD: '',
	          		TOTAL: '',
	          		UTILIDAD: ''
	          	},
              	insert: true,
              	switch_stock: true,
              	selectedFiltro: 'SECCION'
            }
        }, 
        methods: {

        	habilitar_insert() {
	       	  let me = this;
	       	  me.insert = true;
		    },

			filterItems: function(items, codigo) {

			    return items.filter(function(item) {
					return item.SECCION === codigo;
			    })
			},

			filterProductos: function(items, codigo) {

			    return items.filter(function(item) {
					return item.SECCION_CODIGO === codigo;
			    })
			},

        	llamarBusquedas(){	
	          axios.get('busquedas/').then((response) => {
	           	this.sucursales=response.data.sucursales;
	           	this.proveedores = response.data.proveedores;
	           	this.secciones = response.data.seccion;
	          });
	        },

	        descargar(){
	        	let me = this;	

	        	if(me.generarConsulta() === true) {

	        		me.descarga = true;

		        	axios({
					  url: '/export-top-venta',
					  method: 'POST',
					  data: me.datos,
					  responseType: 'blob', // important
					}).then((response) => {
						me.descarga = false;
					   const url = window.URL.createObjectURL(new Blob([response.data]));
					   const link = document.createElement('a');
					   link.href = url;
					   link.setAttribute('download', 'Top'+me.selectedTop+'Venta'+me.selectedInicialFecha+'al'+me.selectedFinalFecha+'.xlsx'); //or any other extension
					   document.body.appendChild(link);
					   link.click();
					});  	
				}
				me.insert = false;
				me.controlar = true;
	        },

	        llamarDatos(){
	        	let me = this;	
	        	
	        	if(me.generarConsulta() === true) {
	        		
	        		me.cargado = true;
	        		Common.generarReporteTopVentaCommon(me.datos).then(data => {
             			me.cargado = false;
						me.responseVentaTop = data.VentaTop;
              		});
				}
	        	me.insert=false;
	        },

	        generarConsulta(){

	        	let me = this;
	        	
	        	if (me.selectedSucursal === '' || me.selectedSucursal === "null") {
	        		me.validarSucursal = true;
	        		me.messageInvalidSucursal = 'Por favor seleccione sucursal';
	        		me.controlar = false;
	        	} else {
	        		me.validarSucursal = false;
	        		me.messageInvalidSucursal = '';
	        	}	

	        	if(me.selectedInicialFecha === null || me.selectedInicialFecha === "") {
	        		me.validarInicialFecha = true;
	        		me.messageInvalidFecha = 'Por favor seleccione una fecha';
	        		me.controlar = false;
	        	} else {
	        		me.validarInicialFecha = false;
	        		me.messageInvalidFecha = '';
	        	}

	        	if(me.selectedFinalFecha === null || me.selectedFinalFecha === "") {
	        		me.validarFinalFecha = true;
	        		me.messageInvalidFecha = 'Por favor seleccione una fecha';
	        		me.controlar = false;
	        	} else {
	        		me.validarFinalFecha = false;
	        		me.messageInvalidFecha = '';
	        	}

	        	if(me.onProveedor === false && me.selectedProveedor.length === 0 && me.selectedFiltro === 'PROVEEDOR') {

	        		me.validarProveedor = true;
	        		me.controlar=false;
	        	} else {
	        		me.validarProveedor = false;
	        	}

	        	if ((me.selectedSeccion === "null" || me.selectedSeccion === '') && me.selectedFiltro === 'SECCION'){
	        		me.validarSeccion = true;
	        		me.messageInvalidSeccion = 'Por favor seleccione la sección';
	        		me.controlar=true;
	        	} else {
	        		me.validarSeccion = false;
	        		me.messageInvalidSeccion = '';
	        	}

	        	if(me.radioAgrupar === ''){

	        		me.validarRadio = true;
	        		me.controlar=false;
	        	} else {
	        		me.validarRadio = false;
	        	}
	        	if(me.controlar === false){
	        		me.controlar = true;
	        		return false;
	        	}

		        if(me.onProveedor === true) {
		        	for (var key in me.proveedores){
		        		me.selectedProveedor[key] = me.proveedores[key].CODIGO;
		        	}
		        }

		       	me.datos = {
			        	Sucursal: me.selectedSucursal,
			        	Inicio: String(me.selectedInicialFecha),
			        	Final: String(me.selectedFinalFecha),
			        	Proveedores: me.selectedProveedor,
			        	Seccion: me.selectedSeccion,
			        	AllProveedores: me.onProveedor,
			        	Agrupar: me.radioAgrupar,
			        	Stock: me.switch_stock,
	        			Insert:me.insert,
	        			filtro: me.selectedFiltro,
	        			Top: me.selectedTop
		        	};

	        	return true;
	        }
        },
        mounted() {
        	let me = this;

        	$(function(){
		   		    $('#sandbox-container .input-daterange').datepicker({
		   		    	    keyboardNavigation: false,
    						forceParse: false
    				});
    				$("#selectedInicialFecha").datepicker().on(
			     		"changeDate", () => {
			     			me.insert=true;
			     			me.selectedInicialFecha = $('#selectedInicialFecha').val()
			     		}
					);
					$("#selectedFinalFecha").datepicker().on(
			     		"changeDate", () => {
			     			me.insert=true;
			     			me.selectedFinalFecha = $('#selectedFinalFecha').val()
			     		}
					);

					$('table').dataTable();
			});

			this.llamarBusquedas();
        }
    }    
</script>

<style>
	
.cuerpoTabla {
	font-size: 12px;
}
</style>>