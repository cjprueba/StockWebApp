<template>
		<!-- COMPRAS POR SECCION Y PROVEEDOR -->
	<div>
		<div class="card shadow border-bottom-primary mt-3" >
		  	<div class="card-header">Compras Por Sectores y Proveedores</div>
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
								   <input  v-on:change="habilitar_insert" type="text" class="input-sm form-control form-control-sm" id="selectedInicialFecha" v-model="selectedInicialFecha" v-bind:class="{ 'is-invalid': validarInicialFecha }"/>
								   <div class="input-group-append form-control-sm">
								   		<span class="input-group-text">a</span>
								   </div>
								   <input v-on:change="habilitar_insert"  type="text" class="input-sm form-control form-control-sm" name="end" id="selectedFinalFecha" v-model="selectedFinalFecha" v-bind:class="{ 'is-invalid': validarFinalFecha }"/>
							</div>
							<div class="invalid-feedback">
					        	{{messageInvalidFecha}}
					    	</div>
						</div>		

						<!-- -------------------------------------------MOSTRAR TIPO----------------------------------------------- -->

						
					</div>

					<div class="col-md-4">
						<label for="validationTooltip01">Seleccione Sección</label> 
						<div class="container_checkbox1 rounded">
		                    <div class="ml-3" v-for="seccion in secciones">
		                      <div class="custom-control custom-checkbox">
		                        <input v-on:change="habilitar_insert"  type="checkbox" class="custom-control-input" :disabled="onSeccion" 
		                        :value="seccion.ID_SECCION" 
		                        :id="seccion.ID_SECCION" 
		                        v-model="selectedSeccion" 
		                        v-bind:class="{ 'is-invalid': validarSeccion }">
		                        <label class="custom-control-label" :for="seccion.ID_SECCION">{{seccion.DESCRIPCION }}</label>
		                      </div>
		                    </div>
		                </div>
						<div>
					        <div class="form-text text-danger">{{messageInvalidSeccion}}</div>
					    </div>
						<div  class="custom-control custom-switch mt-3">
						  <input type="checkbox" class="custom-control-input" id="customSwitch1" v-model="onSeccion" v-on:change="seleccionarTodo">
						  <label class="custom-control-label" for="customSwitch1" >Seleccionar todas las secciones</label>
						</div>
					</div>

					<!-- ------------------------------------------- GONDOLAS ----------------------------------------------- -->
				
					<div class="col-md-4">
						<label for="validationTooltip02">Seleccione Proveedor</label> 
						<div class="container_checkbox1 rounded">
		                    <div class="ml-3" v-for="proveedor in proveedores">
		                      <div class="custom-control custom-checkbox">
		                        <input  v-on:change="habilitar_insert" type="checkbox" class="custom-control-input" :disabled="onProveedor" 
		                        :value="proveedor.CODIGO" 
		                        :id='"Piso_"+proveedor.CODIGO' 
		                        v-model="selectedProveedor" 
		                        v-bind:class="{ 'is-invalid': validarProveedor }">
		                        <label class="custom-control-label" :for='"Piso_"+proveedor.CODIGO' >{{proveedor.NOMBRE}}</label>
		                      </div>
		                    </div>
		                </div>
						<div>
						    <div class="form-text text-danger">{{messageInvalidProveedor}}</div>
						</div>
						<div class="custom-control custom-switch mt-3">
							<input  type="checkbox" class="custom-control-input" id="customSwitch2" v-model="onProveedor" v-on:change="seleccionarTodo">
							<label class="custom-control-label" for="customSwitch2">Seleccionar todos</label>
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
            		<!-- CARD PARA MARCA Y SU CATEGORIA -->
             <div class="col-md-12">
	                <div class="card-body">
						<div class="ct-chart">
							<canvas id="secciones">
								
							</canvas>
						</div>
					</div>
	    	</div>
	     	

         
	           	<div class="col-md-12">
		     		<table class="table table-striped table-hover table-light table-sm" v-if="responseSeccion.length > 0">
					  <thead>
					    <tr>
					      <th scope="col">#</th>
					      <th scope="col">Seccion-Proveedor</th>
					      <th scope="col">Entrada</th>
					      <th scope="col">Vendido</th>
					      <th scope="col">Costo Total</th>
					      <th scope="col">Total</th>
					      <th scope="col">Porcentaje</th>
					    </tr>
					  </thead>
					  <tbody>
					    <tr v-for="(seccion, index) in responseSeccion" v-on:click="clicked(seccion)"  data-toggle="modal" data-target="#exampleModalCenter">
					      <th scope="row">{{index+1}}</th>
					      <td>{{seccion.TOTALES}}</td>
					      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(seccion.ENTRADA)}}</td>
					       <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(seccion.VENDIDO)}}</td>
					      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(seccion.COSTO_TOTAL)}}</td>
					      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(seccion.TOTAL)}}</td>
					        <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(seccion.PORCENTAJE)}}</td>
					    </tr>
					  </tbody>
					  <tfoot>
						<tr>
						  <th></th>
						  <th>TOTALES</th>
						  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseSeccion.reduce((aca, item) => aca + parseInt(item.ENTRADA), 0))}}</th>
						  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseSeccion.reduce((aca, item) => aca + parseInt(item.VENDIDO), 0))}}</th>
						   <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseSeccion.reduce((acc, item) => acc + item.COSTO_TOTAL, 0))}}</th>
						   <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseSeccion.reduce((acc, item) => acc + item.TOTAL, 0))}}</th>
						   <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseSeccion.reduce((acc, item) => acc + item.PORCENTAJE, 0))}}</th>
						</tr>
					  </tfoot>
					</table>
		     	</div>
        </div>
			<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
				  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalCenterTitle">Seccion: <small>{{seccionTitulo}}</small></h5>
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
						      <th style="font-size: 12px" scope="col">ENTRADA</th>
						      <th style="font-size: 12px" scope="col">VENDIDO</th>
						      <th style="font-size: 12px" scope="col">COSTO UNITARIO</th>
						      <th style="font-size: 12px" scope="col">COSTO TOTAL</th>
						      <th style="font-size: 12px" scope="col">TOTAL</th>
						    </tr>
						  </thead>
						  <tbody>
						    <tr style="font-size: 12px" v-for="(compras, index) in filterProductos(responseCompras, datosFilas,datosProveedor)">
						      <th scope="row">{{index+1}}</th>
						      <td>{{compras.COD_PROD}}</td>
						      <td>{{compras.LOTE}}</td>
						      <td>{{compras.MARCA}}</td>
						      <td>{{compras.CATEGORIA}}</td>
						      <td>{{compras.SUBCATEGORIA}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(compras.STOCK)}}</td>
						       <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(compras.ENTRADA)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(compras.VENDIDO)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(compras.COSTO_UNIT)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(compras.COSTO_TOTAL)}}</td>
						       <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(compras.TOTAL)}}</td>
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
				<div class="card border-left-primary mt-3" v-for="seccion in responseSeccionTotales">
					<div class="row">
						
						<div class="col-md-6">
							  <div class="card-header font-weight-bold text-primary">
							    {{seccion.TOTALES}}
							  </div>
					    </div>
					    <div class="col-md-6">
							  <div class="card-header font-weight-bold text-primary text-right">
							    {{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(seccion.COSTO_TOTAL)}}
							  </div>
					    </div>
					</div>  
					
					<div class="card-body">
						<table class="table table-sm">
						  <thead class="thead-light">
						    <tr>
						      <th scope="col">#</th>
						      <th scope="col">Proveedor</th>
						      <th scope="col">Entrada</th>
						       <th scope="col">Vendido</th>
						      <th scope="col">Costo Total</th>
						       <th scope="col">Total</th>
						      <th scope="col">Porcentaje</th>
						    </tr>
						  </thead>
						  <tbody>
						    <tr v-for="(proveedores, index) in filterItems(responseSeccion, seccion.SECCIONES)">
						      <th scope="row">{{index+1}}</th>
						      <td>{{proveedores.PROVEEDOR_NOMBRE}}</td>
						       <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(proveedores.ENTRADA)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(proveedores.VENDIDO)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(proveedores.COSTO_TOTAL)}}</td>
						        <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(proveedores.TOTAL)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(proveedores.PORCENTAJE)}}</td>
						    </tr>
						  </tbody>
						  <tfoot>
							<tr>
							  <th></th>
							  <th>TOTALES</th>
							  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(seccion.ENTRADA)}}</th>
							  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(seccion.VENDIDO)}}</th>
							   <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(seccion.COSTO_TOTAL)}}</th>
							  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(seccion.TOTAL)}}</th>
							   <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(seccion.PORCENTAJE)}}</th>
							</tr>
						  </tfoot>
						</table>
					</div>
				</div>
			</div>



	</div>
		<!-- FIN DE VENTA POR GONDOLA -->




</template>

<script >
	export default {
        data(){
            return {
              	sucursales: [],
              	selectedSucursal: 'null',
              	secciones: [],
              	selectedSeccion: [],
              	controlar:false,
              	onSeccion: false,
              	validarSucursal: false,
              	messageInvalidSucursal: '',
              	validarSeccion: false,
              	messageInvalidSeccion: '',
              	selectedInicialFecha: '',
              	validarInicialFecha: false,
              	messageInvalidFecha: '',
              	selectedFinalFecha: '',
              	validarFinalFecha: false,
              	datos: {},
              	cargado: false,
              	descarga: false,
              	MayoristaContado:false,
              	MayoristaCredito:false,
              	ServicioDelivery:false,
              	proveedores: [],
              	selectedProveedor: [],
              	messageInvalidProveedor: '',
              	validarProveedor: false,
              	onProveedor: false,
              	insert:true,
              	responseSeccion: {},
              	responseSeccionTotales: [],
              	responseCompras: [],
                datosFilas: null,
                datosProveedor:null,
                seccionTitulo:'',
                varTotalSeccion: [],
				varNombreSeccion: [],
				candec:0,
				monedas_descripcion:''
            }
        },
        methods: {
	      	seleccionarTodo(){

	      		let me = this;

	      		if(me.onProveedor === true) {
			        for (var key in me.proveedores){
			        	me.selectedProveedor[key] = me.proveedores[key].CODIGO;
			        }
			    }

	      		if(me.onSeccion === true) {
			        for (var key in me.secciones){
			        	me.selectedSeccion[key] = me.secciones[key].ID_SECCION;
			        }
			    }

			    this.habilitar_insert();
	      	},

            llamarBusquedas(){	
	          axios.get('busquedas/').then((response) => {
	           	this.sucursales = response.data.sucursales;
	           	this.secciones = response.data.secciones;
	           	this.proveedores=response.data.proveedores;
	          }); 

      			// LLAMAR FUNCION PARA FILTRAR GONDOLAS

      			/*Common.obtenerGondolaCommon().then(data => {

	                this.gondolas = data;
      			});*/
	        },
	        filterProductos: function(items, codigo_s,codigo_p) {

			      return items.filter(function(item) {

			      	 return (item.PROVEEDOR_CODIGO === codigo_p && item.SECCION_CODIGO===codigo_s);
			    })
			 },
			 filterItems: function(items, codigo) {
			      return items.filter(function(item) {

                 	
			      
			      return item.SECCIONES === codigo;
			    })
			 },
	        descargar(){
	        	
	        	let me = this;	
	        		
	        	if(this.generarConsulta() === true) {
	        		me.descarga = true;
		        	axios({
					  url: '/export_entrada_seccion_proveedor',
					  method: 'POST',
					  data: me.datos,
					  responseType: 'blob', // important
					}).then((response) => {
						me.descarga = false;
					   const url = window.URL.createObjectURL(new Blob([response.data]));
					   const link = document.createElement('a');
					   link.href = url;
					   link.setAttribute('download', 'Compra_Seccion_Proveedor_'+me.selectedInicialFecha+' al '+me.selectedFinalFecha+'.xlsx'); //or any other extension
					   document.body.appendChild(link);
					   link.click();
					});
				}
				me.insert=false;
	        },
	       habilitar_insert() {
	       	  let me=this;
	       	  me.insert=true;
	       	 
		    },
	        generarConsulta(){
	        	
	        	let me=this;

	        	if (me.selectedSucursal === 'null' || me.selectedSucursal === '') {
	        		me.validarSucursal = true;
	        		me.messageInvalidSucursal = 'Por favor seleccione sucursal';
	        		me.controlar=true;
	        	} else {
	        		me.validarSucursal = false;
	        		me.messageInvalidSucursal = '';
	        	}	

	        	if(me.onSeccion === false && me.selectedSeccion.length === 0) {
	        		me.validarSeccion = true;
	        		me.messageInvalidSeccion = 'Por favor seleccione una o varias secciones';
	        		me.controlar=true;
	        	} else {
	   
	        		me.validarSeccion = false;
	        		me.messageInvalidSeccion = '';
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

	        	if(me.onProveedor === false && me.selectedProveedor.length === 0) {
	        		me.messageInvalidProveedor = 'Por favor seleccione una o varios Proveedores.';
	        		me.validarProveedor = true;
	        		me.controlar = true;
	        	} else {
	        		me.messageInvalidProveedor = '';
	        		me.validarProveedor = false;
	        	}	
	        	if(me.controlar===true){
	        		me.controlar=false;
	        		return;
	        	}

	        	/*if(me.onSeccion === true) {
	        		for (var key in me.secciones){
	        			me.selectedSeccion[key] = me.secciones[key].ID_SECCION;
	        		}
	        	} 

 				if(me.onProveedor === true) {
		        	for (var key in me.gondolas){
		        		me.selectedProveedor[key] = me.gondolas[key].ID;
		        	}
		        }*/

	        	me.datos = {
		        	Sucursal: me.selectedSucursal,
		        	Inicio: String(me.selectedInicialFecha),
		        	Final: String(me.selectedFinalFecha),
		        	secciones: me.selectedSeccion,
		        	AllSecciones: me.onSeccion,
					Proveedores: me.selectedProveedor,
					AllProveedores: me.onProveedor,
				 	Insert:me.insert
	        	};
	        	
	        	return true;
	        },
	         llamarDatos(){
	        	let me = this;	
	        	if(this.generarConsulta() === true) {
	        		me.cargado = true;
	        	Common.generarReporteCompraEntradaSeccionCommon(this.datos).then(data => {
	        		  
             		    me.cargado = false;
						me.responseCompras = data.compras;
					    const seccionArray = Object.keys(data.secciones).map(i => data.secciones[i])
					    me.responseSeccion = seccionArray
					    const seccionTotalesArray = Object.keys(data.secciones_totales).map(i => data.secciones_totales[i])
					    me.responseSeccionTotales = seccionTotalesArray
					   
					   
					  /*  const gondolaArray = Object.keys(data.gondolas).map(i => data.gondolas[i])
					    me.responseGondola = gondolaArray*/
					    me.loadSecciones();
					    me.insert=false;
              });

	        	} 
	        	
	        },
	        clicked(row) {
	       	  this.seccionTitulo = row.TOTALES; 	
		      this.datosFilas = row.SECCIONES;
		      this.datosProveedor=row.PROVEEDORES;
		    },
	         loadSecciones(){
						let me = this;
		            if(me.varNombreSeccion.length > 0){
		   				me.charSeccion.destroy();
		           		}
						me.varNombreSeccion = [];
						me.varTotalSeccion = [];
						me.responseSeccion.map(function(x){

							me.varNombreSeccion.push(x.TOTALES);
							me.varTotalSeccion.push(x.COSTO_TOTAL);
						});

						me.varSeccion = document.getElementById('secciones').getContext('2d');


						 me.charSeccion = new Chart(me.varSeccion, {
						    type: 'bar',
						    data: {
						        labels: me.varNombreSeccion,
						        datasets: [{
						            label: 'Secciones',
						            data: me.varTotalSeccion,
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
			     		
			     		me.selectedInicialFecha = $('#selectedInicialFecha').val();
			     	}
				);
				$("#selectedFinalFecha").datepicker().on(
						
			     	"changeDate", () => {
			     		
			     		me.selectedFinalFecha = $('#selectedFinalFecha').val();
			     	}
				);

				$('table').dataTable();
			});

			me.llamarBusquedas();
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