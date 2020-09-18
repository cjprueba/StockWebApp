<template>
	<div class="container-fluid mt-4">
		<div class="row" v-if="$can('venta.mostrar')">
			<!--   -->
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

			<div class="col-md-12">
				<table id="tablaVentaMostrar" class="table table-striped table-hover table-bordered table-sm mb-3" style="width:100%">
		            <thead>
		                <tr>
		                    <th>Codigo</th>
		                    <th>Caja</th>
		                    <th>Cliente</th>
		                    <th>Fecha</th>
		                    <th>Hora</th>
		                    <th>Tipo</th>
		                    <th>Total</th>
		                    <th>Acción</th>
		                    <th>Total sin letra</th>
		                    <th>Moneda</th>
		                    <th>Candec</th>
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
									
									window.location.href = '/vt2';

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
									
							window.location.href = '/vt2';

						})

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

        	// ------------------------------------------------------------------------

            // PREPARAR DATATABLE 

	 		var tableVentaMostrar = $('#tablaVentaMostrar').DataTable({
                "processing": true,
                "serverSide": true,
                "destroy": true,
                "bAutoWidth": true,
                "select": true,
                "ajax":{
                  "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },	
                  "url": "/venta/datatable",
                  "dataType": "json",
                  "type": "POST"
                },
                "columns": [
                    { "data": "CODIGO" },
                    { "data": "CAJA" },
                    { "data": "CLIENTE" },
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

            // 

            $('#tablaVentaMostrar').on('click', 'tbody tr #eliminarTransferencia', function() {

	          	// *******************************************************************

	          	// REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	          	var row  = $(this).parents('tr')[0];
	          	me.eliminarTransferencia(tableVentaMostrar.row( row ).data().CODIGO);

	          	// *******************************************************************

	      	});

            // ------------------------------------------------------------------------

            // GENERAR FACTURA

            $('#tablaVentaMostrar').on('click', 'tbody tr #imprimirTicket', function() {

	            // *******************************************************************

	            // IMPRIMIR TICKET 
	                   	
	            var row  = $(this).parents('tr')[0];
	                   	
	            me.ticket(tableVentaMostrar.row( row ).data().CODIGO, tableVentaMostrar.row( row ).data().CAJA);

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

	            me.factura(tableVentaMostrar.row( row ).data().CODIGO, tableVentaMostrar.row( row ).data().CAJA);

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
		        me.mostrarModalVenta(tableVentaMostrar.row( row ).data().CODIGO, tableVentaMostrar.row( row ).data().CAJA);

		        // *******************************************************************

	        });

	        // ------------------------------------------------------------------------

	        $('#tablaVentaMostrar').on('click', 'tbody tr #pagarVenta', function() {

		        // *******************************************************************

		        // REDIRIGIR Y ENVIAR CODIGO VENTA

		        var row  = $(this).parents('tr')[0];
		        // me.mostrarModalVenta(tableVentaMostrar.row( row ).data().CODIGO, tableVentaMostrar.row( row ).data().CAJA);
		        me.codigoVenta = tableVentaMostrar.row( row ).data().CODIGO;
		        me.caja.CODIGO = tableVentaMostrar.row( row ).data().CAJA;
		        me.venta.TOTAL = tableVentaMostrar.row( row ).data().TOTAL_SIN_LETRA;
		        me.venta.TOTAL_CRUDO = tableVentaMostrar.row( row ).data().TOTAL_SIN_LETRA;
		        me.moneda.CODIGO = tableVentaMostrar.row( row ).data().MONEDA;
		        me.moneda.DECIMAL = tableVentaMostrar.row( row ).data().CANDEC;
		        me.cliente.CODIGO = tableVentaMostrar.row( row ).data().CLIENTE_CODIGO;
	        	me.$refs.compontente_medio_pago.procesarFormas();

		        // *******************************************************************

	        });

	        // ------------------------------------------------------------------------
	 
        }
    }
</script>