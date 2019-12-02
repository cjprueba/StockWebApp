<template>
	<div class="container-fluid mt-4">
		<div class="row">

			<div class="col-md-12">
				<nav aria-label="breadcrumb">
				  <ol class="breadcrumb">
				    <li class="breadcrumb-item active" aria-current="page">Mostrar Transferencias</li>
				  </ol>
				</nav>
			</div>

			<div class="col-md-12">
				<table id="tablaTransferencias" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
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
	import App from './MostrarTransferencias.vue'


	 export default {
	  props: ['id_sucursal'],	
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
				    	if (!data === true) {
				          throw new Error('error');
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

      			var tableTransferencia = $('#tablaTransferencias').DataTable();

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
				    return Common.enviarTransferenciaCommon(codigo).then(data => {
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

      		}
      },
        mounted() {
        	
        	let me = this;

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

                    // ELIMINAR TRANSFERENCIA

                    $('#tablaTransferencias').on('click', 'tbody tr #imprimirTransferencia', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	                   	Common.generarPdfTransferenciaCommon();
	                   	
	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // ENVIAR TRANSFERENCIA

                    $('#tablaTransferencias').on('click', 'tbody tr #enviarTransferencia', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                    me.enviarTransferencia(tableTransferencia.row( row ).data().CODIGO);

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