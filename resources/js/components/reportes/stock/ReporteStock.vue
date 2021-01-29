<template>
	
	<!-- REPORTE DE STOCK POR CATEGORIA Y SUB CATEGORIA -->
	<div class="container mt-3">
	  <!-- <div v-if="$can('reporte.stock')"> -->
		<div class="card shadow border-bottom-primary" >
		  	<div class="card-header">Stock Por Categoría y Sub-Categoría</div>
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

					<div class="col-md-4">
						<label for="validationTooltip01">Seleccione Categoria</label> 
						<select multiple class="form-control" size="8" v-model="selectedCategoria" :disabled="onCategoria" v-bind:class="{ 'is-invalid': validarCategoria }">
						  <option v-for="categoria in categorias" :value="categoria.CODIGO">{{ categoria.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
					        {{messageInvalidCategoria}}
					    </div>
						<div class="custom-control custom-switch mt-3">
						  <input type="checkbox" class="custom-control-input" id="customSwitch2" v-on:click="todasCategorias">
						  <label class="custom-control-label" for="customSwitch2">Seleccionar todas las Categorias</label>
						</div>
					</div>

					<div class="col-md-4">
						<label for="validationTooltip01">Seleccione Sub Categoría</label> 
						<select multiple class="form-control" size="8" v-model="selectedSubCategoria" :disabled="onSubCategoria" v-bind:class="{ 'is-invalid': validarSubCategoria }">
						   <option v-for="subCategoria in subCategorias" :value="subCategoria.CODIGO">{{ subCategoria.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
					        {{messageInvalidSubCategoria}}
					    </div>
						<div class="custom-control custom-switch mt-3">
						  <input type="checkbox" class="custom-control-input" id="customSwitch1" v-on:click="todoSubCategorias">
						  <label class="custom-control-label" for="customSwitch1" >Seleccionar todas las Sub Categoría</label>
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

	  <!-- <div v-else>
			<cuatrocientos-cuatro></cuatrocientos-cuatro>
	  </div> -->
	</div>

	<!-- FIN REPORTE DE STOCK POR CATEGORIA Y SUB CATEGORIA-->
</template>

<script >
	export default {
        data(){
            return {
              	sucursales: [],
              	categorias: [],
              	subCategorias: [],
              	selectedSucursal: 'null',
              	selectedCategoria: [],
              	selectedSubCategoria: [],
              	selectedInicialFecha: '',
              	selectedFinalFecha: '',
              	switch_stock: true,
              	onSubCategoria: false,
              	onCategoria: false,
              	messageInvalidSucursal: '',
              	messageInvalidCategoria: '',
              	messageInvalidSubCategoria: '',
              	messageInvalidFecha: '',
              	validarSucursal: false,
              	validarCategoria: false,
              	validarSubCategoria: false,
              	validarInicialFecha: false,
              	validarFinalFecha: false,
              	datos: {},
              	descarga: false,
              	controlar: false
            }
        }, 
        methods: {
            llamarBusquedas(){	
	          axios.get('busquedas/').then((response) => {
	           	this.sucursales = response.data.sucursales;
	           	this.subCategorias = response.data.subCategorias;
	           	this.categorias = response.data.categorias;
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

	        todoSubCategorias(e){
	        	this.onSubCategoria = !this.onSubCategoria;
	        },

	        todasCategorias(e){
	        	this.onCategoria = !this.onCategoria;
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

	        	if(me.onCategoria === false && me.selectedCategoria.length===0){

	        		me.validarCategoria = true;
	        		me.messageInvalidCategoria = 'Por favor seleccione una o varias Categorías';
	        		me.controlar=true;
	        	} else {
	        		me.validarCategoria = false;
	        		me.messageInvalidCategoria = '';
	        	}

	        	if(me.onSubCategoria === false && me.selectedSubCategoria.length === 0){
	        		me.validarSubCategoria = true;
	        		me.messageInvalidSubCategoria = 'Por favor seleccione una o varias Sub Categorías';
	        		me.controlar=true;
	        	} else {
	   
	        		me.validarSubCategoria = false;
	        		me.messageInvalidSubCategoria = '';
	        	}

	        	if(me.controlar===true){
	        		return false;
	        	}

	        	if(me.onCategoria === true){
	        		for (var key in me.categorias){
	        			me.selectedCategoria[key] = me.categorias[key].CODIGO;
	        		}
	        	} 

	        	if(me.onSubCategoria === true){
	        		for (var key in me.subCategorias){
	        			me.selectedSubCategoria[key] = me.subCategorias[key].CODIGO;
	        		}
	        	}

	        	me.datos = {
		        	Sucursal: me.selectedSucursal,
		        	SubCategorias: me.selectedSubCategoria,
		        	Categorias: me.selectedCategoria,
		        	AllSubCategory: me.onSubCategoria,
		        	AllCategory: me.onCategoria,
		        	Stock: me.switch_stock
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
