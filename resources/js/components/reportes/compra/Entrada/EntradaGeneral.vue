<template>
	<div>
		
	
	<!-- INICIO DE TARJETA -->
		<div class="card mt-3 shadow border-bottom-primary" >
			<div class="card-header">Entrada General</div>
				<!-- CUERPO DE LA TARJETA -->
				<div class="card-body">
					<div class="row">
						<div class="col-4">

							<!-- -------------------------------------------SELECCIONAR SUCURSAL---------------------------------------- -->
							<label for="validationTooltip01">Seleccione Sucursal</label>
							<select v-on:change="habilitar_insert" class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSucursal }" v-model="selectedSucursal">
								<option value="null">Seleccionar</option>
								<option v-for="sucursal in sucursales" :value="sucursal.CODIGO">{{ sucursal.DESCRIPCION }}</option>
							</select>
							<div class="invalid-feedback">
								{{messageInvalidSucursal}}
							</div>
							<!--FIN--------------------------------------------------------------------------------------------------------------->

							<!-- ----------------------------------------SELECCIONAR FECHA---------------------------------------- -->
							<label class="mt-5">Seleccione Intervalo de Tiempo</label>
							<div id="sandbox-container" class="input-daterange input-group">
								<input v-on:change="habilitar_insert" id='selectedInicialFecha' class="input-sm form-control form-control-sm" v-model="selectedInicialFecha" v-bind:class="{ 'is-invalid': validarInicialFecha }"/>
								<div class="input-group-append form-control-sm">
									<span>a</span>
								</div>
								<input v-on:change="habilitar_insert" name='end' id='selectedFinalFecha' class="input-sm form-control form-control-sm" v-model="selectedFinalFecha" v-bind:class="{ 'is-invalid': validarFinalFecha }"/>
								<div class="invalid-feedback">
								    {{messageInvalidFecha}}
								</div>
							</div>
							<!--FIN--------------------------------------------------------------------------------------------------------------->

							<!-- ----------------------------------------SELECCIONAR FORMA DE AGRUPAR---------------------------------------- -->
							<div class="row" >	
								<div class="col-12 mt-5" align="center">
									<label class="mr-5">Agrupar por:</label>
								</div>
								<div class="col-12 ml-5">
									<div class="ml-4 form-check">
									  <input  v-model="agruparMarca" class="form-check-input" type="checkbox" id="mar">
									  <label class="form-check-label" for="mar">Marca</label>
									</div>
									<div class="ml-4 form-check">
									  <input  v-model="agruparCategoria" class="form-check-input" type="checkbox" id="cat">
									  <label class="form-check-label" for="cat">Categoría</label>
									</div>
									<div class="ml-4 form-check">
									  <input  v-model="agruparSubCategoria" class="form-check-input" type="checkbox" id="subcat">
									  <label class="form-check-label" for="subcat">Sub-Categoría</label>
									</div>
									<div class="ml-4 form-check">
									  <input  v-model="agruparGenero" class="form-check-input" type="checkbox" id="gen">
									  <label class="form-check-label" for="gen">Género</label>
									</div>
								</div>
			    				<div class="col-sm-12">
									<div class="form-text text-danger">{{messageInvalidAgrupar}}</div>
								</div>
							</div>
							<!--FIN--------------------------------------------------------------------------------------------------------------->

						</div>

						<div class="col-7 ">
							<div class="row">

								<!-- ----------------------------------------SELECCIONAR MARCAS---------------------------------------- -->
								<div class="col-6">
									<div>
										<label for="validationTooltip01">Seleccione Marcas</label> 
										<select multiple class="form-control" size="5" v-model="selectedMarca" :disabled="onMarca" v-bind:class="{ 'is-invalid': validarMarca }">
										   <option v-for="marca in marcas" :value="marca.CODIGO">{{ marca.DESCRIPCION }}</option>
										</select>
										<div class="invalid-feedback">
									        {{messageInvalidMarca}}
									    </div>
										<div class="custom-control custom-switch mt-1">
										  <input type="checkbox" class="custom-control-input" id="customSwitch1" v-on:click="todasMarcas">
										  <label class="custom-control-label" for="customSwitch1" >Seleccionar todas las Marcas</label>
										</div>
									</div>
								</div>
								<!--FIN--------------------------------------------------------------------------------------------------------------->

								<!-- ----------------------------------------SELECCIONAR CATEGORÍA---------------------------------------- -->
								<div class="col-6">
									<div>
										<label for="validationTooltip01">Seleccione Categoria</label> 
										<select multiple class="form-control" size="5" v-model="selectedCategoria" :disabled="onCategoria" v-bind:class="{ 'is-invalid': validarCategoria }">
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
								</div>
								<!--FIN--------------------------------------------------------------------------------------------------------------->
							</div>

							<div class="row mt-4">

								<!-- ----------------------------------------SELECCIONAR SUB CATEGORÍA---------------------------------------- -->
								<div class="col-6">
									<div>
										<label for="validationTooltip01">Seleccione Sub Categoría</label> 
										<select v-on:change="habilitar_insert" multiple class="form-control" size="5" v-model="selectedSubCategoria" :disabled="onSubCategoria" v-bind:class="{ 'is-invalid': validarSubCategoria }">
										   <option v-for="subCategoria in subCategorias" :value="subCategoria.CODIGO">{{ subCategoria.DESCRIPCION }}</option>
										</select>
										<div class="invalid-feedback">
									        {{messageInvalidSubCategoria}}
									    </div>
										<div   class="custom-control custom-switch mt-1">
										  <input v-on:change="habilitar_insert" type="checkbox" class="custom-control-input" id="customSwitch3" v-on:click="todasSubCategorias">
										  <label class="custom-control-label" for="customSwitch3" >Seleccionar todas las Sub Categorías</label>
										</div>
									</div>
								</div>
								<!--FIN--------------------------------------------------------------------------------------------------------------->

								<!-- ----------------------------------------SELECCIONAR GÉNERO---------------------------------------- -->
								<div class="col-6">
									<div>
										<label for="validationTooltip01">Seleccione Género</label> 
										<select multiple class="form-control" size="5" v-model="selectedGenero" :disabled="onGenero" v-bind:class="{ 'is-invalid': validarGenero }">
										   <option v-for="genero in generos" :value="genero.CODIGO">{{ genero.DESCRIPCION }}</option>
										</select>
										<div class="invalid-feedback">
									        {{messageInvalidGenero}}
									    </div>
										<div class="custom-control custom-switch mt-1">
										  <input type="checkbox" class="custom-control-input" id="customSwitch4" v-on:click="todasGeneros">
										  <label class="custom-control-label" for="customSwitch4" >Seleccionar todas los Géneros</label>
										</div>
									</div>
								</div>
								<!--FIN--------------------------------------------------------------------------------------------------------------->
							</div>

						</div>

					</div>
					<hr>

					<!-- -------------------------------------- BOTON DESCARGAR--------------------------------------------- -->
					<div class="row">
						<div class="r-md-3 col-4">
							<button class="btn btn-dark btn-sm" type="submit" v-on:click="descargar()">Descargar</button> 
							<button class="btn btn-primary btn-sm" type="submit" v-on:click="llamarDatos">Generar</button> 
						</div>
					</div>
					<!--FIN--------------------------------------------------------------------------------------------------------------->

				</div>
			</div>
		</div>
	</div>
</template>
<script>
	export default {
		data(){
            return {
              	sucursales: [],
              	marcas: [],
              	categorias: [],
              	subCategorias: [],
              	generos: [],
              	selectedMarca: [],
              	selectedCategoria: [],
              	selectedSubCategoria: [],
              	selectedGenero: [],
              	selectedSucursal: "null",
              	selectedInicialFecha: '',
              	selectedFinalFecha: '',
              	validarSucursal: false,
              	validarFinalFecha: false,
              	validarInicialFecha: false,
              	validarSubCategoria: false,
              	validarMarca: false,
              	validarCategoria: false,
              	validarSubCategoria: false,
              	validarGenero: false,
              	messageInvalidFecha: '',
              	messageInvalidAgrupar: '',
              	messageInvalidSucursal: '',
              	messageInvalidMarca: '',
              	messageInvalidCategoria: '',
              	messageInvalidSubCategoria: '',
              	messageInvalidGenero: '',
              	agruparMarca: false,
              	agruparCategoria: false,
              	agruparSubCategoria: false,
              	agruparGenero: false,
              	onMarca: false,
              	onCategoria: false,
              	onSubCategoria: false,
              	onGenero: false,
              	insert:true,
              	controlar: true,
              	
            }
        },
        methods:{
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
	        	if(me.agruparMarca === false && me.agruparMarca === false) {
	        		me.messageInvalidAgrupar = 'Por favor seleccione tipo de agrupación.';
	        		me.controlar = false;
	        	} else {
	        		me.messageInvalidAgrupar = '';
	        	}
	        	if(me.agruparCategoria === false && me.agruparCategoria === false) {
	        		me.messageInvalidAgrupar = 'Por favor seleccione tipo de agrupación.';
	        		me.controlar = false;
	        	} else {
	        		me.messageInvalidAgrupar = '';
	        	}
	        	if(me.agruparSubCategoria === false && me.agruparSubCategoria === false) {
	        		me.messageInvalidAgrupar = 'Por favor seleccione tipo de agrupación.';
	        		me.controlar = false;
	        	} else {
	        		me.messageInvalidAgrupar = '';
	        	}
	        	if(me.agruparGenero === false && me.agruparGenero === false) {
	        		me.messageInvalidAgrupar = 'Por favor seleccione tipo de agrupación.';
	        		me.controlar = false;
	        	} else {
	        		me.messageInvalidAgrupar = '';
	        	}
	        	if(me.onMarca === true){
	        		for (var key in me.marcas){
	        			me.selectedMarca[key] = me.marcas[key].CODIGO;
	        		}
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
	        	// if(me.onGenero === true){
	        	// 	for (var key in me.generos){
	        	// 		me.selectedGenero[key] = me.generos[key].CODIGO;
	        	// 	}
	        	// }
	        	return true;
	        },
	        llamarBusquedas(){	
	          axios.get('busquedas/').then((response) => {
	           	this.sucursales=response.data.sucursales;
	           	this.marcas = response.data.marcas;
	           	this.categorias = response.data.categorias;
	           	this.subCategorias = response.data.subCategorias;
	           	// this.generos = response.data.generos;
	          });
	        },

	        llamarDatos(){
	        	let me = this;
	        },

        	habilitar_insert() {
	       	  let me=this;
	       	  me.insert=true;
	       	 
		    },
		    todasMarcas(e){
	        	this.onMarca = !this.onMarca;
	        },
	        todasCategorias(e){
	        	this.onCategoria = !this.onCategoria;
	        },
	        todasSubCategorias(e){
	        	this.onSubCategoria = !this.onSubCategoria;
	        },
	        todasGeneros(e){
	        	this.onGenero = !this.onGenero;
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
        }
	}
</script>