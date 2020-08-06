<template>
		<!-- VALE DE FUNCIONARIOS -->
	<div class="container">

		<div class="card mt-3 shadow border-bottom-primary" >
		  	<div class="card-header">Vale de Funcionarios</div>
			<div class="card-body">
			  	<div class="row">

			  		<div class="col-md-4 mb-3">
				  		<label for="validationTooltip01">Sucursal</label>
						<select class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSucursal }" v-model="selectedSucursal">
							<option value="null" selected>Seleccionar</option>
							<option v-for="sucursal in sucursales" :value="sucursal.CODIGO">{{ sucursal.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
						    {{messageInvalidSucursal}}
						</div>	  					  
					</div>

					<div class="col-md-4 mb-3 text-center">
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
				</div>

				<div class="row">
					<div class="col-md-4 mt-4">
						<button class="btn btn-dark btn-sm" type="submit" v-on:click="descargar()"><font-awesome-icon icon="download"/> Descargar</button>
						<button class="btn btn-primary btn-sm" type="submit" v-on:click="llamarDatos">Generar</button>
					</div>
				</div>
				<!-- --------------------------------------------MOSTRAR LOADING--------------------------------------------------- -->

			    <div class="col-md-12">
					<div v-if="cargado" class="d-flex justify-content-center mt-3">
						Cargando...
			            <div class="spinner-grow text-primary" role="status" aria-hidden="true"></div>
			        </div>
		        </div>

		        <!-- -------------------------------------------MOSTRAR DOWNLOADING----------------------------------------------- -->

			    <div class="col-md-12">
					<div v-if="descarga" class="d-flex justify-content-center mt-3">
						Descargando...
			            <div class="spinner-grow text-primary" role="status" aria-hidden="true"></div>
			        </div>
		        </div>
			</div>
		</div>

	</div>
	<!-- FIN VALE DE FUNCIONARIOS -->
</template>

<script >
	export default {
        data(){
            return {
              	sucursales: [],
              	selectedSucursal: '',
              	validarSucursal: false,
              	messageInvalidSucursal: '',
              	selectedInicialFecha: '',
              	validarInicialFecha: false,
              	messageInvalidFecha: '',
              	selectedFinalFecha: '',
              	validarFinalFecha: false,
              	cargado: false,
              	descarga: false
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
	        	if(this.generarConsulta() === true) {

	        		me.descarga = true;

		        	var datos = {
			        	sucursal: this.selectedSucursal,
			        	inicio: String(this.selectedInicialFecha),
			        	final: String(this.selectedFinalFecha),
			        	accion: 'descargar'
		        	};
		        	
		        	Common.generarReporteValeCommon(datos).then(function(){
	    				me.descarga = false;
	    			});	
				}
	        },

	        llamarDatos(){

	        	let me = this;

		        if(this.generarConsulta() === true) {
		        	var datos = {
			        	sucursal: this.selectedSucursal,
			        	inicio: String(this.selectedInicialFecha),
			        	final: String(this.selectedFinalFecha),
			        	accion: 'ver'
		        	};

		        	me.cargado = true;
		        	
		        	Common.generarReporteValeCommon(datos).then(function(){
	    				me.cargado = false;
	    			});	
	        	} else {
	        		alert("false");
	        	}
	        },
	        generarConsulta(){
	        	
	        	if (this.selectedSucursal === null || this.selectedSucursal === "null") {
	        		this.validarSucursal = true;
	        		this.messageInvalidSucursal = 'Por favor seleccione sucursal';
	        		return false;
	        	} else {
	        		this.validarSucursal = false;
	        		this.messageInvalidSucursal = '';
	        	}	

	        	if(this.selectedInicialFecha === null || this.selectedInicialFecha === "") {
	        		this.validarInicialFecha = true;
	        		this.messageInvalidFecha = 'Por favor seleccione una fecha Inicial';
	        		return false;
	        	} else {
	        		this.validarInicialFecha = false;
	        		this.messageInvalidFecha = '';
	        	}

	        	if(this.selectedFinalFecha === null || this.selectedFinalFecha === "") {
	        		this.validarFinalFecha = true;
	        		this.messageInvalidFecha = 'Por favor seleccione una fecha Final';
	        		return false;
	        	} else {
	        		this.validarFinalFecha = false;
	        		this.messageInvalidFecha = '';
	        	}		

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
