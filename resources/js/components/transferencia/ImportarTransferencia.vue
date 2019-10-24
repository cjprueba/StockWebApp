<template>
	<div class="container-fluid mt-4">
		<div class="row">

			<div class="col-md-12">
				<nav aria-label="breadcrumb">
				  <ol class="breadcrumb">
				    <li class="breadcrumb-item active" aria-current="page">Importar Transferencias</li>
				  </ol>
				</nav>
			</div>

			<div class="col-md-12">
				<table id="tablaImportarTransferencia" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
		            <thead>
		                <tr>
		                    <th>Codigo</th>
		                    <th>Codigo Origen</th>
		                    <th>Origen</th>
		                    <th>Responsable Envio</th>
		                    <th>Fecha</th>
		                    <th>Hora</th>
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
		</div>

		<!-- ------------------------------------------------------------------------ -->

		<!-- MODAL MOSTRAR DETALLE TRANSFERENCIA -->

		<modal-detalle-transferencia 
		ref="ModalImportarTransferencia"
		></modal-detalle-transferencia>

		<!-- ------------------------------------------------------------------------ -->
	</div>
</template>
<script>
	 export default {
      data(){
        return {
          	codigoTransferencia: ''
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
      		rechazarTransferencia(codigo, codigo_origen){

      			// ------------------------------------------------------------------------

      			// INICIAR VARIABLES

      			var tableTransferencia = $('#tablaImportarTransferencia').DataTable();

      			// ------------------------------------------------------------------------

      			// MOSTRAR LA PREGUNTA DE ELIMINAR 

      			Swal.fire({
				  title: 'Estas seguro ?',
				  text: "Rechazar la Transferencia " + codigo + " !",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  confirmButtonColor: '#d33',
				  cancelButtonColor: '#3085d6',
				  confirmButtonText: 'Si, rechazalo!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
				    return Common.rechazarTransferenciaCommon(codigo, codigo_origen).then(data => {
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
						      'Se ha rechazado la transferencia y devuelto al origen !',
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
      		importarTransferencia(codigo, codigo_origen){

      			// ------------------------------------------------------------------------

      			// INICIAR VARIABLES

      			var tableTransferencia = $('#tablaImportarTransferencia').DataTable();

      			// ------------------------------------------------------------------------

      			// MOSTRAR LA PREGUNTA DE ELIMINAR 

      			Swal.fire({
				  title: 'Estas seguro ?',
				  text: "Importar la Transferencia " + codigo + " !",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  cancelButtonColor: '#3085d6',
				  confirmButtonText: 'Si, importalo!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
				    return Common.importarTransferenciaCommon(codigo, codigo_origen).then(data => {
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

	 				var tableImportarTransferencia = $('#tablaImportarTransferencia').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "url": "/transferenciaMostrarImportar",
                                 "dataType": "json",
                                 "type": "GET",
                                 "contentType": "application/json; charset=utf-8"
                               },       
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "CODIGO_ORIGEN", "visible": false },
                            { "data": "ORIGEN" },
                            { "data": "RESPONSABLE" },
                            { "data": "FECHA" },
                            { "data": "HORA" },
                            { "data": "TOTAL" },
                            { "data": "ESTATUS" },
                            { "data": "ACCION" }
                        ]      
                    });
                    
	 				// ------------------------------------------------------------------------

                	// EDITAR TRANSFERENCIA

                    $('#tablaImportarTransferencia').on('click', 'tbody tr #mostrarTransferencia', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA

	                   	 var row  = $(this).parents('tr')[0];
	                   	 me.mostrarModalTranferencia(tableImportarTransferencia.row( row ).data().CODIGO, tableImportarTransferencia.row( row ).data().CODIGO_ORIGEN);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // ELIMINAR TRANSFERENCIA

                    $('#tablaImportarTransferencia').on('click', 'tbody tr #rechazarTransferencia', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                    me.rechazarTransferencia(tableImportarTransferencia.row( row ).data().CODIGO, tableImportarTransferencia.row( row ).data().CODIGO_ORIGEN);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // IMPORTAR TRANSFERENCIA

                    $('#tablaImportarTransferencia').on('click', 'tbody tr #importarTransferencia', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                    me.importarTransferencia(tableImportarTransferencia.row( row ).data().CODIGO, tableImportarTransferencia.row( row ).data().CODIGO_ORIGEN);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

	 });
        }
    }
</script>