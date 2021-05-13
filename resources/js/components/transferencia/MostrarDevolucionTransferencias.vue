<template>
	<div class="container-fluid mt-4">
		<div v-if="$can('transferencia.mostrardevo') && $can('transferencia')">
		<!-- <div class="row" v-if="$can('transferencia.mostrar')"> -->

				<!-- ------------------------------------------------------------------------------------- -->

				<!-- TITULO  -->
				
				<div class="col-md-12">
					<vs-divider>
						Mostrar Transferencias Devolucion
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
					<table id="tablaTransferenciasDev" class="table table-striped table-hover table-bordered table-sm mb-3" style="width:100%">
			            <thead>
			                <tr>
			                    <th>Codigo</th>
			                    <th>Codigo Transferencia</th>
			                    <th>Origen</th>
			                    <th>Destino</th>
			                    <th>Fecha</th>
			                    <th>Hora</th>
			                    <th>Estatus</th>
			                    <th>Acción</th>
			                </tr>
			            </thead>
			            <tbody>
			                <td></td>
			            </tbody>
			        </table> 


				</div>	
		<!-- 	</div> -->

			<!-- ------------------------------------------------------------------------ -->

			<!-- <div v-else>
				<cuatrocientos-cuatro></cuatrocientos-cuatro>
			</div>
	 -->
			<!-- ------------------------------------------------------------------------ -->
			
			<!-- MODAL MOSTRAR DETALLE TRANSFERENCIA -->

			<modal-detalle-transferencia-dev 
			ref="ModalImportarTransferencia"
			></modal-detalle-transferencia-dev>

		<!-- ------------------------------------------------------------------------ -->

		</div>
		<div v-else>
	    	<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div>
	</div>
</template>
<script>
	import App from './MostrarTransferencias.vue'


	 export default {
	  props: ['id_sucursal'],	
      data(){
        return {
          	codigoTransferencia: '',
          	procesar: false,
          	ajustes: {
          		IMPRESORA_TICKET: '',
          		IMPRESORA_MATRICIAL: ''
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

			},
      		editarTransferencia(codigo){

      			// ------------------------------------------------------------------------

      			// MANDAR CODIGO TRANSFERENCIA

      			 this.$router.push('/tr2/'+ codigo + '');

      			// ------------------------------------------------------------------------
      			
      		}, mostrarModalTranferencia(codigo, codigo_origen) {

      			// ------------------------------------------------------------------------

      			// LLAMAR EL METODO DEL COMPONENTE HIJO

      			this.$refs.ModalImportarTransferencia.mostrarModal(codigo, codigo_origen);

      			// ------------------------------------------------------------------------
      		}, eliminarDevTransferencia(codigo){

      			// ------------------------------------------------------------------------

      			// INICIAR VARIABLES

      			var tableTransferencia = $('#tablaTransferenciasDev').DataTable();

      			// ------------------------------------------------------------------------

      			// MOSTRAR LA PREGUNTA DE ELIMINAR 

      			Swal.fire({
				  title: 'Estas seguro ?',
				  text: "Eliminar la devolucion de Transferencia " + codigo + " !",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  confirmButtonColor: '#d33',
				  cancelButtonColor: '#3085d6',
				  confirmButtonText: 'Si, eliminalo!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
				    return Common.eliminarDevTransferenciaCommon(codigo).then(data => {
				    	if (!data.response === true) {
				          throw new Error(data.statusText);
				        }
				  		return data.response;
				  	}).catch(error => {
				        Swal.showValidationMessage(
				          `Request failed: ${error}`
				        )
				    });
				  }
				}).then((result) => {
				  if (result.value) {
				  	Swal.fire(
						      'Eliminado!',
						      'Se ha eliminado la devolucion de transferencia y devuelto el stock !',
						      'success'
					)

				  	// ------------------------------------------------------------------------

				  	// RECARGAR TABLA 
				  	
					tableTransferencia.ajax.reload( null, false );

					// ------------------------------------------------------------------------

				  }
				})

				// ------------------------------------------------------------------------

      		}, enviarDevTransferencia(codigo){
      			
      			// ------------------------------------------------------------------------

      			// INICIAR VARIABLES

      			var tableTransferencia = $('#tablaTransferenciasDev').DataTable();

      			// ------------------------------------------------------------------------

      			// MOSTRAR LA PREGUNTA DE ELIMINAR 

      			Swal.fire({
				  title: 'Estas seguro ?',
				  text: "Enviar la devolucion de Transferencia " + codigo + " !",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  cancelButtonColor: '#3085d6',
				  confirmButtonText: 'Si, envialo!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
				    return Common.enviarDevTransferenciaCommon(codigo).then(data => {
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
				  if (result.value) {
				  	Swal.fire(
						      'Enviado!',
						      'Se ha enviado la devolucion de transferencia correctamente !',
						      'success'
					)

				  	// ------------------------------------------------------------------------

				  	// RECARGAR TABLA 
				  	
					tableTransferencia.ajax.reload( null, false );

					// ------------------------------------------------------------------------

				  }
				})

				// ------------------------------------------------------------------------

      		}, factura(codigo) {

					// ------------------------------------------------------------------------ 

					let me = this;

					// ------------------------------------------------------------------------ 

					Common.generarPdfFacturaTransferenciaCommon(codigo).then( (response) => {

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

			}
      },
        mounted() {
        	
        	let me = this;

        	// ------------------------------------------------------------------------

        	// LLAMAR LOS DATOS DE LA IMPREOSRA 

        	me.inicio();

        	// ------------------------------------------------------------------------

            $(document).ready( function () {

            		// ------------------------------------------------------------------------

            		// INICIAR VARIABLES

            		
            		const app = new Vue(App)

            		// ------------------------------------------------------------------------

            		// PREPARAR DATATABLE 

	 				var tableTransferencia = $('#tablaTransferenciasDev').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "url": "/transferenciasDev",
                                 "dataType": "json",
                                 "type": "GET",
                                 "contentType": "application/json; charset=utf-8"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "CODIGO_TRANSFERENCIA" },
                            { "data": "ORIGEN" },
                            { "data": "DESTINO" },
                            { "data": "FECHA" },
                            { "data": "HORA" },
                            { "data": "ESTATUS" },
                            { "data": "ACCION" }
                        ]      
                    });
                    
	 				// ------------------------------------------------------------------------

                	// EDITAR TRANSFERENCIA

                    $('#tablaTransferenciasDev').on('click', 'tbody tr #editarTransferencia', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                    me.editarTransferencia(tableTransferencia.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // ELIMINAR TRANSFERENCIA

                    $('#tablaTransferenciasDev').on('click', 'tbody tr #eliminarTransferencia', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                    me.eliminarDevTransferencia(tableTransferencia.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // GENERAR FACTURA

                    $('#tablaTransferenciasDev').on('click', 'tbody tr #imprimirTransferencia', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	       //             	me.procesar = true;
	       //             	var row  = $(this).parents('tr')[0];
	       //             	Common.generarPdfFacturaTransferenciaCommon(tableTransferencia.row( row ).data().CODIGO).then( (data) => {
	       //             		if (data !== undefined) {
	       //             			Swal.fire({
						  // 			title: 'Error',
						  // 			text: "Revise cotización !",
						  // 			type: 'warning',
								// 	showLoaderOnConfirm: true,
								// 	confirmButtonColor: 'btn btn-success',
								// 	confirmButtonText: 'Aceptar'
								// });
	       //             		}
	       //             		me.procesar = false;
	       //             	}).catch((err) => {
	       //             		me.procesar = false;
        //    				});

        				var row  = $(this).parents('tr')[0];

			            me.factura(tableTransferencia.row( row ).data().CODIGO);

			            Swal.fire({
							title: '¡ Imprimiendo Factura !',
							html: 'Por favor espere...',
							onBeforeOpen: () => {
								Swal.showLoading()
							}
						})

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // GENERAR REPORTE PDF

                    $('#tablaTransferenciasDev').on('click', 'tbody tr #imprimirReporte', function() {

	                    // *******************************************************************

	                    // ENVIAR A COMMON FUNCTION PARA GENERAR REPORTE PDF
	    /*                	      Common.arreglar_costo().then(data => {
					        
					            
					              });*/

	                   	me.procesar = true;
	                   	var row  = $(this).parents('tr')[0];
	                   	Common.generarRptPdfTransferenciaCommon(tableTransferencia.row( row ).data().CODIGO, 0).then( () => {
	                   		me.procesar = false;
	                   	});
	                   	

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // ENVIAR TRANSFERENCIA

                    $('#tablaTransferenciasDev').on('click', 'tbody tr #enviarTransferencia', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                    me.enviarDevTransferencia(tableTransferencia.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    $('#tablaTransferenciasDev').on('click', 'tbody tr #mostrarTransferencia', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA

	                   	 var row  = $(this).parents('tr')[0];
	                   	 me.mostrarModalTranferencia(tableTransferencia.row( row ).data().CODIGO, me.id_sucursal);

	                    // *******************************************************************

	                });

	                // ------------------------------------------------------------------------
	 });	
        }
    }
</script>