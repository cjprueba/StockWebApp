<template>
		<!-- SERVICIO DE DELIVERY  -->
	<div class="col-9">

		<div class="card mt-4 ml-4 shadow border-bottom-primary" v-if="$can('reporte.venta.cajero')">
		  	<div class="card-header">Tickets por cajero</div>
		  	<div class="card-body">
		  		<div class="col-12">
					<div class="row">
						<div class="col-md-12">
							<div class="row">
						  		<label class="col-6" for="validationTooltip01">Sucursal
									<select v-on:change="cajeroBusqueda()" class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSucursal }" v-model="selectedSucursal">
										<option value="null" selected>Seleccionar</option>
										<option v-for="sucursal in sucursales" :value="sucursal.CODIGO">{{ sucursal.DESCRIPCION }}</option>
									</select>
									<div class="invalid-feedback">
									    {{messageInvalidSucursal}}
									</div>
								</label>
								
								<label class="col-6" for="validationTooltip01">Cajero

									<select v-if="!selectedSucursal || selectedSucursal==='null'" disabled class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarCajero }" v-model="selectedCajero">
									</select>
									<select v-else class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarCajero }" v-model="selectedCajero">
										<option value="null" selected>Seleccionar</option>
										<option v-for="cajero in cajeros" :value="cajero.ID">{{ cajero.NOMBRE }}</option>
									</select>
									<div class="invalid-feedback">
									    {{messageInvalidCajero}}
									</div>
								</label>
							</div>

							<div class="row mt-3">	
								<label class="col-12 text-center">Seleccione Intervalo de Tiempo</label>
								<div id="sandbox-container" class="input-daterange input-group col-12">
									<input id='selectedInicialFecha' class="input-sm form-control form-control-sm" v-model="selectedInicialFecha" v-bind:class="{ 'is-invalid': validarInicialFecha }"/>
									<div class="input-group-append form-control-sm">
										<span>a</span>
									</div>
									<input name='end' id='selectedFinalFecha' class="input-sm form-control form-control-sm" v-model="selectedFinalFecha" v-bind:class="{ 'is-invalid': validarFinalFecha }"/>
									<div class="invalid-feedback">
								        {{messageInvalidFecha}}
								    </div>
								</div>
							</div>
							<div class="row mt-3">
								<div class="col-12" align="right">
									<button class="btn btn-primary btn-sm" type="submit" v-on:click="llamarDatos">Generar</button>
									<!-- <button class="btn btn-sm" id="rojobtn" type="submit" v-on:click="descargar()"><font-awesome-icon icon="download"/> Descargar</button> -->
								</div>
							</div>
							

					        <!-- -------------------------------------------MOSTRAR DOWNLOADING----------------------------------------------- -->

						    <div class="row">
								<div v-if="descarga" class="ml-5 d-flex justify-content-center mt-3">
									Descargando...
						            <div class="spinner-grow text-primary" role="status" aria-hidden="true"></div>
						        </div>
					        </div>
					        <div class="col-md-12 mt-3">
								<table id="tablaVentaCajero" class="table table-striped table-hover table-bordered table-sm mb-3" style="width:100%">
						            <thead>
						                <tr>
						                    <th>ID</th>
						                    <th>CAJERO</th>
						                    <th>FECHA</th>
						                    <th class="totalColumna">TICKETS POR DÍA</th>
						                </tr>
						            </thead>
						            <tfoot>
						            	<tr>
						            		
							            	<th colspan='3' class="text-center"><strong>TOTAL TICKETS</strong></th>
							            	<th class="text-center"></th> 
							            </tr>
						            </tfoot>
						        </table> 
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div v-else>
	      <cuatrocientos-cuatro></cuatrocientos-cuatro>
	    </div>
	</div>
	

</template>

<script >
	export default {
		props: ['candec'],
        data(){
            return {
              	sucursales: [],
              	selectedSucursal: '',
              	validarSucursal: false,
              	messageInvalidSucursal: '',
              	
              	cajeros: [],
              	selectedCajero: '',
              	validarCajero: false,
              	messageInvalidCajero: '',

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
        	cajeroBusqueda(){
        		var datos= {
        			selectedSucursal: this.selectedSucursal
        		}
	          	Common.busquedaCajeroCommon(datos).then(data=>{
	           		this.cajeros = data.cajero;
	          	});

	        },
	        fechaActual(){
	        	var today = new Date();
				var dd = today.getDate();
				var mm = today.getMonth() + 1; //January is 0!
				var yyyy = today.getFullYear();

				if (dd < 10) {
				  dd = '0' + dd;
				}

				if (mm < 10) {
				  mm = '0' + mm;
				}

				today = mm + '/' + dd + '/' + yyyy;
	        },

        	descargar(){
	        	let me = this;	

	        	if(this.generarConsulta() === true) {

	        		me.descarga = true;

		        	var datos = {
			        	sucursal: this.selectedSucursal,
			        	inicio: String(this.selectedInicialFecha),
			        	final: String(this.selectedFinalFecha),
			        	cajero: this.selectedCajero
		        	};
		        	Common.reporteCajeroVentaCommon(datos).then(function(){
	    				me.descarga = false;
	    			});	
				}
	        },
	        llamarDatos(){

	        	let me = this;

		        if(this.generarConsulta() === true) {

		        	// PREPARAR DATATABLE 

			 		var tableVentaCajero = $('#tablaVentaCajero').DataTable({
		                "processing": true,
		                "serverSide": true,
		                "destroy": true,
		                "bAutoWidth": true,
                		"searching": false,
	                 	"paging": false,
                        "select": true,
                        "dom": "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
						"<'row'<'col-sm-12'tr>>" +
						"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
				        "buttons": [
				            { extend: 'copy', text: '<i class="fa fa-copy"></i>', titleAttr: 'Copiar', className: 'btn btn-secondary', footer: true },
				        	{ extend: 'excelHtml5', text: '<i class="fa fa-file"></i>', titleAttr: 'Excel', className: 'btn btn-success', title: 'Reporte Tickets - Cajero', messageTop: 'Cantidad de tickets realizadas por Cajero', footer: true },
				            { extend: 'pdfHtml5', text: '<i class="fa fa-file"></i>', titleAttr: 'PDF', className: 'btn btn-danger', title: 'Reporte Tickets - Cajero', messageTop: 'Cantidad de tickets realizadas por Cajero', footer: true }, 
				            { extend: 'print', text: '<i class="fa fa-print"></i>', titleAttr: 'Imprimir', className: 'btn btn-secondary', title: 'Reporte Tickets - Cajero', messageTop: ' Cantidad de tickets realizadas por Cajero', footer: true }
				        ],
		                "ajax":{
	                 			"data": {
	                 				sucursal: me.selectedSucursal,
						        	inicio: String(me.selectedInicialFecha),
						        	final: String(me.selectedFinalFecha),
						        	cajero: me.selectedCajero,
						        	"_token": $('meta[name="csrf-token"]').attr('content')
	                 			},
		                  "url": "/ventaCajeroDatatable",
		                  "dataType": "json",
		                  "type": "POST"

		                },
		                "columns": [
		                    { "data": "ID" },
		                    { "data": "CAJERO" },
		                    { "data": "FECHA" },
		                    { "data": "TOTAL" }
		                ],

		                "footerCallback": function(row, data, start, end, display) {

							var api = this.api();

							// TOTAL 

							api.columns('.totalColumna', {}).every(function() {

						    	var sum = this.data().reduce(function(a, b) {

						      	// *******************************************************************

						      	// QUITAR LAS COMAS DE LOS VALORES

						      	a = Common.quitarComaCommon(a);
						      	b = Common.quitarComaCommon(b);
						      	
						      	// *******************************************************************

						        var x = parseFloat(a) || 0;
						        var y = parseFloat(b) || 0;

						        // *******************************************************************

						        // SUMAR VALORES

						        return x + y;

						        // *******************************************************************

						    	}, 0);

							    // CARGAR EN EL FOOTER  

							    $( api.columns('.totalColumna').footer() ).html(
						            Common.darFormatoCommon(sum, 0)
						        ); 
						  	});

						}
					});

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

	        	if (this.selectedCajero === null || this.selectedCajero === "null") {
	        		this.validarCajero = true;
	        		this.messageInvalidCajero = 'Por favor seleccione un cajero';
	        		return false;
	        	} else {
	        		this.validarCajero = false;
	        		this.messageInvalidCajero = '';
	        	}	

	        	if(this.selectedInicialFecha === null || this.selectedInicialFecha === "" ) {
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
	        },
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
			this.fechaActual();
			var tableVentaCajero = $('#tablaVentaCajero').DataTable();
        }
    }    
</script>
<style scoped>
	#rojobtn{background-color:#8c160e; color:#ffffff;}
</style>