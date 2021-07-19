<template>
	<div class="container-fluid mt-4">
		<div class="row" v-if="$can('compra.mostrar') && $can('compra')">

			<!-- ------------------------------------------------------------------------------------- -->

			<!-- TITULO  -->
			
			<div class="col-md-12">
				<vs-divider>
					Mostrar Compras
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
				<table id="tablaCompras" class="table table-striped table-hover table-bordered table-sm mb-3" style="width:100%">
		            <thead>
		                <tr>
		                    <th>Código</th>
		                    <th>Proveedor</th>
		                    <th>Plan</th>
		                    <th>Nro. Factura</th>
		                    <th>Fecha Factura</th>
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

		<div v-else>
			<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div>

		<!-- ------------------------------------------------------------------------ -->

		<!-- MODAL MOSTRAR DETALLE COMPRA -->

		<modal-detalle-compra 
		ref="ModalMostrarDetalleCompra"
		></modal-detalle-compra>

		<!-- ------------------------------------------------------------------------ -->

	</div>
</template>
<script>


	 export default {
	  props: ['id_sucursal'],	
      data(){
        return {
          	codigoCOMPRA: '',
          	procesar: false
        }
      }, 
      methods: {
      		editarCompra(codigo){

      			// ------------------------------------------------------------------------

      			// MANDAR CODIGO COMPRA

      			 this.$router.push('/cr2/'+ codigo + '');

      			// ------------------------------------------------------------------------
      		}, mostrarModalTranferencia(codigo) {

      			// ------------------------------------------------------------------------

      			// LLAMAR EL METODO DEL COMPONENTE HIJO

      			this.$refs.ModalMostrarDetalleCompra.mostrarModal(codigo);

      			// ------------------------------------------------------------------------

      		}, eliminarCompra(codigo){

      			// ------------------------------------------------------------------------

      			// INICIAR VARIABLES

      			var tableCompras = $('#tablaCompras').DataTable();

      			// ------------------------------------------------------------------------

      			// MOSTRAR LA PREGUNTA DE ELIMINAR 

      			Swal.fire({
				  title: '¿Estás seguro?',
				  text: "¡Eliminar la Compra " + codigo + "!",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  confirmButtonColor: '#d33',
				  cancelButtonColor: '#3085d6',
				  confirmButtonText: '¡Sí, eliminalo!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
				    return Common.eliminarCompraCommon(codigo).then(data => {
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
						      '¡Eliminado!',
						      '¡Se ha eliminado la compra y devuelto el stock!',
						      'success'
					)

				  	// ------------------------------------------------------------------------

				  	// RECARGAR TABLA 
				  	
					tableCompras.ajax.reload( null, false );

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

	 				var tableCompra = $('#tablaCompras').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "url": "/compra/datatable",
                                 "dataType": "json",
                                 "type": "GET",
                                 "contentType": "application/json; charset=utf-8"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "PROVEEDOR" },
                            { "data": "TIPO" },
                            { "data": "NRO_FACTURA" },
                            { "data": "FEC_FACTURA" },
                            { "data": "TOTAL" },
                            { "data": "ACCION" }
                        ]      
                    });
                    
	 				// ------------------------------------------------------------------------

	 				// AJUSTAR COLUMNAS DE ACUERDO AL DATO QUE CONTIENEN

            		tableCompra.columns.adjust().draw();

            		// ------------------------------------------------------------------------

                	// EDITAR COMPRA

                    $('#tablaCompras').on('click', 'tbody tr #editar', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO COMPRA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                    me.editarCompra(tableCompra.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // ELIMINAR COMPRA

                    $('#tablaCompras').on('click', 'tbody tr #eliminar', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO COMPRA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                    me.eliminarCompra(tableCompra.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // GENERAR REPORTE PDF

                    $('#tablaCompras').on('click', 'tbody tr #reporte', function() {

	                    // *******************************************************************

	                    // ENVIAR A COMMON FUNCTION PARA GENERAR REPORTE PDF

	                   	me.procesar = true;
	                   	var row  = $(this).parents('tr')[0];
	                   	Common.generarRptPdfCompraCommon(tableCompra.row( row ).data().CODIGO).then( () => {
	                   		me.procesar = false;
	                   	});
	                   	

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    $('#tablaCompras').on('click', 'tbody tr #mostrar', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO COMPRA

	                   	 var row  = $(this).parents('tr')[0];
	                   	 me.mostrarModalTranferencia(tableCompra.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });
	                $('#tablaCompras').on('click', 'tbody tr #qr_caja', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO COMPRA

	                   	 var row  = $(this).parents('tr')[0];
	                   	  	me.procesar = true;
						Common.generarRptPdfCajaCompraQrCommon(tableCompra.row( row ).data().NRO_FACTURA).then( () => {
	                   		me.procesar = false;
	                   	});
	                   	

	                    // *******************************************************************

	                });

	                // ------------------------------------------------------------------------
	 });	
        }
    }
</script>