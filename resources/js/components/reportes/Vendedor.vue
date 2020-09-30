<template>
	<!-- VENTA VENDEDOR  -->
	<div class="container">
		<div class="card mt-3  shadow border-bottom-primary" >
		  <div class="card-header">Ventas por Vendedor</div>
			<div class="card-body">
			  <div class="row">
				<div class="col-4 mb-3 ">
					<div class="row ml-3">
				  		<label for="validationTooltip01">Sucursal</label>
						<select class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSucursal }" v-model="selectedSucursal">
							<option value="null" selected>Seleccionar</option>
							<option v-for="sucursal in sucursales" :value="sucursal.CODIGO">{{ sucursal.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
						    {{messageInvalidSucursal}}
						</div>
					</div>
					<div class="row mt-3 ml-3">
				  		<label for="validationTooltip01">Vendedor</label>
						<select class="custom-select custom-select-sm" v-model="selectedVendedor">
							<option value="null" selected>Seleccionar</option>
							<option v-for="vendedor in vendedores" :value="vendedor.CODIGO">{{ vendedor.NOMBRE }}</option>
						</select>
					</div>
				</div>

				<div class="col-4 mb-3 ">
					<div class="row ml-3">	
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
					<div class="row mt-5 ml-3">
						<div class="col-auto">
							<button class="btn btn-dark btn-sm" type="submit" v-on:click="descargar()"><font-awesome-icon icon="download"/> Descargar</button>
						</div>
						<div class="col-auto">
								
							<button class="btn btn-primary btn-sm" type="submit" v-on:click="llamarDatos">Generar</button> 
						</div>
					</div>
				</div>
				<!-- -------------------------------------------MOSTRAR DOWNLOADING----------------------------------------------- -->

				<div class="col-md-12 mt-3 mb-3">
					<div v-if="descarga" class="ml-5 d-flex justify-content-center">
							Descargando...
				    	<div class="spinner-grow text-primary" role="status" aria-hidden="true"></div>
				    </div>
			    </div>

				<!-- -------------------------------------------DATATABLE VENTAS VENDEDOR---------------------------------------------- -->
				<div class="col-md-12">
					<table id="tablaVentaVendedor" class="table table-striped table-hover table-bordered table-sm mb-3" style="width:100%">
			            <thead>
			                <tr>
			                    <th>Ref</th>
			                    <th>Codigo</th>
			                    <th>Cliente</th>
			                    <th>Fecha</th>
			                    <th>Hora</th>
			                    <th>Vendedor</th>
			                    <th class="totalIVA">IVA</th>
			                    <th class="totalSubtotal">SubTotal</th>
			                    <th class="totalColumna">Total</th>
			                </tr>
			            </thead>
			            <tfoot>
			            	<tr>
			            		<th></th><th></th><th></th>
				            	<th colspan='3' class="text-center"><strong>TOTALES</strong></th>
				            	<th class="text-center"></th>
				            	<th class="text-center"></th>
				            	<th class="text-center"></th> 
				            </tr>
			            </tfoot>
			        </table> 
				</div>
			  		
			  </div>
			</div>
		</div>
	</div>
	<!-- VENTA VENDEDOR -->
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
              	selectedVendedor: '',
              	validarVendedor: false,
              	messageInvalidVendedor: '',
              	vendedores: [],
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
	           	this.vendedores = response.data.vendedor;
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
			        	vendedor: this.selectedVendedor
		        	};
		        	
		        	Common.reporteVentaVendedorCommon(datos).then(function(){
	    				me.descarga = false;
	    			});	
				}
	        },

	        llamarDatos(){

	        	let me = this;

		        if(this.generarConsulta() === true) {

		        	// PREPARAR DATATABLE 

			 		var tableVentaVendedor = $('#tablaVentaVendedor').DataTable({
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
				        	{ extend: 'excelHtml5', text: '<i class="fa fa-file"></i>', titleAttr: 'Excel', className: 'btn btn-success', footer: true },
				            { extend: 'pdfHtml5', text: '<i class="fa fa-file"></i>', titleAttr: 'Pdf', className: 'btn btn-danger', footer: true }, 
				            { extend: 'print', text: '<i class="fa fa-print"></i>', titleAttr: 'Imprimir', className: 'btn btn-secondary', title: 'rptVentaVendedor', messageTop: 'Ventas registradas por Vendedor', footer: true }
				        ],
		                "ajax":{
	                 			"data": {
	                 				sucursal: me.selectedSucursal,
						        	inicio: String(me.selectedInicialFecha),
						        	final: String(me.selectedFinalFecha),
						        	vendedor: me.selectedVendedor
	                 			},
		                  "url": "/ventaVendedorDatatable",
		                  "dataType": "json",
		                  "type": "GET",
		                  "contentType": "application/json; charset=utf-8"
		                },
		                "columns": [
		                    { "data": "ITEM" },
		                    { "data": "CODIGO" },
		                    { "data": "CLIENTE" },
		                    { "data": "FECHA" },
		                    { "data": "HORA" },
		                    { "data": "VENDEDOR" },
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
			var tableVentaVendedor = $('#tablaVentaVendedor').DataTable();
        }
    }    
</script>
