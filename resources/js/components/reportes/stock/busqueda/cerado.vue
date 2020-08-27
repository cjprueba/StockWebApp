<template>
		<!-- VENTA POR MARCA Y CATEGORIA -->
		<!-- v-if="$can('reporte.stock')" -->
	<div class="container-fluid mt-3">

		<div class="alert alert-info" role="alert">
		  <h4 class="alert-heading">Aviso !</h4>
		  <p>La ejecución de este reporte solo sera válido para Tokutokuya.</p>
		  <hr>
		  <p class="mb-0">Para que tome efecto en otras tiendas tendrán que empezar a usar el POS web.</p>
		</div>

		<div class="card shadow border-bottom-primary" >
		  	<div class="card-header">Stock cerado por fecha</div>
			<div class="card-body">
			  	<div class="form-row">

					<!-- ------------------------------------------------------------------------------------- -->

					<!-- SELECCIONE SUCURSAL -->

			  		<div class="col-md-6 mb-3">
			  			
			  			<label for="validationTooltip01">Seleccione Sucursal</label>
						<select class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSucursal }" v-model="selectedSucursal">
							 <option value="null" selected>Seleccionar</option>
							 <option v-for="sucursal in sucursales" :value="sucursal.CODIGO">{{ sucursal.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
					        {{messageInvalidSucursal}}
					    </div>

					</div>

					<!-- ------------------------------------------------------------------------ -->

					<!-- SELECCIONE FECHA -->

					<div class="col-md-6 mb-3">

					  	<label for="validationTooltip01">Seleccione Fecha</label>
						<div id="sandbox-container">
							<div class="input-daterange input-group">
								   <input type="text" class="input-sm form-control form-control-sm" id="selectedInicialFecha" v-model="selectedInicialFecha" v-bind:class="{ 'is-invalid': validarInicialFecha }"/>
								   <div class="input-group-prepend form-control-sm">
								   		<span class="input-group-text">a</span>
								   </div>
								   <input type="text" class="input-sm form-control form-control-sm" name="end" id="selectedFinalFecha" v-model="selectedFinalFecha" v-bind:class="{ 'is-invalid': validarFinalFecha }"/>
							</div>
							<div class="invalid-feedback">
					        	{{messageInvalidFecha}}
					    	</div>
						</div>

					</div>

					<!-- ------------------------------------------------------------------------ -->

				</div>

				<!-- ------------------------------------------------------------------------ -->

				<!-- BOTONES -->

				<button class="btn btn-primary btn-sm" type="submit" v-on:click="llamarDatos()">Generar</button>

				<!-- ------------------------------------------------------------------------ -->

			</div>
		</div>

		<div class="col-md-12 mt-3">
			<table id="tablaStockVacio" class="table table-sm" style="width:100%">
	            <thead>
	              <tr>
	                <th scope="col">Producto</th>
	                <th scope="col">Descripcion</th>
	                <th scope="col">Stock</th>
	                <th scope="col">Fecha Termino</th>
	                <th scope="col">Imagen</th>
	                <th scope="col">Accion</th>
	              </tr>
	            </thead>
	            <tbody>
	            </tbody>
	         </table>
		</div>	

		<!-- ------------------------------------------------------------------------ -->

		<!-- MODAL DETALLE PRODUCTO -->

        <producto-detalle ref="detalle_producto" :codigo="codigo_producto"></producto-detalle>

        <!-- ------------------------------------------------------------------------ -->

	</div>
		<!-- FIN DE VENTA POR MARCA Y CATEGORIA -->

	


</template>

<script >
	export default {
        data(){
            return {
              	sucursales: [],
              	selectedSucursal: 'null',
              	validarSucursal: false,
              	messageInvalidSucursal: '',
              	selectedInicialFecha: '',
              	selectedInicialFechaCompra: '',
              	validarInicialFecha: false,
              	messageInvalidFecha: '',
              	selectedFinalFecha: '',
              	selectedFinalFechaCompra: '',
              	validarFinalFecha: false,
              	codigo_producto: ''
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
	        llamarDatos(){
	        	let me = this;	

	        	if(this.generarConsulta() === true) {
	        		
	        		  // ------------------------------------------------------------------------

			          // PREPARAR DATATABLE 

			          var tableLote = $('#tablaStockVacio').DataTable({
			                        "processing": true,
			                        "serverSide": true,
			                        "destroy": true,
			                        "bAutoWidth": true,
			                        "select": true,
			                        "lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]],
			                        "ajax":{
			                                 "data": {
			                                 	sucursal: me.selectedSucursal,
			                                 	inicio: String(me.selectedInicialFecha),
			                                 	fin: String(me.selectedFinalFecha),
			                                    "_token": $('meta[name="csrf-token"]').attr('content')
			                                 },
			                                 "url": "/lote/terminados/reporte",
			                                 "dataType": "json",
			                                 "type": "POST"
			                               },
			                        "columns": [
			                            { "data": "COD_PROD" },
			                            { "data": "DESCRIPCION" },
			                            { "data": "STOCK" },
			                            { "data": "FECHA" },
			                            { "data": "IMAGEN" },
			                            { "data": "ACCION" }
			                        ]

			                        // PARA COLOREAR UNA FILA 
			                        // ,
			                        // "columnDefs": [
			                        //     {
			                        //         "targets": [ 8 ],
			                        //         "visible": false,
			                        //         "searchable": false
			                        //     }
			                        // ],
			                        // "createdRow": function( row, data, dataIndex){
			                        //       $(row).addClass(data['ESTATUS']);
			                        // }      
			          });
			                    
			          // ------------------------------------------------------------------------

			          // CAMBIAR TAMAÑO FUENTE 

			          $("#tablaStockVacio").css("font-size", 12);
			          tableLote.columns.adjust().draw();

			          // ------------------------------------------------------------------------

			          $('#tablaStockVacio').on('click', 'tbody tr #mostrarDetalle', function() {

			            // *******************************************************************

			            // OBTENER COSTO DEL PRODUCTO DE LA TABLA 

			            me.codigo_producto = tableLote.row($(this).parents('tr')).data().COD_PROD;

			            // *******************************************************************

			            // MOSTRAR DETALLE PRODUCTO 

            			me.$refs.detalle_producto.mostrar();

            			// *******************************************************************

			          });

			          // ------------------------------------------------------------------------

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

	        	if (String(this.selectedInicialFecha) === "") {
	        		this.validarInicialFecha = true;
	        		this.messageInvalidFecha = 'Por favor seleccione fecha inicial';
	        		return false;
	        	} else {
	        		this.validarInicialFecha = false;
	        		this.messageInvalidFecha = '';
	        	}
	        	
	        	if (String(this.selectedFinalFecha) === "") {
	        		this.validarFinalFecha = true;
	        		this.messageInvalidFecha = 'Por favor seleccione fecha inicial';
	        		return false;
	        	} else {
	        		this.validarFinalFecha = false;
	        		this.messageInvalidFecha = '';
	        	}

	        	this.datos = {
		        	Sucursal: this.selectedSucursal,
		        	Inicio: String(this.selectedInicialFecha),
		        	Final: String(this.selectedFinalFecha)
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

					$("#selectedInicialFechaCompra").datepicker().on(
			     		"changeDate", () => {me.selectedInicialFechaCompra = $('#selectedInicialFechaCompra').val()}
					);
					$("#selectedFinalFechaCompra").datepicker().on(
			     		"changeDate", () => {me.selectedFinalFechaCompra = $('#selectedFinalFechaCompra').val()}
					);

					$('table').dataTable();
			});


			this.llamarBusquedas();

	          
        }
    }    
</script>
