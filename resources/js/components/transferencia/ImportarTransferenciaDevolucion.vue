<template>
	<div class="container-fluid mt-4">
		<div v-if="$can('transferencia.importardevo') && $can('transferencia')">
	<!-- 	<div class="row" v-if="$can('transferencia.importar')"> -->

			<!-- ------------------------------------------------------------------------------------- -->

			<!-- TITULO  -->
			
			<div class="col-md-12">
				<vs-divider>
					Importar Transferencias Devueltas
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

			<!-- ------------------------------------------------------------------------------------- -->

			<div class="col-md-12">
				<table id="tablaImportarTransDev" class="table table-hover table-striped table-bordered table-sm mb-3" style="width:100%">
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
		<!-- </div> -->

		<!-- <div v-else>
			<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div> -->
		
		<!-- ------------------------------------------------------------------------ -->

		<!-- MODAL MOSTRAR DETALLE TRANSFERENCIA -->

		<modal-detalle-transferencia-dev-imp 
		ref="ModalImportarTransferencia"
		></modal-detalle-transferencia-dev-imp>

		<!-- ------------------------------------------------------------------------ -->

			

		<!-- ------------------------------------------------------------------------ -->

		</div>
		<div v-else>
	    	<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div>
	</div>

	

</template>
<script>
	 export default {
      data(){
        return {
          	codigoTransferencia: '',
          	procesar: false,
          	
        }
      }, 
      methods: {
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
      		},
			mostrarModalTranferencia(codigo, codigo_origen) {

      			// ------------------------------------------------------------------------

      			// LLAMAR EL METODO DEL COMPONENTE HIJO

      			this.$refs.ModalImportarTransferencia.mostrarModal(codigo, codigo_origen);

      			// ------------------------------------------------------------------------
      		}, 
      		rechazarDevTransferencia(codigo){

      			// ------------------------------------------------------------------------

      			// INICIAR VARIABLES

      			var tableTransferencia = $('#tablaImportarTransDev').DataTable();

      			// ------------------------------------------------------------------------

      			// MOSTRAR LA PREGUNTA DE ELIMINAR 

      			Swal.fire({
				  title: 'Estas seguro ?',
				  text: "Rechazar la devolucion de Transferencia " + codigo + " !",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  confirmButtonColor: '#d33',
				  cancelButtonColor: '#3085d6',
				  confirmButtonText: 'Si, rechazalo!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
				    return Common.rechazarDevTransferenciaCommon(codigo).then(data => {
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
						      'Rechazado !',
						      'Se ha rechazado la devolucion de transferencia y ha regresado al origen !',
						      'success'
					)

				  	// ------------------------------------------------------------------------

				  	// RECARGAR TABLA 
				  	
					tableTransferencia.ajax.reload( null, false );

					// ------------------------------------------------------------------------

				  } 
				})

				// ------------------------------------------------------------------------

      		},
      		importarDevTransferencia(codigo){

      			// ------------------------------------------------------------------------

      			// INICIAR VARIABLES

      			var tableTransferencia = $('#tablaImportarTransDev').DataTable();

      			// ------------------------------------------------------------------------

      			// MOSTRAR LA PREGUNTA DE ELIMINAR 

      			Swal.fire({
				  title: 'Estas seguro ?',
				  text: "Importar la Devolucion de Transferencia " + codigo + " !",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  cancelButtonColor: '#3085d6',
				  confirmButtonText: 'Si, importalo!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
				    return Common.importarDevTransferenciaCommon(codigo).then(data => {
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
						      'Importado !',
						      'Se ha importado la transferencia correctamente !',
						      'success'
					)

				  	// ------------------------------------------------------------------------

				  	// RECARGAR TABLA 
				  	
					tableTransferencia.ajax.reload( null, false );

					// ------------------------------------------------------------------------

				  }
				})

				// ------------------------------------------------------------------------

      		}
      },
        mounted() {
        	
        	let me = this;

            $(document).ready( function () {

            		// ------------------------------------------------------------------------

            		// PREPARAR DATATABLE 

	 				var tableImportarDevTransferencia = $('#tablaImportarTransDev').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                        		 "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/transferenciasDevImportar",
                                 "dataType": "json",
                                 "type": "POST"
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

                    $('#tablaImportarTransDev').on('click', 'tbody tr #mostrarTransferencia', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA

	                   	 var row  = $(this).parents('tr')[0];
	                   	 me.mostrarModalTranferencia(tableImportarDevTransferencia.row( row ).data().CODIGO, tableImportarDevTransferencia.row( row ).data().CODIGO_ORIGEN);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // ELIMINAR TRANSFERENCIA

                    $('#tablaImportarTransDev').on('click', 'tbody tr #rechazarDevTransferencia', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                    me.rechazarDevTransferencia(tableImportarDevTransferencia.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // IMPORTAR TRANSFERENCIA

                    $('#tablaImportarTransDev').on('click', 'tbody tr #importarDevTransferencia', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                    me.importarDevTransferencia(tableImportarDevTransferencia.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // GENERAR REPORTE PDF

                    $('#tablaImportarTransDev').on('click', 'tbody tr #imprimirReporte', function() {

	                    // *******************************************************************

	                    // ENVIAR A COMMON FUNCTION PARA GENERAR REPORTE PDF

	                   	me.procesar = true;
	                   	var row  = $(this).parents('tr')[0];
	                   	Common.generarRptPdfTransferenciaCommon(tableImportarDevTransferencia.row( row ).data().CODIGO, tableImportarDevTransferencia.row( row ).data().CODIGO_ORIGEN).then( () => {
	                   		me.procesar = false;
	                   	});
	                   	

	                    // *******************************************************************

	                });
	                    $('#tablaImportarTransDev').on('click', 'tbody tr #devolucion', function() {


	                    // *******************************************************************

	                    // ENVIAR A COMMON FUNCTION PARA GENERAR REPORTE PDF

	                    
	                   	var row  = $(this).parents('tr')[0];
                        me.mostrarModalDevolucion(tableImportarDevTransferencia.row( row ).data().CODIGO, tableImportarDevTransferencia.row( row ).data().CODIGO_ORIGEN);
                      

	                    // *******************************************************************

	                });


                    // ------------------------------------------------------------------------

	 });
        }
    }
</script>