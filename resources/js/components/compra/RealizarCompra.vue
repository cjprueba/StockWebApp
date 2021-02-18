<template>
	<div class="container-fluid mt-4">

		
		
		<div class="row">

			<!-- ------------------------------------------------------------------------------------- -->

			<!-- TITULO  -->
			
			<vs-divider>
				Realizar Compra
			</vs-divider>
			
	        <!-- ------------------------------------------------------------------------------------- -->

	        <!-- UN PRODUCTO -->

	        <div class="col-12">
			   	<div class="my-1 mb-3">
					<div class="custom-control custom-switch mr-sm-2" >
						<input type="checkbox" class="custom-control-input" id="customControlAutosizing" v-model="switch_un_producto">
						<label class="custom-control-label" for="customControlAutosizing" data-toggle="tooltip" data-placement="top" title="Permite el paso de productos repetidos en la tabla sin la necesidad de agregar nuevamente los datos ya ingresados del anterior agregado">Un Producto</label>
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

						<!-- CODIGO  -->

						<div class="col-2">
								<label for="validationTooltip01">Código</label>
								<div class="input-group ">
									<div class="input-group-prepend">
										<button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".origen-modal" v-on:click=""><font-awesome-icon icon="search"/></button>
									</div>
									<input tabindex="1" class="form-control form-control-sm" type="text" v-model="codigoCompra" v-on:blur="">
								</div>
						</div>

						<!-- ******************************************************************* -->

						<div class="col-2">
							<label for="validationTooltip01">Fecha</label>
							<div class="input-group input-group-sm date">
								<div class="input-group-prepend ">
									    <span class="input-group-text" id="inputGroup-sizing-sm"><font-awesome-icon icon="calendar" /></span>
								</div>
								<input tabindex="2" v-bind:class="{ 'is-invalid': validar.FECHA }" type="text" class="input-sm form-control form-control-sm" id="fecha" v-model="factura.FECHA" data-date-format="yyyy-mm-dd"/>
							</div>
						</div>

		                <!-- ******************************************************************* -->

						<!-- PROVEEDOR  -->

						<div class="col-4">
							<select-proveedor :tabIndexPadre=3 v-model="proveedor" :validar_proveedor="validar.PROVEEDOR"></select-proveedor>
						</div>

						<!-- FIN PROVEEDOR -->

		                <!-- ******************************************************************* -->

						<!-- MONEDA -->

						<div class="col-4">
							<select-moneda :tabIndexPadre=4 v-model="moneda.CODIGO" :deshabilitar="moneda.DESHABILITAR" @descripcion_moneda="descripcionMoneda" @cantidad_decimales="cantidadDecimal"></select-moneda>
						</div>

						<!-- FIN MONEDA -->

		                <!-- ******************************************************************* -->

					</div>

					<div class="row">

		                <!-- ******************************************************************* -->

						<!-- NRO. CAJA -->

						<div class="col-4">
							<label class="mt-1" for="validationTooltip01">Nro. Caja</label>
							<input tabindex="5" class="form-control form-control-sm" type="text"  v-model="factura.NRO_CAJA">
						</div>

						<!-- FIN NRO. CAJA -->

		                <!-- ******************************************************************* -->

						<!-- NRO. PEDIDO -->

						<div class="col-4">
								<label class="mt-1" for="validationTooltip01">Nro. Pedido</label>
								<input tabindex="6" class="form-control form-control-sm" type="text" v-model="factura.NRO_PEDIDO" v-on:blur="">
						</div>	

						<!-- FIN NRO. PEDIDO -->

		                <!-- ******************************************************************* -->

		                <!-- TIPO COMPRA -->

						<div class="col-4">
						    <label class="mt-1">Tipo Compra</label>
						    <select tabindex="7" class="custom-select custom-select-sm" v-model="tipo_compra" v-on:change="change_tipo" >
							    <option value="CO">1 - CONTADO</option>
							    <option value="CR">2 - CREDITO</option>
							    <option value="CS">3 - CONSIGNACION</option>
							</select>
						</div>

						<!-- FIN TIPO COMPRA -->

		                <!-- ******************************************************************* -->

					</div>

					<div class="row">

						<!-- ******************************************************************* -->

						<!-- TOTAL COMPRA -->

						<div class="col-4">
							<label class="mt-1" for="validationTooltip01">Total Compra</label>
							<div class="input-group input-group-sm mb-3" >
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroup-sizing-sm">{{moneda.DESCRIPCION}}</span>
								</div>
					    		<input tabindex="8" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar.TOTAL_COMPRA }" class="form-control form-control-sm" v-on:blur="formatoTotalFactura" type="text" v-model="factura.TOTAL">
							</div>
						</div>

						<!-- ******************************************************************* -->

						<!-- EXENTAS -->
						
						<div class="col-4">
							<label class="mt-1" for="validationTooltip01">Exentas</label>
							<div class="input-group input-group-sm mb-3" >
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroup-sizing-sm">{{moneda.DESCRIPCION}}</span>
								</div>
					    		<input tabindex="9" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar.EXENTAS }" class="form-control form-control-sm" type="text" v-on:blur="formatoExentaFactura" v-model="factura.EXENTAS">
							</div>
						</div>

						<!-- ******************************************************************* -->

						<!-- CANT. CUOTAS -->
						
						<div class="col-4">
							<label class="mt-1" for="validationTooltip01">Cant. Cuotas</label>
							<input v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar.CUOTAS }" class="form-control form-control-sm" type="number" v-model="cuotas" v-on:blur="formatoCantidadCuota" :disabled="deshabilitar_cuota">
						</div>

						<!-- ******************************************************************* -->
					</div>

					<div class="row">
						<div class="col-12">

							<div class="collapse multi-collapse" id="credito">
								<div class="card border-left-info">
										
									<div class=" card-body">
										<h6 class="card-title">Periodo Crédito</h6>
										<div class="row">
											<div class="col-md-1">
												<div class="custom-control custom-radio">
												  <input type="radio" v-bind:class="{ 'is-invalid': validar.OPCIONES }" v-on:change="opcionCredito" v-model="credito.OPCIONES"  value="1" id="customRadio1" name="customRadio" class="custom-control-input">
												  <label class="custom-control-label" for="customRadio1">Días</label>
												</div>
											</div>

											<div class="col-md-1">
												<div class="custom-control custom-radio">
												  <input type="radio" v-bind:class="{ 'is-invalid': validar.OPCIONES }" v-on:change="opcionCredito" v-model="credito.OPCIONES"  value="2" id="customRadio2" name="customRadio" class="custom-control-input">
												  <label class="custom-control-label" for="customRadio2">Cuotas</label>
												</div>
											</div>

											<div class="col-md-1">
												<div class="custom-control custom-radio">
												  <input type="radio" v-bind:class="{ 'is-invalid': validar.OPCIONES }" v-on:change="opcionCredito" v-model="credito.OPCIONES"  value="3" id="customRadio3" name="customRadio" class="custom-control-input">
												  <label class="custom-control-label" for="customRadio3">Fecha</label>
												</div>
											</div>

											<div class="col-md-3">
												<div class="input-group input-group-sm date">
													<div class="input-group-prepend ">
														    <span class="input-group-text" id="inputGroup-sizing-sm"><font-awesome-icon icon="calendar" /></span>
													</div>
													<input v-bind:class="{ 'is-invalid': validar.FECHA_CREDITO }" type="text" class="input-sm form-control form-control-sm" id="fechaCredito" v-model="credito.FECHA" data-date-format="yyyy-mm-dd" :disabled="credito.DESHABILITAR_FECHA" />
												</div>
											</div>

											<div class="col-md-6">
												<div class="input-group input-group-sm date">
													<div class="input-group-prepend ">
														    <span class="input-group-text" id="inputGroup-sizing-sm">días</span>
													</div>
													<input v-bind:class="{'is-invalid': validar.DIAS }" type="text" v-on:keyup="formatoDias" v-model="credito.CANTIDAD" placeholder="Ingrese la cantidad de días" class="input-sm form-control form-control-sm" id="periodo" aria-describedby="emailHelp" :disabled="credito.DESHABILITAR_CANTIDAD">
												</div>
											</div>	
										</div>
									</div>
								</div>
							</div>
						</div>
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
						<div class="col-2">
							<codigo-producto tabIndexPadre="10" :validar_codigo_producto="validar.COD_PROD" @codigo_producto="cargarProductos" ref="compontente_codigo_producto" v-model="producto.CODIGO"></codigo-producto >
						</div>	

						<div class="col-1">
							<label for="validationTooltip01">Cantidad</label>
							<input tabindex="11" class="form-control form-control-sm" type="text" v-on:keyup="formatoCantidad"  v-bind:class="{ 'is-invalid': validar.CANTIDAD }" v-on:keyup.prevent.13="" v-model="producto.CANTIDAD">
						</div>

						<div class="col-2">
							<label for="validationTooltip01">Costo Unitario</label>
							<div class="input-group input-group-sm mb-3" v-bind:class="{ 'shadow-sm': shadow }">
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroup-sizing-sm">{{moneda.DESCRIPCION}}</span>
								</div>
					    		<input tabindex="12" class="form-control form-control-sm" type="text" v-on:blur="formatoCostoUnitario"  v-bind:class="{ 'is-invalid': validar.COSTO_UNITARIO }" v-on:keyup.prevent.13="" v-model="producto.COSTO">
							</div>
						</div>

						<div class="col-md-1" v-if="ocultar">
							<label for="validationTooltip01">%</label>
							<input tabindex="13" class="form-control form-control-sm" type="text" v-on:blur="formatoPorcentaje"  v-on:keyup.prevent.13="" v-model="producto.PORCENTAJE">
						</div>

						<div class="col-md-2">
							<label for="validationTooltip01">Vencimiento</label>
							<div class="input-group input-group-sm date">
								<div class="input-group-prepend ">
									<span class="input-group-text" id="inputGroup-sizing-sm"><font-awesome-icon icon="calendar" /></span>
								</div>
								<input type="text" class="input-sm form-control form-control-sm" id="vencimiento" v-model="producto.VENCIMIENTO" v-bind:class="{ 'is-invalid': validar.VENCIMIENTO }" data-date-format="yyyy-mm-dd" :disabled="!mostrar.VENCIMIENTO"/>
							</div>	
						</div>

						<div class="col-2">
							<label for="validationTooltip01">Precio Unitario</label>
							<div class="input-group input-group-sm mb-3" v-bind:class="{ 'shadow-sm': shadow }">
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroup-sizing-sm">{{moneda.DESCRIPCION}}</span>
								</div>
					    		<input tabindex="13" class="form-control form-control-sm" type="text" v-model="producto.PREC_VENTA"  v-on:blur="formatoPrecio" v-bind:class="{ 'is-invalid': validar.PRECIO_UNITARIO }">
							</div>
						</div>

						<div class="col-2">
							<label for="validationTooltip01">Precio Mayorista</label>
							<div class="input-group input-group-sm mb-3" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar.PREMAYORISTA }">
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroup-sizing-sm">{{moneda.DESCRIPCION}}</span>
								</div>
					    		<input tabindex="14" class="form-control form-control-sm" type="text" v-on:blur="agregarProducto(), formatoPreMayorista" v-model="producto.PREMAYORISTA" v-on:keyup.prevent.13="agregarProducto(), formatoPreMayorista">
							</div>
						</div>
					</div>

					<div class="row mt-2">	

						<div class="col-11">
							<label for="validationTooltip01">Descripción</label>
							<input class="form-control form-control-sm" type="text" v-model="producto.DESCRIPCION"  disabled>
						</div>

						<div class="col-1">
							<label for="validationTooltip01">Lote</label>
							<input class="form-control form-control-sm" type="text" v-model="producto.LOTE" disabled>
						</div>

					</div>	

				</div>	
			</div>		

			<!-- FINAL AGREGAR PRODUCTO -->
			
			<!-- ------------------------------------------------------------------------ -->

	        <!-- TABLA TRANSFERENCIA -->

			<div class="col-12 mt-4">
				<table id="tablaCompra" class="display nowrap table table-striped table-bordered table-sm mb-3" style="width:100%">
	                <thead>
	                    <tr>
	                        <th></th>
	                        <th class="codigoDeclarados">Codigo</th>
	                        <th>Descripción</th>
	                        <th>Lote</th>
	                        <th>%</th>
	                        <th class="cantidadColumna">Cant.</th>
	                        <th class="precioColumna">Precio</th>
	                        <th class="mayoristaColumna">May.</th>
	                        <th class="costoColumna">Costo</th>
	                        <th class="costoTotalColumna">C. Total</th>
	                        <th>Venc.</th>
	                        <th>Acción</th>
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
	                	</tr>
	                </tfoot>	
	            </table>
			</div>

			<!-- ******************************************************************* -->

			<!-- SEPARADOR -->

			<hr>
			
			<!-- ******************************************************************* -->

			<!-- BOTONES ABM -->

			<div class="col-12 mt-3 mb-3">
					<div class="text-right" v-if="btnguardar">
						<button v-on:click="guardarCompra()" class="btn btn-primary" id="guardar">Guardar</button>
					</div>
					<div class="text-right" v-else>
						<button v-on:click="guardarCompra()" class="btn btn-warning" id="modificar">Modificar</button>
					</div>	
			</div>

			<!-- ------------------------------------------------------------------------ -->

			<!-- TOAST PRODUCTO TRANSFERENCIA MODIFICADO -->

			<b-toast id="toast-producto-compra-modificado" variant="success" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <strong class="mr-auto">Éxito !</strong>
		          <small class="text-muted mr-2">modificado</small>
		        </div>
		      </template>
		      Este producto ha sido modificado con éxito !
		    </b-toast>

		    <!-- ------------------------------------------------------------------------ -->

		</div>

		<!-- ------------------------------------------------------------------------ -->

		

		<!-- ------------------------------------------------------------------------ -->

    </div>   	
</template>
<script>
	 export default {
	  props: ['candec', 'monedaCodigo'],	
      data(){
        return {
          	codigoCompra: '',
          	ocultar:false,
          	procesar: false,
          	switch_un_producto: false,
          	proveedor: '',
          	descripcionProveedor: '',
          	tipo_compra: 'CO',
          	cuotas: '',
          	producto: {
          		CODIGO: '',
          		DESCRIPCION: '',
				LOTE: '',
				PREC_VENTA: '',
				CANTIDAD: '',
				COSTO: '',
				PORCENTAJE: '',
				COSTO_TOTAL: '',
				VENCIMIENTO: '',
				PREMAYORISTA: ''
          	},
          	factura: {
          		FECHA: '',
          		TOTAL: '',
          		NRO_PEDIDO: '',
          		NRO_CAJA: '',
          		EXENTAS: ''
          	},
          	moneda: {
          		CODIGO: '',
          		DESCRIPCION: '',
          		DECIMAL: '',
          		DESHABILITAR: false
          	},
          	validar: {
          		PROVEEDOR: false,
          		TOTAL_COMPRA: false,
          		EXENTAS: false,
          		CODIGO: false,
          		COD_PROD: false,
          		DESCRIPCION: false,
          		PRECIO_UNITARIO: false,
          		CANTIDAD: false,
          		COSTO_UNITARIO: false,
          		PREMAYORISTA: false,
          		DIAS: false,
          		CUOTAS: false,
          		FECHA_CREDITO: false,
          		VENCIMIENTO: false
          	},
          	credito: {
          		FECHA: '',
          		OPCIONES: [],
          		CANTIDAD: '',
          		DESHABILITAR_FECHA: true,
          		DESHABILITAR_CANTIDAD: true
          	},
          	mostrar: {
          		VENCIMIENTO: false
          	},
          	deshabilitar_cuota: true,
          	shadow: false,
          	btnguardar: true,

        }
      }, 
      methods: {
      		mostrarCompra(codigo){

        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES 

        	let me = this;
        	me.codigoCompra = codigo;

        	// ------------------------------------------------------------------------

        	// SE ENCARGA DE COMPROBAR SI EXISTE CODIGO EN LA URL PARA PODER CARGAR UNA TRANSFERENCIA EXISTENTE
        	
        	if (codigo !== undefined) {

        		// ------------------------------------------------------------------------

        		// OCULTAR BOTON GUARDAR 

        		me.btnguardar = false;

        		// ------------------------------------------------------------------------

        		// AGREGAR CABECERA, ENVIO CERO PARA CODIGO ORIGEN PARA ESPECIFICAR QUE
        		// NECESITO DATOS DE LA PROPIA SUCURSAL DEL USUARIO

        		Common.obtenerCabeceraCompraCommon(codigo).then(data => {

        			// ------------------------------------------------------------------------

        			me.factura.FECHA = data.FEC_FACTURA;
        			me.proveedor = data.PROVEEDOR.toString();
        			me.moneda.CODIGO = data.MONEDA.toString();
        			me.factura.NRO_CAJA = data.NRO_FACTURA;
        			me.tipo_compra = data.TIPO;
        			me.factura.TOTAL = data.TOTAL;
        			me.factura.EXENTAS = data.EXENTAS;
        			me.cuotas = data.CUOTAS;
        			me.credito.CANTIDAD = data.PLAN_PAGO;
        			me.credito.OPCIONES = data.DEUDA_TIPO;

        			// ------------------------------------------------------------------------

        			// MOSTRAR OPCIONES DE CREDITO Y GENERAR FECHA

        			me.change_tipo();
        			me.formatoDias();

        			// ------------------------------------------------------------------------

        		});

        		// ------------------------------------------------------------------------

        		// AGREGAR CUERPO, ENVIO CERO PARA CODIGO ORIGEN PARA ESPECIFICAR QUE
        		// NECESITO DATOS DE LA PROPIA SUCURSAL DEL USUARIO

        		Common.obtenerCuerpoCompraCommon(codigo).then(data => {
        			data.map(function(x) {
					   
					   // ------------------------------------------------------------------------
        			   	
					   // EMPEZAR A CARGAR PRODUCTOS EN TRANSFERENCIA 

					   me.agregarFilaTabla(x.COD_PROD, x.DESCRIPCION, x.LOTE, x.PORCENTAJE, x.CANTIDAD, x.PREC_VENTA, x.PREMAYORISTA, x.COSTO, x.COSTO_TOTAL, x.VENCIMIENTO);
					  
					   // ------------------------------------------------------------------------

					});
        		});

        		// ------------------------------------------------------------------------

        	} else {

        		// ------------------------------------------------------------------------

        		// OCULTAR BOTON MODIFICAR 

        		me.btnguardar = true;

        		// ------------------------------------------------------------------------

        	}

        	// ------------------------------------------------------------------------

        }, cargarProductos(codigo) {
        	
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

            // CONSULTAR PRODUCTO CON LOTE 

            Common.obtenerProductoCompraCommon(codigo, me.moneda.CODIGO).then(data => {

            	if (data.response  === true) {

	            	// ------------------------------------------------------------------------

	            	// AGREGAR PRODUCTO 

	            	me.producto.CODIGO = data.producto.CODIGO;
	            	me.producto.DESCRIPCION = data.producto.DESCRIPCION;
	            	me.producto.PREC_VENTA = data.producto.PREC_VENTA;
	            	me.producto.PREMAYORISTA = data.producto.PREMAYORISTA;
	            	me.producto.LOTE = data.producto.LOTE;
	            	me.mostrar.VENCIMIENTO = data.producto.VENCIMIENTO;

	            	// ------------------------------------------------------------------------

			        // SI SWITCH ESTA ACTIVADO AGREGAR PRODUCTO

					if (me.switch_un_producto === true) {
						
						if (me.agregarProductoRapido() === true) {
							me.producto.CODIGO = '';
							me.inivarAgregarProducto();
						}

					}

					// ------------------------------------------------------------------------

				} else {

					Swal.fire(
						'Error !',
						data.statusText,
						'error'
					)

				}

            })       

    		// ------------------------------------------------------------------------
    		

        }, agregarProductoRapido(){

        	// ------------------------------------------------------------------------

        	//	INICIAR VARIABLES 

        	let me = this;
        	var tableCompra = $('#tablaCompra').DataTable();
        	var productoExistente = [];
        	var cantidadNueva = 0;

        	// ------------------------------------------------------------------------

            // CARGAR DATO EN TABLA TRANSFERENCIAS

            if (me.switch_un_producto === true) {
            
            	// ------------------------------------------------------------------------

            	// REVISAR SI EXISTE PRODUCTO EN TABLA 

            	productoExistente = Common.existeProductoDataTableCommon(tableCompra, me.producto.CODIGO, 3);
           		
           		// ------------------------------------------------------------------------

           		// SUMAR LA CANTIDAD E IVA

            	cantidadNueva = parseFloat(productoExistente.cantidad) + parseFloat(1);

            	// ------------------------------------------------------------------------

           		// SI EXISTE EDITAR 

            	if (productoExistente.respuesta == true) {
            		me.editarCantidadProducto(tableCompra, cantidadNueva, productoExistente.costo, productoExistente.row);
            		me.$bvToast.show('toast-producto-compra-modificado');
            		return true;
            	} else {
            		return false;
            	}

            	// ------------------------------------------------------------------------

            } 

            // ------------------------------------------------------------------------

        }, 
        agregarProducto(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES

            let me = this;
            var iva = 0;
            var tableCompra = $("#tablaCompra").DataTable();

            // ------------------------------------------------------------------------

            // AGREGAR FORMATO A PRECIO MAYORISTA

            me.formatoPreMayorista();
             
            // ------------------------------------------------------------------------

            // CONTROLADOR

            if (me.producto.CODIGO.length === 0) {
                me.validar.CODIGO = true;
                return;
            } else {
                me.validar.CODIGO = false;
            }

            if (me.producto.DESCRIPCION.length === 0) {
                me.validar.DESCRIPCION = true;
                return;
            } else {
                me.validar.DESCRIPCION = false;
            }

            if (me.producto.PREC_VENTA.length === 0  || me.producto.PREC_VENTA === '0') {
                me.validar.PRECIO_UNITARIO = true;
                return;
            } else {
                me.validar.PRECIO_UNITARIO = false;
            }

            if (me.producto.CANTIDAD.length === 0  || me.producto.CANTIDAD === '0') {
                me.validar.CANTIDAD = true;
                return;
            } else {
                me.validar.CANTIDAD = false;
            }
            
            if (me.producto.VENCIMIENTO.length === 0 && me.mostrar.VENCIMIENTO === 1 ) {
                me.validar.VENCIMIENTO = true;
                return;
            } else {
                me.validar.VENCIMIENTO = false;
            }

            // ------------------------------------------------------------------------

            //  QUITAR COMA DE PRECIO

            me.producto.COSTO_TOTAL = Common.multiplicarCommon(me.producto.CANTIDAD, me.producto.COSTO, me.moneda.DECIMAL);
         
            // ------------------------------------------------------------------------

            //  DAR FORMATO AL RESULTADO FINAL PARA MOSTRAR EN DATATABLE 

            me.producto.COSTO_TOTAL = Common.darFormatoCommon(me.producto.COSTO_TOTAL, me.moneda.DECIMAL);

            // ------------------------------------------------------------------------

            // CARGAR DATO EN TABLA COMPRAS

            me.agregarFilaTabla(me.producto.CODIGO, me.producto.DESCRIPCION, me.producto.LOTE, me.producto.PORCENTAJE, me.producto.CANTIDAD, me.producto.PREC_VENTA, me.producto.PREMAYORISTA, me.producto.COSTO, me.producto.COSTO_TOTAL, me.producto.VENCIMIENTO);

            // ------------------------------------------------------------------------

            // VACIAR TEXTBOX AGREGAR PRODUCTO

            me.inivarAgregarProducto();

            // ------------------------------------------------------------------------

            // VERIFICAR SI LA TABLA YA TIENE PRODUCTOS PARA DESHABILITAR MONEDA 

            if (tableCompra.rows().data().length > 0) {
    			me.moneda.DESHABILITAR = true;
    		}

    		// ------------------------------------------------------------------------

	        // LLAMAR AL METODO HIJO 

	        this.producto.CODIGO = '';
	        this.$refs.compontente_codigo_producto.vaciarDevolver();

	        // ------------------------------------------------------------------------

        }, formatoPrecio(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A PRECIO

            me.producto.PREC_VENTA = Common.darFormatoCommon(me.producto.PREC_VENTA, me.moneda.DECIMAL);

            // ------------------------------------------------------------------------
            
        }, formatoCantidad(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // DAR FORMATO A CANTIDAD
            
            me.producto.CANTIDAD = Common.darFormatoCommon(me.producto.CANTIDAD, 0);

            // ------------------------------------------------------------------------

        }, formatoPorcentaje(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;
            var multiplicar = 0;
            var dividir = 0;
            var suma = 0;

            // ------------------------------------------------------------------------

            // DAR FORMATO A CANTIDAD
            
            me.producto.PORCENTAJE = Common.darFormatoCommon(me.producto.PORCENTAJE, 0);

            // ------------------------------------------------------------------------
            
            // CALCULAR PRECIO POR COSTO

            multiplicar = Common.multiplicarCommon(me.producto.COSTO, me.producto.PORCENTAJE, me.moneda.DECIMAL);
            dividir = Common.dividirCommon(multiplicar, 100, me.moneda.DECIMAL);
            suma = Common.sumarCommon(dividir, me.producto.COSTO, me.moneda.DECIMAL);
            me.producto.PREC_VENTA = suma;
             
            // ------------------------------------------------------------------------

        }, change_tipo(){

        	// ------------------------------------------------------------------------
        	
        	if (this.tipo_compra === "CR") {
        		$('#credito').collapse('show');
        		this.deshabilitar_cuota = false;
        	} else {
        		$('#credito').collapse('hide');
        		this.deshabilitar_cuota = true;
        	}
        	
        	// ------------------------------------------------------------------------

        }, formatoCostoUnitario(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A COSTO
         
            me.producto.COSTO = Common.darFormatoCommon(me.producto.COSTO, me.moneda.DECIMAL);

            // ------------------------------------------------------------------------
            
        }, formatoPreMayorista(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A COSTO

            me.producto.PREMAYORISTA = Common.darFormatoCommon(me.producto.PREMAYORISTA, me.moneda.DECIMAL);

            // ------------------------------------------------------------------------
            
        }, formatoTotalFactura(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A COSTO

            me.factura.TOTAL = Common.darFormatoCommon(me.factura.TOTAL, me.moneda.DECIMAL);

            // ------------------------------------------------------------------------
            
        }, formatoExentaFactura(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A COSTO

            me.factura.EXENTAS = Common.darFormatoCommon(me.factura.EXENTAS, me.moneda.DECIMAL);

            // ------------------------------------------------------------------------
            
        }, formatoCantidadCuota(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A COSTO

            me.cuotas = Common.darFormatoCommon(me.cuotas, 0);

            // ------------------------------------------------------------------------
            
        }, formatoDias(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A COSTO

            me.credito.CANTIDAD = Common.darFormatoCommon(me.credito.CANTIDAD, 0);

            // ------------------------------------------------------------------------

            // OBTENER FECHA A PARTIR DE DIAS

            if (me.factura.FECHA !== '' && parseFloat(me.credito.CANTIDAD) !== 0) {
	            var fecha = new Date(me.factura.FECHA);
				fecha.setDate(fecha.getDate() + parseFloat(me.credito.CANTIDAD));
				me.credito.FECHA = Common.formatDateCommon(fecha); 
            }

            // ------------------------------------------------------------------------

        }, agregarFilaTabla(codigo, descripcion, lote, porcentaje, cantidad, precio, premayorista, costo, costo_total, vencimiento){

        	// ------------------------------------------------------------------------

        	//	INICIAR VARIABLES 

        	let me = this;
        	var tableCompra = $('#tablaCompra').DataTable();
        	var productoExistente = [];
        	var cantidadNueva = 0;

        	// ------------------------------------------------------------------------

            // REVISAR SI EXISTE VALORES REPETIDOS EN TABLA TRANSFERENCIAS 
            // LA OPCION 1 ES PARA DEVOLVER SOLO TRUE O FALSE SI EXISTE O NO
            // LA OPCION 2 ES PARA DEVOLVER MAS DATOS DEL PRODUCTO 

            productoExistente = Common.existeProductoDataTableCommon(tableCompra, codigo, 2);
           
            if (productoExistente.respuesta == true) {

            	// ------------------------------------------------------------------------

            	// SUMAR LA CANTIDAD E IVA

            	cantidadNueva = parseFloat(productoExistente.cantidad) + parseFloat(cantidad);

            	// ------------------------------------------------------------------------

            	// EDITAR CANTIDAD PRODUCTO 

            	me.editarCantidadProducto(tableCompra, cantidadNueva, costo, productoExistente.row);
            	return;

            	// ------------------------------------------------------------------------

            } else {
            	me.validarCodigoProducto = false;
            }

            // ------------------------------------------------------------------------

            // VENCIMIENTO 

            if (me.mostrar.VENCIMIENTO === 0) {
            	vencimiento = 'N/A';
            }

            // ------------------------------------------------------------------------

        	// AGREGAR FILAS 

        	 tableCompra.rows.add( [ {
		                    "ITEM": '',
		                    "CODIGO":   codigo,
		                    "DESCRIPCION":     descripcion,
		                    "LOTE": lote,
		                    "PORCENTAJE": porcentaje,
		                    "CANTIDAD": cantidad,
		                    "PRECIO":    precio,
		                    "MAYORISTA":    premayorista,
		                    "COSTO":    costo,
		                    "COSTO_TOTAL":    costo_total,
		                    "VENCIMIENTO": vencimiento,
		                    "ACCION":    "&emsp;<a role='button' id='mostrarProductoFila' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a> &emsp;<a role='button' id='editarProducto' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>&emsp;<a role='button'  title='Eliminar'><i id='eliminarProducto' class='fa fa-trash text-danger' aria-hidden='true'></i></a>"
		                } ] )
		     .draw();

		    // ------------------------------------------------------------------------ 

            // AGREGAR INDEX A LA TABLA TRANSFERNCIAS

            tableCompra.on( 'order.dt search.dt', function () {
                tableCompra.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();

            // ------------------------------------------------------------------------

            // AJUSTAR COLUMNAS DE ACUERDO AL DATO QUE CONTIENEN

            tableCompra.columns.adjust().draw();

            // ------------------------------------------------------------------------

        }, inivarAgregarProducto(){

			// ------------------------------------------------------------------------

			// VACIAR TEXTBOX 

			this.producto = {
				CODIGO: '',
				DESCRIPCION: '',
				LOTE: '',
				PREC_VENTA: '',
				CANTIDAD: '',
				COSTO: '',
				COSTO_TOTAL: '',
				PORCENTAJE: '',
				VENCIMIENTO: '',
				PREMAYORISTA: ''
			}

            // ------------------------------------------------------------------------   

		}, editarCantidadProducto(tabla, cantidad, costo, row){

        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES

        	let me = this;
        	var costo_total = 0;
        	var iva = 0;

        	// ------------------------------------------------------------------------

            // PROHIBIR EDITADO SI CANTIDAD O PRECIO ES CERO

            if (cantidad === '0' || costo === '0') {
                me.$bvToast.show('toast-editar-cero');
                return;	
            }

            // ------------------------------------------------------------------------

            // CARGAR LO EDITADO

            tabla.cell(row, 5).data(cantidad).draw();

            // ------------------------------------------------------------------------
            
            // CARGAR COSTO 

            tabla.cell(row, 8).data(costo).draw();

            // ------------------------------------------------------------------------

            // CALCULAR PRECIO TOTAL

            costo_total = Common.multiplicarCommon(cantidad, costo, me.moneda.DECIMAL);

		    // ------------------------------------------------------------------------

		    // CARGAR PRECIO CALCULADO 

            tabla.cell(row, 9).data(costo_total).draw();

            // ------------------------------------------------------------------------

            // LLAMAR TOAST MODIFICADO

            me.$bvToast.show('toast-producto-transferencia-modificado');

            // ------------------------------------------------------------------------

        }, controlador() {

        	// ------------------------------------------------------------------------

        	let me = this;
        	var falta = false;
        	var tableCompra = $('#tablaCompra').DataTable();

        	// ------------------------------------------------------------------------

        	if (me.proveedor.length === 0) {
                me.validar.PROVEEDOR = true;
                falta = true;
            } else {
                me.validar.PROVEEDOR = false;
            }
            
            if (me.factura.TOTAL === "0" || me.factura.TOTAL === '' || me.factura.TOTAL === "0.00") {
                me.validar.TOTAL_COMPRA = true;
                falta = true;
            } else {
                me.validar.TOTAL_COMPRA = false;
            }

            // if (me.factura.EXENTAS === "0" || me.factura.EXENTAS === '' || me.factura.EXENTAS === "0.00") {
            //     me.validar.EXENTAS = true;
            //     falta = true;
            // } else {
            //     me.validar.EXENTAS = false;
            // }

            if (tableCompra.rows().data().length === 0) {
            	falta = true;
            }

            if (me.factura.FECHA.length === 0) {
                me.validar.FECHA = true;
                falta = true;
            } else {
                me.validar.FECHA = false;
            }
            
            if (me.credito.OPCIONES.length === 0 && me.tipo_compra === "CR") {
                me.validar.OPCIONES = true;
                falta = true;
            } else {
                me.validar.OPCIONES = false;
            }

            if (me.credito.OPCIONES === "1" && me.tipo_compra === "CR" && me.credito.CANTIDAD <= 0) {
                me.validar.DIAS = true;
                falta = true;
            } else if (me.credito.OPCIONES === "1"){
                me.validar.DIAS = false;
            }

            if (me.credito.OPCIONES === "2" && me.tipo_compra === "CR" && me.credito.CANTIDAD <= 0) {
                me.validar.DIAS = true;
                falta = true;
            } else if (me.credito.OPCIONES === "2" || me.credito.OPCIONES === "3") {
                me.validar.DIAS = false;
            }

            if (me.credito.OPCIONES === "2" && me.tipo_compra === "CR" && me.cuotas <= 0) {
                me.validar.CUOTAS = true;
                falta = true;
            } else  {
                me.validar.CUOTAS = false;
            }

            if (me.credito.OPCIONES === "3" && me.tipo_compra === "CR" && me.credito.FECHA.length === 0) {
                me.validar.FECHA_CREDITO = true;
                falta = true;
            } else  {
                me.validar.FECHA_CREDITO = false;
            }

            // ------------------------------------------------------------------------

            // RETORNAR FALTA - SI ES TRUE SE DETIENE EL GUARDADO 
            // SI ES FALSE CONTINUA LA OPERACION 

            return falta;

        	// ------------------------------------------------------------------------

        }, creditoFunction() {

        	// ------------------------------------------------------------------------

        	// RETORNAR PROMISE 

        	return new Promise(respuesta => {

        		// ------------------------------------------------------------------------

        		Swal.fire({
					  title: 'Periodo de Tiempo',
					  icon: 'info',
					  html:
					    '<div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1"><label class="form-check-label" for="inlineRadio1">Días</label></div><div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2"><label class="form-check-label" for="inlineRadio2">Cuotas</label></div><div class="form-check form-check-inline"></div><br/><br/>' +
					    	'<div class="form-group"><input type="text" class="form-control" id="periodo" aria-describedby="emailHelp" v-on:click="hola"><small id="emailHelp" class="form-text text-muted">Ingrese la cantidad de días o cuotas</small></div>',
					  showCloseButton: true,
					  showCancelButton: true,
					  focusConfirm: false,
					  confirmButtonText:
					    '<i class="fa fa-thumbs-up"></i> Great!',
					  confirmButtonAriaLabel: 'Thumbs up, great!',
					  cancelButtonText:
					    '<i class="fa fa-thumbs-down"></i>',
					  cancelButtonAriaLabel: 'Thumbs down'
					}).then((result) => {
					  return respuesta(false);
				})

				// ------------------------------------------------------------------------

			});		

        	// ------------------------------------------------------------------------

        }, guardarCompra() {

        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES 

        	let me = this;
        	var tableCompra = $('#tablaCompra').DataTable();

        	// ------------------------------------------------------------------------
        	
        	// ESPERAR VALOR 

        	// var credito = await me.creditoFunction();

        	// ------------------------------------------------------------------------

        	// if (credito === false) {
        	// 	return;
        	// }

        	// ------------------------------------------------------------------------

        	// CONTROLAR TEXTBOX / SI ES TRUE SE DETIENE LA OPERACION DE GUARDADO

        	if (me.controlador() === true){
        		return;
        	}

        	// ------------------------------------------------------------------------

        	var data = {
        		codigo: me.codigoCompra,
        		proveedor: me.proveedor,
        		moneda: me.moneda.CODIGO,
        		fecha: me.factura.FECHA,
        		nro_caja: me.factura.NRO_CAJA,
        		nro_pedido: me.factura.NRO_PEDIDO,
        		tipo_compra: me.tipo_compra,
        		total_compra: me.factura.TOTAL,
        		exentas: me.factura.EXENTAS,
        		cuotas: me.cuotas,
        		productos: tableCompra.rows().data().toArray(),
        		guardar: me.btnguardar,
        		credito: {
        			opcion: me.credito.OPCIONES,
        			fecha: me.credito.FECHA,
        			dias: me.credito.CANTIDAD,
        			cuotas: me.cuotas
        		}
        	};

        	// ------------------------------------------------------------------------

        	// 
        	// ------------------------------------------------------------------------

      		// SWEET ALERT

      		Swal.fire({
				title: 'Estas seguro ?',
				text: "Guardar la compra !",
				type: 'warning',
				showLoaderOnConfirm: true,
				showCancelButton: true,
				confirmButtonColor: '#d33',
				cancelButtonColor: '#3085d6',
				confirmButtonText: 'Si, guardalo!',
				cancelButtonText: 'Cancelar',
				preConfirm: () => {
				    return Common.guardarModificarCompraCommon(data).then(data => {
				    	if (!data.response === true) {
				          throw new Error(data.statusText);
				        }
				  		return data;
				  	}).catch(error => {
				        Swal.showValidationMessage(
				          `Request failed: ${error}`
				        )
				    });
				}
			}).then((result) => {

				if (result.value.response) {

					Swal.fire(
						'Guardado!',
						'Se ha guardado correctamente la compra con codigo '+result.value.codigo+' !',
						'success'
					)

					// ------------------------------------------------------------------------

					// REDIRIGIR A MOSTRAR COMPRAS
					  	
					me.$router.push('/cr1');

					// ------------------------------------------------------------------------

				}
			})

			// ------------------------------------------------------------------------

        	
        }, descripcionMoneda(valor){

        	// ------------------------------------------------------------------------

        	// DESCRIPCION MONEDA

        	this.moneda.DESCRIPCION = valor;

        	// ------------------------------------------------------------------------

        }, cantidadDecimal(valor){

        	// ------------------------------------------------------------------------

        	// DESCRIPCION MONEDA

        	this.moneda.DECIMAL = valor;
        	
        	// ------------------------------------------------------------------------

        	// VACIAR TEXTBOX AGREGAR PRODUCTO

            this.inivarAgregarProducto();

            // ------------------------------------------------------------------------

        	// LLAMAR FORMATOS DE PRECIOS AL CAMBIAR DE MONEDA

        	this.formatoTotalFactura();
        	this.formatoExentaFactura();
        	this.formatoPrecio();
        	this.formatoCostoUnitario();
        	this.formatoPreMayorista();

        	// ------------------------------------------------------------------------

        }, opcionCredito() {

        	// ------------------------------------------------------------------------

        	// OPCION CREDITO 
        	
        	if (this.credito.OPCIONES === "1") {
        		this.deshabilitar_cuota = true;
        		this.credito.DESHABILITAR_CANTIDAD = false;
        		this.credito.DESHABILITAR_FECHA = true;
        		this.formatoDias();
        	} else if (this.credito.OPCIONES === "2") {
        		this.deshabilitar_cuota = false;
        		this.credito.DESHABILITAR_FECHA = true;
        		this.credito.DESHABILITAR_CANTIDAD = false;
        		this.formatoDias();
        	} else if (this.credito.OPCIONES === "3") {
        		this.deshabilitar_cuota = true;
        		this.credito.DESHABILITAR_CANTIDAD = true;
        		this.credito.DESHABILITAR_FECHA = false;
        	}

        	// ------------------------------------------------------------------------

        }, hola(x){
        	return new Promise(resolve => {
			    setTimeout(() => {
			      resolve(x);
			    }, 10000);
			  });
        }, async f1() {
		  	var x = await this.hola(4);
        	alert("ahora me ejecute"); // 10
		}
      },
        mounted() {
        		
        		// ------------------------------------------------------------------------

        		let me = this;
        		
        		// ------------------------------------------------------------------------

        		// INICIALIZAR MONEDA 

        		me.moneda.CODIGO = String(me.monedaCodigo);

        		// ------------------------------------------------------------------------

        		// OBTENER CODIGO COMPRA 
        		// SI CODIGO ENVIADO A TRAVES DE URL ES UNDEFINED 

        		if (me.$route.params.id === undefined) {
        			Common.obtenerCodigoCompraCommon().then( data => {
        				me.codigoCompra = data;
        			})
        		}

        		// ------------------------------------------------------------------------

        		$('#sandbox-container .input-daterange').datepicker({
		   		    	    keyboardNavigation: false,
    						forceParse: false
    			});

    			$("#vencimiento").datepicker().on(
			     		"changeDate", () => {me.producto.VENCIMIENTO = $('#vencimiento').val()}
				);

				$("#fecha").datepicker().on(
			     		"changeDate", () => {
			     			me.factura.FECHA = $('#fecha').val();
			     			me.formatoDias();
			     		}
				);

				$("#fechaCredito").datepicker().on(
			     		"changeDate", () => {me.credito.FECHA = $('#fechaCredito').val()}
				);

    			$('[data-toggle="tooltip"]').tooltip()

        		// ------------------------------------------------------------------------
                // >>
                // INICIAR EL DATATABLE TRANSFERENCIA 
                // ------------------------------------------------------------------------

                var tableCompra = $('#tablaCompra').DataTable({
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "responsive": true,
                        "dom": "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
						"<'row'<'col-sm-12'tr>>" +
						"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
				        "buttons": [
				            { extend: 'copy', text: '<i class="fa fa-copy"></i>', titleAttr: 'Copiar', className: 'btn btn-secondary' },
				        	{ extend: 'excelHtml5', text: '<i class="fa fa-file"></i>', titleAttr: 'Excel', className: 'btn btn-success' },
				            { extend: 'pdfHtml5', text: '<i class="fa fa-file"></i>', titleAttr: 'Pdf', className: 'btn btn-danger' }, 
				            { extend: 'print', text: '<i class="fa fa-print"></i>', titleAttr: 'Imprimir', className: 'btn btn-secondary', title: 'Transferencia', messageTop: 'Productos registrados para Transferencia' }
				        ], 
                        "columns": [
                            { "data": "ITEM" },
                            { "data": "CODIGO" },
                            { "data": "DESCRIPCION" },
                            { "data": "LOTE" },
                            { "data": "PORCENTAJE" },
                            { "data": "CANTIDAD" },
                            { "data": "PRECIO" },
                            { "data": "MAYORISTA" },
                            { "data": "COSTO" },
                            { "data": "COSTO_TOTAL" },
                            { "data": "VENCIMIENTO" },
                            { "data": "ACCION" }
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
					                Common.darFormatoCommon(precio, me.moneda.DECIMAL)
					           );

						      // *******************************************************************

						  });

						  // *******************************************************************

						  // PRECIO

						  api.columns('.mayoristaColumna', {
						    
						  }).every(function() {
						    var mayorista = this
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

						      $( api.columns('.mayoristaColumna').footer() ).html(
					                Common.darFormatoCommon(mayorista, me.moneda.DECIMAL)
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
					                Common.darFormatoCommon(costo, me.moneda.DECIMAL)
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
					                me.factura.TOTAL = Common.darFormatoCommon(costoTotal, me.moneda.DECIMAL)
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

    			// AJUSTAR TAMAÑO DE FUENTE DE TABLA

    			$("#tablaCompra").css("font-size", 12);
				tableCompra.columns.adjust().draw();

				// ------------------------------------------------------------------------

				// DESPUES DE INICIAR LA TABLA TRANSFERENCIAS LLAMAR A LA CONSULTA PARA CARGAR CABECERA Y CUERPO 

            	me.mostrarCompra(me.$route.params.id);

            	// ------------------------------------------------------------------------

            	// ------------------------------------------------------------------------
                // ------------------------------------------------------------------------
                //                  ELIMINAR FILA DATATABLE TRANSFERENICAS
                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------

                // MOSTRAR SWEET ELIMINAR

                $('#tablaCompra').on('click', 'tbody tr #eliminarProducto', function() {

                    // *******************************************************************

                    // ABRIR EL SWEET ALERT

                    Swal.fire({
					  title: 'Estas seguro ?',
					  text: "Eliminar el producto " + tableCompra.row($(this).parents('tr')).data().CODIGO + " !",
					  type: 'warning',
					  showLoaderOnConfirm: true,
					  showCancelButton: true,
					  confirmButtonColor: '#d33',
					  cancelButtonColor: '#3085d6',
					  confirmButtonText: 'Si, eliminalo!',
					  cancelButtonText: 'Cancelar'
					}).then((result) => {
					  if (result.value) {

					  	// *******************************************************************

	                    // ELIMINAR 
	                    
	                    tableCompra.row($(this).parents('tr')).remove().draw(); 

	                    // *******************************************************************

					  	Swal.fire(
							      'Eliminado!',
							      'Se ha eliminado el producto de la tabla !',
							      'success'
						)

						// *******************************************************************

						// VERIFICAR SI LA TABLA YA NO TIENE PRODUCTOS PARA HABILITAR MONEDA 
            
	            		if (tableCompra.rows().data().length === 0) {
	    					me.moneda.DESHABILITAR = false;
	    				} 

    					// *******************************************************************

					  }
					})

                    // *******************************************************************

                });

                // ------------------------------------------------------------------------

                // MOSTRAR MODAL EDITAR

                $('#tablaCompra').on('click', 'tbody tr #editarProducto', function() {

                	// *******************************************************************

                	// INICIAR VARIABLE 

                	var productoExistente = [];

                	// *******************************************************************

                	// OBTENER COSTO DEL PRODUCTO DE LA TABLA 

                	productoExistente = Common.existeProductoDataTableCommon(tableCompra, tableCompra.row($(this).parents('tr')).data().CODIGO, 3);

                    // *******************************************************************

                    // ABRIR EL SWEET ALERT
                     
                    Swal.mixin({
					  input: 'text',
					  confirmButtonText: 'Next &rarr;',
					  showCancelButton: true,
					  progressSteps: ['1']
					}).queue([
					  {
					    title: 'Cantidad',
					    text: 'Ingrese la nueva cantidad'
					  }
					]).then((result) => {
					  if (result.value) {
					    const cantidad = result.value[0];
					    me.editarCantidadProducto(tableCompra, cantidad, productoExistente.costo, productoExistente.row);
					    Swal.fire({
					      title: 'Cambiado',
					      confirmButtonText: 'Aceptar !'
					    })
					  }
					})

                    // *******************************************************************

                });

                // ------------------------------------------------------------------------

                // ENFOCAR 

                // CODIGO PARA CAMBIAR EL FOCUS CON ENTER EN INPUT
        	
	        	$(document).on('keypress', 'input', function(e) {
	        		
				  if(e.keyCode == 13 ) {
				           
				           me.cb = parseInt($(this).attr('tabindex'));
				    	   
				           if ($(':input[tabindex=\'' + (me.cb + 1) + '\']') != null) {

				               $(':input[tabindex=\'' + (me.cb + 1) + '\']').focus();
				               $(':input[tabindex=\'' + (me.cb + 1) + '\']').select();
				               e.preventDefault();
				    
				               return false;
				           }
				  }

				});

				// -------------------------------------------------------------------------------------

				// CODIGO PARA CAMBIAR EL FOCUS CON ENTER EN SELECT
	        	
	        	$(document).on('keypress', 'select', function(e) {
	        		
	        	  

				  if(e.keyCode == 13 ) {
				           me.cb = parseInt($(this).attr('tabindex'));

				           if ($(':input[tabindex=\'' + (me.cb + 1) + '\']') != null) {
				           	   
				               $(':input[tabindex=\'' + (me.cb + 1) + '\']').focus();
				               $(':input[tabindex=\'' + (me.cb + 1) + '\']').select();
				               e.preventDefault();
				               return false;
				               
				           }


				  }

				});


	 			// ------------------------------------------------------------------------
        }
    }
</script>