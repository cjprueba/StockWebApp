<template>
		<!-- VENTA DIARIA -->

	<div v-if="$can('reporte.venta')">
		<div class="card shadow border-bottom-primary" >
		  	<div class="card-header">Reporte Diario</div>
			<div class="card-body">
			  	<div class="form-row">
			  		<div class="col-md-4">
			  			<label for="validationTooltip01">Seleccione Sucursal</label>
						<select class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSucursal }" v-model="selectedSucursal">
							 <option value="null" selected>Seleccionar</option>
							 <option v-for="sucursal in sucursales" :value="sucursal.CODIGO">{{ sucursal.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
					        {{messageInvalidSucursal}}
					    </div>
					</div>
			  		<div class="col-md-4 ml-3">
					  	<label for="validationTooltip01">Seleccione Intervalo de Tiempo</label>
						<div id="sandbox-container">
							<div class="input-daterange input-group">
								   <input   type="text" class="input-sm form-control form-control-sm" id="selectedInicialFecha" v-model="selectedInicialFecha" v-bind:class="{ 'is-invalid': validarInicialFecha }"/>
								   <div class="input-group-append form-control-sm">
								   		<span class="input-group-text">a</span>
								   </div>
								   <input   type="text" class="input-sm form-control form-control-sm" name="end" id="selectedFinalFecha" v-model="selectedFinalFecha" v-bind:class="{ 'is-invalid': validarFinalFecha }"/>
							</div>
							<div class="invalid-feedback">
					        	{{messageInvalidFecha}}
					    	</div>
						</div>	
					</div>
			  		<div class="col-md mt-4 text-center">	  
						<button class="btn btn-dark btn-sm" type="submit" v-on:click="descargar()"><font-awesome-icon icon="download" /> Descargar</button>
					</div>
				</div>
			</div>
		</div>

		<div class="row">

			<!-- SPINNER DESCARGA -->

			<div class="col-md-12">
				<div v-if="descarga" class="d-flex justify-content-center mt-3">
					<strong>Descargando...   </strong>
	                <div class="spinner-grow text-primary" role="status" aria-hidden="true"></div>
	             </div>
            </div>
       </div>
	</div>

	<!-- FIN DE VENTA DIARIA -->

	<!-- ------------------------------------------------------------------------ -->

	<div v-else>
		<cuatrocientos-cuatro></cuatrocientos-cuatro>
	</div>

	<!-- ------------------------------------------------------------------------ -->

</template>

<script>
	export default {
        data(){
            return{
              	sucursales: [],
              	selectedSucursal: 'null',
              	validarSucursal: false,
              	messageInvalidSucursal: '',
              	selectedInicialFecha: '',
              	validarInicialFecha: false,
              	messageInvalidFecha: '',
              	selectedFinalFecha: '',
              	validarFinalFecha: false,
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
			        	final: String(this.selectedFinalFecha)
		        	};
		        	
		        	Common.generarReporteDiarioCommon(datos).then(function(){
	    				me.descarga = false;
	    			});	
				}
	        },
	        generarConsulta(){
	        	
	        	let me=this;

	        	
	        	if (me.selectedSucursal === null || me.selectedSucursal === "null") {
	        		me.validarSucursal = true;
	        		me.messageInvalidSucursal = 'Por favor seleccione una sucursal';
	        		return false;
	        	} else {
	        		me.validarSucursal = false;
	        		me.messageInvalidSucursal = '';
	        	}	

	        	if(me.selectedInicialFecha === null || me.selectedInicialFecha === "") {
	        		me.validarInicialFecha = true;
	        		me.messageInvalidFecha = 'Por favor seleccione una fecha Inicial';
	        		return false;
	        	} else {
	        		me.validarInicialFecha = false;
	        		me.messageInvalidFecha = '';
	        	}

	        	if(me.selectedFinalFecha === null || me.selectedFinalFecha === "") {
	        		me.validarFinalFecha = true;
	        		me.messageInvalidFecha = 'Por favor seleccione una fecha Final';
	        		return false;
	        	} else {
	        		me.validarFinalFecha = false;
	        		me.messageInvalidFecha = '';
	        	}		

	        	return true;
	        },
	    },
        mounted(){
        	let me = this;

        	$(function(){
		   		    $('#sandbox-container .input-daterange').datepicker({
		   		    	    keyboardNavigation: false,
    						forceParse: false
    				});
    				$("#selectedInicialFecha").datepicker().on(
			     		"changeDate", () => {
			     			me.selectedInicialFecha = $('#selectedInicialFecha').val();
			     		}
					);
					$("#selectedFinalFecha").datepicker().on(
						
			     		"changeDate", () => {
			     			me.selectedFinalFecha = $('#selectedFinalFecha').val();
			     		}
					);
					$('table').dataTable();
			});
			this.llamarBusquedas();
        }
    }
</script>