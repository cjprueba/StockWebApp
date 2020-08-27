<template>
		<!-- VENTA POR MARCA Y CATEGORIA -->
	<div>
		<div class="card shadow border-bottom-primary" >
		  	<div class="card-header">Venta por marcas y categorías</div>
			<div class="card-body">
			  	<div class="form-row">
			  		<div class="col-md-4 mb-3">
			  			
			  			<label for="validationTooltip01">Seleccione Sucursal</label>
						<select v-on:change="habilitar_insert" class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSucursal }" v-model="selectedSucursal">
							 <option value="null" selected>Seleccionar</option>
							 <option v-for="sucursal in sucursales" :value="sucursal.CODIGO">{{ sucursal.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
					        {{messageInvalidSucursal}}
					    </div>

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
						<label for="validationTooltip01">Seleccione Marcas</label> 
						<select  v-on:change="habilitar_insert" multiple class="form-control" size="4" v-model="selectedMarca" :disabled="onMarca" v-bind:class="{ 'is-invalid': validarMarca }">
						   <option v-for="marca in marcas" :value="marca.CODIGO">{{ marca.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
					        {{messageInvalidMarca}}
					    </div>
						<div   class="custom-control custom-switch mt-3">
						  <input v-on:change="habilitar_insert" type="checkbox" class="custom-control-input" id="customSwitch1" v-on:click="todasMarcas">
						  <label class="custom-control-label" for="customSwitch1" >Seleccionar todas las Marcas</label>
						</div>
					</div>

					<div class="col-md-4">
						<label  for="validationTooltip01">Seleccione Categoria</label> 
						<select  v-on:change="habilitar_insert" multiple class="form-control" size="4" v-model="selectedCategoria" :disabled="onCategoria" v-bind:class="{ 'is-invalid': validarCategoria }">
						  <option v-for="categoria in categorias" :value="categoria.CODIGO">{{ categoria.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
					        {{messageInvalidCategoria}}
					    </div>
						<div class="custom-control custom-switch mt-3">
						  <input  v-on:change="habilitar_insert" type="checkbox" class="custom-control-input" id="customSwitch2" v-on:click="todasCategorias">
						  <label class="custom-control-label" for="customSwitch2">Seleccionar todas las Categorias</label>
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
							<canvas id="marcas">
								
							</canvas>
						</div>
					</div>
	    	</div>
	     	

         
	           	<div class="col-md-12">
		     		<table class="table table-striped table-hover table-light table-sm" v-if="responseMarca.length > 0">
					  <thead>
					    <tr>
					      <th scope="col">#</th>
					      <th scope="col">Marca</th>
					      <th scope="col">Vendido</th>
					      <th scope="col">Descuento</th>
					      <th scope="col">Costo Total</th>
					      <th scope="col">Totales</th>
					    </tr>
					  </thead>
					  <tbody>
					    <tr v-for="(marca, index) in responseMarca" v-on:click="clicked(marca)"  data-toggle="modal" data-target="#exampleModalCenter">
					      <th scope="row">{{index+1}}</th>
					      <td>{{marca.TOTALES}}</td>
					      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.VENDIDO)}}</td>
					      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.DESCUENTO)}}</td>
					      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.COSTO)}}</td>
					      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.TOTAL)}}</td>
					    </tr>
					  </tbody>
					  <tfoot>
						<tr>
						  <th></th>
						  <th>TOTALES</th>
						  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseMarca.reduce((aca, item) => aca + parseInt(item.VENDIDO), 0))}}</th>
						  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseMarca.reduce((acc, item) => acc + item.DESCUENTO, 0))}}</th>
						   <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseMarca.reduce((acc, item) => acc + item.COSTO, 0))}}</th>
						  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseMarca.reduce((acc, item) => acc + item.TOTAL, 0))}}</th>
						</tr>
					  </tfoot>
					</table>
		     	</div>
           </div>
	     	
	     	<!-- CARD PARA MARCA Y SU CATEGORIA -->

			<!-- <div class="card border-left-primary mt-3 col-md-12" v-for="marca in responseMarca">
				<div class="row">
					
					<div class="col-md-6">
						  <div class="card-header font-weight-bold text-primary">
						    {{marca.MARCA}}
						  </div>
				    </div>
				    <div class="col-md-6">
						  <div class="card-header font-weight-bold text-primary text-right">
						    {{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.TOTAL)}}
						  </div>
				    </div>
				</div>  
				
				<ul class="list-group list-group-flush">
				    <li class="list-group-item" v-for="marca in filterItems(responseCategoria, marca.CODIGO)">
				    	<div class="row">
				    		<div class="col-md-6">
				    			{{marca.LINEA}}
				    		</div>
				    		<div class="col-md-6 text-right">
				    			{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.TOTAL)}}
				    		</div>
				    	</div>
					</li>
				</ul>
			</div> -->

			<!-- MODAL DE TABLA PARA DATOS CRUDOS -->

				<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
				  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalCenterTitle">Marca: <small>{{marcaTitulo}}</small></h5>
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
						      <th style="font-size: 12px" scope="col">MARCA</th>
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
						      <td>{{venta.MARCA}}</td>
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
				<div class="card border-left-primary mt-3" v-for="marca in responseMarca">
					<div class="row">
						
						<div class="col-md-6">
							  <div class="card-header font-weight-bold text-primary">
							    {{marca.TOTALES}}
							  </div>
					    </div>
					    <div class="col-md-6">
							  <div class="card-header font-weight-bold text-primary text-right">
							    {{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.TOTAL)}}
							  </div>
					    </div>
					</div>  
					
					<div class="card-body">
						<table class="table table-sm">
						  <thead class="thead-light">
						    <tr>
						      <th scope="col">#</th>
						      <th scope="col">Categoria</th>
						      <th scope="col">Vendido</th>
						      <th scope="col">Descuento</th>
						      <th scope="col">Costo Total</th>
						      <th scope="col">Total</th>
						    </tr>
						  </thead>
						  <tbody>
						    <tr v-for="(categoria, index) in filterItems(responseCategoria, marca.MARCAS)">
						      <th scope="row">{{index+1}}</th>
						      <td>{{categoria.DESCRI_L}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(categoria.VENDIDO)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(categoria.DESCUENTO)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(categoria.COSTO_TOTAL)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(categoria.TOTAL)}}</td>
						    </tr>
						  </tbody>
						  <tfoot>
							<tr>
							  <th></th>
							  <th>TOTALES</th>
							  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.VENDIDO)}}</th>
							  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.DESCUENTO)}}</th>
							  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.COSTO)}}</th>
							  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.TOTAL)}}</th>
							</tr>
						  </tfoot>
						</table>
					</div>
				</div>
			</div>

		</div>

		<!-- CARD PARA MARCA Y SU CATEGORIA -->

	</div>
		<!-- FIN DE VENTA POR MARCA Y CATEGORIA -->


</template>

<script >
	export default {
        data(){
            return {
              	sucursales: [],
              	selectedSucursal: '',
              	marcas: [],
              	selectedMarca: [],
              	datosFilas: null,
              	marcaTitulo: '',
              	controlar:false,
              	categorias: [],
              	monedas_descripcion:'',
              	candec:'',
              	selectedCategoria: [],
              	onMarca: false,
              	onCategoria: false,
              	insert:true,
              	validarSucursal: false,
              	messageInvalidSucursal: '',
              	validarMarca: false,
              	messageInvalidMarca: '',
              	validarCategoria: false,
              	messageInvalidCategoria: '',
              	selectedInicialFecha: '',
              	validarInicialFecha: false,
              	messageInvalidFecha: '',
              	selectedFinalFecha: '',
              	validarFinalFecha: false,
              	datos: {},
              	responseMarca: {},
              	responseCategoria: [],
              	responseVenta: [],
              	varTotalMarca: [],
				varNombreMarca: [],
              	cargado: false,
              	descarga: false
            }
        }, 
        methods: {
            llamarBusquedas(){	
	          axios.get('busquedas/').then((response) => {
	           	this.sucursales = response.data.sucursales;
	           	this.marcas = response.data.marcas;
	           	this.categorias = response.data.categorias;
	          }); 
	        },
	        descargar(){
	        	
	        	let me = this;	
	        		    Common.encontrarfotoCommon().then(data => {
	        		  
             			
              });
	        	/*if(this.generarConsulta() === true) {
	        		me.descarga = true;
		        	axios({
					  url: '/export_marca_categoria',
					  method: 'POST',
					  data: me.datos,
					  responseType: 'blob', // important
					}).then((response) => {
						me.descarga = false;
					   const url = window.URL.createObjectURL(new Blob([response.data]));
					   const link = document.createElement('a');
					   link.href = url;
					   link.setAttribute('download', 'Venta_Marca_Categoria_'+me.selectedInicialFecha+' al '+me.selectedFinalFecha+'.xlsx'); //or any other extension
					   document.body.appendChild(link);
					   link.click();
					});
				}
				me.insert=false;*/
	        },
	        clicked(row) {
	       	  this.marcaTitulo = row.TOTALES; 	
		      this.datosFilas = row.MARCAS;
		    },
		    habilitar_insert() {
	       	  let me=this;
	       	  me.insert=true;
	       	 
		    },
	        filterItems: function(items, codigo) {

			      return items.filter(function(item) {

                 
			      
			      return item.MARCA === codigo;
			    })
			 },
			 filterProductos: function(items, codigo) {

			      return items.filter(function(item) {

			      
			      return item.MARCAS_CODIGO === codigo;
			    })
			 },
	        todasMarcas(e){
	        	this.onMarca = !this.onMarca;
	        },
	        todasCategorias(e){
	        	this.onCategoria = !this.onCategoria;
	        },
	        llamarDatos(){
	        	let me = this;	
	        	if(this.generarConsulta() === true) {
	        		me.cargado = true;
	        	Common.generarReporteVentaCommon(this.datos).then(data => {
	        		  
             				me.cargado = false;
						me.responseVenta = data.ventas;
					    const marcaArray = Object.keys(data.marcas).map(i => data.marcas[i])
					    me.responseMarca = marcaArray
					   
					    const categoriaArray = Object.keys(data.categorias).map(i => data.categorias[i])
					    me.responseCategoria = categoriaArray
					    me.loadMarcas();
              });

	        	} 
	        	me.insert=false;
	        },
	        generarConsulta(){
	        	
	        	let me=this;

	        	if (me.selectedSucursal === null || me.selectedSucursal === '') {
	        		me.validarSucursal = true;
	        		me.messageInvalidSucursal = 'Por favor seleccione sucursal';
	        		me.controlar=true;
	        	} else {
	        		me.validarSucursal = false;
	        		me.messageInvalidSucursal = '';
	        	}	

	        	if(me.onMarca === false && me.selectedMarca.length === 0) {
	        		me.validarMarca = true;
	        		me.messageInvalidMarca = 'Por favor seleccione una o varias Marcas';
	        		me.controlar=true;
	        	} else {
	   
	        		me.validarMarca = false;
	        		me.messageInvalidMarca = '';
	        	}


	        	if(me.onCategoria === false && me.selectedCategoria.length===0) {

	        		me.validarCategoria = true;
	        		me.messageInvalidCategoria = 'Por favor seleccione una o varias Categorias';
	        		me.controlar=true;
	        	} else {
	        		me.validarCategoria = false;
	        		me.messageInvalidCategoria = '';
	        	}

	        	if(me.selectedInicialFecha === null || me.selectedInicialFecha === "") {
	        		me.validarInicialFecha = true;
	        		me.messageInvalidFecha = 'Por favor seleccione una fecha Inicial';
	        		me.controlar=true;
	        	} else {
	        		me.validarInicialFecha = false;
	        		me.messageInvalidFecha = '';
	        	}

	        	if(me.selectedFinalFecha === null || me.selectedFinalFecha === "") {
	        		me.validarFinalFecha = true;
	        		me.messageInvalidFecha = 'Por favor seleccione una fecha Final';
	        		me.controlar=true;
	        	} else {
	        		me.validarFinalFecha = false;
	        		me.messageInvalidFecha = '';
	        	}		
	        	if(me.controlar===true){
	        		return;
	        	}

	        	if(me.onMarca === true) {
	        		for (var key in me.marcas){
	        			me.selectedMarca[key] = me.marcas[key].CODIGO;
	        		}
	        	} 

	        	if(me.onCategoria === true) {
	        		for (var key in me.categorias){
	        			me.selectedCategoria[key] = me.categorias[key].CODIGO;
	        		}
	        	}

	        	me.datos = {
	        	Sucursal: me.selectedSucursal,
	        	Inicio: String(me.selectedInicialFecha),
	        	Final: String(me.selectedFinalFecha),
	        	Marcas: me.selectedMarca,
	        	Categorias: me.selectedCategoria,
	        	AllBrand: me.onMarca,
	        	AllCategory: me.onCategoria, 
	        	Insert:me.insert
	        	};
	        	
	        	return true;
	        },
	        loadMarcas(){
				let me = this;
            if(me.varNombreMarca.length > 0){
   				me.charMarca.destroy();
           		}
				me.varNombreMarca = [];
				me.varTotalMarca = [];
				me.responseMarca.map(function(x){

					me.varNombreMarca.push(x.TOTALES);
					me.varTotalMarca.push(x.TOTAL);
				});

				me.varMarca = document.getElementById('marcas').getContext('2d');


				 me.charMarca = new Chart(me.varMarca, {
				    type: 'bar',
				    data: {
				        labels: me.varNombreMarca,
				        datasets: [{
				            label: 'Marcas',
				            data: me.varTotalMarca,
				            backgroundColor: 'rgba(75, 192, 192, 0.2)',
				            borderColor: 'rgba(75, 192, 192, 1)',
				            borderWidth: 1
				        }]
				    },
				    options: {
				    	tooltips: {
				              callbacks: {
				                  label: function(tooltipItem, data) {
				                      var value = data.datasets[0].data[tooltipItem.index];
				                      
				                      return me.monedas_descripcion + ' ' + new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(value) + '';
				                  }
				              }
				          },
				        scales: {
				            yAxes: [{
				                ticks: {
				                    beginAtZero: true,
				                    callback: function(value, index, values) {
							          return value.toLocaleString();
							        }
				                }
				            }]
				        }
				    }
				});


			}

        },
        mounted() {
        	let me = this;
        	Common.obtenerParametroCommon().then(data => {
		            		

				               	me.candec=data.parametros[0].CANDEC;
                                me.monedas_descripcion =data.parametros[0].DESCRIPCION;
                               
                            

                                

				                 
		
				    
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
