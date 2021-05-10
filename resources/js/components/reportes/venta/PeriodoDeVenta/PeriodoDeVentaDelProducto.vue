<template>
	<!-- REPORTE PERIODO DE VENTA  -->
	<div v-if="$can('producto.mostrar')">
		<!-- INICIO DE TARJETA -->
		<div class="card mt-3 shadow border-bottom-primary" >
		  <div class="card-header">Periodo de Venta</div>
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

				  	<!-- -----------------------------------SWITCH STOCK------------------------------------- -->

					<div class="my-1 mb-3 mt-4">
						<div class="custom-control custom-switch mr-sm-2" >
							<input type="checkbox" class="custom-control-input" id="customControlAutosizing" v-model="switch_stock">
							<label class="custom-control-label" for="customControlAutosizing" data-toggle="tooltip" data-placement="top">Stock</label>
						</div>
					</div>
				</div>
				<div class="col-3">
					
					<!-- -------------------------------- FILTRO POR SECCION O PROVEEDOR ----------------------------------- -->

					<label>Filtrar Por</label>
				    <select v-model="selectedFiltro" class="custom-select custom-select-sm">
				  	  	<option value="SECCION">Sección</option>
				    	<option value="PROVEEDOR">Proveedor y Categoría</option>

				  	</select>
				  	<!-- -----------------------------------SELECT SECCION------------------------------------- -->

					<div class="mt-3" v-if="selectedFiltro === 'SECCION'">
						<label for="validationTooltip01">Seleccione Sección</label>
						<select class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSeccion }" v-model="selectedSeccion">
							<option value="null" selected>Seleccionar</option>
							<option v-for="seccion in secciones" :value="seccion.ID_SECCION">{{ seccion.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
						    {{messageInvalidSeccion}}
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

				<div class="col-md-3" v-if="selectedFiltro === 'PROVEEDOR'">
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

				<div class="col-md-3" v-if="selectedFiltro === 'SECCION'">
					<label for="validationTooltip03">Seleccione Categoría</label> 
					<select multiple class="form-control" size="5" v-model="selectedSeccionCategoria" :disabled="onCategoriaSeccion" v-bind:class="{ 'is-invalid': validarCategoriaSeccion }">
						<option v-for="categoriaSeccion in seccionCategorias" :value="categoriaSeccion.CODIGO">{{ categoriaSeccion.DESCRIPCION }}</option>
					</select>
					<div class="invalid-feedback">
					    {{messageInvalidCategoriaSeccion}}
					</div>
					<div class="custom-control custom-switch mt-3">
						<input type="checkbox" class="custom-control-input" id="customSwitch3" v-model="onCategoriaSeccion">
						<label class="custom-control-label" for="customSwitch3">Seleccionar todas las Categorías</label>
					</div>
				</div>

				<!-- -------------------------------------------MOSTRAR BOTONES--------------------------------------------- -->

			  </div>
				<div class="r-md-3">
					<button class="btn btn-dark btn-sm" type="submit" v-on:click="descargar()">Descargar</button> 
					<button class="btn btn-primary btn-sm" type="submit" v-on:click="llamarDatos">Generar</button> 
				</div>
					<!-- SPINNER DESCARGA -->
				<div class="row">
					<div class="col-md-12">
						<div v-if="descarga" class="d-flex justify-content-center mt-3">
							<strong>Descargando...   </strong>
			                <div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
			             </div>
		            </div>
			  	</div>
			<!-- FINAL DEL CUERPO -->
			<!-- CARD PARA CATEGORIA Y SUB -->
				<div class="col-md-12 mt-3">
					<table id="tablaVentaPeriodo" class="table mb-3" style="width:100%">
				    	<thead>
					      <tr> 
					        <th>Código</th>
					        <th>Imagen</th>
					        <th>Descripción</th>
			            	<th>Categoría</th>
			            	<th>Proveedor</th>
					        <th>Precio Venta</th>
					        <th>Precio Mayorista</th>
					        <th>Stock</th>
					        <th>Últ. Entrada</th>
					        <th>Últ. Movimiento</th>
					        <th>Últ. Venta</th>
					      </tr>
					    </thead>
					</table>			
				</div>
			</div>
		<!-- FINAL DE TARJETA -->
		</div>
	</div>

	<!-- ------------------------------------------------------------------------ -->

	<div v-else>
		<cuatrocientos-cuatro></cuatrocientos-cuatro>
	</div>

	<!-- REPORTE TOP VENTAS  -->
</template>

<script >
	export default {
      props: ['candec', 'descripcion'],
        data(){
            return {
          		codigo: '',
              	sucursales: [],
              	proveedores: [],
              	secciones: [],
              	categorias: [],
              	seccionCategorias: [],
              	selectedProveedor: [],
              	selectedCategoria: [],
              	selectedSeccionCategoria: [],
              	selectedSeccion: "null",
              	selectedSucursal: "null",
              	selectedInicialFecha: '',
              	selectedFinalFecha: '',
              	selectedTop: 10,
              	messageInvalidSucursal: '',
              	messageInvalidFecha: '',
              	messageInvalidSeccion: '',
              	messageInvalidProveedor: '',
              	messageInvalidCategoria: '',
              	messageInvalidCategoriaSeccion: '',
              	validarSucursal: false,
              	validarInicialFecha: false,
              	validarFinalFecha: false,
              	validarSeccion: false,
              	validarProveedor: false,
              	validarCategoria: false,
              	validarCategoriaSeccion: false,
              	datos: {},
              	descarga: false,
              	onProveedor: false,
              	onCategoria: false,
              	onCategoriaSeccion: false,
              	radioAgrupar: '',
              	controlar: true,
              	switch_stock: true,
              	selectedFiltro: 'SECCION'
            }
        }, 
        methods: {

        	llamarBusquedas(){	
	          axios.get('busquedas/').then((response) => {
	           	this.sucursales=response.data.sucursales;
	           	this.secciones = response.data.seccion;
	           	this.categorias = response.data.categorias;
	           	if((this.secciones).length > 1){

	           		this.seccionCategorias = response.data.categorias;
	           	}else{
	           		this.seccionCategorias = response.data.seccionCategorias;
	           	}
	           	this.proveedores = response.data.proveedores;
	          });
	        },

	        descargar(){
	        	
	        	let me = this;	
	        	
	        	if(this.generarConsulta() === true){
	        		me.descarga = true;
		        	axios({
					  url: '/export_venta_periodo',
					  method: 'POST',
					  data: me.datos,
					  responseType: 'blob', // important
					}).then((response) => {
						me.descarga = false;
					   const url = window.URL.createObjectURL(new Blob([response.data]));
					   const link = document.createElement('a');
					   link.href = url;
					   link.setAttribute('download', 'Venta_Periodo_'+me.selectedInicialFecha+' al '+me.selectedFinalFecha+'.xlsx'); //or any other extension
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
				
				if((me.onCategoriaSeccion === false && me.selectedSeccionCategoria.length===0) &&  me.selectedFiltro === 'SECCION'){

	        		me.validarCategoriaSeccion = true;
	        		me.messageInvalidCategoriaSeccion = 'Por favor seleccione una o varias Categorías';
	        		me.controlar=false;
	        	} else {
	        		me.validarCategoriaSeccion = false;
	        		me.messageInvalidCategoriaSeccion = '';
	        	}

	        	if((me.onCategoria === false && me.selectedCategoria.length===0) &&  me.selectedFiltro !== 'SECCION'){

	        		me.validarCategoria = true;
	        		me.messageInvalidCategoria = 'Por favor seleccione una o varias Categorías';
	        		me.controlar=false;
	        	} else {
	        		me.validarCategoria = false;
	        		me.messageInvalidCategoria = '';
	        	}

	        	if ((me.selectedSeccion === "null" || me.selectedSeccion === '') &&  me.selectedFiltro === 'SECCION'){
	        		me.validarSeccion = true;
	        		me.messageInvalidSeccion = 'Por favor seleccione la sección';
	        		me.controlar=false;
	        	} else {
	        		me.validarSeccion = false;
	        		me.messageInvalidSeccion = '';
	        	}

	        	if(me.onProveedor === false && me.selectedProveedor.length === 0) {
	        		me.messageInvalidProveedor = 'Por favor seleccione uno o varios Proveedores';
	        		me.validarProveedor = true;
	        		me.controlar=false;
	        	} else {
	        		me.messageInvalidProveedor = '';
	        		me.validarProveedor = false;
	        	}

	        	if(me.controlar===false){
	        		me.controlar=true;
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

	        	if(me.onCategoriaSeccion === true){
	        		for (var key in me.seccionCategorias){
	        			me.selectedSeccionCategoria[key] = me.seccionCategorias[key].CODIGO;
	        		}
	        	} 

	        	me.datos = {
		        	Sucursal: me.selectedSucursal,
		        	Inicio: me.selectedInicialFecha,
		        	Final: me.selectedFinalFecha,
		        	Categorias: me.selectedCategoria,
		        	CategoriaSeccion: me.selectedSeccionCategoria,
		        	AllCategory: me.onCategoria,
		        	AllCategorySeccion: me.onCategoriaSeccion,
		        	Stock: me.switch_stock,
		        	Seccion: me.selectedSeccion,
					Proveedores: me.selectedProveedor,
					AllProveedores: me.onProveedor,
					Filtro: me.selectedFiltro
	        	};

	        	return true;
	        },

	        llamarDatos(){

	        	let me = this;

		        if(me.generarConsulta() === true) {

		        	// PREPARAR DATATABLE 

		        	var tableVentaPeriodo = $('#tablaVentaPeriodo').DataTable({
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
	                 				datos: me.datos,
						        	"_token": $('meta[name="csrf-token"]').attr('content')
	                 			},
		                  "url": "/ventaPeriodoDatatable",
		                  "dataType": "json",
		                  "type": "POST"

		                },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "IMAGEN" },
                            { "data": "DESCRIPCION" },
                            { "data": "CATEGORIA" },
                            { "data": "PROVEEDOR" },
                            { "data": "PREC_VENTA" },
                            { "data": "PREMAYORISTA" },
                            { "data": "STOCK" },                            
                            { "data": "ULTIMA_ENTRADA" },
                            { "data": "ULTIMO_MOVIMIENTO" },
                            { "data": "ULTIMA_VENTA" },
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


			var tableVentaPeriodo = $('#tablaVentaPeriodo').DataTable();
        }
    }    
</script>