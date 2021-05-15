<template>
		<!-- REPORTE DE VENCIMIENTO DE PRODUCTOS -->
	<div>
		<div class="card mt-3 shadow border-bottom-primary mb-3">
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
						<button class="btn btn-primary btn-sm" type="submit" v-on:click="llamarDatos">Generar</button> 
						</div>
					</div>

					<!-- -------------------------------------------MOSTRAR DOWNLOADING----------------------------------------------- -->

				    <div class="col-md-12">
						<div v-if="descarga" class="ml-5 d-flex justify-content-center mt-3">
							Descargando...
				            <div class="spinner-grow text-primary" role="status" aria-hidden="true"></div>
				        </div>
			        </div>
			        <div class="col-md-12 mt-3">
					<table id="tablaVencimientoProducto" class="table mb-3" style="width:100%">
				    	<thead>
					      <tr> 
					        <th>Código</th>
					        <th>Imagen</th>
					        <th>Descripción</th>
			            	<th>Categoría</th>
			            	<th>Proveedor</th>
					        <th>Últ. Entrada</th>
					        <th>Fecha Vencimiento</th>
					        <th>Lote</th>
					        <th>Cantidad Inicial</th>
					        <th>Cantidad Actual</th>
					        <th>Prec. Venta</th>
					        <th>Prec. Mayorista</th>
					      </tr>
					    </thead>
					</table>			
				</div>
				</div>
			</div>
		  </div>
		</div>

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
	        		me.controlar = true;
	        		return false;
	        	}

				me.datos = {
		        	Sucursal: me.selectedSucursal,
		        	Inicio: me.selectedInicialFecha,
		        	Final: me.selectedFinalFecha
	        	};

	        	return true;
	        },

	        llamarDatos(){

	        	let me = this;

		        if(me.generarConsulta() === true) {

		        	// PREPARAR DATATABLE 

		        	var tableVencimientoProducto = $('#tablaVencimientoProducto').DataTable({
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
				            { extend: 'print', text: '<i class="fa fa-print"></i>', titleAttr: 'Imprimir', className: 'btn btn-secondary', title: 'rptvencimiento', messageTop: 'Ventas registradas por Vendedor', footer: true }
				        ],
		                "ajax":{
	                 			"data": {
	                 				datos: me.datos,
						        	"_token": $('meta[name="csrf-token"]').attr('content')
	                 			},
		                  "url": "/vencimientoProductoDatatable",
		                  "dataType": "json",
		                  "type": "POST"

		                },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "IMAGEN" },
                            { "data": "DESCRIPCION" },
                            { "data": "CATEGORIA" },
                            { "data": "PROVEEDOR" },                      
                            { "data": "ULTIMA_ENTRADA" },
                            { "data": "FECHA_VENCIMIENTO" }, 
                            { "data": "LOTE" }, 
                            { "data": "CANTIDAD_INICIAL" },
                            { "data": "STOCK" },  
                            { "data": "PREC_VENTA" },
                            { "data": "PREMAYORISTA" },    
                        ],
                        "footerCallback": function(row, data, start, end, display) {
						}     
                    });

		        }
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
			var tableVencimientoProducto = $('#tablaVencimientoProducto').DataTable();
        }
    }    
</script>
