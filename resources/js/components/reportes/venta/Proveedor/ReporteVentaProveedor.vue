<template>
	<!-- REPORTE DE VENTAS POR PROVEEDOR  -->
	<div v-if="$can('reporte.venta')">
		<!-- INICIO DE TARJETA -->
		<div class="card mt-3 shadow border-bottom-primary" >
		  <div class="card-header">Ventas Por Proveedores</div>
			<!-- CUERPO DE LA TARJETA -->
			<div class="card-body">
			  <div class="row">
				<div class="col-3">

					<!-- -------------------------------------------SELECCIONAR SUCURSAL---------------------------------------- -->
					
				  	<label for="validationTooltip01">Sucursal</label>
					<select class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSucursal }" v-model="selectedSucursal">
						<option value="null">Seleccionar</option>
						<option v-for="sucursal in sucursales" :value="sucursal.CODIGO">{{ sucursal.DESCRIPCION }}</option>
					</select>
					<div class="invalid-feedback">
						{{messageInvalidSucursal}}
					</div>

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

					<!-- -------------------------------------- BOTON DESCARGAR--------------------------------------------- -->

					<div class="r-md-3 mt-4">
						<button class="btn btn-dark btn-sm" type="submit" v-on:click="descargar()">Descargar</button> 
					</div>
				</div>

				<!-- -------------------------------------------MOSTRAR TIPO----------------------------------------------- -->

				<div class="col-3 mb-3 ">
					<div class="row ml-3">
						<div class="col-sm-2">
							<label>Tipo</label>
						</div>
						<div class="col-sm">
							<div class="form-check mt-3">
							  <input v-model="selectedTipo" class="form-check-input" type="checkbox" value="CO" id="contado" checked>
							  <label class="form-check-label" for="contado">
							    CONTADO
							  </label>
							</div>
							<div class="form-check">
							  <input v-model="selectedTipo" class="form-check-input" type="checkbox" value="CR" id="credito">
							  <label class="form-check-label" for="credito">
							    CRÉDITO
							  </label>
							</div>
							<div class="form-check">
							  <input v-model="selectedTipo" class="form-check-input" type="checkbox" value="PE" id="pagoAlEntregar">
							  <label class="form-check-label" for="pagoAlEntregar">
							    PAGO AL ENTREGAR
							  </label>
							</div>
	    				</div>
	    				<div class="col-sm-12">
							<div class="form-text text-danger">{{messageInvalidTipo}}</div>
						</div>
					</div>
				</div>
				
				<!-- ------------------------------------------- PROVEEDOR ----------------------------------------------- -->
				
				<div class="col-md-3">
					<label for="validationTooltip01">Seleccione Proveedor</label> 
					<select multiple class="form-control" size="5" v-model="selectedProveedor" :disabled="onProveedor" v-bind:class="{ 'is-invalid': validarProveedor }">
						<option v-for="proveedor in proveedores" :value="proveedor.CODIGO">{{proveedor.NOMBRE}}</option>
					</select>
					<div class="invalid-feedback">
					    {{messageInvalidProveedor}}
					</div>
					<div class="custom-control custom-switch mt-3">
						<input type="checkbox" class="custom-control-input" id="customSwitch1" v-model="onProveedor">
						<label class="custom-control-label" for="customSwitch1">Seleccionar todos</label>
					</div>
	            </div>

				<div class="col-md-3">
					<label for="validationTooltip02">Seleccione Categoría</label> 
					<select multiple class="form-control" size="5" v-model="selectedCategoria" :disabled="onCategoria" v-bind:class="{ 'is-invalid': validarCategoria }">
						<option v-for="categoria in categorias" :value="categoria.CODIGO">{{ categoria.DESCRIPCION }}</option>
					</select>
					<div class="invalid-feedback">
					    {{messageInvalidCategoria}}
					</div>
					<div class="custom-control custom-switch mt-3">
						<input type="checkbox" class="custom-control-input" id="customSwitch2" v-model="onCategoria">
						<label class="custom-control-label" for="customSwitch2">Seleccionar todas las Categorías</label>
					</div>
				</div> 
			  </div>	

				<!-- ------------------------------------------- SPINNER DESCARGA --------------------------------------------- -->
				<div class="row">
					<div class="col-md-12">
						<div v-if="descarga" class="d-flex justify-content-center mt-3">
							<strong>Descargando...   </strong>
			                <div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
			             </div>
		            </div>
			  	</div>
			<!-- FINAL DEL CUERPO -->
			<!-- CARD -->
			</div>
		<!-- FINAL DE TARJETA -->
		</div>
	</div>

	<!-- ------------------------------------------------------------------------ -->

	<div v-else>
		<cuatrocientos-cuatro></cuatrocientos-cuatro>
	</div>

	<!-- ------------------------------------------------------------------------ -->

	<!-- REPORTE TOP VENTAS  -->
</template>

<script >
	export default {
      props: ['candec', 'descripcion'],
        data(){
            return {
              	sucursales: [],
              	proveedores: [],
              	categorias: [],
              	selectedProveedor: [],
              	selectedCategoria: [],
              	selectedSucursal: "null",
              	selectedInicialFecha: '',
              	selectedFinalFecha: '',
              	messageInvalidSucursal: '',
              	messageInvalidFecha: '',
              	messageInvalidProveedor: '',
              	messageInvalidCategoria: '',
              	messageInvalidTipo: '',
              	validarSucursal: false,
              	validarInicialFecha: false,
              	validarFinalFecha: false,
              	validarProveedor: false,
              	validarCategoria: false,
              	datos: {},
              	descarga: false,
              	onProveedor: false,
              	onCategoria: false,
              	controlar: true,
              	selectedTipo: []
            }
        }, 
        methods: {

        	llamarBusquedas(){	
	          axios.get('busquedas/').then((response) => {
	           	this.sucursales=response.data.sucursales;
	           	this.categorias = response.data.categorias;
	           	this.proveedores = response.data.proveedores;
	          });
	        },

	        descargar(){
	        	
	        	let me = this;	
	        	
	        	if(this.generarConsulta() === true){
	        		me.descarga = true;
		        	axios({
					  url: '/export_venta_proveedor',
					  method: 'POST',
					  data: me.datos,
					  responseType: 'blob', // important
					}).then((response) => {
						me.descarga = false;
					   const url = window.URL.createObjectURL(new Blob([response.data]));
					   const link = document.createElement('a');
					   link.href = url;
					   link.setAttribute('download', 'Venta_Proveedor_'+me.selectedInicialFecha+' al '+me.selectedFinalFecha+'.xlsx'); //or any other extension
					   document.body.appendChild(link);
					   link.click();
					});
				}
				me.controlar = true;
	        },

	        generarConsulta(){

	        	let me = this;
	        	
	        	if (me.selectedSucursal === '' || me.selectedSucursal === "null") {
	        		me.validarSucursal = true;
	        		me.messageInvalidSucursal = 'Por favor seleccione sucursal.';
	        		me.controlar = false;
	        	} else {
	        		me.validarSucursal = false;
	        		me.messageInvalidSucursal = '';
	        	}	

	        	if(me.selectedInicialFecha === "null" || me.selectedInicialFecha === "") {
	        		me.validarInicialFecha = true;
	        		me.messageInvalidFecha = 'Por favor seleccione una fecha.';
	        		me.controlar = false;
	        	} else {
	        		me.validarInicialFecha = false;
	        		me.messageInvalidFecha = '';
	        	}

	        	if(me.selectedFinalFecha === "null" || me.selectedFinalFecha === "") {
	        		me.validarFinalFecha = true;
	        		me.messageInvalidFecha = 'Por favor seleccione una fecha.';
	        		me.controlar = false;
	        	} else {
	        		me.validarFinalFecha = false;
	        		me.messageInvalidFecha = '';
	        	}

	        	if(me.selectedTipo === [] || me.selectedTipo.length === 0) {
	        		me.messageInvalidTipo = 'Por favor seleccione tipo de venta.';
	        		me.controlar = false;
	        	} else {
	        		me.messageInvalidTipo = '';
	        	}

	        	if(me.onCategoria === false && me.selectedCategoria.length===0){

	        		me.validarCategoria = true;
	        		me.messageInvalidCategoria = 'Por favor seleccione una o varias Categorías.';
	        		me.controlar=false;
	        	} else {
	        		me.validarCategoria = false;
	        		me.messageInvalidCategoria = '';
	        	}

	        	if(me.onProveedor === false && me.selectedProveedor.length === 0) {
	        		me.messageInvalidProveedor = 'Por favor seleccione uno o varios Proveedores.';
	        		me.validarProveedor = true;
	        		me.controlar=false;
	        	} else {
	        		me.messageInvalidProveedor = '';
	        		me.validarProveedor = false;
	        	}

	        	if(me.controlar===false){
	        		return false;
	        	}

	        	if(me.onCategoria === true){
	        		for (var key in me.categorias){
	        			me.selectedCategoria[key] = me.categorias[key].CODIGO;
	        		}
	        	} 

 				if(me.onProveedor === true) {
		        	for (var key in me.proveedores){
		        		me.selectedProveedor[key] = me.proveedores[key].CODIGO;
		        	}
		        }

	        	me.datos = {
		        	Sucursal: me.selectedSucursal,
		        	Inicio: me.selectedInicialFecha,
		        	Final: me.selectedFinalFecha,
		        	Categorias: me.selectedCategoria,
		        	AllCategory: me.onCategoria,
					Proveedores: me.selectedProveedor,
					AllProveedores: me.onProveedor,
					Tipo: me.selectedTipo
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
			     			me.selectedInicialFecha = $('#selectedInicialFecha').val()
			     		}
					);
					$("#selectedFinalFecha").datepicker().on(
			     		"changeDate", () => {
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
	.form-text{
        font-size: 12px;
	}
</style>