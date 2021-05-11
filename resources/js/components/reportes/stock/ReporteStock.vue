<template>
	
	<!-- REPORTE DE STOCK POR CATEGORIA Y PROVEEDOR -->
	<div class="container-fluid mt-3">
	  <!-- <div v-if="$can('reporte.stock')"> -->
		<div class="card shadow border-bottom-primary" >
		  	<div class="card-header">Stock Por Proveedor y Categoría</div>
			<div class="card-body">
			  	<div class="form-row">

					<!-- ------------------------------------------------------------------------------------- -->

			  		<div class="col-md-4 mb-3">

			  			<label for="validationTooltip01">Seleccione Sucursal</label>
						<select class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSucursal }" v-model="selectedSucursal">
							 <option value="null" selected>Seleccionar</option>
							 <option v-for="sucursal in sucursales" :value="sucursal.CODIGO">{{ sucursal.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
					        {{messageInvalidSucursal}}
					    </div>

					    <!-- -------------------------------- FILTRO POR SECCION O PROVEEDOR ----------------------------------- -->

						<label class="mt-3">Filtrar Por</label>
				    	<select v-model="selectedFiltro" class="custom-select custom-select-sm">
				  	  		<option value="SECCION">Sección</option>
				    		<option value="PROVEEDOR">Proveedor y Categoría</option>
				  		</select>

						<!-- -----------------------------------SELECT SECCION------------------------------------- -->

						<div v-if="selectedFiltro === 'SECCION'">
						    <label class="mt-3" for="validationTooltip01">Seleccione Sección</label>
							<select class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSeccion }" v-model="selectedSeccion">
								 <option value="null" selected>Seleccionar</option>
								 <option v-for="seccion in secciones" :value="seccion.ID_SECCION">{{ seccion.DESCRIPCION }}</option>
							</select>
							<div class="invalid-feedback">
						        {{messageInvalidSeccion}}
						    </div>
						</div>

					   	<div class="my-1 mb-3 mt-4">
							<div class="custom-control custom-switch mr-sm-2" >
								<input type="checkbox" class="custom-control-input" id="customControlAutosizing" v-model="switch_stock">
								<label class="custom-control-label" for="customControlAutosizing" data-toggle="tooltip" data-placement="top" title="Permite el paso de productos repetidos en la tabla sin la necesidad de agregar nuevamente los datos ya ingresados del anterior agregado">Stock</label>
							</div>
						</div>

					    <div class="mt-4">
					    	<button class="btn btn-dark btn-sm" type="submit" v-on:click="descargar()"><font-awesome-icon icon="download" /> Descargar</button>
					    </div>
					</div>

					<!-- ------------------------------------------- PROVEEDOR ----------------------------------------------- -->
				
					<div class="col-md-3 ml-3">
						<label for="validationTooltip01">Seleccione Proveedor</label> 
						<select multiple class="form-control" size="8" v-model="selectedProveedor" :disabled="onProveedor" v-bind:class="{ 'is-invalid': validarProveedor }">
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

					<div class="col-md-4 ml-3" v-if="selectedFiltro === 'PROVEEDOR'">
						<label for="validationTooltip02">Seleccione Categoría</label> 
						<select multiple class="form-control" size="8" v-model="selectedCategoria" :disabled="onCategoria" v-bind:class="{ 'is-invalid': validarCategoria }">
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

					<div class="col-md-4 ml-3" v-if="selectedFiltro === 'SECCION'">
						<label for="validationTooltip03">Seleccione Categoría</label> 
						<select multiple class="form-control" size="8" v-model="selectedSeccionCategoria" :disabled="onCategoriaSeccion" v-bind:class="{ 'is-invalid': validarCategoriaSeccion }">
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
				</div>
			</div>
		</div>

		<!-- CARD PARA CATEGORIA Y SUB -->

		<div class="row">

			<!-- SPINNER DESCARGA -->

			<div class="col-md-12">
				<div v-if="descarga" class="d-flex justify-content-center mt-3">
					<strong>Descargando...   </strong>
	                <div class="spinner-border text-success" role="status" aria-hidden="true"></div>
	             </div>
            </div>

	  	</div>
	  </div>
		<!-- ------------------------------------------------------------------------ -->

	 <!--  <div v-else>
			<cuatrocientos-cuatro></cuatrocientos-cuatro>
	  </div> -->
	</div>

	<!-- FIN REPORTE DE STOCK POR CATEGORIA Y PROVEEDOR-->
</template>

<script >
	export default {
        data(){
            return {
              	sucursales: [],
              	categorias: [],
              	seccionCategorias: [],
              	selectedSucursal: 'null',
              	secciones: [],
              	selectedProveedor: [],
              	selectedCategoria: [],
              	selectedSeccionCategoria: [],
              	selectedInicialFecha: '',
              	selectedFinalFecha: '',
              	switch_stock: true,
              	onCategoria: false,
              	onCategoriaSeccion: false,
              	messageInvalidSucursal: '',
              	messageInvalidCategoria: '',
              	messageInvalidCategoria: '',
              	messageInvalidFecha: '',
              	messageInvalidCategoriaSeccion: '',
              	validarSucursal: false,
              	validarCategoria: false,
              	validarCategoriaSeccion: false,
              	validarInicialFecha: false,
              	validarFinalFecha: false,
              	datos: {},
              	descarga: false,
              	controlar: false,
              	proveedores: [],
              	selectedSeccion: "null",
              	messageInvalidSeccion: '',
              	messageInvalidProveedor: '',
              	validarSeccion: false,
              	validarProveedor: false,
              	onProveedor: false,
              	selectedFiltro: 'PROVEEDOR'
            }
        }, 
        methods: {
            llamarBusquedas(){	
	          axios.get('busquedas/').then((response) => {
	           	this.sucursales = response.data.sucursales;
	           	this.categorias = response.data.categorias;
	           	this.secciones = response.data.seccion;
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
	        	if(this.generarConsulta() === true) {
	        		me.descarga = true;
		        	axios({
					  url: '/export/Stock/Image',
					  method: 'POST',
					  data: me.datos,
					  responseType: 'blob', // important
					}).then((response) => {
						me.descarga = false;
					   const url = window.URL.createObjectURL(new Blob([response.data]));
					   const link = document.createElement('a');
					   link.href = url;
					   link.setAttribute('download', 'Stock-'+me.selectedInicialFecha+'-'+me.selectedFinalFecha+'.xlsx'); //or any other extension
					   document.body.appendChild(link);
					   link.click();
					});
				}
				me.controlar = false;
	        },

	        clicked(row) {
	       	  this.categoriaTitulo = row.CATEGORIA; 	
		      this.datosFilas = row.CODIGO;
		    },

	        filterItems: function(items, codigo) {
			      return items.filter(function(item) {
			      return item.CATEGORIA === codigo;
			    })
			},

	        generarConsulta(){
	        	
	        	let me = this;

	        	if (me.selectedSucursal === "null" || me.selectedSucursal === ''){
	        		me.validarSucursal = true;
	        		me.messageInvalidSucursal = 'Por favor seleccione sucursal';
	        		me.controlar=true;
	        	} else {
	        		me.validarSucursal = false;
	        		me.messageInvalidSucursal = '';
	        	}

	        	if((me.onCategoriaSeccion === false && me.selectedSeccionCategoria.length===0) &&  me.selectedFiltro === 'SECCION'){

	        		me.validarCategoriaSeccion = true;
	        		me.messageInvalidCategoriaSeccion = 'Por favor seleccione una o varias Categorías';
	        		me.controlar=true;
	        	} else {
	        		me.validarCategoriaSeccion = false;
	        		me.messageInvalidCategoriaSeccion = '';
	        	}

	        	if((me.onCategoria === false && me.selectedCategoria.length===0) &&  me.selectedFiltro !== 'SECCION'){

	        		me.validarCategoria = true;
	        		me.messageInvalidCategoria = 'Por favor seleccione una o varias Categorías';
	        		me.controlar=true;
	        	} else {
	        		me.validarCategoria = false;
	        		me.messageInvalidCategoria = '';
	        	}

	        	if ((me.selectedSeccion === "null" || me.selectedSeccion === '') &&  me.selectedFiltro === 'SECCION'){
	        		me.validarSeccion = true;
	        		me.messageInvalidSeccion = 'Por favor seleccione la sección';
	        		me.controlar=true;
	        	} else {
	        		me.validarSeccion = false;
	        		me.messageInvalidSeccion = '';
	        	}

	        	if(me.onProveedor === false && me.selectedProveedor.length === 0) {
	        		me.messageInvalidProveedor = 'Por favor seleccione uno o varios Proveedores';
	        		me.validarProveedor = true;
	        		me.controlar=true;
	        	} else {
	        		me.messageInvalidProveedor = '';
	        		me.validarProveedor = false;
	        	}

	        	if(me.controlar===true){
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
