<template>
		<!-- REPORTE DE VENCIMIENTO DE PRODUCTOS -->
	<div>
		<div class="card mt-3 shadow border-bottom-primary" >
		  <div class="card-header">Reporte Vencimiento de Productos</div>
		    <div class="card-body">
			  	<div class="row">
					<div class="col-4 ml-3">
				  		<label for="validationTooltip01">Sucursal</label>
						<select class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSucursal }" v-model="selectedSucursal">
							<option value="null" selected>Seleccionar</option>
							<option v-for="sucursal in sucursales" :value="sucursal.CODIGO">{{ sucursal.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
						    {{messageInvalidSucursal}}
						</div>
					</div>

					<div class="col-4 ml-3">	
						<label>Seleccione Intervalo de Tiempo</label>
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
					<div class="col-auto mt-4 ml-3">
						<div class="col-auto">
							<button class="btn btn-dark btn-sm" type="submit" v-on:click="descargar()"><font-awesome-icon icon="download"/> Descargar</button>
						</div>
					</div>

					<!-- -------------------------------------------MOSTRAR DOWNLOADING----------------------------------------------- -->

				    <div class="row">
						<div v-if="descarga" class="ml-5 d-flex justify-content-center mt-3">
							Descargando...
				            <div class="spinner-grow text-primary" role="status" aria-hidden="true"></div>
				        </div>
			        </div>
				</div>
			</div>
		  </div>
		</div>
		<!-- ------------------------------------------------------------------------ -->
		
		<!-- <div v-else>
			<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div> -->

		<!-- ------------------------------------------------------------------------ -->
	</div>
	<!-- FIN REPORTE DE VENCIMIENTO DE PRODUCTOS -->
</template>

<script >
	export default {
      	props: ['candec'],
        data(){
            return {
              	sucursales: [],
              	selectedSucursal: 'null',
              	validarSucursal: false,
              	messageInvalidSucursal: '',
              	selectedInicialFecha: '',
              	validarInicialFecha: false,
              	messageInvalidFecha: '',
              	selectedFinalFecha: '',
              	validarFinalFecha: false,
              	descarga: false,
              	controlar: true,
              	datos: {}
            }
        }, 
        methods: {
        	llamarBusquedas(){	
	          axios.get('busquedas/').then((response) => {
	           	this.sucursales = response.data.sucursales;
	          });
	        },

	        descargar(){

	        	let me = this;	

	        	if(this.generarConsulta() === true){
	        		me.descarga = true;
		        	axios({
					  url: '/export_producto_vencimiento',
					  method: 'POST',
					  data: me.datos,
					  responseType: 'blob', // important
					}).then((response) => {
						me.descarga = false;
					   const url = window.URL.createObjectURL(new Blob([response.data]));
					   const link = document.createElement('a');
					   link.href = url;
					   link.setAttribute('download', 'Vencimiento'+me.selectedInicialFecha+'_al_'+me.selectedFinalFecha+'.xlsx'); //or any other extension
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

	        	if(me.controlar === false){
	        		return false;
	        	}

				me.datos = {
		        	Sucursal: me.selectedSucursal,
		        	Inicio: me.selectedInicialFecha,
		        	Final: me.selectedFinalFecha
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
			     		"changeDate", () => {me.selectedInicialFecha = $('#selectedInicialFecha').val()}
					);
					$("#selectedFinalFecha").datepicker().on(
			     		"changeDate", () => {me.selectedFinalFecha = $('#selectedFinalFecha').val()}
					);

					$('table').dataTable();
			});
			this.llamarBusquedas();
        }
    }    
</script>
