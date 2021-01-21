<template>
		<!-- VENTA POR MARCA Y CATEGORIA -->
 <div class="container">
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
							 <option v-model="secciones" :value="secciones.ID_SECCION">{{ secciones.DESCRIPCION }}</option>
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
			    <button class="btn btn-primary btn-sm" type="submit" v-on:click="llamarDatos">Generar</button>
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

			<!-- SPINNER CONSULTA -->

			<div class="col-md-12">
				<div v-if="cargado" class="d-flex justify-content-center mt-3">
					<strong>Cargando...   </strong>
	                <div class="spinner-grow" role="status" aria-hidden="true"></div>
	             </div>
            </div>
            
            <!-- CHART MARCAS -->

      	 <div class="col-md-12">
	            <div class="card-body">
					<div class="ct-chart">
						<canvas id="categorias">
								
						</canvas>
					</div>
				</div>
	    	</div>
         
	        <div class="col-md-12">
		     	<table class="table table-striped table-hover table-light table-sm" v-if="responseCategoria.length > 0">
					<thead>
					    <tr>
					      <th scope="col">#</th>
					      <th scope="col">Categoria</th>
					      <th scope="col">Vendido</th>
					      <th scope="col">Descuento</th>
					      <th scope="col">Costo Total</th>
					      <th scope="col">Totales</th>
					    </tr>
					</thead>
					<tbody>
					    <tr v-for="(categoria, index) in responseCategoria" v-on:click="clicked(categoria)"  data-toggle="modal" data-target="#exampleModalCenter">
					      <th scope="row">{{index+1}}</th>
					      <td>{{categoria.TOTALES}}</td>
					      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(categoria.VENDIDO)}}</td>
					      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(categoria.DESCUENTO)}}</td>
					      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(categoria.COSTO)}}</td>
					      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(categoria.TOTAL)}}</td>
					    </tr>
					</tbody>
					<tfoot>
						<tr>
						  <th></th>
						  <th>TOTALES</th>
						  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseCategoria.reduce((aca, item) => aca + parseInt(item.VENDIDO), 0))}}</th>
						  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseCategoria.reduce((acc, item) => acc + item.DESCUENTO, 0))}}</th>
						   <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseCategoria.reduce((acc, item) => acc + item.COSTO, 0))}}</th>
						  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseCategoria.reduce((acc, item) => acc + item.TOTAL, 0))}}</th>
						</tr>
					</tfoot>
				</table>
		    </div>
        </div>

		<!-- MODAL DE TABLA PARA DATOS CRUDOS -->

		<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
				<div class="modal-content">
				    <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalCenterTitle">Categoria: <small>{{categoriaTitulo}}</small></h5>
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				          <span aria-hidden="true">&times;</span>
				        </button>
				    </div>
				    <div class="modal-body">
				        <table class="table" v-if="datosFilas !== null">
						  <thead>
						    <tr >
						      <th style="font-size: 12px" scope="col">#</th>
						      <th style="font-size: 12px" scope="col">CODIGO</th>
						      <th style="font-size: 12px" scope="col">LOTE</th>
						      <th style="font-size: 12px" scope="col">CATEGORIA</th>
						      <th style="font-size: 12px" scope="col">SUBCATEGORIA</th>
						      <th style="font-size: 12px" scope="col">STOCK</th>
						      <th style="font-size: 12px" scope="col">VENDIDO</th>
						      <th style="font-size: 12px" scope="col">PRECIO UNITARIO</th>
						      <th style="font-size: 12px" scope="col">TOTAL</th>
						      <th style="font-size: 12px" scope="col">DESCUENTO TOTAL</th>
						      <th style="font-size: 12px" scope="col">COSTO UNITARIO</th>
						      <th style="font-size: 12px" scope="col">COSTO TOTAL</th>
						      <th style="font-size: 12px" scope="col">DESCUENTO %</th>
						    </tr>
						  </thead>
						  <tbody>
						    <tr style="font-size: 12px" v-for="(venta, index) in filterProductos(responseVenta, datosFilas)">
						      <th scope="row">{{index+1}}</th>
						      <td>{{venta.COD_PROD}}</td>
						       <td>{{venta.LOTE}}</td>
						      <td>{{venta.CATEGORIA}}</td>
						      <td>{{venta.SUBCATEGORIA}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(venta.STOCK)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(venta.VENDIDO)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(venta.PRECIO_UNIT)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(venta.TOTAL)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(venta.DESCUENTO)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(venta.COSTO_UNIT)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(venta.COSTO_TOTAL)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(venta.DESCUENTO_PORCENTAJE)}}</td>
						    </tr>
						  </tbody>
						</table>
				      </div>
				      <div class="modal-footer">
				        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				      </div>
				    </div>
				  </div>
				</div>	

			<!-- FIN DE TALA DE DATOS CRUDOS -->

			<div class="col-md-12">
				<div class="card border-left-primary mt-3" v-for="categoria in responseCategoria">
					<div class="row">
						
						<div class="col-md-6">
							  <div class="card-header font-weight-bold text-primary">
							    {{categoria.TOTALES}}
							  </div>
					    </div>
					    <div class="col-md-6">
							  <div class="card-header font-weight-bold text-primary text-right">
							    {{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(categoria.TOTAL)}}
							  </div>
					    </div>
					</div>  
					
					<div class="card-body">
						<table class="table table-sm">
						  <thead class="thead-light">
						    <tr>
						      <th scope="col">#</th>
						      <th scope="col">SubCategoria</th>
						      <th scope="col">Vendido</th>
						      <th scope="col">Descuento</th>
						      <th scope="col">Costo Total</th>
						      <th scope="col">Total</th>
						    </tr>
						  </thead>
						  <tbody>
						    <tr v-for="(categoria, index) in filterItems(responseCategoria, categoria.CATEGORIAS)">
						      <th scope="row">{{index+1}}</th>
						      <td>{{categoria.DESCRI_L}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(subCategoria.VENDIDO)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(subCategoria.DESCUENTO)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(subCategoria.COSTO_TOTAL)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(subCategoria.TOTAL)}}</td>
						    </tr>
						  </tbody>
						  <tfoot>
							<tr>
							  <th></th>
							  <th>TOTALES</th>
							  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(categoria.VENDIDO)}}</th>
							  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(categoria.DESCUENTO)}}</th>
							  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(categoria.COSTO_TOTAL)}}</th>
							  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(categoria.TOTAL)}}</th>
							</tr>
						  </tfoot>
						</table>
					</div>
				</div>
			</div>

		<!-- CARD PARA CATEGORIA Y SUB CATEGORIA -->

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
              	selectedSucursal: '',
              	selectedSeccion: '',
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
              	secciones: {
              		ID_SECCION: '',
              		SECCION: ''
              	},
              	selectedSeccion: '',
              	validarSeccion: false,
              	messageInvalidSeccion: ''
            }
        }, 
        methods:{

            llamarBusquedas(){	

	          axios.get('busquedas/').then((response) => {
	           	this.sucursales = response.data.sucursales;
	           	this.subCategorias = response.data.subCategorias;
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

	        llamarDatos(){

	        	let me = this;	
	        	if(this.generarConsulta() === true) {

	        		me.cargado = true;

		        	Common.generarReporteVentaSeccionCommon(this.datos).then(data => {
		        		  
	             		me.cargado = false;
						me.responseVenta = data.ventas;
						const categoriaArray = Object.keys(data.categorias).map(i => data.categorias[i])
						me.responseCategoria = categoriaArray
						   
						const subCategoriaArray = Object.keys(data.subCategorias).map(i => data.subCategorias[i])
						me.responsesubCategoria = subCategoriaArray
						me.loadCategorias();
	              	});
	        	} 

	        	me.insert = false;
	        },

	        generarConsulta(){
	        	
	        	let me = this;

	        	if (me.selectedSucursal === null || me.selectedSucursal === ''){
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

				if(me.selectedInicialFecha === null || me.selectedInicialFecha === ""){
	        		me.validarInicialFecha = true;
	        		me.messageInvalidFecha = 'Por favor seleccione una fecha Inicial';
	        		me.controlar=true;
	        	} else {
	        		me.validarInicialFecha = false;
	        		me.messageInvalidFecha = '';
	        	}

	        	if(me.selectedFinalFecha === null || me.selectedFinalFecha === ""){
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
	        	
	        	console.log(me.datos);
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

        	Common.obtenerParametroCommon().then(data => {
		        me.candec = data.parametros[0].CANDEC;
                me.monedas_descripcion = data.parametros[0].DESCRIPCION;
               
			});

        	$(function(){
		   		    $('#sandbox-container .input-daterange').datepicker({
		   		    	    keyboardNavigation: false,
    						forceParse: false
    				});
    				$("#selectedInicialFecha").datepicker().on(
			     		"changeDate", () => {
			     			me.insert=true;
			     			console.log(me.insert);
			     			me.selectedInicialFecha = $('#selectedInicialFecha').val();
			     		}
					);
					$("#selectedFinalFecha").datepicker().on(
						
			     		"changeDate", () => {
			     			me.insert=true;
			     			console.log(me.insert);
			     			me.selectedFinalFecha = $('#selectedFinalFecha').val();
			     		}
					);

					$('table').dataTable();
			});

			this.llamarBusquedas();
        }
    }    
</script>
