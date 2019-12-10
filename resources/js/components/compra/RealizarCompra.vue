<template>
	<div class="container-fluid mt-4">

		<!-- ------------------------------------------------------------------------------------- -->

		<!-- TITULO  -->
		
		<vs-divider>
			Realizar Compra
		</vs-divider>
	
        <!-- ------------------------------------------------------------------------------------- -->

        <!-- UN PRODUCTO -->

        <div class="col-12">
		   	<div class="my-1 mb-3">
				<div class="custom-control custom-switch mr-sm-2">
					<input type="checkbox" class="custom-control-input" id="customControlAutosizing" v-model="switch_un_producto">
					<label class="custom-control-label" for="customControlAutosizing">Un Producto</label>
				</div>
			</div>
		</div>
		
		<!-- ------------------------------------------------------------------------------------- -->

	   	<vs-divider position="left">
		 	Compra
	   	</vs-divider>

       	<!-- ------------------------------------------------------------------------------------- -->

       	<div class="col-12">
		  <div class="mt-3">
				<div class="row">

					<!-- ******************************************************************* -->

					<!-- ORIGEN  -->

					<div class="col-md-4">
							<label for="validationTooltip01">Código</label>
							<div class="input-group ">
								<div class="input-group-prepend">
									<button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".origen-modal" v-on:click=""><font-awesome-icon icon="search"/></button>
								</div>
								<input class="form-control form-control-sm" type="text" v-model="codigoCompra" v-on:blur="">
							</div>
					</div>

	                <!-- ******************************************************************* -->

					<!-- ORIGEN  -->

					<div class="col-md-1">
							<label for="validationTooltip01">Proveedor</label>
							<div class="input-group ">
								<div class="input-group-prepend">
									<button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".origen-modal" v-on:click=""><font-awesome-icon icon="search"/></button>
								</div>
								<input class="form-control form-control-sm" type="text" v-model="proveedor" v-on:blur="">
							</div>
					</div>	

					<div class="col-md-3">
						<label for="validationTooltip01" >Descripción</label>
						<input class="form-control form-control-sm" v-bind:class="{ 'is-invalid': validarProveedor }" type="text"  v-model="descripcionProveedor" disabled>
					</div>

					<!-- FIN ORIGEN -->

	                <!-- ******************************************************************* -->

					<!-- DESTINO -->

					<div class="col-md-1">
						<label for="validationTooltip01">Moneda</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".origen-modal" v-on:click=""><font-awesome-icon icon="search"/></button>
							</div>
							<input class="form-control form-control-sm" v-model="codigoMoneda" v-on:blur="" type="text" >
						</div>
					</div>	

					<div class="col-md-3">
						<label  for="validationTooltip01">Descripción</label>
						<input class="form-control form-control-sm" v-bind:class="{ 'is-invalid': validarMoneda }" type="text" v-model="descripcionMoneda" disabled>
					</div>

					<!-- FIN DESTINO -->

	                <!-- ******************************************************************* -->

				</div>

				<div class="row">

	                <!-- ******************************************************************* -->

					<!-- NRO. CAJA -->

					<div class="col-md-4">
						<label class="mt-1" for="validationTooltip01">Nro. Caja</label>
						<input class="form-control form-control-sm" type="text"  v-model="nro_caja">
					</div>

					<!-- FIN NRO. CAJA -->

	                <!-- ******************************************************************* -->

					<!-- NRO. PEDIDO -->

					<div class="col-md-4">
							<label class="mt-1" for="validationTooltip01">Nro. Pedido</label>
							<div class="input-group ">
								<div class="input-group-prepend">
									<button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".origen-modal" v-on:click=""><font-awesome-icon icon="search"/></button>
								</div>
								<input class="form-control form-control-sm" type="text" v-model="nro_pedido" v-on:blur="">
							</div>
					</div>	

					<!-- FIN NRO. PEDIDO -->

	                <!-- ******************************************************************* -->

	                <!-- TIPO COMPRA -->

					<div class="col-md-4">
					    <label class="mt-1">Tipo Compra</label>
					    <select class="custom-select custom-select-sm" v-model="tipo_compra" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_presentacion }">
						    <option value="1">1 - CONTADO</option>
						    <option value="2">2 - CREDITO</option>
						    <option value="3">3 - CONSIGNACION</option>
						</select>
					</div>

					<!-- FIN TIPO COMPRA -->

	                <!-- ******************************************************************* -->

				</div>

				<div class="row">

					<!-- ******************************************************************* -->

					<!-- TOTAL COMPRA -->
					
					<div class="col-md-4">
						<label class="mt-1" for="validationTooltip01">Total Compra</label>
						<input class="form-control form-control-sm" type="text" v-model="total_compra">
					</div>

					<!-- ******************************************************************* -->

					<!-- EXENTAS -->
					
					<div class="col-md-4">
						<label class="mt-1" for="validationTooltip01">Exentas</label>
						<input class="form-control form-control-sm" type="text" v-model="exentas">
					</div>

					<!-- ******************************************************************* -->

					<!-- CANT. CUOTAS -->
					
					<div class="col-md-4">
						<label class="mt-1" for="validationTooltip01">Cant. Cuotas</label>
						<input class="form-control form-control-sm" type="number" v-model="cuotas">
					</div>

					<!-- ******************************************************************* -->
				</div>
		  	</div>
		</div>

		<!-- FINAL DE FORMULARIO -->

	   <!-- ------------------------------------------------------------------------------------- -->

	   <vs-divider position="left">
		 	Agregar Producto
	   </vs-divider>

	   <!-- ------------------------------------------------------------------------------------- -->
	   
	   <!-- AGREGAR PRODUCTO -->

		<div class="col-12">
	
			<div class="mt-3">	
				<div class="row">
					<div class="col-md-2">
						<codigo-producto @codigo_producto="cargarProductos" ref="compontente_codigo_producto" v-model="codigoProducto"></codigo-producto >
					</div>	

					<div class="col-md-5">
						<label for="validationTooltip01">Descripción</label>
						<input class="form-control form-control-sm" type="text" v-model="descripcionProducto" v-bind:class="{ 'is-invalid': validarDescripcionProducto }" disabled>
					</div>

					<div class="col-md-1">
						<label for="validationTooltip01">Lote</label>
						<input class="form-control form-control-sm" type="text" v-model="loteProducto" v-bind:class="{ 'is-invalid': validarlote }" disabled>
					</div>

					<div class="col-md-2">
						<label for="validationTooltip01">Precio Unitario</label>
						<input class="form-control form-control-sm" type="text" v-model="precioProducto" v-bind:class="{ 'is-invalid': validarPrecioUnitario }" disabled>
					</div>

					<div class="col-md-2">
						<label for="validationTooltip01">Cantidad</label>
						<input class="form-control form-control-sm" type="text" v-on:keyup="formatoCantidad" v-on:blur="" v-bind:class="{ 'is-invalid': validarCantidad }" v-on:keyup.prevent.13="" v-model="cantidadProducto">
					</div>

				</div>

				<div class="row mt-1">	
					<div class="col-md-2">
						<label for="validationTooltip01">Costo Unitario</label>
						<input class="form-control form-control-sm" type="text" v-on:keyup="formatoCantidad" v-on:blur="" v-bind:class="{ 'is-invalid': validarCantidad }" v-on:keyup.prevent.13="" v-model="cantidadProducto">
					</div>

					<div class="col-md-2">
						<label for="validationTooltip01">%</label>
						<input class="form-control form-control-sm" type="text" v-on:keyup="formatoCantidad" v-on:blur="" v-bind:class="{ 'is-invalid': validarCantidad }" v-on:keyup.prevent.13="" v-model="cantidadProducto">
					</div>

					<div class="col-md-2">
						<label for="validationTooltip01">Costo</label>
						<input class="form-control form-control-sm" type="text" v-on:keyup="formatoCantidad" v-on:blur="" v-bind:class="{ 'is-invalid': validarCantidad }" v-on:keyup.prevent.13="" v-model="cantidadProducto">
					</div>

					<div class="col-md-4">
						<label for="validationTooltip01">Vencimiento</label>
						<input class="form-control form-control-sm" type="text" v-on:keyup="formatoCantidad" v-on:blur="" v-bind:class="{ 'is-invalid': validarCantidad }" v-on:keyup.prevent.13="" v-model="cantidadProducto">
					</div>

					<div class="col-md-2">
						<label for="validationTooltip01">Precio Mayorista</label>
						<input class="form-control form-control-sm" type="text" v-on:keyup="formatoCantidad" v-on:blur="agregarProducto()" v-bind:class="{ 'is-invalid': validarCantidad }" v-on:keyup.prevent.13="agregarProducto()" v-model="cantidadProducto">
					</div>
				</div>	

				
			</div>	
		</div>		

		<!-- FINAL AGREGAR PRODUCTO -->
		
		<!-- ------------------------------------------------------------------------ -->

        <!-- TABLA TRANSFERENCIA -->

		<div class="col-md-12 mt-4">
			<table id="tablaCompra" class="display nowrap table table-striped table-bordered table-sm mb-3" style="width:100%">
                <thead>
                    <tr>
                        <th></th>
                        <th class="codigoDeclarados">Codigo Producto</th>
                        <th>Descripción</th>
                        <th>Lote</th>
                        <th>Porcentaje</th>
                        <th class="cantidadColumna">Cantidad</th>
                        <th class="precioColumna">Precio</th>
                        <th class="costoColumna">Costo</th>
                        <th class="costoTotalColumna">Costo Total</th>
                        <th class="ivaColumna">IVA</th>
                        <th class="totalColumna">Total</th>
                        <th>Acción</th>
                        <th>IVA-PORCENTAJE</th>
                        <th>STOCK</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
                <tfoot>
                	<tr>
                		<th></th>
                		<th></th>
                		<th></th>
	                	<th></th>
	                	<th>TOTALES</th>
	                	<th></th>
	                	<th></th>
	                	<th></th>
	                	<th></th>
	                	<th></th>
	                	<th></th>
	                	<th></th>
	                	<th></th>
	                	<th></th>
                	</tr>
                </tfoot>	
            </table>
		</div>

		<!-- ******************************************************************* -->

		<!-- SEPARADOR -->

		<hr>
		
		<!-- ******************************************************************* -->

		<!-- BOTONES ABM -->

		<div class="col-md-12 mt-3 mb-3">
				<div class="text-right" v-if="btnguardar">
					<button v-on:click="guardarTransferencia()" class="btn btn-primary" id="guardar">Guardar</button>
				</div>
				<div class="text-right" v-else>
					<button v-on:click="modificarTransferencia()" class="btn btn-warning" id="modificar">Modificar</button>
				</div>	
		</div>

		<!-- ******************************************************************* -->

		<!-- ------------------------------------------------------------------------ -->
    </div>   	
</template>
<script>
	 export default {
      data(){
        return {
          	codigoCompra: '',
          	procesar: false,
          	switch_un_producto: false,
          	proveedor: '',
          	descripcionProveedor: '',
          	codigoMoneda: '',
          	descripcionMoneda: '',
          	nro_caja: '',
          	nro_pedido: '',
          	tipo_compra: '1',
          	total_compra: '',
          	exentas: '',
          	cuotas: '',
          	descripcionProducto: '',
          	precioProducto: '',
          	cantidadProducto: '',
          	stockProducto: '',
          	validarProveedor: '',
          	validarMoneda: '',
          	validar_presentacion: '',
          	shadow: false,
          	codigoProducto: '',
          	validarDescripcionProducto: '',
          	validarPrecioUnitario: '',
          	validarCantidad: '',
          	btnguardar: true,

        }
      }, 
      methods: {
      		cargarProductos(codigo) {
        	
            // ------------------------------------------------------------------------

            // INICIAR VARIABLES

            let me = this;

            // ------------------------------------------------------------------------

            // CONTROLAR SI HAY DATOS EN EL TEXTBOX Y RETORNAR SI NO EXISTE DATO

            if (codigo.length <= 0) {
                me.inivarAgregarProducto();
                return;
            }	
            
            // ------------------------------------------------------------------------
    			
    		// SI DATA ES IGUAL A 0 NO ENCONTRO CODIGO INTERNO 
    		
	        var data = {
	            'codigo': codigo,
	            'monedaSistema': me.monedaCodigo, 
	            'candec': me.candec, 
	            'tab_unica': me.tab_unica
	        }; 
	        
            // ------------------------------------------------------------------------

            // CONSULTAR PRODUCTO DETERMINADO

            axios.post('/producto', {'data': data, 'Opcion': 1}).then(function (response) {
                   
                    if(response.data.producto === 0) {

                        // *******************************************************************

                        // MARCAR EN ROJO TEXTBOX SI NO EXISTE PRODUCTO
                    
                        me.validarCodigoProducto = true;
                        me.codigoProducto = '';
                        me.descripcionProducto = '';
                        me.precioProducto = '';
                        me.stockProducto = '';
                        em.monedaProducto = '';
                        me.ivaProducto = '';

                        // *******************************************************************

                    } else {

                        // *******************************************************************

                        // LLENAR DESCRIPCION DE PRODUCTO

                        me.codigoProducto = response.data.producto.CODIGO;
                        me.descripcionProducto = response.data.producto.DESCRIPCION;
                        me.stockProducto  = response.data.producto.STOCK;
                        me.ivaProducto = response.data.producto.IVA;
                        me.monedaProducto = response.data.producto.MONEDA;
                        me.precioProducto = response.data.valor;

						// *******************************************************************

	                    // AGREGAR PRODUCTO TRANSFERENCIA AUTOMATICAMENTE 

				    	if (me.switch_un_producto === true) {
				    		me.cantidadProducto = 1;
				    		me.agregarProducto();
				    		me.codigoProducto = '';
				    	}

			            // *******************************************************************

                        me.validarCodigoProducto = false;

                        // *******************************************************************
                    }
            });

    		// ------------------------------------------------------------------------

        }, 
        agregarProducto(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES

            let me = this;
            var precio = 0;
            var iva = 0;

            // ------------------------------------------------------------------------

            // CONTROLADOR

            if (me.codigoProducto.length === 0) {
                me.validarCodigoProducto = true;
                return;
            } else {
                me.validarCodigoProducto = false;
            }

            if (me.descripcionProducto.length === 0) {
                me.validarDescripcionProducto = true;
                return;
            } else {
                me.validarDescripcionProducto = false;
            }

            if (me.precioProducto.length === 0  || me.precioProducto === '0') {
                me.validarPrecioUnitario = true;
                return;
            } else {
                me.validarPrecioUnitario = false;
            }

            if (me.cantidadProducto.length === 0  || me.cantidadProducto === '0') {
                me.validarCantidad = true;
                return;
            } else {
                me.validarCantidad = false;
            }

            // ------------------------------------------------------------------------

            //  QUITAR COMA DE PRECIO

            precio = Common.multiplicarCommon(me.cantidadProducto, me.precioProducto, me.candec);
         
            // ------------------------------------------------------------------------

            //  DAR FORMATO AL RESULTADO FINAL PARA MOSTRAR EN DATATABLE 

            precio = Common.darFormatoCommon(precio, me.candec);

            // ------------------------------------------------------------------------

            // CALCULAR IVA

            iva = Common.calcularIVACommon(precio, me.ivaProducto, me.candec);

            // ------------------------------------------------------------------------

            // CARGAR DATO EN TABLA TRANSFERENCIAS

            me.agregarFilaTabla(me.codigoProducto, me.descripcionProducto, me.cantidadProducto, me.precioProducto, iva, precio, me.ivaProducto, me.stockProducto);

            // ------------------------------------------------------------------------

            // VACIAR TEXTBOX AGREGAR PRODUCTO

            me.inivarAgregarProducto();

            // ------------------------------------------------------------------------

	        // LLAMAR AL METODO HIJO 

	        this.codigoProducto = '';
	        this.$refs.compontente_codigo_producto.vaciarDevolver();

	        // ------------------------------------------------------------------------

        }, formatoPrecio(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A PRECIO

            me.precioProducto = Common.darFormatoCommon(me.precioProducto, me.candec);

            // ------------------------------------------------------------------------
            
        }, formatoCantidad(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // DAR FORMATO A CANTIDAD
            
            me.cantidadProducto = Common.darFormatoCommon(me.cantidadProducto, 0);

            // ------------------------------------------------------------------------
            
        }, formatoEditarPrecio(){

			// ------------------------------------------------------------------------

			// DAR FORMATO A LOS VALORES DE MODAL EDITAR 

			this.editarCantidad = Common.darFormatoCommon(this.editarCantidad, this.candec);

			// ------------------------------------------------------------------------

		}
      },
        mounted() {
        		
        		// ------------------------------------------------------------------------

        		let me = this;

        		// ------------------------------------------------------------------------
                // >>
                // INICIAR EL DATATABLE TRANSFERENCIA 
                // ------------------------------------------------------------------------

                var tableCompra = $('#tablaCompra').DataTable({
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "dom": "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
						"<'row'<'col-sm-12'tr>>" +
						"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
				        "buttons": [
				            { extend: 'copy', text: '<i class="fa fa-copy"></i>', titleAttr: 'Copiar', className: 'btn btn-secondary' },
				        	{ extend: 'excelHtml5', text: '<i class="fa fa-file"></i>', titleAttr: 'Excel', className: 'btn btn-success' },
				            { extend: 'pdfHtml5', text: '<i class="fa fa-file"></i>', titleAttr: 'Pdf', className: 'btn btn-danger' }, 
				            { extend: 'print', text: '<i class="fa fa-print"></i>', titleAttr: 'Imprimir', className: 'btn btn-secondary', title: 'Transferencia', messageTop: 'Productos registrados para Transferencia' }
				        ],
				        "columnDefs": [
				            {
				                "targets": [ 12 ],
				                "visible": false,
				                "searchable": false
				            },
				            {
				                "targets": [ 13 ],
				                "visible": false,
				                "searchable": false
				            }
				        ], 
                        "columns": [
                            { "data": "ITEM" },
                            { "data": "CODIGO" },
                            { "data": "DESCRIPCION" },
                            { "data": "LOTE" },
                            { "data": "PORCENTAJE" },
                            { "data": "CANTIDAD" },
                            { "data": "PRECIO" },
                            { "data": "COSTO" },
                            { "data": "COSTO_TOTAL" },
                            { "data": "IVA" },
                            { "data": "TOTAL" },
                            { "data": "ACCION" },
                            { "data": "IVA_PORCENTAJE" },
                            { "data": "STOCK" }
                        ],
                        "footerCallback": function(row, data, start, end, display) {
						  var api = this.api();
						 
						  // *******************************************************************

						  // CANTIDAD 

						  api.columns('.cantidadColumna', {
						    
						  }).every(function() {
						    var cantidad = this
						      .data()
						      .reduce(function(a, b) {

						      	// *******************************************************************

						      	// QUITAR LAS COMAS DE LOS VALORES

						      	a = Common.quitarComaCommon(a);
						      	b = Common.quitarComaCommon(b);
						      	
						      	// *******************************************************************

						        var x = parseFloat(a) || 0;
						        var y = parseFloat(b) || 0;

						        // *******************************************************************

						        // SUMAR VALORES

						        return x + y;

						        // *******************************************************************

						      }, 0);

						      // *******************************************************************

						      // CARGAR EN EL FOOTER

						      $( api.columns('.cantidadColumna').footer() ).html(
					                Common.darFormatoCommon(cantidad, 0)
					           );

						      // *******************************************************************

						  });

						  // *******************************************************************

						  // PRECIO

						  api.columns('.precioColumna', {
						    
						  }).every(function() {
						    var precio = this
						      .data()
						      .reduce(function(a, b) {

						      	// *******************************************************************

						      	// QUITAR LAS COMAS DE LOS VALORES

						      	a = Common.quitarComaCommon(a);
						      	b = Common.quitarComaCommon(b);
						      	
						      	// *******************************************************************

						        var x = parseFloat(a) || 0;
						        var y = parseFloat(b) || 0;

						        // *******************************************************************

						        // SUMAR VALORES

						        return x + y;

						        // *******************************************************************

						      }, 0);

						      // *******************************************************************

						      // CARGAR EN EL FOOTER

						      $( api.columns('.precioColumna').footer() ).html(
					                Common.darFormatoCommon(precio, me.candec)
					           );

						      // *******************************************************************

						  });

						  // *******************************************************************

						  // COSTO

						  api.columns('.costoColumna', {
						    
						  }).every(function() {
						    var costo = this
						      .data()
						      .reduce(function(a, b) {

						      	// *******************************************************************

						      	// QUITAR LAS COMAS DE LOS VALORES

						      	a = Common.quitarComaCommon(a);
						      	b = Common.quitarComaCommon(b);
						      	
						      	// *******************************************************************

						        var x = parseFloat(a) || 0;
						        var y = parseFloat(b) || 0;

						        // *******************************************************************

						        // SUMAR VALORES

						        return x + y;

						        // *******************************************************************

						      }, 0);

						      // *******************************************************************

						      // CARGAR EN EL FOOTER

						      $( api.columns('.costoColumna').footer() ).html(
					                Common.darFormatoCommon(costo, me.candec)
					           );

						      // *******************************************************************

						  });

						  // *******************************************************************

						  // COSTO TOTAL

						  api.columns('.costoTotalColumna', {
						    
						  }).every(function() {
						    var costoTotal = this
						      .data()
						      .reduce(function(a, b) {

						      	// *******************************************************************

						      	// QUITAR LAS COMAS DE LOS VALORES

						      	a = Common.quitarComaCommon(a);
						      	b = Common.quitarComaCommon(b);
						      	
						      	// *******************************************************************

						        var x = parseFloat(a) || 0;
						        var y = parseFloat(b) || 0;

						        // *******************************************************************

						        // SUMAR VALORES

						        return x + y;

						        // *******************************************************************

						      }, 0);

						      // *******************************************************************

						      // CARGAR EN EL FOOTER

						      $( api.columns('.costoTotalColumna').footer() ).html(
					                Common.darFormatoCommon(costoTotal, me.candec)
					           );

						      // *******************************************************************

						  });

						  // *******************************************************************

						  // TOTAL 

						  api.columns('.totalColumna', {
						    
						  }).every(function() {
						    var sum = this
						      .data()
						      .reduce(function(a, b) {

						      	// *******************************************************************

						      	// QUITAR LAS COMAS DE LOS VALORES

						      	a = Common.quitarComaCommon(a);
						      	b = Common.quitarComaCommon(b);
						      	
						      	// *******************************************************************

						        var x = parseFloat(a) || 0;
						        var y = parseFloat(b) || 0;

						        // *******************************************************************

						        // SUMAR VALORES

						        return x + y;

						        // *******************************************************************

						      }, 0);

						    // *******************************************************************

						    // CARGAR EN EL FOOTER  

						    $( api.columns('.totalColumna').footer() ).html(
					                Common.darFormatoCommon(sum, me.candec)
					         );  

						    // *******************************************************************

						  });

						  // *******************************************************************

						  // TOTAL IVA 

						  api.columns('.ivaColumna', {
						    
						  }).every(function() {
						    var ivaSum = this
						      .data()
						      .reduce(function(a, b) {

						      	// *******************************************************************

						      	// QUITAR LAS COMAS DE LOS VALORES

						      	a = Common.quitarComaCommon(a);
						      	b = Common.quitarComaCommon(b);
						      	
						      	// *******************************************************************

						        var x = parseFloat(a) || 0;
						        var y = parseFloat(b) || 0;

						        // *******************************************************************

						        // SUMAR VALORES

						        return x + y;

						        // *******************************************************************

						      }, 0);

						      // *******************************************************************

						      // CARGAR EN EL FOOTER

						      $( api.columns('.ivaColumna').footer() ).html(
					                Common.darFormatoCommon(ivaSum, me.candec)
					           );

						      // *******************************************************************

						  });

						  // *******************************************************************
						}      
                });
                
                // ------------------------------------------------------------------------

                // ASIGNAR INLINE BUTTONS 

                tableCompra.buttons().container()
    			.appendTo( $('div.eight.column:eq(0)', tableCompra.table().container()) );

				// ------------------------------------------------------------------------

            	// DESPUES DE INICIAR LA TABLA TRANSFERENCIAS LLAMAR A LA CONSULTA PARA CARGAR CABECERA Y CUERPO 
	 
        }
    }
</script>