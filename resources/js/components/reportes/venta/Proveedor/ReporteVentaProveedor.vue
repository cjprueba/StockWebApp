<template>
	<!-- REPORTE DE VENTAS POR PROVEEDOR  -->
	<div v-if="$can('reporte.venta')">
		<!-- INICIO DE TARJETA -->
		<div class="card mt-3 shadow border-bottom-primary" >
		  <div class="card-header">Ventas Por Proveedores</div>
			<!-- CUERPO DE LA TARJETA -->
			<div class="card-body">
			  <div class="row">
				<div class="col-3">

					<!-- -------------------------------------------SELECCIONAR SUCURSAL---------------------------------------- -->
					
				  	<label for="validationTooltip01">Seleccione Sucursal</label>
					<select v-on:change="habilitar_insert" class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSucursal }" v-model="selectedSucursal">
						<option value="null">Seleccionar</option>
						<option v-for="sucursal in sucursales" :value="sucursal.CODIGO">{{ sucursal.DESCRIPCION }}</option>
					</select>
					<div class="invalid-feedback">
						{{messageInvalidSucursal}}
					</div>

					<!-- ----------------------------------------SELECCIONAR FECHA---------------------------------------- -->

					<label class="mt-3">Seleccione Intervalo de Tiempo</label>
					<div id="sandbox-container" class="input-daterange input-group">
						<input v-on:change="habilitar_insert" id='selectedInicialFecha' class="input-sm form-control form-control-sm" v-model="selectedInicialFecha" v-bind:class="{ 'is-invalid': validarInicialFecha }"/>
						<div class="input-group-append form-control-sm">
							<span class="input-group-text">a</span>
						</div>
						<input v-on:change="habilitar_insert" name='end' id='selectedFinalFecha' class="input-sm form-control form-control-sm" v-model="selectedFinalFecha" v-bind:class="{ 'is-invalid': validarFinalFecha }"/>
						<div class="invalid-feedback">
						    {{messageInvalidFecha}}
						</div>
					</div>

					<!-- -------------------------------------- BOTON DESCARGAR--------------------------------------------- -->

					<div class="r-md-3 mt-4">
						<button class="btn btn-dark btn-sm" type="submit" v-on:click="descargar()">Descargar</button> 
						<button class="btn btn-primary btn-sm" type="submit" v-on:click="llamarDatos">Generar</button> 
					</div>
				</div>

				<!-- -------------------------------------------MOSTRAR TIPO----------------------------------------------- -->

				<div class="col-3 mb-3 ">
					<div class="row ml-3">
						<div class="col-sm-3">
							<label>Tipo de Venta:</label>
						</div>
						<div class="col-sm">
							<div class="form-check mt-3">
							  <input v-on:change="habilitar_insert" v-model="selectedTipo" class="form-check-input" type="checkbox" value="CO" id="contado">
							  <label class="form-check-label" for="contado">
							    Contado
							  </label>
							</div>
							<div class="form-check">
							  <input v-on:change="habilitar_insert" v-model="selectedTipo" class="form-check-input" type="checkbox" value="CR" id="credito">
							  <label class="form-check-label" for="credito">
							    Crédito
							  </label>
							</div>
							<div class="form-check">
							  <input v-on:change="habilitar_insert" v-model="selectedTipo" class="form-check-input" type="checkbox" value="PE" id="pagoAlEntregar">
							  <label class="form-check-label" for="pagoAlEntregar">
							    Pago Al Entregar
							  </label>
							</div>
	    				</div>
	    				 <div class="col-md-12">
							<div class="form-text text-primary">Obs: Si no marca ninguna casilla se ejecutará de forma general</div>
						</div>
					</div>

					<!-- ------------------------------------------- RADIO AGRUPAR ----------------------------------------------- -->

					<div class="row ml-3">	
						
						<div class="col-sm-3">
							<label aling=left>Agrupar por:</label>
						</div>
						<div class="col-sm">
							<div class="form-check mt-3">
							  <input  v-model="agruparProveedor" class="form-check-input" type="checkbox" id="prov">
							  <label class="form-check-label" for="prov">
							    Proveedor
							  </label>
							</div>
							<div class="form-check">
							  <input  v-model="agruparCategoria" class="form-check-input" type="checkbox" id="cat">
							  <label class="form-check-label" for="cat">
							    Categoría
							  </label>
							</div>
						</div>
	    				<div class="col-sm-12">
							<div class="form-text text-danger">{{messageInvalidAgrupar}}</div>
						</div>
					</div>
				</div>
				
				<!-- ------------------------------------------- PROVEEDOR ----------------------------------------------- -->
				
				<div class="col-md-3">
					<label for="validationTooltip01">Seleccione Proveedor</label> 
					<select v-on:change="habilitar_insert" multiple class="form-control" size="5" v-model="selectedProveedor" :disabled="onProveedor" v-bind:class="{ 'is-invalid': validarProveedor }">
						<option v-for="proveedor in proveedores" :value="proveedor.CODIGO">{{proveedor.NOMBRE}}</option>
					</select>
					<div class="invalid-feedback">
					    {{messageInvalidProveedor}}
					</div>
					<div class="custom-control custom-switch mt-3">
						<input v-on:change="habilitar_insert" type="checkbox" class="custom-control-input" id="customSwitch1" v-model="onProveedor">
						<label class="custom-control-label" for="customSwitch1">Seleccionar todos</label>
					</div>
	            </div>

				<div class="col-md-3">
					<label for="validationTooltip02">Seleccione Categoría</label> 
					<select v-on:change="habilitar_insert" multiple class="form-control" size="5" v-model="selectedCategoria" :disabled="onCategoria" v-bind:class="{ 'is-invalid': validarCategoria }">
						<option v-for="categoria in categorias" :value="categoria.CODIGO">{{ categoria.DESCRIPCION }}</option>
					</select>
					<div class="invalid-feedback">
					    {{messageInvalidCategoria}}
					</div>
					<div class="custom-control custom-switch mt-3">
						<input v-on:change="habilitar_insert" type="checkbox" class="custom-control-input" id="customSwitch2" v-model="onCategoria">
						<label class="custom-control-label" for="customSwitch2">Seleccionar todas las Categorías</label>
					</div>
				</div> 
			  </div>	

				
            
            	<!-- CHART MARCAS -->



			<!-- FINAL DEL CUERPO -->
			<!-- CARD -->
			</div>
		 <!-- FINAL DE TARJETA -->
			
	
		</div>
		<!-- ------------------------------------------- SPINNER DESCARGA --------------------------------------------- -->
				<div class="row">
					<div class="col-md-12">
						<div v-if="descarga" class="d-flex justify-content-center mt-3">
							<strong>Descargando...   </strong>
			                <div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
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
		             <div class="col-md-12">
			                <div class="card-body">
								<div class="ct-chart">
									<canvas id="proveedores">
										
									</canvas>
								</div>
							</div>
			    	</div> 

            	<div class="col-md-12">
		     		<table class="table table-striped table-hover table-light table-sm" v-if="responseProveedor.length > 0">
					  <thead>
					    <tr>
					      <th scope="col">#</th>
					      <th scope="col">Proveedor</th>
					      <th scope="col">Vendido</th>
					      <th scope="col">Descuento</th>
					      <th scope="col">Costo Total</th>
					      <th scope="col">Totales</th>
					    </tr>
					  </thead>
					  <tbody>
					    <tr v-for="(proveedor, index) in responseProveedor" v-on:click="clicked(proveedor)"  data-toggle="modal" data-target="#exampleModalCenter">
					      <th scope="row">{{index+1}}</th>
					      <td>{{proveedor.TOTALES}}</td>
					      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(proveedor.VENDIDO)}}</td>
					      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(proveedor.DESCUENTO)}}</td>
					      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(proveedor.COSTO)}}</td>
					      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(proveedor.TOTAL)}}</td>
					    </tr>
					  </tbody>
					  <tfoot>
						<tr>
						  <th></th>
						  <th>TOTALES</th>
						  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseProveedor.reduce((aca, item) => aca + parseInt(item.VENDIDO), 0))}}</th>
						  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseProveedor.reduce((acc, item) => acc + item.DESCUENTO, 0))}}</th>
						   <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseProveedor.reduce((acc, item) => acc + item.COSTO, 0))}}</th>
						  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(responseProveedor.reduce((acc, item) => acc + item.TOTAL, 0))}}</th>
						</tr>
					  </tfoot>
					</table>
		     	</div>
		     		<!-- MODAL DE TABLA PARA DATOS CRUDOS -->
			<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
				  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalCenterTitle">Proveedor: <small>{{proveedorTitulo}}</small></h5>
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
						    <tr style="font-size: 12px" v-for="(venta, index) in filterProductos(responseVenta, datosFilas,datosLineas,agrupar_proveedor_control,agrupar_categoria_control)">
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
			 <div v-if="(agrupar_proveedor_control && !agrupar_categoria_control)" class="col-md-12">
				<div class="card border-left-primary mt-3" v-for="proveedor in responseProveedor">
					<div class="row">
						
						<div class="col-md-6">
							  <div class="card-header font-weight-bold text-primary">
							    {{proveedor.TOTALES}}
							  </div>
					    </div>
					    <div class="col-md-6">
							  <div class="card-header font-weight-bold text-primary text-right">
							    {{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(proveedor.TOTAL)}}
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
						    <tr v-for="(categoria, index) in filterItems(responseCategoria, proveedor.PROVEEDORES)">
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
							  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(proveedor.VENDIDO)}}</th>
							  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(proveedor.DESCUENTO)}}</th>
							  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(proveedor.COSTO_TOTAL)}}</th>
							  <th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(proveedor.TOTAL)}}</th>
							</tr>
						  </tfoot>
						</table>
					</div>
				</div>
			</div>

	</div>

	<!-- ------------------------------------------------------------------------ -->

	<div v-else>
		<cuatrocientos-cuatro></cuatrocientos-cuatro>
	</div>

	<!-- ------------------------------------------------------------------------ -->

	<!-- REPORTE TOP VENTAS  -->
</template>

<script >
	export default {
   
        data(){
            return {
              	sucursales: [],
              	proveedores: [],
              	categorias: [],
              	selectedProveedor: [],
              	selectedCategoria: [],
              	selectedSucursal: "null",
              	selectedInicialFecha: '',
              	selectedFinalFecha: '',
              	messageInvalidSucursal: '',
              	messageInvalidFecha: '',
              	messageInvalidProveedor: '',
              	messageInvalidCategoria: '',
              	candec:0,
              	monedas_descripcion:'',
              	messageInvalidTipo: '',
              	messageInvalidAgrupar: '',
              	validarSucursal: false,
              	validarInicialFecha: false,
              	validarFinalFecha: false,
              	validarProveedor: false,
              	validarCategoria: false,
              	validarRadio: false,
              	datos: {},
              	descarga: false,
              	cargado:false,
              	onProveedor: false,
              	onCategoria: false,
              	controlar: true,
              	selectedTipo: [],
              	agruparCategoria: false,
              	agruparProveedor: false,
              	responseProveedor: {},
              	responseCategoria: [],
              	responseVenta: [],
              	varTotalProveedor: [],
				varNombreProveedor: [],
				proveedorTitulo: '',
				datosFilas: null,
				controlar_tipo:false,
				insert:true,
				datosLineas:null,
				agrupar_proveedor_control:false,
				agrupar_categoria_control:false
            }
        }, 
        methods: {

        	llamarBusquedas(){	
	          axios.get('busquedas/').then((response) => {
	           	this.sucursales=response.data.sucursales;
	           	this.categorias = response.data.categorias;
	           	this.proveedores = response.data.proveedores;
	          });
	        },
			
			llamarDatos(){
	        	let me = this;	
	        	
	        	if(me.generarConsulta() === true) {
	        		

	        			me.cargado = true;
	        	Common.generarReporteVentaProveedorCommon(this.datos).then(data => {
	        		    me.agrupar_categoria_control=me.agruparCategoria;
	        		    me.agrupar_proveedor_control=me.agruparProveedor;
	        		  
             				me.cargado = false;
						me.responseVenta = data.ventas;
					    const ProveedorArray = Object.keys(data.proveedores).map(i => data.proveedores[i])
					    me.responseProveedor = ProveedorArray
					   
					   
					    const categoriaArray = Object.keys(data.categorias).map(i => data.categorias[i])
					    me.responseCategoria = categoriaArray
					    me.loadProveedores();
					    me.insert=false;
              				});

			 		
				}
	        	me.controlar = true;
	        },
	        descargar(){
	        	
	        	let me = this;	
	        	
	        	if(this.generarConsulta() === true){
	        		me.descarga = true;
		        	axios({
					  url: '/export_venta_proveedor',
					  method: 'POST',
					  data: me.datos,
					  responseType: 'blob', // important
					}).then((response) => {
						me.descarga = false;
					   const url = window.URL.createObjectURL(new Blob([response.data]));
					   const link = document.createElement('a');
					   link.href = url;
					   link.setAttribute('download', 'Venta_Proveedor_'+me.selectedInicialFecha+' al '+me.selectedFinalFecha+'.xlsx'); //or any other extension
					   document.body.appendChild(link);
					   link.click();
					});
				}
					me.insert=false;
				me.controlar = true;
	        },
	        clicked(row) {
	       	  this.proveedorTitulo = row.TOTALES; 	
		      this.datosFilas = row.PROVEEDORES;
		      this.datosLineas=row.LINEAS;
		    },
	        filterProductos: function(items, codigo_p,codigo_l,agrupar_proveedor,agrupar_categoria) {
				
			      return items.filter(function(item) {
			     /* 	console.log(item);*/


			      if(agrupar_proveedor===true && agrupar_categoria===true){
			      		 return (item.PROVEEDOR_CODIGO === codigo_p && item.LINEAS_CODIGO===codigo_l);
			      }else{
			      	if(agrupar_proveedor===true){
			      			 return (item.PROVEEDOR_CODIGO === codigo_p);
			      	}else{
			      		if(agrupar_categoria===true){
			      			 return (item.LINEAS_CODIGO===codigo_l);
			      		}
			      	}
			      }
			     
			    })
			 },
			 filterItems: function(items, codigo) {

			      return items.filter(function(item) {

                 
			      
			      return item.PROVEEDOR === codigo;
			    })
			 },

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

	        	if(me.selectedTipo === [] || me.selectedTipo.length === 0) {
	        			me.controlar_tipo = true;
						
	        	} else {
	        		me.controlar_tipo = false;
	        	}

	        	if(me.agruparCategoria === false && me.agruparProveedor === false) {
	        		me.messageInvalidAgrupar = 'Por favor seleccione tipo de agrupación.';
	        		me.controlar = false;
	        	} else {
	        		me.messageInvalidAgrupar = '';
	        	}

	        	if(me.onCategoria === false && me.selectedCategoria.length===0){

	        		me.validarCategoria = true;
	        		me.messageInvalidCategoria = 'Por favor seleccione una o varias Categorías.';
	        		me.controlar=false;
	        	} else {
	        		me.validarCategoria = false;
	        		me.messageInvalidCategoria = '';
	        	}

	        	if(me.onProveedor === false && me.selectedProveedor.length === 0) {
	        		me.messageInvalidProveedor = 'Por favor seleccione uno o varios Proveedores.';
	        		me.validarProveedor = true;
	        		me.controlar=false;
	        	} else {
	        		me.messageInvalidProveedor = '';
	        		me.validarProveedor = false;
	        	}

	        	if(me.controlar===false){
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
		        if(me.controlar_tipo===true){
		        		me.datos = {
				        	Sucursal: me.selectedSucursal,
				        	Inicio: me.selectedInicialFecha,
				        	Final: me.selectedFinalFecha,
				        	Categorias: me.selectedCategoria,
				        	AllCategory: me.onCategoria,
							Proveedores: me.selectedProveedor,
							AllProveedores: me.onProveedor,
							AgruparProveedor: me.agruparProveedor,
							Insert:me.insert,
							agruparCategoria: me.agruparCategoria
			        	};
		        	}else{
		        		me.datos = {
				        	Sucursal: me.selectedSucursal,
				        	Inicio: me.selectedInicialFecha,
				        	Final: me.selectedFinalFecha,
				        	Categorias: me.selectedCategoria,
				        	AllCategory: me.onCategoria,
							Proveedores: me.selectedProveedor,
							AllProveedores: me.onProveedor,
							Tipo: me.selectedTipo,
							AgruparProveedor: me.agruparProveedor,
							Insert:me.insert,
							agruparCategoria: me.agruparCategoria
			        	};
		        	}

	        	

	        	return true;
	        },
	        loadProveedores(){
				let me = this;
		            if(me.varNombreProveedor.length > 0){
		   				me.charMarca.destroy();
		           		}
						me.varNombreProveedor = [];
						me.varTotalProveedor = [];
						me.responseProveedor.map(function(x){

							me.varNombreProveedor.push(x.TOTALES);
							me.varTotalProveedor.push(x.TOTAL);
						});

						me.varMarca = document.getElementById('proveedores').getContext('2d');


						 me.charMarca = new Chart(me.varMarca, {
						    type: 'bar',
						    data: {
						        labels: me.varNombreProveedor,
						        datasets: [{
						            label: 'Proveedores',
						            data: me.varTotalProveedor,
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


			},
		    habilitar_insert() {
	       	  let me=this;
	       	  me.insert=true;
	       	 
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

			var tableVentaTop = $('#tablaVentaProveedor').DataTable();
        }
    }    
</script>

<style>
	.form-text{
        font-size: 12px;
	}
</style>