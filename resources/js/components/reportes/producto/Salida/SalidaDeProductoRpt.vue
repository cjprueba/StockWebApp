<template>

	<!-- REPORTE DE SALIDA DE PRODUCTOS -->
	<div>

		<div class="card mt-3 shadow border-bottom-primary mb-3">
		  <div class="card-header">Reporte Salida de Productos</div>
		    <div class="card-body">
		    	<div class="form-row">
		    		
					<div class="col-md-4 mb-3">
				  		<label for="validationTooltip01">Sucursal</label>
						<select class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSucursal }" v-model="selectedSucursal">
							<option value="null" selected>Seleccionar</option>
							<option v-for="sucursal in sucursales" :value="sucursal.CODIGO">{{ sucursal.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
						    {{messageInvalidSucursal}}
						</div>
						 <label class="mt-3">Seleccione Intervalo de Tiempo</label>
							<div id="sandbox-container">
								<div class="input-daterange input-group">
									<input id="selectedInicialFecha" class="input-sm form-control form-control-sm" v-model="selectedInicialFecha" v-bind:class="{ 'is-invalid': validarInicialFecha }"/>
									<div class="input-group-append form-control-sm">
										<span class="input-group-text">a</span>
									</div>
									<input name="end" id="selectedFinalFecha" class="input-sm form-control form-control-sm" v-model="selectedFinalFecha" v-bind:class="{ 'is-invalid': validarFinalFecha }"/>
									<div class="invalid-feedback">
								        {{messageInvalidFecha}}
								    </div>
								</div>
							</div>
 						 <button class="btn btn-dark btn-sm mt-3" type="submit" v-on:click="descargar()"><font-awesome-icon icon="download"/> Descargar</button>
						 <button class="btn btn-primary btn-sm mt-3" type="submit" v-on:click="llamarDatos">Generar</button> 		
						
					</div>
					<div class="col-md-4">
		                <label  for="validationTooltip01">Seleccione Tipo</label>
			                <div class="container_checkbox1 rounded">
			                   
			                      <div class="custom-control custom-checkbox">
			                      	<div class="ml-3">
			                      		<input  type="checkbox" class="custom-control-input" :disabled="onTipos" :value="1" :id="1" v-model="selectedTipo" v-bind:class="{ 'is-invalid': validarTipo }">
			                        	<label class="custom-control-label" :for="1">AVERIÓ</label>
			                      	</div>
			                        <div class="ml-3">
			                        	<input  type="checkbox" class="custom-control-input" :disabled="onTipos" :value="2" :id="2" v-model="selectedTipo" v-bind:class="{ 'is-invalid': validarTipo }">
			                        	<label class="custom-control-label" :for="2">VENCIDOS</label>
									</div>
			                        <div class="ml-3">
			                        	<input  type="checkbox" class="custom-control-input" :disabled="onTipos" :value="3" :id="3" v-model="selectedTipo" v-bind:class="{ 'is-invalid': validarTipo }">
			                        	<label class="custom-control-label" :for="3">ROBADO</label>
									</div>
			                        <div class="ml-3">
			                        	<input  type="checkbox" class="custom-control-input" :disabled="onTipos" :value="4" :id="4" v-model="selectedTipo" v-bind:class="{ 'is-invalid': validarTipo }">
			                        	<label class="custom-control-label" :for="4">MUESTRA</label>
									</div>
									<div class="ml-3">
										<input  type="checkbox" class="custom-control-input" :disabled="onTipos" :value="5" :id="5" v-model="selectedTipo" v-bind:class="{ 'is-invalid': validarTipo }">
			                        	<label class="custom-control-label" :for="5">EXTRAVIADO</label>
									</div>
			                        <div class="ml-3">
			                        	<input  type="checkbox" class="custom-control-input" :disabled="onTipos" :value="6" :id="6" v-model="selectedTipo" v-bind:class="{ 'is-invalid': validarTipo }">
			                       		<label class="custom-control-label" :for="6">REGALO</label>
									</div>
							        <div class="ml-3">
							        	<input  type="checkbox" class="custom-control-input" :disabled="onTipos" :value="7" :id="7" v-model="selectedTipo" v-bind:class="{ 'is-invalid': validarTipo }">
			                        	<label class="custom-control-label" :for="7">USO INTERNO</label>
									</div>
			                        
			                       
			                        
			                      </div>
			                    
			                </div>  
			            <div>
					        <div class="form-text text-danger">{{messageInvalidTipo}}</div>
					    </div>
						<div  class="custom-control custom-switch mt-3">
						  <input type="checkbox" class="custom-control-input" id="customSwitch1" v-model="onTipos" >
						  <label class="custom-control-label" for="customSwitch1" >Seleccionar todos los Tipo</label>
						</div>

		            </div>

				</div>




			</div>

		  </div>
			  	<div class="row">
				  		<div class="row mt-3">
		<!-- -------------------------------------------MOSTRAR DOWNLOADING----------------------------------------------- -->

						    <div class="col-md-12">
								<div v-if="descarga" class="ml-5 d-flex justify-content-center mt-3">
									Descargando...
						            <div class="spinner-grow text-primary" role="status" aria-hidden="true"></div>
						        </div>
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
		     		<table class="table table-striped table-hover table-light table-sm" v-if="responseSalida.length > 0">
					  <thead>
					    <tr>
					      <th scope="col">#</th>
					      <th scope="col">Tipo</th>
					      <th scope="col">Salida Piezas</th>
					      <th scope="col">Costo Total</th>
					    </tr>
					  </thead>
					  <tbody>
					    <tr v-for="(salida, index) in responseSalida" v-on:click="clicked(salida)"  data-toggle="modal" data-target="#exampleModalCenter">
					      <th scope="row">{{index+1}}</th>
					      <td>{{salida.TOTALES}}</td>
					      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(salida.SALIDA_PIEZAS)}}</td>
					      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(salida.COSTO_TOTAL)}}</td>
					    </tr>
					  </tbody>
					  <tfoot>
						<tr>
						  <th></th>
						  <th>TOTALES</th>
						  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseSalida.reduce((aca, item) => aca + parseInt(item.SALIDA_PIEZAS), 0))}}</th>
						  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseSalida.reduce((acc, item) => acc + item.COSTO_TOTAL, 0))}}</th>
						</tr>
					  </tfoot>
					</table>
		     	</div>

			  </div>
			  <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
				  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalCenterTitle">Tipo: <small>{{tipoTitulo}}</small></h5>
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
						      <th style="font-size: 12px" scope="col">DESCRIPCION</th>
						      <th style="font-size: 12px" scope="col">CATEGORIA</th>
						      <th style="font-size: 12px" scope="col">SUBCATEGORIA</th>
						      <th style="font-size: 12px" scope="col">NOMBRE</th>
						      <th style="font-size: 12px" scope="col">COMENTARIO</th>
						      <th style="font-size: 12px" scope="col">CANTIDAD</th>
						      <th style="font-size: 12px" scope="col">COSTO UNITARIO</th>
						      <th style="font-size: 12px" scope="col">COSTO TOTAL</th>
						    </tr>
						  </thead>
						  <tbody>
						    <tr style="font-size: 12px" v-for="(SalidasProductos, index) in filterProductos(responseSalidaProductos, datosFilas)">
						      <th scope="row">{{index+1}}</th>
						      <td>{{SalidasProductos.COD_PROD}}</td>
						       <td>{{SalidasProductos.LOTE}}</td>
						      <td>{{SalidasProductos.MARCA}}</td>
						      <td>{{SalidasProductos.CATEGORIA}}</td>
						      <td>{{SalidasProductos.SUBCATEGORIA}}</td>
						      <td>{{SalidasProductos.NOMBRE}}</td>
						      <td>{{SalidasProductos.COMENTARIO}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(SalidasProductos.CANTIDAD)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(SalidasProductos.COSTO_UNITARIO)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(SalidasProductos.COSTO_TOTAL)}}</td>
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

		</div>


		<!-- ------------------------------------------------------------------------ -->
	</div>
	<!-- FIN REPORTE DE SALIDA DE PRODUCTOS -->
</template>
<script >
	export default {
        data(){
            return {
              	sucursales: [],
              	selectedSucursal: 'null',
              	selectedTipo:[],
              	validarSucursal: false,
              	messageInvalidSucursal: '',
              	messageInvalidTipo:'',
              	selectedInicialFecha: '',
              	onTipos:false,
              	validarInicialFecha: false,
              	messageInvalidFecha: '',
              	selectedFinalFecha: '',
              	validarFinalFecha: false,
              	validarTipo: false,
              	descarga: false,
              	controlar: true,
              	datos: {},
	        	tipo: null,
	            cargado: false,
	            responseSalida: {},
              	responseSalidaProductos: [],
                datosFilas: null,
                tipoTitulo:'',
                varTotalSalida: [],
				varNombreTipo: [],
				candec:0,
				monedas_descripcion:''
            }
        }, 
        methods: {
        	llamarBusquedas(){	
	          axios.get('busquedas/').then((response) => {
	           	this.sucursales = response.data.sucursales;
	          });
	        },

	        descargar(){

	        	let me = this;	

	        	if(this.generarConsulta() === true){
	        		me.descarga = true;
		        	axios({
					  url: '/export_producto_salida',
					  method: 'POST',
					  data: me.datos,
					  responseType: 'blob', // important
					}).then((response) => {
						me.descarga = false;
					   const url = window.URL.createObjectURL(new Blob([response.data]));
					   const link = document.createElement('a');
					   link.href = url;
					   link.setAttribute('download', 'Salidas'+me.selectedInicialFecha+'_al_'+me.selectedFinalFecha+'.xlsx'); //or any other extension
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

	        	if(me.selectedInicialFecha === null || me.selectedInicialFecha === "") {
	        		me.validarInicialFecha = true;
	        		me.messageInvalidFecha = 'Por favor seleccione una fecha';
	        		me.controlar = false;
	        	} else {
	        		me.validarInicialFecha = false;
	        		me.messageInvalidFecha = '';
	        	}

	            if (me.selectedTipo ===[]) {
	                me.validarTipo = true;
	                me.controlar = false;
	            } else {
	                me.validarTipo = false;
	            }

	        	if(me.selectedFinalFecha === null || me.selectedFinalFecha === "") {
	        		me.validarFinalFecha = true;
	        		me.messageInvalidFecha = 'Por favor seleccione una fecha';
	        		me.controlar = false;
	        	} else {
	        		me.validarFinalFecha = false;
	        		me.messageInvalidFecha = '';
	        	}		

	        	if(me.controlar === false){
	        		me.controlar = true;
	        		return false;
	        	}

				me.datos = {
		        	Sucursal: me.selectedSucursal,
		        	Inicio: me.selectedInicialFecha,
		        	Final: me.selectedFinalFecha,
		        	Tipos: me.selectedTipo,
		        	AllTipos:me.onTipos
	        	};

	        	return true;
	        },

	        llamarDatos(){

	        	let me = this;

		        if(me.generarConsulta() === true) {

		        	me.cargado = true;
		         Common.generarReporteSalidaProductosCommon(this.datos).then(data => {
	        		  
             		    me.cargado = false;
						me.responseSalidaProductos = data.salidaProductos;
					    const salidasArray = Object.keys(data.salidas).map(i => data.salidas[i])
					    me.responseSalida = salidasArray
					   
					    me.loadSalidas();
              });


		        }
		    },
		     clicked(row) {
	       	  this.tipoTitulo = row.TOTALES; 	
		      this.datosFilas = row.TIPO;
		    },
		    filterProductos: function(items, codigo) {
			      return items.filter(function(item) {

			      
			      return item.TIPO === codigo;
			    })
			 },
		    loadSalidas(){
						let me = this;
		            if(me.varNombreTipo.length > 0){
		   				me.charSalida.destroy();
		           		}
						me.varNombreTipo = [];
						me.varTotalSalida = [];
						me.responseSalida.map(function(x){

							me.varNombreTipo.push(x.TOTALES);
							me.varTotalSalida.push(x.COSTO_TOTAL);
						});

						me.varSeccion = document.getElementById('secciones').getContext('2d');


						 me.charSalida = new Chart(me.varSeccion, {
						    type: 'bar',
						    data: {
						        labels: me.varNombreTipo,
						        datasets: [{
						            label: 'Secciones',
						            data: me.varTotalSalida,
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
			this.llamarBusquedas();
			var tableSalidaProducto = $('#tablaSalidaProducto').DataTable();
        }
    }    
</script>