<template>
	<div class="container-fluid mt-4">
		<div class="row" >

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
		                    <th>Fecha</th>
		                    <th>Tipo</th>
		                    <th>Total</th>
		                    <th>Acción</th>
		                </tr>
		            </thead>
		            <tbody>
		                <td></td>
		            </tbody>
		        </table> 


			</div>	
		</div>

		<!-- ------------------------------------------------------------------------ -->

		<!-- <div v-else>
			<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div> -->

		<!-- ------------------------------------------------------------------------ -->

	</div>
</template>
<script>


	 export default {
	  props: ['id_sucursal'],	
      data(){
        return {
          	codigoVenta: '',
          	procesar: false
        }
      }, 
      methods: {
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
						     me.imprimir(base64data);
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

					   return qz.print(config, data);
						 
					   
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

					   return qz.print(config, data);
						 
					   
					}).catch(function(e) { console.error(e); });

			}
      },
        mounted() {
        	
        	let me = this;

            $(document).ready( function () {

            		// ------------------------------------------------------------------------

            		// PREPARAR DATATABLE 

	 				var tableVentaMostrar = $('#tablaVentaMostrar').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "url": "/venta/datatable",
                                 "dataType": "json",
                                 "type": "GET",
                                 "contentType": "application/json; charset=utf-8"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "FECHA" },
                            { "data": "TIPO" },
                            { "data": "TOTAL" },
                            { "data": "ACCION" }
                        ]      
                    });

                    // ------------------------------------------------------------------------

                    // ELIMINAR TRANSFERENCIA

                    $('#tablaVentaMostrar').on('click', 'tbody tr #eliminarTransferencia', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                    me.eliminarTransferencia(tableVentaMostrar.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // GENERAR FACTURA

                    $('#tablaVentaMostrar').on('click', 'tbody tr #imprimirTransferencia', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	                   	me.procesar = true;
	                   	var row  = $(this).parents('tr')[0];
	                   	Common.generarPdfFacturaTransferenciaCommon(tableVentaMostrar.row( row ).data().CODIGO).then( (data) => {
	                   		if (data !== undefined) {
	                   			Swal.fire({
						  			title: 'Error',
						  			text: "Revise cotización !",
						  			type: 'warning',
									showLoaderOnConfirm: true,
									confirmButtonColor: 'btn btn-success',
									confirmButtonText: 'Aceptar'
								});
	                   		}
	                   		me.procesar = false;
	                   	}).catch((err) => {
	                   		me.procesar = false;
           				});

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // GENERAR REPORTE PDF

                    $('#tablaVentaMostrar').on('click', 'tbody tr #imprimirReporte', function() {

	                    // *******************************************************************

	                    // ENVIAR A COMMON FUNCTION PARA GENERAR REPORTE PDF

	                   	me.procesar = true;
	                   	var row  = $(this).parents('tr')[0];
	                   	Common.generarRptPdfTransferenciaCommon(tableVentaMostrar.row( row ).data().CODIGO, 0).then( () => {
	                   		me.procesar = false;
	                   	});
	                   	

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    $('#tablaVentaMostrar').on('click', 'tbody tr #mostrarTransferencia', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA

	                   	 var row  = $(this).parents('tr')[0];
	                   	 me.mostrarModalTranferencia(tableVentaMostrar.row( row ).data().CODIGO, me.id_sucursal);

	                    // *******************************************************************

	                });

	                // ------------------------------------------------------------------------
	 });	
        }
    }
</script>