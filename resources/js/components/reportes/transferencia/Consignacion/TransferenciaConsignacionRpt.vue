<template>
	<!-- TRANSFERENCIA A CONSIGNACIONv-if="$can('reporte.venta')" -->
	<div>
		<!-- INICIO DE TARJETA -->
		<div class="card mt-3 shadow border-bottom-primary" >
		  <div class="card-header">Transferencia a Consignación</div>
			<!-- CUERPO DE LA TARJETA -->
			<div class="card-body">
			  <div class="row">
				<div class="col-4">

					<!-- -------------------------------------------SELECCIONAR SUCURSAL---------------------------------------- -->
					
				  	<label for="validationTooltip01">Sucursal</label>
					<select class="custom-select custom-select-sm" v-on:change="habilitar_insert" v-bind:class="{ 'is-invalid': validarSucursal }" v-model="selectedSucursal">
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
				</div>

				<!-- ------------------------------------------- RADIO AGRUPAR ----------------------------------------------- -->
				
				<div class="col-2">
					<label  for="validationTooltip01">Agrupar por:</label> 
					<div class="form-check form-check">
	  		            <input v-model="radioAgrupar" v-on:change="" class="form-check-input" type="radio" name="radioOptions" id="radio0" value="0" v-bind:class="{ 'is-invalid': validarRadio }" v-on:change="habilitar_insert">
	  		            <label class="form-check-label" for="radio0">Proveedor</label>
	  		        </div>
	  		        <div class="form-check form-check">
	  		            <input  v-model="radioAgrupar"  v-on:change="" class="form-check-input" type="radio" name="radioOptions" id="radio1" value="1" v-bind:class="{ 'is-invalid': validarRadio }" v-on:change="habilitar_insert">
	  		            <label class="form-check-label" for="radio1">Sucursal</label>
	  		        </div>
	  		        <div class="invalid-feedback">
					    {{messageInvalidRadio}}
					</div>
				</div>

				<!-- ------------------------------------------- SUCURSAL ORIGEN ---------------------------------------------- -->

				<div class="col-md-3">
					<label for="validationTooltip01">Seleccione Sucursal Origen</label> 
					<select multiple class="form-control" size="4" v-model="selectedSucursalOrigen" :disabled="onSucursalOrigen" v-bind:class="{ 'is-invalid': validarSucursalOrigen }" v-on:change="habilitar_insert">
						<option v-for="sucursal in sucursales " :value="sucursal.CODIGO">{{ sucursal.DESCRIPCION }}</option>
					</select>
					<div class="invalid-feedback">
					    {{messageInvalidSucursalOrigen}}
					</div>
					<div class="custom-control custom-switch mt-3">
						<input type="checkbox" class="custom-control-input" id="customSwitch1" v-model="onSucursalOrigen" v-on:change="habilitar_insert">
						<label class="custom-control-label" for="customSwitch1">Seleccionar todas</label>
					</div>
				</div>

				<!-- ------------------------------------------- PROVEEDOR ----------------------------------------------- -->
				
				<div class="col-md-3">
					<label  for="validationTooltip01">Seleccione Proveedor</label> 
					<select multiple class="form-control" size="4" v-model="selectedProveedor" :disabled="onProveedor" v-bind:class="{ 'is-invalid': validarProveedor }" v-on:change="habilitar_insert">
						  <option v-for="proveedores in proveedores" :value="proveedores.CODIGO">{{ proveedores.NOMBRE }}</option>
					</select>
					<div class="invalid-feedback">
					    {{messageInvalidProveedor}}
					</div>
					<div class="custom-control custom-switch mt-3">
						<input type="checkbox" class="custom-control-input" id="customSwitch2" v-model="onProveedor" v-on:change="habilitar_insert">
						<label class="custom-control-label" for="customSwitch2">Seleccionar todos</label>
					</div>
				</div>
			  </div>
				<!-- -------------------------------------------MOSTRAR BOTONES----------------------------------------------- -->

					<div class="col-12">
						<button class="btn btn-dark btn-sm" type="submit" v-on:click="descargar()"><font-awesome-icon icon="download"/> Descargar</button>
						<button class="btn btn-primary btn-sm" type="submit" v-on:click="llamarDatos">Generar</button> 
					</div>
			  
			<!-- FINAL DEL CUERPO -->
			</div>

		<!-- FINAL DE TARJETA -->
		</div>
		<!-- ------------------------------------------------------------------------ -->
		
		<!-- <div v-else>
			<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div> -->

		<!-- ------------------------------------------------------------------------ -->

		<!-- -------------------------------------------MOSTRAR DOWNLOADING------------------------------------------ -->

		<div class="col-md-12 mt-3">
			<div v-if="descarga" class="ml-5 d-flex justify-content-center">
					Descargando...
				<div class="spinner-grow text-primary" role="status" aria-hidden="true"></div>
			</div>
		</div>

		<!-- -------------------------------------------MOSTRAR CARGANDO--------------------------------------------- -->

		<div class="col-md-12">
			<div v-if="cargado" class="ml-5 d-flex justify-content-center">
				Cargando...
		        <div class="spinner-grow" role="status" aria-hidden="true"></div>
		    </div>
	    </div>

	    <!-- -------------------------------------------CHART SUCURSALES------------------------------------------- -->

      	<div class="col-md-12">
	        <div class="card-body">
				<div class="ct-chart">
					<canvas id="sucursales">
								
					</canvas>
				</div>
			</div>
	    </div>

	    <!-- -------------------------------------------TABLA------------------------------------------- -->

	    <div class="col-md-12">
		    <table class="table table-striped table-hover table-light table-sm" v-if="responseSucursal.length > 0">
				<thead>
					<tr>
					    <th scope="col">#</th>
					    <th scope="col">Sucursal</th>
					    <th scope="col">Vendido</th>
					    <th scope="col">Descuento</th>
					    <th scope="col">Costo Total</th>
					    <th scope="col">Totales</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(sucursal, index) in responseSucursal" v-on:click="clicked(sucursal)"  data-toggle="modal" data-target="#exampleModalCenter">
					    <th scope="row">{{index+1}}</th>
					    <td>{{sucursal.TOTALES}}</td>
					    <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(sucursal.VENDIDO)}}</td>
					    <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(sucursal.DESCUENTO)}}</td>
					    <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(sucursal.COSTO)}}</td>
					    <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(sucursal.TOTAL)}}</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<th></th>
						<th>TOTALES</th>
						<th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseSucursal.reduce((aca, item) => aca + parseInt(item.VENDIDO), 0))}}</th>
						<th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseSucursal.reduce((acc, item) => acc + item.DESCUENTO, 0))}}</th>
						<th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseSucursal.reduce((acc, item) => acc + item.COSTO, 0))}}</th>
						<th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseSucursal.reduce((acc, item) => acc + item.TOTAL, 0))}}</th>
					</tr>
				</tfoot>
			</table>
		</div>

		<!-- -------------------------------------MODAL DE TABLA PARA DATOS------------------------------------- -->

		<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
				<div class="modal-content">
				    <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalCenterTitle">Sucursal: <small>{{sucursalTitulo}}</small></h5>
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
						    <tr style="font-size: 12px" v-for="(transferencia, index) in filterProductos(responseTransferencia, datosFilas)">
						      <th scope="row">{{index+1}}</th>
						      <td>{{transferencia.COD_PROD}}</td>
						       <td>{{transferencia.LOTE}}</td>
						      <td>{{transferencia.MARCA}}</td>
						      <td>{{transferencia.CATEGORIA}}</td>
						      <td>{{transferencia.SUBCATEGORIA}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(transferencia.STOCK)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(transferencia.VENDIDO)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(transferencia.PRECIO_UNIT)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(transferencia.TOTAL)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(transferencia.DESCUENTO)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(transferencia.COSTO_UNIT)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(transferencia.COSTO_TOTAL)}}</td>
						      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(transferencia.DESCUENTO_PORCENTAJE)}}</td>
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

		<!-- ----------------------------------------TABLA DE DATOS---------------------------------------- -->

		<div class="col-md-12">
			<div class="card border-left-primary mt-3" v-for="sucursal in responseSucursal">
				<div class="row">
						
					<div class="col-md-6">
						<div class="card-header font-weight-bold text-primary">
							{{sucursales.TOTALES}}
						</div>
					</div>
					<div class="col-md-6">
						<div class="card-header font-weight-bold text-primary text-right">
							{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(sucursal.TOTAL)}}
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
							<tr v-for="(proveedor, index) in filterItems(responseProveedor, sucursal.MARCAS)">
							    <th scope="row">{{index+1}}</th>
							    <td>{{proveedor.DESCRI_L}}</td>
							    <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(proveedor.VENDIDO)}}</td>
							    <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(proveedor.DESCUENTO)}}</td>
							    <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(proveedor.COSTO_TOTAL)}}</td>
							    <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(proveedor.TOTAL)}}</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<th></th>
								<th>TOTALES</th>
								<th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(sucursal.VENDIDO)}}</th>
								<th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(sucursal.DESCUENTO)}}</th>
								<th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(sucursal.COSTO)}}</th>
								<th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(sucursal.TOTAL)}}</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>

	    <!-- -------------------------------------------FIN TABLA------------------------------------------- -->
	</div>

	<!-- TRANSFERENCIA A CONSIGNACION -->
</template>

<script >
	export default {
      props: ['candec', 'descripcion'],
        data(){
            return {
              	sucursales: [],
              	proveedores: [],
              	selectedProveedor: [],
              	selectedSucursalOrigen: [],
              	datos: {},
              	selectedSucursal: '',
              	messageInvalidSucursal: '',
              	messageInvalidSucursalOrigen: '',
              	messageInvalidFecha: '',
              	messageInvalidProveedor: '',
              	messageInvalidRadio: '',
              	selectedInicialFecha: '',
              	selectedFinalFecha: '',
              	validarSucursal: false,
              	validarSucursalOrigen: false,
              	validarInicialFecha: false,
              	validarFinalFecha: false,
              	validarProveedor: false,
              	validarRadio: false,
              	cargado: false,
              	descarga: false,
              	onSucursalOrigen: false,
              	onProveedor: false,
              	radioAgrupar: '',
              	controlar: true,
              	responseTransferencia: [],
              	responseProveedor: [],
              	responseSucursal: [],
              	sucursalArray: [],
              	proveedorArray: [],
              	varNombreSucursal: [],
              	varTotalSucursal: [],
              	charSucursal: '',
              	sucursalTitulo: '',
              	datosFilas: null,
              	insert: true
            }
        }, 
        methods: {
        	habilitar_insert() {
	       	  let me = this;
	       	  me.insert = true;
		    },

        	clicked(row) {
	       	  this.sucursalTitulo = row.TOTALES; 	
		      this.datosFilas = row.SUCURSALES;
		    },

			filterItems: function(items, codigo) {

			    return items.filter(function(item) {
					return item.SUCURSAL === codigo;
			    })
			},

			filterProductos: function(items, codigo) {

			    return items.filter(function(item) {
					return item.MARCAS_CODIGO === codigo;
			    })
			},

        	llamarBusquedas(){	
	          axios.get('busquedas/').then((response) => {
	           	this.sucursales = response.data.sucursales;
	           	this.proveedores = response.data.proveedores;
	          });
	        },

	        descargar(){
	        	let me = this;	

	        	if(me.generarConsulta() === true) {

	        		me.descarga = true;

		        	axios({
					  url: '/export-transferencia-consignacion',
					  method: 'POST',
					  data: me.datos,
					  responseType: 'blob', // important
					}).then((response) => {
						me.descarga = false;
					   const url = window.URL.createObjectURL(new Blob([response.data]));
					   const link = document.createElement('a');
					   link.href = url;
					   link.setAttribute('download', 'Transferencia-Consignacion'+me.selectedInicialFecha+'al'+me.selectedFinalFecha+'.xlsx'); //or any other extension
					   document.body.appendChild(link);
					   link.click();
					});  	
				}
				me.insert = false;
				me.controlar = true;
	        },

	        llamarDatos(){
	        	let me = this;	
	        	
	        	if(me.generarConsulta() === true) {
	        		
	        		me.cargado = true;
	        		Common.generarReporteTransferenciaConsignacionCommon(this.datos).then(data => {
	        		  
             			me.cargado = false;
						me.responseTransferencia = data.transferencia;
					    const sucursalArray = Object.keys(data.sucursales).map(i => data.sucursales[i])
					    me.responseSucursal = sucursalArray
					    const proveedorArray = Object.keys(data.proveedores).map(i => data.proveedores[i])
					    me.responseProveedor = proveedorArray
					    me.loadSucursales();
              		});
				} 
				me.controlar = true;
	        	me.insert=false;
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
	        		me.messageInvalidFecha = 'Por favor seleccione una fecha Inicial';
	        		me.controlar = false;
	        	} else {
	        		me.validarInicialFecha = false;
	        		me.messageInvalidFecha = '';
	        	}

	        	if(me.selectedFinalFecha === null || me.selectedFinalFecha === "") {
	        		me.validarFinalFecha = true;
	        		me.messageInvalidFecha = 'Por favor seleccione una fecha Final';
	        		me.controlar = false;
	        	} else {
	        		me.validarFinalFecha = false;
	        		me.messageInvalidFecha = '';
	        	}		
	        	if(me.onSucursalOrigen === false && me.selectedSucursalOrigen.length === 0) {
	        		me.validarSucursalOrigen = true;
	        		me.messageInvalidSucursalOrigen = 'Por favor seleccione una o varias sucursales de origen';
	        		me.controlar=false;
	        	} else {
	   
	        		me.validarSucursalOrigen = false;
	        		me.messageInvalidSucursalOrigen = '';
	        	}

	        	if(me.onProveedor === false && me.selectedProveedor.length === 0) {

	        		me.validarProveedor = true;
	        		me.messageInvalidProveedor = 'Por favor seleccione uno o varios proveedores';
	        		me.controlar=false;
	        	} else {
	        		me.validarProveedor = false;
	        		me.messageInvalidProveedor = '';
	        	}

	        	if(me.radioAgrupar === '') {

	        		me.validarRadio = true;
	        		me.messageInvalidRadio = 'Por favor seleccione una opción';
	        		me.controlar=false;
	        	} else {
	        		me.validarRadio = false;
	        		me.messageInvalidRadio = '';
	        	}
	        	if(me.controlar === false){
	        		return false;
	        	}


	        	if(me.onSucursalOrigen === true) {
		        	for (var key in me.sucursales){
		        		me.selectedSucursalOrigen[key] = me.sucursales[key].CODIGO;
		        	}
		        } 

		        if(me.onProveedor === true) {
		        	for (var key in me.proveedores){
		        		me.selectedProveedor[key] = me.proveedores[key].CODIGO;
		        	}
		        }

		       	me.datos = {
			        	Sucursal: me.selectedSucursal,
			        	Inicio: String(me.selectedInicialFecha),
			        	Final: String(me.selectedFinalFecha),
			        	Proveedores: me.selectedProveedor,
			        	SucursalOrigen: me.selectedSucursalOrigen,
			        	AllProveedores: me.onProveedor,
			        	AllSucursales: me.onSucursalOrigen,
			        	Agrupar: me.radioAgrupar, 
	        			Insert:me.insert
		        	};

	        	return true;
	        },

	        loadSucursales(){

				let me = this;

	            if(me.varNombreSucursal.length > 0){
	   				me.charSucursal.destroy();
	           	}

				me.varNombreSucursal = [];
				me.varTotalSucursal = [];

				me.responseMarca.map(function(x){

					me.varNombreSucursal.push(x.TOTALES);
					me.varTotalSucursal.push(x.TOTAL);
				});

				me.varSucursal = document.getElementById('sucursal').getContext('2d');

				me.charSucursal = new Chart(me.varSucursal, {
				    type: 'bar',
				    data: {
				        labels: me.varNombreSucursal,
				        datasets: [{
				            label: 'Sucursales',
				            data: me.varTotalSucursal,
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
				                      
				                      return me.descripcion + ' ' + new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(value) + '';
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
			     		"changeDate", () => {
			     			me.insert=true;
			     			console.log(me.insert);
			     			me.selectedInicialFecha = $('#selectedInicialFecha').val()
			     		}
					);
					$("#selectedFinalFecha").datepicker().on(
			     		"changeDate", () => {
			     			me.insert=true;
			     			console.log(me.insert);
			     			me.selectedFinalFecha = $('#selectedFinalFecha').val()
			     		}
					);

					$('table').dataTable();
			});

			this.llamarBusquedas();
        }
    }    
</script>