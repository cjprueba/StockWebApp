<template>
	<!-- REPORTE NOTAS DE CREDITO  -->
	<div class="container" v-if="$can('reporte.notadecredito') && $can('reporte.web')">
		<!-- INICIO DE TARJETA -->
		<div class="col">
		<div class="card mt-3 shadow border-bottom-primary" >
		  <div class="card-header">Notas De Crédito</div>
			<!-- CUERPO DE LA TARJETA -->
			<div class="card-body">
			  <div class="row">

				<!-- -------------------------------------------MOSTRAR SUCURSAL E INTERVALO ----------------------------------------------- -->

				<div class="col-md-3">

					<!-- -------------------------------------------SELECCIONAR SUCURSAL---------------------------------------- -->
					
				  	<label for="validationTooltip01">Seleccione Sucursal</label>
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
						<button class="btn btn-primary btn-sm" type="submit" v-on:click="llamarDatos">Generar</button> 
					</div>

				</div>

				<!-- ------------------------------------------- MOSTRAR TIPO DE NOTA DE CRE ----------------------------------------------- -->

				<div class="col-md-5 mb-3">

					<!-- -------------------------------------------MOSTRAR CLIENTE----------------------------------------------- -->

					<div class="row ml-3">
			    		<div class="col-sm">
			    			<label>Cliente</label>
					    	<cliente-filtrar ref="componente_textbox_cliente" :codigo="selectedCliente" @codigo="cargarCodigo" v-model='selectedCliente'></cliente-filtrar>
					    </div>
			    	</div>
					<div class="row mt-3 ml-3">
						<div class="col-sm-3">
							<label>Tipo Nota:</label>
						</div>
						<div class="col-sm">
							<div class="form-check mt-3">
							  <input v-model="selectedTipo" class="form-check-input" type="checkbox" :value=1 id="devProducto">
							  <label class="form-check-label" for="devProducto">
							    Por Devolución
							  </label>
							</div>
							<div class="form-check">
							  <input v-model="selectedTipo" class="form-check-input" type="checkbox" :value=2 id="descVenta">
							  <label class="form-check-label" for="descVenta">
							    Por Descuento de Venta
							  </label>
							</div>
							<div class="form-check">
							  <input v-model="selectedTipo" class="form-check-input" type="checkbox" :value=3 id="descProd">
							  <label class="form-check-label" for="descProd">
							    Por Descuento de Mercadería
							  </label>
							</div>
	    				</div>
	    				 <div class="col-md-12">
							<div class="form-text text-danger">{{messageInvalidTipo}}</div>
						</div>

					</div>
				</div>

				<!-- ------------------------------------------------------------------------------------------ -->

				<div class="col-md-3 mb-3">

					<!-- -------------------------------------------MOSTRAR NRO CAJA----------------------------------------------- -->

					<div class="row ml-3">
			    		<div class="col-sm">
			    			<label for="validationTooltip01">Nro. Caja</label>
							<input  class="form-control form-control-sm" type="number" v-model="selectedCaja">
					    </div>
			    	</div>

					<!-- ------------------------------------------- ESTADO DE NOTA DE CRED----------------------------------------------- -->
					<div class="row mt-3 ml-3">	
						
						<div class="col-sm-3">
							<label aling=left>Estado:</label>
						</div>
						<div class="col-sm">
							<div class="form-check mt-3">
							  <input  v-model="procesado" class="form-check-input" type="checkbox" :value=0 id="pendiente">
							  <label class="form-check-label" for="pendiente">
							    Pendiente
							  </label>
							</div>
							<div class="form-check">
							  <input  v-model="procesado" class="form-check-input" type="checkbox" :value=1 id="procesado">
							  <label class="form-check-label" for="procesado">
							    Procesado
							  </label>
							</div>
							<div class="form-check">
							  <input  v-model="procesado" class="form-check-input" type="checkbox" :value=2 id="cancelado">
							  <label class="form-check-label" for="cancelado">
							    Cancelado
							  </label>
							</div>
						</div>
	    				<div class="col-sm-12">
							<div class="form-text text-danger">{{messageInvalidProceso}}</div>
						</div>
					</div>
				</div>
			</div>

			<!-- -------------------------------------------DATATABLE VENTAS VENDEDOR------------------------------------------ -->
				
			<div class="row mt-3">
				<div class="col-md-12">
					<table id="tablaNotaCreditoRpt" class="table table-sm mb-3" style="width:100%">
				        <thead>
				            <tr>
				                <th>#</th>
				                <th>ID</th>
				                <th>Cliente</th>
				                <th>Caja</th>
				                <th>Nro. Factura</th>
				                <th>ID Venta</th>
				                <th>Fecha</th>
				                <th>Tipo</th>
				                <th>Estado</th>
				                <th class="totalDesc">Descuento</th>
				                <th class="totalIVA">IVA</th>
				                <th class="totalSubtotal">SubTotal</th>
				                <th class="totalColumna">Total</th>
				            </tr>
				        </thead>
				        <tfoot>
				         	<tr>
				            	<th></th>
				            	<th></th>
				            	<th></th>
				            	<th></th>
				            	<th></th>
				            	<th></th>
				            	<th></th>
				            	<th></th>
					           	<th class="text-center" style="font-size: 14px"><strong>TOTALES</strong></th>
					           	<th class="text-center" style="font-size: 14px"></th>
					           	<th class="text-center" style="font-size: 14px"></th>
					           	<th class="text-center" style="font-size: 14px"></th>
					           	<th class="text-center" style="font-size: 14px"></th> 
					        </tr>
				        </tfoot>
				    </table> 
				</div>
			</div>
		</div>	
		
		<!-- FINAL DE TARJETA -->
	
		</div>
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
   
        data(){
            return {
              	sucursales: [],
              	selectedSucursal: "null",
              	selectedInicialFecha: '',
              	selectedFinalFecha: '',
              	messageInvalidSucursal: '',
              	messageInvalidFecha: '',
              	messageInvalidTipo: '',
              	messageInvalidProceso: '',
              	validarSucursal: false,
              	validarInicialFecha: false,
              	validarFinalFecha: false,
              	validarRadio: false,
              	descarga: false,
              	cargado:false,
              	controlar: true,
              	procesado: [],
              	selectedTipo: [],
				selectedCaja: '',
				selectedCliente: ''
            }
        }, 
        methods: {

        	llamarBusquedas(){	
	          axios.get('busquedas/').then((response) => {
	           	this.sucursales=response.data.sucursales;
	          });
	        },
			
			llamarDatos(){
	        	let me = this;	
	        	
	        	if(me.generarConsulta() === true) {
	        		// PREPARAR DATATABLE 

			 		var tableNotaCreditoRpt = $('#tablaNotaCreditoRpt').DataTable({
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
				            { 
				              extend: 'copy', 
				              title: 'reporteNotaCredito'+me.selectedInicialFecha+'al'+me.selectedFinalFecha,
				              text: '<i class="fa fa-copy"></i>', 
				              titleAttr: 'Copiar', 
				              className: 'btn btn-secondary',
				              footer: true
				            },
				        	{ 
				        	  extend: 'excelHtml5', 
				        	  title: 'reporteNotaCredito'+me.selectedInicialFecha+'al'+me.selectedFinalFecha,
				        	  text: '<i class="fa fa-file"></i>', 
				        	  titleAttr: 'Excel', 
				        	  className: 'btn btn-success', 
				        	  footer: true,
				        	},
				            { 
				              extend: 'pdfHtml5', 
				              title: 'reporteNotaCredito'+me.selectedInicialFecha+'al'+me.selectedFinalFecha,
				              text: '<i class="fa fa-file"></i>', 
				              titleAttr: 'Pdf', 
				              className: 'btn btn-danger', 
							  orientation: 'landscape',
				              footer: true
				            }, 
				            { 
				              extend: 'print', 
				              title: 'reporteNotaCredito'+me.selectedInicialFecha+'al'+me.selectedFinalFecha,
				              text: '<i class="fa fa-print"></i>', 
				              titleAttr: 'Imprimir', 
				              className: 'btn btn-secondary', 
				              footer: true 
				            }
				        ],
		                "ajax":{
	                 			"data": {
								    Sucursal: me.selectedSucursal,
								    Inicio: me.selectedInicialFecha,
								    Final: me.selectedFinalFecha,
									Tipo: me.selectedTipo,
									Procesado: me.procesado,
									Cliente: me.selectedCliente,
									Caja: parseInt(me.selectedCaja),
						        	"_token": $('meta[name="csrf-token"]').attr('content')
	                 			},
		                  "url": "/notaCreditoRptDatatable",
		                  "dataType": "json",
		                  "type": "POST"

		                },
		                "columns": [
		                    { "data": "ITEM" },
		                    { "data": "ID" },
		                    { "data": "CLIENTE" },
		                    { "data": "CAJA" },
		                    { "data": "NRO_FACTURA" },
		                    { "data": "FK_VENTA" },
		                    { "data": "FECHA" },
		                    { "data": "TIPO" },
		                    { "data": "PROCESADO" },
		                    { "data": "DESCUENTO_MONTO" },
		                    { "data": "IVA" },
		                    { "data": "SUBTOTAL" },
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
						            Common.darFormatoCommon(sum, this.candec)
						        ); 
						  	});

							// TOTAL DESCUENTO 

							api.columns('.totalDesc', {}).every(function() {

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

							    $( api.columns('.totalDesc').footer() ).html(
						            Common.darFormatoCommon(sum, this.candec)
						        ); 
						  	});

					        //IVA

						  	api.columns('.totalIVA', {}).every(function() {

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

							    $( api.columns('.totalIVA').footer() ).html(
						            Common.darFormatoCommon(sum, this.candec)
						        );

						  	});

					        //SUBTOTAL

					        api.columns('.totalSubtotal', {}).every(function() {

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

							    $( api.columns('.totalSubtotal').footer() ).html(
						            Common.darFormatoCommon(sum, this.candec)
						        ); 
						  	});
						}
					});

				}
	        	me.controlar = true;
	        },

	        cargarCodigo(valor){

				this.selectedCliente = valor;
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
	        		me.messageInvalidTipo = 'Por favor seleccione tipo de nota de credito.';
	        		me.controlar = false;
						
	        	} else {
	        		me.messageInvalidTipo = '';
	        	}

	        	if(me.procesado === [] || me.procesado.length === 0) {
	        		me.messageInvalidProceso = 'Por favor seleccione estado de la nota de credito.';
	        		me.controlar = false;
	        	} else {
	        		me.messageInvalidProceso = '';
	        	}

	        	if(me.controlar===false){
	        		return false;
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
			
			var tableNotaCreditoRpt = $('#tablaNotaCreditoRpt').DataTable();
        }
    }    
</script>

<style>
	.form-text{
        font-size: 12px;
	}
</style>