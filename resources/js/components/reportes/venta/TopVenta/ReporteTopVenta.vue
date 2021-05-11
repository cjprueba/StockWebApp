<template>
	<!-- REPORTE TOP VENTAS  -->
	<div v-if="$can('producto.mostrar')">
		<!-- INICIO DE TARJETA -->
		<div class="card mt-3 shadow border-bottom-primary" >
		  <div class="card-header">Top En Ventas</div>
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

					<!-- ----------------------------------------SELECCIONAR TOP---------------------------------------- -->
					
					<label class="mt-3">Seleccione Top</label>
			    	<select v-model="selectedTop" class="custom-select custom-select-sm">
			  	  		<option value=10>TOP 10</option>
			  	  		<option value=30>TOP 30</option>
			    		<option value=50>TOP 50</option>
			  	  		<option value=70>TOP 70</option>
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

					<!-- -------------------------------- FILTRO POR SECCION O PROVEEDOR ----------------------------------- -->

					<label>Filtrar Por</label>
			    	<select v-model="selectedFiltro" class="custom-select custom-select-sm">
			  	  		<option value="SECCION">Sección</option>
			    		<option value="PROVEEDOR">Proveedor</option>
			  		</select>
			  		<!-- ----------------------------------- SELECT SECCION ------------------------------------- -->

	                <div class="mt-3" v-if="selectedFiltro === 'SECCION'">
						<label for="validationTooltip01">Seleccione Sección</label>
						<select class="custom-select custom-select-sm" v-model="selectedSeccion" v-bind:class="{ 'is-invalid': validarSeccion }">
							<option value="null">Seleccionar</option>
							<option v-for="seccion in secciones" :value="seccion.ID_SECCION">{{ seccion.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
							{{messageInvalidSeccion}}
						</div>
					</div>

					<!-- ---------------------------------------- RADIO AGRUPAR ---------------------------------------- -->

					<label for="validationTooltip01" class="mt-3">Agrupar por:</label> 
					<div class="form-check form-check">
	  		            <input v-model="radioAgrupar" v-on:change="" class="form-check-input" type="radio" name="radioOptions" id="radio0" value=true v-bind:class="{ 'is-invalid': validarRadio }">
	  		            <label class="form-check-label" for="radio0">Cantidad</label>
	  		        </div>
	  		        <div class="form-check form-check">
	  		            <input  v-model="radioAgrupar"  v-on:change="" class="form-check-input" type="radio" name="radioOptions" id="radio1" value=false v-bind:class="{ 'is-invalid': validarRadio }">
	  		            <label class="form-check-label" for="radio1">Monto</label>
	  		        </div>

					<div class="custom-control custom-switch mr-sm-2 mt-3" >
						<input type="checkbox" class="custom-control-input" id="customControlAutosizing" v-model="switch_stock" v-on:change="marcarTodo">
						<label class="custom-control-label" for="customControlAutosizing" data-toggle="tooltip" data-placement="top">Stock</label>
					</div>
				</div>

                

				<!-- ------------------------------------------- PROVEEDOR ----------------------------------------------- -->
				
				<div class="col-md-3">
					<label>Seleccione Proveedor</label> 
					<div class="container_checkbox1 rounded mr-2">
	                    <div class="mt-2 mb-2 pl-2" v-for="proveedor in proveedores" >
	                      <div class="custom-control custom-checkbox">
	                        <input type="checkbox" class="custom-control-input" :value="proveedor.CODIGO" :id="proveedor.CODIGO" v-model="selectedProveedor" v-bind:class="{ 'is-invalid': validarProveedor }" :disabled="onProveedor">
	                        <label class="custom-control-label" :for="proveedor.CODIGO">{{proveedor.NOMBRE}} </label>
	                      </div>
	                    </div>
	                </div>
						<div class="invalid-feedback">
					        {{messageInvalidProveedor}}
					    </div>
					<div class="custom-control custom-switch mt-3">
						<input type="checkbox" class="custom-control-input" id="customSwitch2" v-model="onProveedor">
						<label class="custom-control-label" for="customSwitch2">Seleccionar todos</label>
					</div>
                </div> 
				<!-- -------------------------------------------MOSTRAR BOTONES----------------------------------------------- -->

				<div class="col-md-3 mt-4">
					<button class="btn btn-primary btn-sm" type="submit" v-on:click="llamarDatos">Generar</button> 
				</div>

	    		<!-- ------------------------------------------- DATATABLE ------------------------------------------- -->
				<div class="col-md-12 mt-3">
					<table id="tablaVentaTop" class="table mb-3" style="width:100%">
			            <thead>
			                <tr>
			                    <th>#</th>
				      			<th>Código</th>
				      			<th width="25%">Descripción</th>
				      			<th width="25%">Categoría</th>
				      			<th class="vendidoColumna">Vendido</th>
				      			<th class="precioColumna">Precio</th>
			                    <th class="totalColumna">Total</th>
				      			<th class="utilidadColumna">Utilidad</th>
				      			<th class="descuentoColumna">Descuento</th>
				      			<th class="cantidadColumna">Stock</th>
			                </tr>
			            </thead>
			            <tfoot>
			            	<tr>
			            		<th></th>
			            		<th></th>
			            		<th></th>
				            	<th><strong>TOTALES</strong></th>
				            	<th></th>
				            	<th></th>
				            	<th></th>
				            	<th></th>  
				            	<th></th> 
				            	<th></th> 
				            </tr>
			            </tfoot>
			        </table> 
	    			<!-- -------------------------------------------FIN TABLA------------------------------------------- -->
				</div>
			  </div>
			  
			<!-- FINAL DEL CUERPO -->
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
              	secciones: [],
              	selectedProveedor: [],
              	selectedSeccion: "null",
              	selectedSucursal: "null",
              	selectedTop: 10,
              	messageInvalidSucursal: '',
              	messageInvalidFecha: '',
              	messageInvalidSeccion: '',
              	messageInvalidProveedor: '',
              	selectedInicialFecha: '',
              	selectedFinalFecha: '',
              	validarSucursal: false,
              	validarInicialFecha: false,
              	validarFinalFecha: false,
              	validarRadio: false,
              	validarSeccion: false,
              	validarProveedor: false,
              	cargado: false,
              	onProveedor: false,
              	radioAgrupar: '',
              	controlar: true,
              	switch_stock: false,
              	selectedFiltro: 'SECCION'
            }
        }, 
        methods: {
        	marcarTodo(){

				let me = this;

	      		if(me.onProveedor === true) {
			        for (var key in me.proveedores){
			        	me.selectedProveedor[key] = me.proveedores[key].ID;
			        }
			    }else{

			    }
        	},

        	llamarBusquedas(){	
	          axios.get('busquedas/').then((response) => {
	           	this.sucursales=response.data.sucursales;
	           	this.proveedores = response.data.proveedores;
	           	this.secciones = response.data.seccion;
	          });
	        },

	        llamarDatos(){
	        	let me = this;	
	        	
	        	if(me.generarConsulta() === true) {
	        		
	        		me.cargado = true;

	        		// PREPARAR DATATABLE 

			 		var tableVentaTop = $('#tablaVentaTop').DataTable({
		                "processing": true,
		                "serverSide": true,
		                "destroy": true,
		                "bAutoWidth": true,
                		"searching": false,
	                 	"paging": false,
                        "select": true,
    					"ordering":  false,
                        "dom": "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
						"<'row'<'col-sm-12'tr>>" +
						"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
				        "buttons": [
				            { extend: 'copy', text: '<i class="fa fa-copy"></i>', titleAttr: 'Copiar', className: 'btn btn-secondary', footer: true },
				        	{ extend: 'excelHtml5', text: '<i class="fa fa-file"></i>', titleAttr: 'Excel', className: 'btn btn-success', footer: true },
				            { extend: 'pdfHtml5', text: '<i class="fa fa-file"></i>', titleAttr: 'Pdf', className: 'btn btn-danger', footer: true }, 
				            { extend: 'print', text: '<i class="fa fa-print"></i>', titleAttr: 'Imprimir', className: 'btn btn-secondary', title: 'rptVentaTop', messageTop: 'TOP DE VENTAS REGISTRADAS', footer: true }
				        ],
		                "ajax":{
	                 			"data": {
						        	Sucursal: me.selectedSucursal,
						        	Inicio: String(me.selectedInicialFecha),
						        	Final: String(me.selectedFinalFecha),
						        	Proveedores: me.selectedProveedor,
						        	Seccion: me.selectedSeccion,
						        	AllProveedores: me.onProveedor,
						        	Agrupar: me.radioAgrupar,
						        	Stock: me.switch_stock,
				        			filtro: me.selectedFiltro,
				        			Top: me.selectedTop,
						        	"_token": $('meta[name="csrf-token"]').attr('content')
	                 			},
		                  "url": "/ventaTopDatatable",
		                  "dataType": "json",
		                  "type": "POST"

		                },
		                "columns": [
		                    { "data": "ITEM" },
		                    { "data": "COD_PROD" },
		                    { "data": "DESCRIPCION" },
		                    { "data": "CATEGORIA" },
		                    { "data": "VENDIDO" },
		                    { "data": "PRECIO" },
		                    { "data": "TOTAL" },
		                    { "data": "UTILIDAD" },
		                    { "data": "DESCUENTO" },
		                    { "data": "CANTIDAD" }
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

					        //CANTIDAD

						  	api.columns('.cantidadColumna', {}).every(function() {

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

							    $( api.columns('.cantidadColumna').footer() ).html(
						            Common.darFormatoCommon(sum, 0)
						        );

						  	});

					        //UTILIDAD

					        api.columns('.utilidadColumna', {}).every(function() {

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

							    $( api.columns('.utilidadColumna').footer() ).html(
						            Common.darFormatoCommon(sum, this.candec)
						        ); 
						  	});

						  	//DESCUENTO

					        api.columns('.descuentoColumna', {}).every(function() {

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

							    $( api.columns('.descuentoColumna').footer() ).html(
						            Common.darFormatoCommon(sum, this.candec)
						        ); 
						  	});

						  	//PRECIO 

					        api.columns('.precioColumna', {}).every(function() {

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

							    $( api.columns('.precioColumna').footer() ).html(
						            Common.darFormatoCommon(sum, this.candec)
						        ); 
						  	});

						  	//VENDIDO 

					        api.columns('.vendidoColumna', {}).every(function() {

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

							    $( api.columns('.vendidoColumna').footer() ).html(
						            Common.darFormatoCommon(sum, 0)
						        ); 
						  	});

						}

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

	        	if(me.selectedInicialFecha === "null" || me.selectedInicialFecha === "") {
	        		me.validarInicialFecha = true;
	        		me.messageInvalidFecha = 'Por favor seleccione una fecha';
	        		me.controlar = false;
	        	} else {
	        		me.validarInicialFecha = false;
	        		me.messageInvalidFecha = '';
	        	}

	        	if(me.selectedFinalFecha === "null" || me.selectedFinalFecha === "") {
	        		me.validarFinalFecha = true;
	        		me.messageInvalidFecha = 'Por favor seleccione una fecha';
	        		me.controlar = false;
	        	} else {
	        		me.validarFinalFecha = false;
	        		me.messageInvalidFecha = '';
	        	}

	        	if(me.onProveedor === false && me.selectedProveedor.length === 0) {
	        		me.messageInvalidProveedor = 'Por favor seleccione uno o varios Proveedores';
	        		me.validarProveedor = true;
	        		me.controlar=false;
	        	} else {
	        		me.messageInvalidProveedor = '';
	        		me.validarProveedor = false;
	        	}

	        	if ((me.selectedSeccion === "null" || me.selectedSeccion === '') && me.selectedFiltro === 'SECCION'){
	        		me.validarSeccion = true;
	        		me.messageInvalidSeccion = 'Por favor seleccione la sección';
	        		me.controlar=false;
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
	        		return false;
	        	}

		        if(me.onProveedor === true) {
		        	for (var key in me.proveedores){
		        		me.selectedProveedor[key] = me.proveedores[key].CODIGO;
		        	}
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
			var tableVentaTop = $('#tablaVentaTop').DataTable();
        }
    }    
</script>
<style>
	.container_checkbox1 { 
		border:2px solid #ccc; 
		height: 224px; 
		overflow-y: scroll; 
	}
</style>