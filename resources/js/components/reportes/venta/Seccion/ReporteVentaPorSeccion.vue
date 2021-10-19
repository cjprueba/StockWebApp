<template>
		<!-- VENTA POR SECCION -->
 <div>
	<div v-if="$can('producto.mostrar')">
		<div class="card shadow border-bottom-primary mt-3">
		  	<div class="card-header">Venta por Sección</div>
			<div class="card-body">
			  	<div class="form-row">
			  		<div class="col-md-4 mb-3">
			  			
						<!-- -----------------------------------SELECT SUCURSAL------------------------------------- -->

			  			<label for="validationTooltip01">Seleccione Sucursal</label>
						<select v-on:change="habilitar_insert" class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSucursal }" v-model="selectedSucursal">
							 <option value="null" selected>Seleccionar</option>
							 <option v-for="sucursal in sucursales" :value="sucursal.CODIGO">{{ sucursal.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
					        {{messageInvalidSucursal}}
					    </div>

						<!-- -----------------------------------SELECT SECCION------------------------------------- -->

					    <label class="mt-3" for="validationTooltip01">Seleccione Sección</label>
						<select v-on:change="habilitar_insert" class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSeccion }" v-model="selectedSeccion">
							 <option value="null" selected>Seleccionar</option>
							 <option v-for="seccion in secciones" :value="seccion.ID_SECCION">{{ seccion.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
					        {{messageInvalidSeccion}}
					    </div>

						<!-- -----------------------------------SELECT INTERVALO------------------------------------- -->
						
					  	<label class="mt-3" for="validationTooltip01">Seleccione Intervalo de Tiempo</label>
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

					<div class="col-md-4">
						<label  for="validationTooltip01">Seleccione Categoría</label> 
						<select  v-on:change="habilitar_insert" multiple class="form-control" size="6" v-model="selectedCategoria" :disabled="onCategoria" v-bind:class="{ 'is-invalid': validarCategoria }">
						  <option v-for="categoria in categorias" :value="categoria.CODIGO">{{ categoria.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
					        {{messageInvalidCategoria}}
					    </div>
						<div class="custom-control custom-switch mt-3">
						  <input  v-on:change="habilitar_insert" type="checkbox" class="custom-control-input" id="customSwitch2" v-on:click="todasCategorias">
						  <label class="custom-control-label" for="customSwitch2">Seleccionar todas las Categorías</label>
						</div>
					</div>

					<div class="col-md-4">
						<label for="validationTooltip01">Seleccione Sub Categoría</label> 
						<select  v-on:change="habilitar_insert" multiple class="form-control" size="6" v-model="selectedSubCategoria" :disabled="onSubCategoria" v-bind:class="{ 'is-invalid': validarSubCategoria }">
						   <option v-for="subCategoria in subCategorias" :value="subCategoria.CODIGO">{{ subCategoria.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
					        {{messageInvalidSubCategoria}}
					    </div>
						<div   class="custom-control custom-switch mt-3">
						  <input v-on:change="habilitar_insert" type="checkbox" class="custom-control-input" id="customSwitch1" v-on:click="todasSubCategorias">
						  <label class="custom-control-label" for="customSwitch1" >Seleccionar todas las Sub Categorías</label>
						</div>
					</div>
				</div>
				<button class="btn btn-dark btn-sm" type="submit" v-on:click="descargar()"><font-awesome-icon icon="download" /> Descargar</button>
			</div>
		</div>

		<!-- CARD PARA MARCA Y SU CATEGORIA -->

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

	<!-- FIN DE VENTA POR SECCION -->

	<!-- ------------------------------------------------------------------------ -->

	<div v-else>
		<cuatrocientos-cuatro></cuatrocientos-cuatro>
	</div>

	<!-- ------------------------------------------------------------------------ -->
  </div>
</template>

<script >
	export default{
        data(){
            return{
              	sucursales: [],
              	selectedSucursal: 'null',
              	selectedSeccion: 'null',
              	subCategorias: [],
              	selectedSubCategoria: [],
              	datosFilas: null,
              	categoriaTitulo: '',
              	controlar:false,
              	categorias: [],
              	monedas_descripcion:'',
              	candec:'',
              	selectedCategoria: [],
              	onSubCategoria: false,
              	onCategoria: false,
              	insert:true,
              	validarSucursal: false,
              	messageInvalidSucursal: '',
              	validarSubCategoria: false,
              	messageInvalidSubCategoria: '',
              	validarCategoria: false,
              	messageInvalidCategoria: '',
              	selectedInicialFecha: '',
              	validarInicialFecha: false,
              	messageInvalidFecha: '',
              	selectedFinalFecha: '',
              	validarFinalFecha: false,
              	datos: {},
              	responseSubCategoria: {},
              	responseCategoria: [],
              	responseVenta: [],
              	varTotalCategoria: [],
				varNombreCategoria: [],
              	cargado: false,
              	descarga: false,
              	secciones: [],
              	validarSeccion: false,
              	messageInvalidSeccion: ''
            }
        }, 
        methods:{

            llamarBusquedas(){	

	          axios.get('busquedas/').then((response) => {
	           	this.sucursales = response.data.sucursales;
	           	this.subCategorias = response.data.seccionSubCategorias;
	           	this.categorias = response.data.seccionCategorias;
	           	this.secciones = response.data.seccion;
	          }); 
	        },

	        descargar(){
	        	
	        	let me = this;	
	        	
	        	if(this.generarConsulta() === true){
	        		me.descarga = true;
		        	axios({
					  url: '/export_venta_seccion',
					  method: 'POST',
					  data: me.datos,
					  responseType: 'blob', // important
					}).then((response) => {
						me.descarga = false;
					   const url = window.URL.createObjectURL(new Blob([response.data]));
					   const link = document.createElement('a');
					   link.href = url;
					   link.setAttribute('download', 'Venta_Seccion_'+me.selectedInicialFecha+' al '+me.selectedFinalFecha+'.xlsx'); //or any other extension
					   document.body.appendChild(link);
					   link.click();
					});
				}
				me.insert = false;
	        },

	        clicked(row){
	       	  
	       	  this.categoriaTitulo = row.TOTALES; 	
		      this.datosFilas = row.CATEGORIAS;
		    },

		    habilitar_insert(){
	       	  
	       	  let me = this;
	       	  me.insert = true;
		    },

	        filterItems: function(items, codigo){

			    return items.filter(function(item){

			      return item.CATEGORIA === codigo;
			    })
			 },

			filterProductos: function(items, codigo){

			    return items.filter(function(item) {

			      return item.CATEGORIAS_CODIGO === codigo;
			    })
			},

	        todasCategorias(e){
	        	this.onCategoria = !this.onCategoria;
	        },

	        todasSubCategorias(e){
	        	this.onSubCategoria = !this.onSuvCategoria;
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

	        	if (me.selectedSeccion === "null" || me.selectedSeccion === ''){
	        		me.validarSeccion = true;
	        		me.messageInvalidSeccion = 'Por favor seleccione la sección';
	        		me.controlar=true;
	        	} else {
	        		me.validarSeccion = false;
	        		me.messageInvalidSeccion = '';
	        	}	

				if(me.selectedInicialFecha === "null" || me.selectedInicialFecha === ""){
	        		me.validarInicialFecha = true;
	        		me.messageInvalidFecha = 'Por favor seleccione una fecha Inicial';
	        		me.controlar=true;
	        	} else {
	        		me.validarInicialFecha = false;
	        		me.messageInvalidFecha = '';
	        	}

	        	if(me.selectedFinalFecha === "null" || me.selectedFinalFecha === ""){
	        		me.validarFinalFecha = true;
	        		me.messageInvalidFecha = 'Por favor seleccione una fecha Final';
	        		me.controlar=true;
	        	} else {
	        		me.validarFinalFecha = false;
	        		me.messageInvalidFecha = '';
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
	        		me.controlar = false;
	        		return;
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
		        	Inicio: String(me.selectedInicialFecha),
		        	Final: String(me.selectedFinalFecha),
		        	SubCategorias: me.selectedSubCategoria,
		        	Categorias: me.selectedCategoria,
		        	AllSubCategory: me.onSubCategoria,
		        	AllCategory: me.onCategoria, 
		        	Insert:me.insert,
		        	Seccion: me.selectedSeccion
	        	};
	      
	        	return true;
	        },

	        loadCategorias(){

				let me = this;

	            if(me.varNombreCategiria.length > 0){
	   				
	   				me.charCategiria.destroy();
	           	}

				me.varNombreCategiria = [];
				me.varTotalCategiria = [];
					
				me.responseCategiria.map(function(x){
					me.varNombreCategiria.push(x.TOTALES);
					me.varTotalCategiria.push(x.TOTAL);
				});

				me.varCategoria = document.getElementById('categorias').getContext('2d');

				me.charCategoria = new Chart(me.varCategoria, {
					type: 'bar',
					data: {
					    labels: me.varNombreCategoria,
					    datasets: [{
					        label: 'Categorias',
					        data: me.varTotalCategoria,
					        backgroundColor: 'rgba(75, 192, 192, 0.2)',
					        borderColor: 'rgba(75, 192, 192, 1)',
					        borderWidth: 1
					    }]
				    },
				    options: {
				    	tooltips: {
				              callbacks: {
				                  label: function(tooltipItem, data){
				                    var value = data.datasets[0].data[tooltipItem.index];
				                      
				                    return me.monedas_descripcion + ' ' + new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(value) + '';
				                  }
				              }
				        },
				        scales: {
				            yAxes: [{
				                ticks: {
				                    beginAtZero: true,
				                    callback: function(value, index, values){
							          return value.toLocaleString();
							        }
				                }
				            }]
				        }
				    }
				});
			}
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
			     			me.insert=true;
			     			me.selectedInicialFecha = $('#selectedInicialFecha').val();
			     		}
					);
					$("#selectedFinalFecha").datepicker().on(
						
			     		"changeDate", () => {
			     			me.insert=true;
			     			me.selectedFinalFecha = $('#selectedFinalFecha').val();
			     		}
					);

					$('table').dataTable();
			});

			this.llamarBusquedas();
        }
    }    
</script>
