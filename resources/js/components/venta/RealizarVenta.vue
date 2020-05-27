<template>
	<div class="container-fluid bg-light">

		<div class="row">

			<div class="col-md-10">

				<div class="row">

					<!-- ------------------------------------------------------------------ -->

				    <div class="col-md-12 mt-5">
							
						<form class="form-inline">

						    <!-- ------------------------------------------------------------------ -->

						    <!-- HABILITAR MAYORISTA -->
									
							<div class="my-1">
								<div class="custom-control custom-switch mr-sm-3">
									<input type="checkbox" class="custom-control-input" id="switchMayorista" v-model="checked.MAYORISTA">
									<label class="custom-control-label" for="switchMayorista">Mayorista</label>
								</div>
							</div>

							<!-- ------------------------------------------------------------------ -->

							<!-- MOSTRAR TICKET -->
									
							<div class="my-1">
								<div class="custom-control custom-switch mr-sm-3">
									<input type="checkbox" class="custom-control-input" id="switchTicket" v-model="checked.TICKET">
									<label class="custom-control-label" for="switchTicket">Mostrar Ticket</label>
								</div>
							</div>

							<!-- ------------------------------------------------------------------ -->

							<!-- MOSTRAR FACTURA -->
									
							<div class="my-1">
								<div class="custom-control custom-switch mr-sm-3">
									<input type="checkbox" class="custom-control-input" id="switchFactura" v-model="checked.FACTURA">
									<label class="custom-control-label" for="switchFactura">Mostrar Factura</label>
								</div>
							</div>

							<!-- ------------------------------------------------------------------ -->

						</form>

						<!-- ------------------------------------------------------------------ -->

				    </div>	
				    	
				    <!-- ------------------------------------------------------------------ -->

					<div class="col-md-2 mt-5">
					    <label class="mb-0"><b>Guaranies</b></label>
					    <h6>{{cotizacion.GUARANIES}} </h6>
					    
					</div>

					<div class="col-md-2 mt-5">
					    <label class="mb-0"><b>Dolares</b></label>
					    <h6>{{cotizacion.DOLARES}}</h6>
					</div>

					<div class="col-md-2 mt-5">
					    <label class="mb-0"><b>Reales</b></label>
					    <h6>{{cotizacion.REALES}}</h6>
					</div>

					<div class="col-md-2 mt-5">
					    <label class="mb-0"><b>Pesos</b></label>
					    <h6>{{cotizacion.PESOS}}</h6>
					</div>

					<div class="col-md-4 text-right mt-3">
						<label class="mb-0">Total {{moneda.DESCRIPCION}}</label>
						<h1 v-on:change="gravadas">{{venta.TOTAL}}</h1>
					</div>
					
					<div class="col-md-12">
						<hr>
					</div>

					<div class="col-md-4">
						<!-- <div>
							 -->
						<label><span class="text-primary">Nro. Ticket:</span> {{venta.CODIGO + 1}}</label><br>
						<label><span class="text-primary">Nro. Caja:</span> {{venta.CODIGO_CAJA}}</label>
					</div>

					<div class="col-md-4">
						<label class="text-primary">Cliente</label>
						<h6>{{cliente.NOMBRE}}</h6>
					</div> 

					<div class="col-md-4">
						<label class="text-primary">Vendedor</label>
						<h6>{{vendedor.NOMBRE}}</h6>
					</div> 

					<!-- <div class="col-md-2">
						<label class="text-primary">Exentas</label>
						<h6>2,300.00 USD</h6>
					</div> 

					<div class="col-md-2">
						<label class="text-primary">Gravadas</label>
						<h6>{{venta.GRAVADAS}}</h6>
					</div> 

					<div class="col-md-2">
						<label class="text-primary">I.V.A.</label>
						<h6>{{venta.IMPUESTO}}</h6>
					</div>   -->
					
					<div class="col-md-12">
						<hr>
					</div>

					<!-- ------------------------------------------------------------------------------------- -->
		   
				   	<!-- AGREGAR PRODUCTO -->

					<div class="col-md-12">
				
						<div class="mt-3">

							<div class="row">
								<div class="col-2">
									<codigo-producto :validar_codigo_producto="validar.COD_PROD" @codigo_producto="cargarProductos" ref="compontente_codigo_producto" v-model="producto.COD_PROD"></codigo-producto >
								</div>	

								<div class="col-md-1">
									<label for="validationTooltip01">Cantidad</label>
									<input v-model="producto.CANTIDAD" class="form-control form-control-sm" type="text">
								</div>

								<div class="col-md-1">
									<label for="validationTooltip01">Descuento</label>
									<input class="form-control form-control-sm" type="text" v-model="producto.DESCUENTO">
								</div>

								<div class="col-md-2">
									<label for="validationTooltip01">Precio Venta</label>
									<div class="input-group input-group-sm mb-3" >
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroup-sizing-sm">{{moneda.DESCRIPCION}}</span>
										</div>
							    		<input v-model="producto.PREC_VENTA" class="form-control form-control-sm" type="text">
									</div>
								</div>

								<div class="col-md-1">
									<label for="validationTooltip01">Stock</label>
									<input v-model="producto.STOCK" class="form-control form-control-sm" type="text" disabled>
								</div>

								<div class="col-md-5">
									<label for="validationTooltip01">Descripción</label>
									<input v-model="producto.DESCRIPCION" class="form-control form-control-sm" type="text"  disabled>
								</div>

							</div>

						</div>	
					</div>		

					<!-- FINAL AGREGAR PRODUCTO -->
					
					<!-- ------------------------------------------------------------------------ -->

					<div class="col-md-12 mt-4">

						<!-- ------------------------------------------------------------------------ -->

				        <!-- TABLA VENTA -->

							<table id="tablaVenta" class="display nowrap table table-striped table-bordered table-sm mb-3" style="width:100%">
				                <thead>
				                    <tr>
				                        <th></th>
				                        <th class="codigoDeclarados">Codigo</th>
				                        <th>Descripción</th>
				                        <th>Lote</th>
				                        <th>%</th>
				                        <th>Descuento</th>
				                        <th class="cantidadColumna">Cant.</th>
				                        <th class="impuestoColumna">Impuesto</th>
				                        <th class="precioColumna">Precio</th>
				                        <th class="precioTotalColumna">Total</th>
				                        <th>Acción</th>
				                        <th>IVA</th>
				                        <th>Codigo Real</th>
				                        <th>Precio Mayorista</th>
				                        <th>Descuento Unitario</th>
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

						<!-- ------------------------------------------------------------------------ -->

					</div>

					<div class="col-md-12 mt-3">
						<div class="text-right">
						</div>	
					</div>

				</div>

			</div>	

			<div class="col-md-2 mt-3">
				<busqueda-cliente-modal @codigo="codigoCliente" @nombre="nombreCliente"></busqueda-cliente-modal>
				<busqueda-vendedor-modal @codigo="codigoVendedor" @nombre="nombreVendedor"></busqueda-vendedor-modal>
				<button class="btn btn-primary btn-sm btn-block mt-2" v-on:click="recargar"><small>Nuevo</small></button>
				<button class="btn btn-primary btn-sm btn-block" v-on:click="guardar"><small>Facturar</small></button>
				<button class="btn btn-primary btn-sm btn-block" v-on:click="ticket_mostrar"><small>Último Ticket</small></button>
				<button class="btn btn-primary btn-sm btn-block" v-on:click="factura_test"><small>Última Factura</small></button>
				<button class="btn btn-primary btn-sm btn-block" v-on:click="resumen_test"><small>Resumen Caja</small></button>
			</div>

		</div>
		
		<!-- ------------------------------------------------------------------------ -->

		<!-- FORMA PAGO PROVEEDOR -->

		<forma-pago-textbox :total="venta.TOTAL" :total_crudo="venta.TOTAL_CRUDO" :moneda="moneda.CODIGO" :candec="moneda.DECIMAL" @datos="formaPago" ref="compontente_medio_pago"></forma-pago-textbox>

		<!-- ------------------------------------------------------------------------ -->	

		<!-- TOAST STOCK CERO -->

			<b-toast id="toast-stock-cero" variant="warning" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">Error !</strong>
		          <small class="text-muted mr-2">cero</small>
		        </div>
		      </template>
		      Este producto ya no tiene stock !
		    </b-toast>

		<!-- ------------------------------------------------------------------------ -->

		<!-- TOAST FALTA COMPLETAR -->

			<b-toast id="toast-falta-completar" variant="warning" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">Error !</strong>
		          <small class="text-muted mr-2">Incompleto</small>
		        </div>
		      </template>
		      Para realizar la venta se necesita añadir más datos !
		    </b-toast>

		<!-- ------------------------------------------------------------------------ -->

		<!-- TOAST DESCUENTO SUPERADO -->

			<b-toast id="toast-descuento-error" variant="warning" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">Error !</strong>
		          <small class="text-muted mr-2">Equivocado</small>
		        </div>
		      </template>
		      Error en la aplicación del descuento, favor verifque su valor !
		    </b-toast>

		<!-- ------------------------------------------------------------------------ -->

		<!-- TOAST STOCK SUPERADO -->

			<b-toast id="toast-cantidad-superada" variant="warning" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">Error !</strong>
		          <small class="text-muted mr-2">superado</small>
		        </div>
		      </template>
		      La cantidad ingresada ya supera el stock !
		    </b-toast>

		<!-- ------------------------------------------------------------------------ -->

		<!-- MODAL DETALLE PRODUCTO -->

		<producto-detalle ref="detalle_producto" :codigo="codigo_detalle"></producto-detalle>

		<!-- ------------------------------------------------------------------------ -->

	</div>
</template>
<script>
	export default {
      data(){
        return {
         	moneda: {
         		DECIMAL: '',
         		CODIGO: '',
         		DESCRIPCION: '',
         		DESHABILITAR: false
         	},
         	cliente: {
         		CODIGO: 1,
         		CI: '',
         		NOMBRE: ''
         	},
         	vendedor: {
         		CODIGO: 1,
         		CI: '',
         		NOMBRE: ''
         	},
         	caja: {
         		CODIGO: 1,
         	},
         	producto: {
         		COD_PROD: '',
         		DESCRIPCION: '',
         		PREC_VENTA: '',
         		PREMAYORISTA: '',
         		LOTE: '',
         		STOCK: '',
         		PRECIO_TOTAL: 0,
         		CANTIDAD: 1,
         		DESCUENTO: 0,
         		DESCUENTO_MONTO: 0,
         		DESCUENTO_UNITARIO: 0,
         		TIPO_DESCUENTO: 0,
         		IVA: 0,
         		IMPUESTO: 0,
         		CODIGO_REAL: '',
         		DESCUENTO_CATEGORIA: 0
         	},
         	validar: {
         		COD_PROD: false
         	}, 
         	venta: {
         		CODIGO: '',
         		TOTAL: 0,
         		GRAVADAS: 0,
         		IMPUESTO: 0,
         		TOTAL_CRUDO: 0,
         		CODIGO_CAJA: ''
         	}, respuesta: {
         		cabecera: '',
         		moneda: '',
         		pago: ''
         	}, cotizacion: {
         		DOLARES: '',
         		GUARANIES: '',
         		PESOS: '',
         		REALES: ''
         	}, ajustes: {
         		LIMITE_MAYORISTA: 0,
         		IMPRESORA_TICKET: '',
         		IMPRESORA_MATRICIAL: ''
         	}, checked: {
         		MAYORISTA: false,
         		TICKET: false,
         		FACTURA: false
         	}, codigo_detalle: '',
         	impresion: {
         		TICKET: false,
         		FACTURA: false,
         		TIPO: ''
         	} 

        }
      }, 
      methods: {
      		formaPago(datos) {

      			// ------------------------------------------------------------------------

	        	let me = this;
	        	var tableVenta = $('#tablaVenta').DataTable();

	        	// ------------------------------------------------------------------------

	        	// GUARDAR 

	        	this.respuesta = {
	        		cabecera: this.venta,
	        		cliente: this.cliente,
	        		vendedor: this.vendedor,
	        		caja: this.caja,
	        		moneda: this.moneda.CODIGO,
	        		pago: datos,
	        		productos: tableVenta.rows().data().toArray()
	        	}

	        	Swal.fire({
					title: 'Estas seguro ?',
					text: "Guardar la venta !",
					type: 'warning',
					showLoaderOnConfirm: true,
					showCancelButton: true,
					confirmButtonColor: '#d33',
					cancelButtonColor: '#3085d6',
					confirmButtonText: 'Si, guardalo!',
					cancelButtonText: 'Cancelar',
					preConfirm: () => {
					    return Common.guardarVentaCommon(me.respuesta).then(data => {
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

						// ------------------------------------------------------------------------

						Swal.fire(
							'Guardado !',
							 result.value.statusText,
							'success'
						)

						// ------------------------------------------------------------------------

						// GENERAR FACTURA Y TICKET

						me.impresion.TIPO = datos.TIPO_IMPRESION;

						if (me.checked.FACTURA === true && datos.TIPO_IMPRESION === "2") {
							Common.generarPdfFacturaVentaVisualizarCommon(result.value.CODIGO, result.value.CAJA);
						} 

						if (me.checked.TICKET === true) {
							Common.generarPdfTicketVentaVisualizarCommon(result.value.CODIGO, result.value.CAJA);
						} else {
							me.ticket(result.value.CODIGO, result.value.CAJA);
						}
						// ------------------------------------------------------------------------

						// VOLVER TRUE FACTURA SI ES QUE SOLO IMPRIMIO TICKET 

						if(datos.TIPO_IMPRESION === "1") {
							me.impresion.FACTURA = true;
						}

						// ------------------------------------------------------------------------ 

					}
				})

	        	// ------------------------------------------------------------------------

      		},
      		recargar(){

      			// ------------------------------------------------------------------------ 

      			// RECARGAR LA PAGINA 

      			//this.test();

      			if (this.impresion.TICKET === true && this.impresion.FACTURA === true) {
      				window.location.href = '/vt2';
      			}

				// ------------------------------------------------------------------------ 

      		},
      		controlador(){

      			// ------------------------------------------------------------------------

	        	let me = this;
	        	var falta = false;
	        	var tableVenta = $('#tablaVenta').DataTable();

	        	// ------------------------------------------------------------------------

	        	// if (me.proveedor.length === 0) {
	         //        me.validar.PROVEEDOR = true;
	         //        falta = true;
	         //    } else {
	         //        me.validar.PROVEEDOR = false;
	         //    }

	            // ------------------------------------------------------------------------

	            // REVISAR TABLA VENTA 

	            if (tableVenta.rows().data().length === 0) {
	            	falta = true;
	            }

            	// ------------------------------------------------------------------------

	            // RETORNAR FALTA - SI ES TRUE SE DETIENE EL GUARDADO 
            	// SI ES FALSE CONTINUA LA OPERACION 

            	return falta;

        		// ------------------------------------------------------------------------

      		},
      		guardar() {

	        	// ------------------------------------------------------------------------
	        	
	        	// CONTROLADOR

	        	if (this.controlador() === true) {
	        		this.$bvToast.show('toast-falta-completar');
	        		return;
	        	}

	        	// QUITAR FORMATO TOTAL

	        	this.venta.TOTAL_CRUDO = Common.quitarComaCommon(this.venta.TOTAL);

	        	// ------------------------------------------------------------------------

	        	this.$refs.compontente_medio_pago.procesarFormas();

	        	// ------------------------------------------------------------------------

        	},
        	ticket_mostrar(){

        		//this.ticket(8, 1);
        		Common.generarPdfTicketVentaVisualizarCommon(this.venta.CODIGO, 1);
        	},
        	factura_test(){
        		Common.generarPdfFacturaVentaVisualizarCommon(this.venta.CODIGO, 1);
        		//this.factura(8, 1);
        	},
        	resumen_test(){
        		Common.generarPdfResumenCajaVentaCommon(8, 1);
        	},
      		gravadas(){

      			// ------------------------------------------------------------------------

      			this.venta.GRAVADAS = Common.darFormatoCommon(Common.quitarComaCommon(this.venta.TOTAL) - Common.quitarComaCommon(this.venta.IMPUESTO), 0);

      			// ------------------------------------------------------------------------

      		},
      		nombreCliente(cliente){

      			// ------------------------------------------------------------------------

      			this.cliente.NOMBRE = cliente;

      			// ------------------------------------------------------------------------

      		},
      		codigoCliente(cliente){

      			// ------------------------------------------------------------------------

      			this.cliente.CODIGO = cliente;

      			// ------------------------------------------------------------------------

      		},
      		nombreVendedor(vendedor){

      			// ------------------------------------------------------------------------

      			this.vendedor.NOMBRE = vendedor;

      			// ------------------------------------------------------------------------

      		},
      		codigoVendedor(vendedor){

      			// ------------------------------------------------------------------------

      			this.vendedor.CODIGO = vendedor;

      			// ------------------------------------------------------------------------

      		},
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

		        // REVISAR SI ES PARA DESCUENTO O CANTIDAD 

		        if (codigo.substring(0, 1) === "+") {

		        	me.producto.CANTIDAD = codigo.substring(1, 20);
		        	me.producto.COD_PROD = '';
		        	return;

		        } else if (codigo.substring(0, 1) === "*") {

		        	me.producto.DESCUENTO = codigo.substring(1, 20);

		        	// ------------------------------------------------------------------------

		        	// SI EL DESCUENTO SUPERA EL 100 O ESTA POR DEBAJO

		        	if (me.producto.DESCUENTO > 100 || me.producto.DESCUENTO < 0) {
		        		me.$bvToast.show('toast-descuento-error');
		        		me.producto.DESCUENTO = 0;
		        		return;
		        	}

		        	// ------------------------------------------------------------------------

		        	me.producto.TIPO_DESCUENTO = 3;
		        	me.producto.COD_PROD = '';
		        	return;
		        }

	            // ------------------------------------------------------------------------

	            // CONSULTAR PRODUCTO CON LOTE 

	            Common.obtenerProductoPOSCommon(codigo, me.moneda.CODIGO).then(data => {

	            	if (data.response  === true) {

		            	// ------------------------------------------------------------------------

		            	// REVISAR SI EL STOCK ES CERO 

		            	if (data.producto.STOCK === 0) {
		            		me.$bvToast.show('toast-stock-cero');
		            		return;
		            	} else if (me.producto.CANTIDAD > data.producto.STOCK) {
		            		me.$bvToast.show('toast-cantidad-superada');
		            		return;
		            	}

		            	// ------------------------------------------------------------------------

		            	// AGREGAR PRODUCTO 

		            	me.producto.COD_PROD = data.producto.CODIGO;
		            	me.producto.DESCRIPCION = data.producto.DESCRIPCION;
		            	me.producto.PREC_VENTA = data.producto.PREC_VENTA;
		            	me.producto.PREMAYORISTA = data.producto.PREMAYORISTA;
		            	me.producto.LOTE = data.producto.LOTE;
		            	me.producto.STOCK = data.producto.STOCK;
		            	me.producto.IVA = data.producto.IMPUESTO;
		            	me.producto.CODIGO_REAL = data.producto.CODIGO_REAL;
		            	me.producto.DESCUENTO_CATEGORIA = data.producto.DESCUENTO_CATEGORIA;

						// ------------------------------------------------------------------------

						// DESCUENTO POR MARCA O CATEGORIA

						if (data.descuento_marca !== false) {
							me.producto.DESCUENTO = data.descuento_marca.DESCUENTO;
							me.producto.TIPO_DESCUENTO = 5;
						} else if (data.descuento_categoria > 0 && me.producto.DESCUENTO === 0) {
							me.producto.DESCUENTO = data.descuento_categoria;
							me.producto.TIPO_DESCUENTO = 6;
						}

						// ------------------------------------------------------------------------

						// AGREGAR PRODUCTO 

						me.agregarProducto();

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
	    		

	        },
	        agregarProducto(){

	            // ------------------------------------------------------------------------

	            // INICIAR VARIABLES

	            let me = this;
	            var iva = 0;
	            var tableVenta = $("#tablaVenta").DataTable();
	             
	            // ------------------------------------------------------------------------

	            // CONTROLADOR

	            if (me.producto.COD_PROD.length === 0) {
	                me.validar.COD_PROD = true;
	                return;
	            } else {
	                me.validar.COD_PROD = false;
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

	            // ------------------------------------------------------------------------

	            // SWITCH MAYORISTA

	            if (me.checked.MAYORISTA === false) {

		            // ------------------------------------------------------------------------

		            // SI LA CANTIDAD SUPERA LA CANTIDAD DE PRECIO MAYORISTA O LO IGUALA 

		            if ((Common.quitarComaCommon(me.producto.CANTIDAD) >= me.ajustes.LIMITE_MAYORISTA) && (me.producto.DESCUENTO === 0 || me.producto.DESCUENTO === 0.00)) {
		            	me.producto.PREC_VENTA = me.producto.PREMAYORISTA;
		            	me.producto.DESCUENTO = 0;
		            }

		            // ------------------------------------------------------------------------

		            // PRECIO MAYORISTA CODIGO REAL

		            if (Common.mayoristaCommon(me.producto.CODIGO_REAL,tableVenta, me.ajustes.LIMITE_MAYORISTA, me.producto.PREMAYORISTA, me.producto.CANTIDAD, me.moneda.DECIMAL) === true) {
		            	me.producto.PREC_VENTA = me.producto.PREMAYORISTA;
		            	me.producto.DESCUENTO = 0;
		            } 

		            // ------------------------------------------------------------------------

	            } else if (me.checked.MAYORISTA === true && me.producto.PREMAYORISTA !== 0 && me.producto.PREMAYORISTA !== 0.00){

	            	// ------------------------------------------------------------------------

	            	me.producto.PREC_VENTA = me.producto.PREMAYORISTA;

	            	if (me.producto.DESCUENTO < 100) {
	            		me.producto.DESCUENTO = 0;
	            	}
	            	

	            	// ------------------------------------------------------------------------
	            }

	            // ------------------------------------------------------------------------

	            //  QUITAR COMA DE PRECIO

	            me.producto.PRECIO_TOTAL = Common.multiplicarCommon(me.producto.CANTIDAD, me.producto.PREC_VENTA, me.moneda.DECIMAL);
	         
	            // ------------------------------------------------------------------------

	            //  DAR FORMATO AL RESULTADO FINAL PARA MOSTRAR EN DATATABLE 

	            me.producto.PRECIO_TOTAL = Common.darFormatoCommon(me.producto.PRECIO_TOTAL, me.moneda.DECIMAL);

	            // ------------------------------------------------------------------------

	            // DESCUENTO UNITARIO 

	            me.producto.DESCUENTO_UNITARIO = Common.descuentoCommon(me.producto.DESCUENTO, me.producto.PREC_VENTA, me.moneda.DECIMAL);

	            // ------------------------------------------------------------------------

	            // RESTAR DESCUENTO DE PRECIO UNITARIO 

	            me.producto.PREC_VENTA = Common.restarCommon(me.producto.PREC_VENTA, me.producto.DESCUENTO_UNITARIO, me.moneda.DECIMAL);

	            // ------------------------------------------------------------------------

	            // CALCULAR DESCUENTO

	            me.producto.DESCUENTO_MONTO = Common.descuentoCommon(me.producto.DESCUENTO, me.producto.PRECIO_TOTAL, me.moneda.DECIMAL);

	            // ------------------------------------------------------------------------

	            // RESTAR DESCUENTO 

	            me.producto.PRECIO_TOTAL = Common.restarCommon(me.producto.PRECIO_TOTAL, me.producto.DESCUENTO_MONTO, me.moneda.DECIMAL);

	            // ------------------------------------------------------------------------

	            // CALCULAR IVA

            	me.producto.IMPUESTO = Common.calcularIVACommon(me.producto.PRECIO_TOTAL, me.producto.IVA, me.moneda.DECIMAL);
            	
            	// ------------------------------------------------------------------------

	            // CARGAR DATO EN TABLA COMPRAS

	            me.agregarFilaTabla(me.producto.COD_PROD, me.producto.DESCRIPCION, me.producto.LOTE, me.producto.DESCUENTO, me.producto.CANTIDAD, me.producto.IMPUESTO, me.producto.PREC_VENTA, me.producto.PRECIO_TOTAL, me.producto.IVA, me.producto.DESCUENTO_MONTO, me.producto.CODIGO_REAL, me.producto.PREMAYORISTA, me.producto.DESCUENTO_UNITARIO);

	            // ------------------------------------------------------------------------

	            // VACIAR TEXTBOX AGREGAR PRODUCTO

	            me.inivarAgregarProducto();

	            // ------------------------------------------------------------------------

	            // VERIFICAR SI LA TABLA YA TIENE PRODUCTOS PARA DESHABILITAR MONEDA 

	            if (tableVenta.rows().data().length > 0) {
	    			me.moneda.DESHABILITAR = true;
	    		}

	    		// ------------------------------------------------------------------------

		        // LLAMAR AL METODO HIJO 

		        this.producto.COD_PROD = '';
		        this.$refs.compontente_codigo_producto.vaciarDevolver();

		        // ------------------------------------------------------------------------

	        }, agregarFilaTabla(codigo, descripcion, lote, descuento, cantidad, impuesto, precio, precio_total, iva, descuento_total, codigo_real, premayorista, descuento_unitario){

	        	// ------------------------------------------------------------------------

	        	//	INICIAR VARIABLES 

	        	let me = this;
	        	var tableVenta = $('#tablaVenta').DataTable();
	        	var productoExistente = [];
	        	var cantidadNueva = 0;

	        	// ------------------------------------------------------------------------

	            // REVISAR SI EXISTE VALORES REPETIDOS EN TABLA TRANSFERENCIAS 
	            // LA OPCION 1 ES PARA DEVOLVER SOLO TRUE O FALSE SI EXISTE O NO
	            // LA OPCION 2 ES PARA DEVOLVER MAS DATOS DEL PRODUCTO 

	            productoExistente = Common.existeProductoDataTableCommon(tableVenta, codigo, 2);
	           	
	            if (productoExistente.respuesta == true) {

	            	// ------------------------------------------------------------------------

	            	// SUMAR LA CANTIDAD E IVA

	            	cantidadNueva = parseFloat(productoExistente.cantidad) + parseFloat(cantidad);

	            	// ------------------------------------------------------------------------

	            	// REVISAR SI LA NUEVA CANTIDAD SUPERA AL STOCK

	            	if (cantidadNueva > me.producto.STOCK) {
	            		me.$bvToast.show('toast-cantidad-superada');
		            	return;
	            	}

	            	// ------------------------------------------------------------------------

	            	// EDITAR CANTIDAD PRODUCTO 

	            	me.editarCantidadProducto(tableVenta, cantidadNueva, impuesto, precio, productoExistente.row, descuento, descuento_total, descuento_unitario);
	            	return;

	            	// ------------------------------------------------------------------------

	            } else {
	            	me.validarCodigoProducto = false;
	            }

	            // ------------------------------------------------------------------------

	        	// AGREGAR FILAS 

	        	 tableVenta.rows.add( [ {
			                    "ITEM": '',
			                    "CODIGO":   codigo,
			                    "DESCRIPCION":     descripcion,
			                    "LOTE": lote,
			                    "DESCUENTO": descuento,
			                    "CANTIDAD": cantidad,
			                    "IMPUESTO": impuesto,
			                    "PRECIO":    precio,
			                    "PRECIO_TOTAL":    precio_total,
			                    "ACCION":    "&emsp;<a role='button' id='mostrarProductoFila' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a> &emsp;<a role='button' id='editarProducto' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>&emsp;<a role='button'  title='Eliminar'><i id='eliminarProducto' class='fa fa-trash text-danger' aria-hidden='true'></i></a>",
			                    "IVA": iva,
			                    "DESCUENTO_TOTAL": descuento_total,
			                    "CODIGO_REAL": codigo_real,
			                    "PREMAYORISTA": premayorista,
			                    "DESCUENTO_UNITARIO": descuento_unitario
			                } ] )
			     .draw();
			     
			    // ------------------------------------------------------------------------ 

	            // AGREGAR INDEX A LA TABLA TRANSFERNCIAS

	            tableVenta.on( 'order.dt search.dt', function () {
	                tableVenta.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	                    cell.innerHTML = i+1;
	                } );
	            } ).draw();

	            // ------------------------------------------------------------------------

	            this.gravadas();

	            // ------------------------------------------------------------------------

	            // AJUSTAR COLUMNAS DE ACUERDO AL DATO QUE CONTIENEN

	            tableVenta.columns.adjust().draw();

	            // ------------------------------------------------------------------------

	        }, editarCantidadProducto(tabla, cantidad, impuesto, precio, row, descuento, descuento_total, descuento_unitario){

	        	// ------------------------------------------------------------------------

	        	// INICIAR VARIABLES

	        	let me = this;
	        	var precio_total = 0;
	        	var iva = 0;

	        	// ------------------------------------------------------------------------

	            // PROHIBIR EDITADO SI CANTIDAD O PRECIO ES CERO

	            if (cantidad === '0' || precio === '0') {
	                me.$bvToast.show('toast-editar-cero');
	                return;	
	            }

	            // ------------------------------------------------------------------------

	            // CARGAR DESCUENTO

	            tabla.cell(row, 4).data(descuento).draw();

	            // ------------------------------------------------------------------------

	            // CARGAR CANTIDAD

	            tabla.cell(row, 6).data(cantidad).draw();

	            // ------------------------------------------------------------------------
	            
	            // CARGAR IMPUESTO

	            tabla.cell(row, 7).data(impuesto).draw();

	            // ------------------------------------------------------------------------

	            // CARGAR PRECIO 

	            tabla.cell(row, 8).data(precio).draw();

	            // ------------------------------------------------------------------------

	            // DESCUENTO UNITARIO

	            tabla.cell(row, 14).data(descuento_unitario).draw();

	            // ------------------------------------------------------------------------

	            // DESCUENTO TOTAL

	            tabla.cell(row, 14).data(descuento_unitario).draw();

	            // ------------------------------------------------------------------------

	            // CALCULAR PRECIO TOTAL

	            precio_total = Common.multiplicarCommon(cantidad, precio, me.moneda.DECIMAL);

			    // ------------------------------------------------------------------------

			    // CALCULAR DESCUENTO TOTAL

	            descuento_total = Common.descuentoCommon(descuento, precio_total, me.moneda.DECIMAL);
	            tabla.cell(row, 5).data(descuento_total).draw();

	            // ------------------------------------------------------------------------

			    // CARGAR PRECIO CALCULADO 

			    precio_total = Common.restarCommon(precio_total, descuento_total, me.moneda.DECIMAL);
	            tabla.cell(row, 9).data(precio_total).draw();

	            // ------------------------------------------------------------------------

	            // LLAMAR TOAST MODIFICADO

	            me.$bvToast.show('toast-producto-transferencia-modificado');

	            // ------------------------------------------------------------------------

	        }, inivarAgregarProducto(){

				// ------------------------------------------------------------------------

				// VACIAR TEXTBOX 

				this.producto = {
					COD_PROD: '',
					DESCRIPCION: '',
					LOTE: '',
					PREC_VENTA: '',
					CANTIDAD: '',
					PRECIO_TOTAL: '',
					PREC_VENTA: '',
					CANTIDAD: 1,
					DESCUENTO: 0
				}

	            // ------------------------------------------------------------------------   

			}, 
			numeracion() {

				let me = this;


				Common.numeracionVentaCommon(me.respuesta).then(data => {
					me.venta.CODIGO = data.CODIGO;
					me.venta.CODIGO_CAJA = data.CODIGO_CAJA;
				}).catch(error => {
					Swal.showValidationMessage(
					  `Request failed: ${error}`
					)
				});

			}, 
			inicio() {

				// ------------------------------------------------------------------------ 

				let me = this;

				// ------------------------------------------------------------------------ 

				// OBTENER DATOS DE INICIO PARA VENTA

				Common.inicioVentaCommon().then(data => {

					// ------------------------------------------------------------------------ 

					me.cliente.NOMBRE = data.CLIENTE.NOMBRE;
					me.cliente.CODIGO = data.CLIENTE.CODIGO;
					me.vendedor.CODIGO = data.EMPLEADO.CODIGO;
					me.vendedor.NOMBRE = data.EMPLEADO.NOMBRE;
					me.moneda.DECIMAL = data.MONEDA.CANDEC;
					me.moneda.CODIGO = data.MONEDA.CODIGO;
					me.moneda.DESCRIPCION = data.MONEDA.DESCRIPCION;
					me.ajustes.LIMITE_MAYORISTA = data.LIMITE_MAYORISTA;
					me.ajustes.IMPRESORA_TICKET = data.IMPRESORA_TICKET;
					me.ajustes.IMPRESORA_MATRICIAL = data.IMPRESORA_MATRICIAL;

					// ------------------------------------------------------------------------ 

				}).catch(error => {
					Swal.showValidationMessage(
					  `Request failed: ${error}`
					)
				});

				// ------------------------------------------------------------------------ 

				// COTIZACION DIA 

				Common.obtenerCotizacionDia().then(data => {
					me.cotizacion.GUARANIES = data.Guaranies;
					me.cotizacion.DOLARES = data.Dolares;
					me.cotizacion.REALES = data.Reales;
					me.cotizacion.PESOS = data.Pesos;
				});

				// ------------------------------------------------------------------------ 

			},
			test() {

				// qz.printers.find("EPSON TM-U220 Receipt").then(function(found) {
				//    alert("Printer: " + found);
				// });

				qz.websocket.connect().then(function() { 
				   return qz.printers.find("TICKET");              // Pass the printer name into the next Promise
				}).then(function(printer) {
				   var config = qz.configs.create(printer);       // Create a default config for the found printer
				   var data = ['^XA^FO50,50^ADN,36,20^FDRAW ZPL EXAMPLE^FS^XZ'];   // Raw ZPL
				   return qz.print(config, data);
				}).catch(function(e) { console.error(e); });

			},
			factura(numero, caja) {

				// ------------------------------------------------------------------------ 

				let me = this;

				// ------------------------------------------------------------------------ 

				Common.generarPdfFacturaVentaCommon(numero, caja).then(response => {

						var reader = new FileReader();
						 reader.readAsDataURL(new Blob([response])); 
						reader.onloadend = function() {
						     var base64data = reader.result;
						     base64data = base64data.replace("data:application/octet-stream;base64,", "");
						     me.imprimir_factura(base64data);
						 }

				});

				// ------------------------------------------------------------------------ 

				// Common.generarPdfTicketVentaTestCommon();

			},
			ticket(numero, caja) {

				// ------------------------------------------------------------------------ 

				let me = this;

				// ------------------------------------------------------------------------ 

				Common.generarPdfTicketVentaCommon(numero, caja).then(response => {

						var reader = new FileReader();
						 reader.readAsDataURL(new Blob([response])); 
						reader.onloadend = function() {
						     var base64data = reader.result;
						     base64data = base64data.replace("data:application/octet-stream;base64,", "");
						     me.imprimir(base64data, numero, caja);
						 }

				});

				// ------------------------------------------------------------------------ 

				// Common.generarPdfTicketVentaTestCommon();

			}, imprimir(base64, numero, caja) {

				let me = this;

				qz.websocket.connect().then(function() { 
					   return qz.printers.find(me.ajustes.IMPRESORA_TICKET);              // Pass the printer name into the next Promise
					}).then(function(printer) {

						     var config = qz.configs.create(printer, { copies: 2 });
						var data = [{ 
						   type: 'pixel',
           					format: 'pdf',
           					flavor: 'base64',
						   data: base64
						}];

					   return qz.print(config, data).then(function() {

					   		// ------------------------------------------------------------------------ 

					   		// AVISAR QUE YA IMPRIMIO TICKET 

					   		me.impresion.TICKET = true;

					   		// ------------------------------------------------------------------------ 

					   		// CUANDO SE DESCONECTA TICKET ENVIAR IMPRESION FACTURA

					   		qz.websocket.disconnect();

					   		if (me.impresion.TIPO === "2" && me.checked.FACTURA === false) {
								me.factura(numero, caja);
							}

					   		// ------------------------------------------------------------------------ 

					   		// RECARGAR LA PAGINA 

					   		me.recargar();

					   		// ------------------------------------------------------------------------ 

					   });
						 
					   
					}).catch(function(e) { console.error(e); });

			}, imprimir_factura(base64) {

				let me = this;
				
				qz.websocket.connect().then(function() { 
					   return qz.printers.find(me.ajustes.IMPRESORA_MATRICIAL);              // Pass the printer name into the next Promise
					}).then(function(printer) {

						var config = qz.configs.create(printer);
						var data = [{ 
						   type: 'pixel',
           					format: 'pdf',
           					flavor: 'base64',
						   data: base64
						}];

					   return qz.print(config, data).then(function() {
					   		me.impresion.FACTURA = true;
					   		qz.websocket.disconnect();
					   		me.recargar();
					   });
						 
					   
					}).catch(function(e) { console.error(e); });

			}
      },
        mounted() {

        	let me = this;

        	me.numeracion();
        	me.inicio();

   //      	qz.websocket.connect().then(function() {
			//    alert("Connected!");
			// });

            	// ------------------------------------------------------------------------
                // >>
                // INICIAR EL DATATABLE TRANSFERENCIA 
                // ------------------------------------------------------------------------

                var tableVenta = $('#tablaVenta').DataTable({
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
                            { "data": "DESCUENTO" },
                            { "data": "DESCUENTO_TOTAL" },
                            { "data": "CANTIDAD" },
                            { "data": "IMPUESTO" },
                            { "data": "PRECIO" },
                            { "data": "PRECIO_TOTAL" },
                            { "data": "ACCION" },
                            { "data": "IVA" },
                            { "data": "CODIGO_REAL" },
                            { "data": "PREMAYORISTA" },
                            { "data": "DESCUENTO_UNITARIO" }
                        ],
                        "columnDefs": [
				            {
				                "targets": [ 11 ],
				                "visible": false,
				                "searchable": false
				            },
				            {
				                "targets": [ 12 ],
				                "visible": false,
				                "searchable": false
				            },
				            {
				                "targets": [ 13 ],
				                "visible": false,
				                "searchable": false
				            },
				            {
				                "targets": [ 14 ],
				                "visible": false,
				                "searchable": false
				            }
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

						  // CANTIDAD 

						  api.columns('.impuestoColumna', {
						    
						  }).every(function() {
						    var impuesto = this
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

						      $( api.columns('.impuestoColumna').footer() ).html(
					                me.venta.IMPUESTO = Common.darFormatoCommon(impuesto, 0)
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

						  // PRECIO TOTAL

						  api.columns('.precioTotalColumna', {
						    
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

						      $( api.columns('.precioTotalColumna').footer() ).html(
					                me.venta.TOTAL = Common.darFormatoCommon(precio, me.moneda.DECIMAL)
					           );

						      // *******************************************************************

						  });

						  
						  // *******************************************************************
						  
						}      
                });
                
                // ------------------------------------------------------------------------

                // AJUSTAR TAMAÑO DE FUENTE DE TABLA

    			$("#tablaVenta").css("font-size", 12);
				tableVenta.columns.adjust().draw();

				// ------------------------------------------------------------------------

				// MOSTRAR SWEET ELIMINAR

                $('#tablaVenta').on('click', 'tbody tr #eliminarProducto', function() {

                    // *******************************************************************

                    // ABRIR EL SWEET ALERT

                    Swal.fire({
					  title: 'Estas seguro ?',
					  text: "Eliminar el producto " + tableVenta.row($(this).parents('tr')).data().CODIGO + " !",
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
	                    
		                tableVenta.row($(this).parents('tr')).remove().draw(); 

	                    // *******************************************************************

					  	Swal.fire(
							      'Eliminado!',
							      'Se ha eliminado el producto de la tabla !',
							      'success'
						)

						// *******************************************************************

					  }
					})

                    // *******************************************************************

                });

                // ------------------------------------------------------------------------

                // MOSTRAR MODAL PRODUCTO

                $('#tablaVenta').on('click', 'tbody tr #mostrarProductoFila', function() {

                	// *******************************************************************

                	// OBTENER DATOS DEL PRODUCTO DATATABLE JS

                	me.codigo_detalle = tableVenta.row($(this).parents('tr')).data().CODIGO;
                	me.$refs.detalle_producto.mostrar();
                	// OBTENER IMAGEN - UTIL
                	// me.imagen = $(tableProductos.row($(this).parents('tr')).data().IMAGEN).attr('src');

                    // *******************************************************************

                });

                // ------------------------------------------------------------------------
        }
    }
				
</script>