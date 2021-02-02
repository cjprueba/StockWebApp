<template>
	<div class="container-fluid mt-4">
		<div class="row" v-if="$can('venta.mostrar')">
			
			<!-- ------------------------------------------------------------------------------------- -->

			<!-- TITULO  -->
			
			<div class="col-md-12">
				<vs-divider>
					Mostrar Ventas
				</vs-divider>
			</div>
			
	        <!-- ------------------------------------------------------------------------------------- -->

	        <!-- MOSTRAR LOADING -->

	        <div class="col-md-12">
				<div v-if="procesar" class="d-flex justify-content-center mt-3">
					<strong>Procesando...   </strong>
	                <div class="spinner-grow" role="status" aria-hidden="true"></div>
	             </div>
            </div>

			<!-- ------------------------------------------------------------------------ -->

			<div class="col-md-11 mb-3">	
						<div id="sandbox-container" class="input-daterange input-group input-group-sm">
							<input id='selectedInicialFecha' data-date-format="yyyy-mm-dd" class=" form-control " v-model="selectedInicialFecha" v-bind:class="{ 'is-invalid': validar.inicialFecha }"/>
							<div class="input-group-append ">
								<span class="input-group-text">a</span>
							</div>
							<input name='end' id='selectedFinalFecha' data-date-format="yyyy-mm-dd" class=" form-control " v-model="selectedFinalFecha" v-bind:class="{ 'is-invalid': validar.finalFecha }"/>
							<div class="invalid-feedback">
						        {{mensaje.fechaInvalida}}
						    </div>
						</div>

						
			</div>

			<div class="col-md-1 mb-0 text-right">
				<label></label>
				<button class="btn btn-primary btn-sm" v-on:click="obtenerDatatable()">Buscar</button>
			</div>

			<div class="col-md-12">
				<table id="tablaVentaMostrar" class="table table-striped table-hover table-bordered table-sm mb-3" style="width:100%">
		            <thead>
		                <tr>
		                	<th>ID</th>
		                    <th>Codigo</th>
		                    <th>Caja</th>
		                    <th>Cliente</th>
		                    <th>Vendedor</th>
		                    <th>Fecha</th>
		                    <th>Hora</th>
		                    <th>Tipo</th>
		                    <th>Total</th>
		                    <th>Acción</th>
		                    <th>Total sin letra</th>
		                    <th>Moneda</th>
		                    <th>Candec</th>
		                    <th>Cliente Codigo</th>
		                </tr>
		            </thead>
		            <tbody>
		                <td></td>
		            </tbody>
		        </table> 


			</div>	

			<!-- ------------------------------------------------------------------------ -->
		
			<!-- MODAL MOSTRAR DETALLE VENTA -->

			<modal-detalle-venta 
			ref="ModalImportarVenta"
			></modal-detalle-venta>

		</div>

		<!-- ------------------------------------------------------------------------ -->

		<div v-else>
			<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div>

		<!-- ------------------------------------------------------------------------ -->
		
		<!-- MODAL MOSTRAR DETALLE VENTA -->

		<!-- <modal-detalle-venta 
		ref="ModalImportarVenta"
		></modal-detalle-venta> -->

		<!-- ------------------------------------------------------------------------ -->

		<!-- FORMA PAGO -->

		<forma-pago-textbox :total="venta.TOTAL" :total_crudo="venta.TOTAL_CRUDO" :moneda="moneda.CODIGO" :candec="moneda.DECIMAL" :customer="cliente.CODIGO" :tipo="2" @datos="formaPago" ref="compontente_medio_pago"></forma-pago-textbox>

		<!-- ------------------------------------------------------------------------ -->	

		<!-- MODAL ELECCION DE MONEDA -->

                  <div class="modal fade" id="modalMoneda" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">

                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Opciones</h5>
                        </div>

                        <div class="modal-body">

                          <div class="row">
                            <legend class="col-form-label col-sm-2 pt-0">Moneda</legend>
                            <div class="col-sm-10">
                              <div class="form-check form-check-inline">
                                <input  v-model="radio.moneda" class="form-check-input" type="radio" name="radioVuelto" id="gridRadios1" value="1">
                                <label class="form-check-label" for="gridRadios1">
                                  Guaranies
                                </label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input  v-model="radio.moneda" class="form-check-input" type="radio" name="radioVuelto" id="gridRadios2" value="2">
                                <label class="form-check-label" for="gridRadios2">
                                  Dolares
                                </label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input  v-model="radio.moneda" class="form-check-input" type="radio" name="radioVuelto" id="gridRadios3" value="3">
                                <label class="form-check-label" for="gridRadios3">
                                  Reales
                                </label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input  v-model="radio.moneda" class="form-check-input" type="radio" name="radioVuelto" id="gridRadios4" value="4">
                                <label class="form-check-label" for="gridRadios4">
                                  Pesos
                                </label>
                              </div>
                            </div>
                          </div>

                        </div>

                        <div class="modal-footer">
                          <button type="button" class="btn btn-primary" data-dismiss="modal" v-on:click="imprimirPDF()">Aceptar</button>
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        </div>

                      </div>
                    </div>
                  </div>
                  
        <!-- ------------------------------------------------------------------------ -->

	</div>
</template>
<script>

	 export default {
	  props: ['id_sucursal'],	
      data(){
        return {
          	codigoVenta: '',
          	procesar: false,
          	ajustes: {
          		IMPRESORA_TICKET: '',
          		IMPRESORA_MATRICIAL: ''
          	},
          	venta: {
          		TOTAL: 0,
          		TOTAL_CRUDO: 0.00
          	},
          	moneda: {
          		CODIGO: '',
          		DECIMAL: ''
          	},
          	cliente: {
          		CODIGO: ''
          	}, 
          	caja: {
          		CODIGO: null
          	},
          	radio: {
          		moneda: '1'
          	},
          	tableVentaMostrar: '',
          	selectedInicialFecha: '',
          	selectedFinalFecha: '',
          	validar: {
          		inicialFecha: '',
          		finalFecha: ''
          	},
          	mensaje: {
          		fechaInvalida: ''
          	}
        }
      }, 
      methods: {
			inicio() {

				// ------------------------------------------------------------------------ 

				let me = this;

				// ------------------------------------------------------------------------ 

				// OBTENER DATOS DE INICIO PARA VENTA

				Common.inicioVentaCommon().then(data => {

					// ------------------------------------------------------------------------ 

					me.ajustes.IMPRESORA_TICKET = data.IMPRESORA_TICKET;
					me.ajustes.IMPRESORA_MATRICIAL = data.IMPRESORA_MATRICIAL;

					// ------------------------------------------------------------------------ 

				}).catch(error => {
					Swal.showValidationMessage(
					  `Request failed: ${error}`
					)
				});

				// ------------------------------------------------------------------------ 

			}, factura(numero, caja) {

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
						    return me.imprimir(base64data);
						 }

				});

				// ------------------------------------------------------------------------ 

				// Common.generarPdfTicketVentaTestCommon();

			}, imprimir(base64) {

				let me = this;

				qz.websocket.connect().then(function() { 
					return qz.printers.find(me.ajustes.IMPRESORA_TICKET);              // Pass the printer name into the next Promise
				}).then(function(printer) {

					var config = qz.configs.create(printer);
					var data = [{ 
						type: 'pixel',
           				format: 'pdf',
           				flavor: 'base64',
						data: base64
					}];

					return qz.print(config, data).then(function() {
					   	qz.websocket.disconnect();
					   	Swal.close();
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
						qz.websocket.disconnect();
						Swal.close();
					});

				}).catch(function(e) { console.error(e); });

			},
			mostrarModalVenta(codigo, caja) {

      			// ------------------------------------------------------------------------

      			// LLAMAR EL METODO DEL COMPONENTE HIJO

      			this.$refs.ModalImportarVenta.mostrarModal(codigo, caja);

      			// ------------------------------------------------------------------------

      		},
			imprimirPDF() {

      			// ------------------------------------------------------------------------

      			// LLAMAR EL METODO DEL COMPONENTE HIJO


      			Common.generarRptPdfVentaCommon(this.codigoVenta, this.caja.CODIGO, this.radio.moneda).then( () => {
	                   		
	            });

      			// ------------------------------------------------------------------------

      		}, formaPago(datos) {

      			// ------------------------------------------------------------------------

      			let me = this;
      			
	        	// ------------------------------------------------------------------------

	        	// INICIAR VARIABLES

      			var tableVentaMostrar = $('#tablaVentaMostrar').DataTable();

      			// ------------------------------------------------------------------------

	        	if (me.caja.CODIGO === null) {
	        		Swal.fire({
						title: 'NO SE PUDO OBTENER CAJA',
						type: 'warning',
						confirmButtonColor: '#d33',
						confirmButtonText: 'Aceptar',
					})
	        		return;
	        	}

	        	// ------------------------------------------------------------------------

	        	// GUARDAR 

	        	this.respuesta = {
	        		codigo: this.codigoVenta,
	        		caja: this.caja.CODIGO,
	        		moneda: this.moneda.CODIGO,
	        		estatus: 2,
	        		venta: this.codigoVenta,
	        		pago: datos,
	        	}

	        	Swal.fire({
				  title: 'Guardando Pago',
				  html: 'Cerrare en cuanto modifique la venta.',
				  onBeforeOpen: () => {

				  	// ------------------------------------------------------------------------

				  	// MOSTRAR CARGAR 

				    Swal.showLoading()
				    
				    // ------------------------------------------------------------------------

				    Common.guardarPagoPECommon(me.respuesta).then(data => {

				    		// ------------------------------------------------------------------------

					    	if (!data.response === true) {
					          throw new Error(data.statusText);
					        }

					        if (data.response) {

					        	Swal.close();

								// ------------------------------------------------------------------------

								Swal.fire(
									'Guardado !',
									 data.statusText,
									'success'
								)

								// ------------------------------------------------------------------------ 

								// RECARGAR TABLA 
				  	
								tableVentaMostrar.ajax.reload( null, false );

								// ------------------------------------------------------------------------

								me.$refs.compontente_medio_pago.limpiar();

								// ------------------------------------------------------------------------
								
							}

							// ------------------------------------------------------------------------

					}).catch(error => {
					        Swal.showValidationMessage(
					          `Request failed: ${error}`
					        )
					 });
				  }
				}).then((result) => {
				  	

				})

      		},
      		obtenerCaja() {

      			let me=this;
          		
          		// ------------------------------------------------------------------------

          		// OBTENER CAJA 

          		Common.obtenerIPCommon(function(){

          			if (window.IPv !== false) {
          				axios.post('/cajaObtener', {'id': window.IPv}).then(function (response) {
	                	  if (response.data.response === true) {
	                	  	  me.caja.CODIGO  =   response.data.caja[0].CAJA;
	                	  	  me.obtenerDatatable();
	                	  	  // me.caja.CANTIDAD_PERSONALIZADA  =   response.data.caja[0].CANTIDAD_PERSONALIZADA;
	                	  	  // me.caja.CANTIDAD_TICKET = response.data.caja[0].CANTIDAD_TICKET;
	                	  	  // me.numeracion();
	                	  } else {
	                	  		
	                	  	  	Swal.fire({
									title: 'NO SE PUDO OBTENER CAJA',
									type: 'warning',
									confirmButtonColor: '#d33',
									confirmButtonText: 'Aceptar',
								}).then((result) => {
									
									window.location.href = '/vt0';

								})	

	                	  }		
			              
			            })
          			} else {


          				Swal.fire({
							title: 'NO SE PUDO OBTENER LA IP DE LA MAQUINA',
							type: 'warning',
							confirmButtonColor: '#d33',
							confirmButtonText: 'Aceptar',
						}).then((result) => {
									
							window.location.href = '/vt0';

						})

          			}
                	
                });

                // ------------------------------------------------------------------------

      		}, obtenerDatatable() {

      			// ------------------------------------------------------------------------

      			let me = this;

      			// ------------------------------------------------------------------------

	            // PREPARAR DATATABLE 

		 		this.tableVentaMostrar = $('#tablaVentaMostrar').DataTable({
	                "processing": true,
	                "serverSide": true,
	                "destroy": true,
	                "bAutoWidth": true,
	                "select": true,
	                "ajax":{
	                  "data": {
		                 				caja: me.caja.CODIGO,
		                 				inicial: me.selectedInicialFecha,
		                 				final: me.selectedFinalFecha,
		                 				"_token": $('meta[name="csrf-token"]').attr('content')
		                 			},  	
	                  "url": "/venta/datatable",
	                  "dataType": "json",
	                  "type": "POST"
	                },
	                "columns": [
	                	{ "data": "ID" },
	                    { "data": "CODIGO" },
	                    { "data": "CAJA" },
	                    { "data": "CLIENTE" },
	                    { "data": "VENDEDOR" },
	                    { "data": "FECHA" },
	                    { "data": "HORA" },
	                    { "data": "TIPO" },
	                    { "data": "TOTAL" },
	                    { "data": "ACCION" },
	                    { "data": "TOTAL_SIN_LETRA", "visible": false },
	                    { "data": "MONEDA", "visible": false },
	                    { "data": "CANDEC", "visible": false },
	                    { "data": "CLIENTE_CODIGO", "visible": false },
	                ],
	                "createdRow": function( row, data, dataIndex){
	                    $(row).addClass(data['ESTATUS']);
	                }       
	            });

	            // ------------------------------------------------------------------------

	            
      		}
      },
        mounted() {
        	
        	// ------------------------------------------------------------------------

        	let me = this;

        	// ------------------------------------------------------------------------

        	// LLAMAR LOS DATOS DE LA IMPREOSRA 

        	me.inicio();
        	me.obtenerCaja();

	        // ------------------------------------------------------------------------

	 		// MARCAR LA FECHA DE HOY

			var today = new Date();
			var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();

    		// ------------------------------------------------------------------------

    		me.selectedInicialFecha = date;
    		me.selectedFinalFecha = date;

			// ------------------------------------------------------------------------

	 		// FECHAS 

	 		$(function(){
		   		    $('#sandbox-container .input-daterange').datepicker({
		   		    	    keyboardNavigation: false,
    						forceParse: false,
    				});
    				$("#selectedInicialFecha").datepicker().on(
			     		"changeDate", () => {me.selectedInicialFecha = $('#selectedInicialFecha').val()}
					);
					$("#selectedFinalFecha").datepicker().on(
			     		"changeDate", () => {me.selectedFinalFecha = $('#selectedFinalFecha').val()}
					);

			});

	 		// ------------------------------------------------------------------------

	 		

	 		this.tableVentaMostrar = $('#tablaVentaMostrar').DataTable();

	 		// ------------------------------------------------------------------------

	            // 

	            $('#tablaVentaMostrar').on('click', 'tbody tr #eliminarTransferencia', function() {

		          	// *******************************************************************

		          	// REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
		                   	
		          	var row  = $(this).parents('tr')[0];
		          	me.eliminarTransferencia(me.tableVentaMostrar.row( row ).data().CODIGO);

		          	// *******************************************************************

		      	});

	            // ------------------------------------------------------------------------

	            // GENERAR FACTURA

	            $('#tablaVentaMostrar').on('click', 'tbody tr #imprimirTicket', function() {

		            // *******************************************************************

		            // IMPRIMIR TICKET 
		                   	
		            var row  = $(this).parents('tr')[0];
		                   	
		            me.ticket(me.tableVentaMostrar.row( row ).data().CODIGO, me.tableVentaMostrar.row( row ).data().CAJA);

		            Swal.fire({
						title: '¡ Imprimiendo Ticket !',
						html: 'Por favor espere...',
						onBeforeOpen: () => {
							Swal.showLoading()
						}
					})

		            // *******************************************************************

		        });

	            // ------------------------------------------------------------------------

	            // GENERAR REPORTE PDF

	            $('#tablaVentaMostrar').on('click', 'tbody tr #imprimirFactura', function() {

		            // *******************************************************************

		            // ENVIAR A COMMON FUNCTION PARA GENERAR REPORTE PDF

		            var row  = $(this).parents('tr')[0];

		            me.factura(me.tableVentaMostrar.row( row ).data().CODIGO, me.tableVentaMostrar.row( row ).data().CAJA);

		            Swal.fire({
						title: '¡ Imprimiendo Factura !',
						html: 'Por favor espere...',
						onBeforeOpen: () => {
							Swal.showLoading()
						}
					})

		            // *******************************************************************

		            // *******************************************************************

		        });

	           	// ------------------------------------------------------------------------

	           	$('#tablaVentaMostrar').on('click', 'tbody tr #mostrarDetalle', function() {

			        // *******************************************************************

			        // REDIRIGIR Y ENVIAR CODIGO VENTA

			        var row  = $(this).parents('tr')[0];
			        me.mostrarModalVenta(me.tableVentaMostrar.row( row ).data().CODIGO, me.tableVentaMostrar.row( row ).data().CAJA);

			        // *******************************************************************

		        });

		        // ------------------------------------------------------------------------

		        $('#tablaVentaMostrar').on('click', 'tbody tr #pagarVenta', function() {

			        // *******************************************************************

			        // REDIRIGIR Y ENVIAR CODIGO VENTA

			        var row  = $(this).parents('tr')[0];
			        // me.mostrarModalVenta(me.tableVentaMostrar.row( row ).data().CODIGO, me.tableVentaMostrar.row( row ).data().CAJA);
			        me.codigoVenta = me.tableVentaMostrar.row( row ).data().CODIGO;
			        me.caja.CODIGO = me.tableVentaMostrar.row( row ).data().CAJA;
			        me.venta.TOTAL = me.tableVentaMostrar.row( row ).data().TOTAL_SIN_LETRA;
			        me.venta.TOTAL_CRUDO = me.tableVentaMostrar.row( row ).data().TOTAL_SIN_LETRA;
			        me.moneda.CODIGO = me.tableVentaMostrar.row( row ).data().MONEDA;
			        me.moneda.DECIMAL = me.tableVentaMostrar.row( row ).data().CANDEC;
			        me.cliente.CODIGO = me.tableVentaMostrar.row( row ).data().CLIENTE_CODIGO;
		        	me.$refs.compontente_medio_pago.procesarFormas();

			        // *******************************************************************

		        });

		        // ------------------------------------------------------------------------

	           	$('#tablaVentaMostrar').on('click', 'tbody tr #imprimirPdf', function() {

			        // *******************************************************************

			        // REDIRIGIR Y ENVIAR CODIGO VENTA

			        $('#modalMoneda').modal('show');

			        var row  = $(this).parents('tr')[0];
			        
			        me.codigoVenta = me.tableVentaMostrar.row( row ).data().CODIGO;
			        me.caja.CODIGO = me.tableVentaMostrar.row( row ).data().CAJA;

			        // *******************************************************************

		        });
        }
    }
</script>