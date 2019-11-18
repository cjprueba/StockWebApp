<template>
		<!-- VENTA POR MARCA Y CATEGORIA -->
	<div>
		<div class="card shadow border-bottom-primary" >
		  	<div class="card-header">Venta por Proveedores</div>
			<div class="card-body">
			  	<div class="form-row">
			  		<div class="col-md-4 mb-3">
			  			
			  			<label for="validationTooltip01">Seleccione Sucursal</label>
						<select class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSucursal }" v-model="selectedSucursal">
							 <option value="null" selected>Seleccionar</option>
							 <option v-for="sucursal in sucursales" :value="sucursal.CODIGO">{{ sucursal.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
					        {{messageInvalidSucursal}}
					    </div>
                  <label for="validationTooltip01">Seleccione modo</label>
						<select class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid':  validarReporte }" v-model="selectedReporte">
							 <option value="null" selected>Seleccionar</option>
							 <option value=1 selected>Stock igual a conteo</option>
							  <option value=2 selected>conteo mayor a stock</option>
							  <option value=3 selected>conteo menor a stock</option>
							   <option value=4 selected>General</option>
						</select>
						<div class="invalid-feedback">
					        {{messageInvalidReporte}}
					    </div>
					  			  

					</div>

					<div class="col-md-4">
						<label for="validationTooltip01">Seleccione Marca</label> 
						<select multiple class="form-control" size="4" v-model="selectedMarca" :disabled="onMarca" v-bind:class="{ 'is-invalid': validarMarca }">
						   <option v-for="marca in marcas" :value="marca.CODIGO">{{ marca.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
					        {{messageInvalidMarca}}
					    </div>
						<div class="custom-control custom-switch mt-3">
						  <input type="checkbox" class="custom-control-input" id="customSwitch1" v-on:click="todasMarcas">
						  <label class="custom-control-label" for="customSwitch1" >Seleccionar todas las Marcas</label>
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

            <div class="col-xl-6 col-lg-6">
	                <div class="card-body">
						<div class="ct-chart">
							<canvas id="marcas">
								
							</canvas>
						</div>
					</div>
	    	</div>
	     	
	     	<div class="col-xl-6 col-lg-6 mt-3">
	     		<table class="table table-striped table-hover table-light table-sm" v-if="responseMarca.length > 0">
				  <thead>
				    <tr>
				      <th scope="col">#</th>
				      <th scope="col">Proveedor</th>
				      <th scope="col">Vendido</th>
				      <th scope="col">Stock Vendidos</th>
				      <th scope="col">Stock General</th>
				      <th scope="col">Costo</th>
				      <th scope="col">Totales</th>
				    </tr>
				  </thead>
				  <tbody>
				    <tr v-for="(marca, index) in responseMarca" v-on:click="clicked(marca)"  data-toggle="modal" data-target="#exampleModalCenter">
				      <th scope="row">{{index+1}}</th>
				      <td>{{marca.MARCA}}</td>
				      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.VENDIDO)}}</td>
				      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.STOCK)}}</td>
				      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.STOCK_G)}}</td>
				       <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.COSTO)}}</td>
				      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.TOTAL)}}</td>
				    </tr>
				  </tbody>
				  <tfoot>
					<tr>
					  <th></th>
					  <th>TOTALES</th>
					  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseMarca.reduce((acc, item) => acc + item.VENDIDO, 0))}}</th>
					  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseMarca.reduce((acc, item) => acc + item.STOCK, 0))}}</th>
					   <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseMarca.reduce((acc, item) => acc + item.STOCK_G, 0))}}</th>
					    <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseMarca.reduce((acc, item) => acc + item.COSTO, 0))}}</th>
					  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseMarca.reduce((acc, item) => acc + item.TOTAL, 0))}}</th>
					</tr>
				  </tfoot>
				</table>
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
						    <tr>
						      <th scope="col">#</th>
						      <th scope="col">CODIGO</th>
						      <th scope="col">DESCRIPCION</th>
						      <th scope="col">STOCK</th>
						      <th scope="col">VENDIDO</th>
						       <th scope="col">COSTO</th>
						      <th scope="col">PRECIO</th>
						    </tr>
						  </thead>
						  <tbody>
						    <tr v-for="(venta, index) in filterItems(responseVenta, datosFilas)">
						      <th scope="row">{{index+1}}</th>
						      <td>{{venta.COD_PROD}}</td>
						      <td>{{venta.DESCRIPCION}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(venta.STOCK)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(venta.VENDIDO)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(venta.COSTO)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(venta.PRECIO)}}</td>
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
							    {{marca.MARCA}}
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
						      <th scope="col">Stock Vendido</th>
						      <th scope="col">Stock General</th>
						      <th scope="col">Total</th>
						    </tr>
						  </thead>
						  <tbody>
						    <tr v-for="(categoria, index) in filterItems(responseMarca, marca.CODIGO)">
						      <th scope="row">{{index+1}}</th>
						      <td>{{categoria.LINEA}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(categoria.VENDIDO)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(categoria.STOCK)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(categoria.STOCK_G)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(categoria.TOTAL)}}</td>
						    </tr>
						  </tbody>
						  <tfoot>
							<tr>
							  <th></th>
							  <th>TOTALES</th>
							  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.VENDIDO)}}</th>
							  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.STOCK)}}</th>
							  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.STOCK_G)}}</th>
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
              	categorias: [],
              	selectedCategoria: [],
              	onMarca: false,
              	onCategoria: false,
              	validarSucursal: false,
              	messageInvalidSucursal: '',
              	messageInvalidReporte: '',
              	validarMarca: false,
              	messageInvalidMarca: '',
              	validarReporte: false,
              	validarCategoria: false,
              	messageInvalidCategoria: '',
              	selectedInicialFecha: '',
              	validarInicialFecha: false,
              	messageInvalidFecha: '',
              	selectedFinalFecha: '',
              	selectedReporte:'null',
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
	        	
	  
	        	if(this.generarConsulta() === true) {
	        		me.descarga = true;
		        	axios({
					  url: '/ExportInventario',
					  method: 'POST',
					  data: me.datos,
					  responseType: 'blob', // important
					}).then((response) => {
						me.descarga = false;
					   const url = window.URL.createObjectURL(new Blob([response.data]));
					   const link = document.createElement('a');
					   link.href = url;
					   link.setAttribute('download', 'Venta-'+me.selectedInicialFecha+'-'+me.selectedFinalFecha+'.xlsx'); //or any other extension
					   document.body.appendChild(link);
					   link.click();
					});
				}
	        },
	        clicked(row) {
	       	  this.marcaTitulo = row.MARCA; 	
		      this.datosFilas = row.CODIGO;
		    },
	        filterItems: function(items, codigo) {
			      return items.filter(function(item) {
			      return item.MARCA === codigo;
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
	        		
					axios.post('/proveedores', this.datos).then(function (response) {

						me.cargado = false;
						me.responseVenta = response.data.ventas;
					    const marcaArray = Object.keys(response.data.marcas).map(i => response.data.marcas[i])
					    me.responseMarca = marcaArray
					    /*const categoriaArray = Object.keys(response.data.categorias).map(i => response.data.categorias[i])
					    me.responseCategoria = categoriaArray*/

					    me.loadProveedores();
					});
	        	} else {
	        		alert("false");
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

	        	if(this.onMarca === false && this.selectedMarca === null) {
	        		this.validarMarca = true;
	        		this.messageInvalidMarca = 'Por favor seleccione una o varias Marcas';
	        		return false;
	        	} else {
	        		this.validarMarca = false;
	        		this.messageInvalidMarca = '';
	        	}
	        	  	if (this.selectedReporte === null || this.selectedReporte === "null") {
	        		this.validarReporte = true;
	        		this.messageInvalidReporte = 'Por favor seleccione tipo de Reporte';
	        		return false;
	        	} else {
	        		this.validarReporte = false;
	        		this.messageInvalidReporte = '';
	        	}



	        	if(this.onMarca === true) {
	        		for (var key in this.marcas){
	        			this.selectedMarca[key] = this.marcas[key].CODIGO;
	        		}
	        	} 



	        	this.datos = {
	        	Sucursal: this.selectedSucursal,
	        	Marcas: this.selectedMarca,
	        	Modo:this.selectedReporte,
	        	AllBrand: this.onMarca,
	        	};
	        	
	        	return true;
	        },
	        loadProveedores(){
	       
				let me = this;
				me.varNombreMarca = [];
				me.varTotalMarca = [];
				me.responseMarca.map(function(x){
					me.varNombreMarca.push(x.MARCA);
					me.varTotalMarca.push(x.TOTAL);
				});

				me.varMarca = document.getElementById('marcas').getContext('2d');

				 me.charMarca = new Chart(me.varMarca, {
				    type: 'bar',
				    data: {
				        labels: me.varNombreMarca,
				        datasets: [{
				            label: 'Proveedores',
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
				                      
				                      return 'Gs. ' + new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(value) + '';
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
