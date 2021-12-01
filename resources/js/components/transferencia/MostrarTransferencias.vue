<template>
	<div class="container-fluid mt-4">
		<div v-if="$can('transferencia.mostrar') && $can('transferencia')">
			<!-- <div class="row" v-if="$can('transferencia.mostrar')"> -->

				<!-- ------------------------------------------------------------------------------------- -->

				<!-- TITULO  -->
				
				<div class="col-md-12">
					<vs-divider>
						Mostrar Transferencias
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
					<table id="tablaTransferencias" class="table table-striped table-hover table-bordered table-sm mb-3" style="width:100%">
			            <thead>
			                <tr>
			                    <th>Codigo</th>
			                    <th>Origen</th>
			                    <th>Destino</th>
			                    <th>N. Caja</th>
			                    <th>IVA</th>
			                    <th>Total</th>
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

			<modal-detalle-transferencia 
			ref="ModalImportarTransferencia"
			></modal-detalle-transferencia>

			<!-- ------------------------------------------------------------------------ -->

		</div>
		<div v-else>
	    	<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div>
		<autorizacion @data="autorizacionData" ref="autorizacion_componente"></autorizacion>
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
          	enviar: '',
          	ajustes: {
          		IMPRESORA_TICKET: '',
          		IMPRESORA_MATRICIAL: ''
          	},
          	autorizacion: {
	            HABILITAR: 0,
	            CODIGO: 0,
	            ID_USUARIO: 0,
	            PERMITIDO: 0,
	            ID_USER_SUPERVISOR: 0
	        }
        }
      }, 
      methods: {
      		autorizar(){
		        this.$refs.autorizacion_componente.mostrarModal();
		    },
		    autorizacionData(data){

		        // ------------------------------------------------------------------------

		        // LLAMAR MODAL
		        
		        if (data.response === true) {

		             
		          this.autorizacion.ID_USUARIO = data.usuario;
		          this.autorizacion.ID_USER_SUPERVISOR = data.id_user_supervisor;
		          this.enviarTransferencia(this.enviar);
		        }

		        // ------------------------------------------------------------------------

		    },
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
      		}, eliminarTransferencia(codigo){

      			// ------------------------------------------------------------------------

      			// INICIAR VARIABLES

      			var tableTransferencia = $('#tablaTransferencias').DataTable();

      			// ------------------------------------------------------------------------

      			// MOSTRAR LA PREGUNTA DE ELIMINAR 

      			Swal.fire({
				  title: 'Estas seguro ?',
				  text: "Eliminar la Transferencia " + codigo + " !",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  confirmButtonColor: '#d33',
				  cancelButtonColor: '#3085d6',
				  confirmButtonText: 'Si, eliminalo!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
				    return Common.eliminarTransferenciaCommon(codigo).then(data => {
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
						      'Se ha eliminado la transferencia y devuelto el stock !',
						      'success'
					)

				  	// ------------------------------------------------------------------------

				  	// RECARGAR TABLA 
				  	
					tableTransferencia.ajax.reload( null, false );

					// ------------------------------------------------------------------------

				  }
				})

				// ------------------------------------------------------------------------

      		}, enviarTransferencia(codigo){
      			
      			// ------------------------------------------------------------------------

      			// INICIAR VARIABLES
      			let me=this;
      			var tableTransferencia = $('#tablaTransferencias').DataTable();
      			var data = {
	              	codigo: codigo,
	              	autorizacion:me.autorizacion
	            }

      			// ------------------------------------------------------------------------

      			// MOSTRAR LA PREGUNTA DE ELIMINAR 

      			Swal.fire({
				  title: 'Estas seguro ?',
				  text: "Enviar la Transferencia " + codigo + " !",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  cancelButtonColor: '#3085d6',
				  confirmButtonText: 'Si, envialo!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
				    return Common.enviarTransferenciaCommon(data).then(data => {
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
						      'Se ha enviado la transferencia correctamente !',
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

	 				var tableTransferencia = $('#tablaTransferencias').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "url": "/transferencias",
                                 "dataType": "json",
                                 "type": "GET",
                                 "contentType": "application/json; charset=utf-8"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "ORIGEN" },
                            { "data": "DESTINO" },
                            { "data": "NRO_CAJA" },
                            { "data": "IVA" },
                            { "data": "TOTAL" },
                            { "data": "ESTATUS" },
                            { "data": "ACCION" }
                        ]      
                    });
                     $('#tablaTransferencias_filter input').unbind();
				    $('#tablaTransferencias_filter input').bind('keyup', function (e) {
				        if (e.keyCode == 13) {
				            tableTransferencia.search(this.value).draw();
				        }
				    });
                    
	 				// ------------------------------------------------------------------------

                	// EDITAR TRANSFERENCIA

                    $('#tablaTransferencias').on('click', 'tbody tr #editarTransferencia', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                    me.editarTransferencia(tableTransferencia.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // ELIMINAR TRANSFERENCIA

                    $('#tablaTransferencias').on('click', 'tbody tr #eliminarTransferencia', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                    me.eliminarTransferencia(tableTransferencia.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // GENERAR FACTURA

                    $('#tablaTransferencias').on('click', 'tbody tr #imprimirTransferencia', function() {

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

                    $('#tablaTransferencias').on('click', 'tbody tr #imprimirReporte', function() {

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

                    $('#tablaTransferencias').on('click', 'tbody tr #enviarTransferencia', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                   	me.enviar = tableTransferencia.row( row ).data().CODIGO;
	                    me.autorizar();
	                    

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    $('#tablaTransferencias').on('click', 'tbody tr #mostrarTransferencia', function() {

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