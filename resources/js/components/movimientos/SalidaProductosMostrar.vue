<template>
	<div v-if="$can('movimientos.mostrarsalidadeproducto') && $can('movimientos')" class="container-fluid mt-4">
		<!-- <div class="row" v-if="$can('proveedor.mostrar.devolucion')"> -->

			<!-- ------------------------------------------------------------------------------------- -->

			<!-- TITULO  -->
            
            <div class="col-md-12 mt-3">
                <div class="section-title">
                    <h4>Mostrar salida de productos</h4>
                    <p>Lista de salida de productos.</p>
                </div>
            </div>

			<!-- ------------------------------------------------------------------------ -->

			<div class="col-md-12">
				<table id="tablaSalidaProductos" class="table table-striped table-hover table-bordered table-sm mb-3" style="width:100%">
		            <thead>
		                <tr>
		                    <th>Codigo</th>
		                    <th>Tipo</th>
		                    <th>Observacion</th>
		                    <th>Total</th>
		                    <th>Creación</th>
		                    <th>Acción</th>
		                </tr>
		            </thead>
		            <tbody>
		                <td></td>
		            </tbody>
		        </table> 


			</div>	
		<!-- </div> -->

		<!-- ------------------------------------------------------------------------ -->

		<!-- <div v-else>
			<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div> -->
		
		<!-- ------------------------------------------------------------------------ -->
		
		<modal-detalle-salida-productos ref="componente_modal_detalle_salida"></modal-detalle-salida-productos>

		<!-- ------------------------------------------------------------------------ -->

	</div>
	<div v-else>
		<cuatrocientos-cuatro></cuatrocientos-cuatro>
	</div>	
</template>
<script>


	 export default {
      data(){
        return {
        	procesar: false
        }
      }, 
      methods: {
      		mostrarModalDetalle(codigo) {

      			// ------------------------------------------------------------------------

      			// LLAMAR EL METODO DEL COMPONENTE HIJO
      			
      			this.$refs.componente_modal_detalle_salida.mostrarModal(codigo);

      			// ------------------------------------------------------------------------

      		}, eliminarCompra(codigo){

      			// ------------------------------------------------------------------------

      			// INICIAR VARIABLES

      			var tableCompras = $('#tablaCompras').DataTable();

      			// ------------------------------------------------------------------------

      			// MOSTRAR LA PREGUNTA DE ELIMINAR 

      			Swal.fire({
				  title: 'Estas seguro ?',
				  text: "Eliminar la Compra " + codigo + " !",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  confirmButtonColor: '#d33',
				  cancelButtonColor: '#3085d6',
				  confirmButtonText: 'Si, eliminalo!',
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
						      'Eliminado!',
						      'Se ha eliminado la transferencia y devuelto el stock !',
						      'success'
					)

				  	// ------------------------------------------------------------------------

				  	// RECARGAR TABLA 
				  	
					tableCompras.ajax.reload( null, false );

					// ------------------------------------------------------------------------

				  }
				})

				// ------------------------------------------------------------------------

      		}, 
      		generarReporte(id){

      			// ------------------------------------------------------------------------


	            Common.generarRptPdfSalidaCommon(id).then( () => {
	                me.procesar = false;
	            });

      			// ------------------------------------------------------------------------

      		}, 
      		devolverSalida(id){

      			// ------------------------------------------------------------------------

      			var tableSalidaProductos = $('#tablaSalidaProductos').DataTable();

      			Swal.fire({
					title: 'Estas seguro ?',
					text: "Devolver los productos !",
					type: 'warning',
					showLoaderOnConfirm: true,
					showCancelButton: true,
					confirmButtonColor: '#d33',
					cancelButtonColor: '#3085d6',
					confirmButtonText: 'Si, devolver !',
					cancelButtonText: 'Cancelar',
					preConfirm: () => {
					    return Common.devolverSalidaCommon(id).then(data => {
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
							'Devuelto !',
							 result.value.statusText,
							'success'
						)

						tableSalidaProductos.ajax.reload( null, false );

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

	 				var tableSalidaProductos = $('#tablaSalidaProductos').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "url": "/salida/mostrar",
                                 "dataType": "json",
                                 "type": "GET",
                                 "contentType": "application/json; charset=utf-8"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "TIPO" },
                            { "data": "OBSERVACION" },
                            { "data": "TOTAL" },
                            { "data": "CREACION" },
                            { "data": "ACCION" }
                        ], "drawCallback": function( settings ) {
					        //tableProveedor.columns.adjust().draw();
					    },
		                "createdRow": function( row, data, dataIndex){
		                    $(row).addClass(data['ESTATUS']);
		                },      
                    });
                    
	 				// ------------------------------------------------------------------------

	 				// AJUSTAR COLUMNAS DE ACUERDO AL DATO QUE CONTIENEN

            		tableSalidaProductos.columns.adjust().draw();

            		// ------------------------------------------------------------------------

                	// EDITAR TRANSFERENCIA

                    tableSalidaProductos.on('click', 'tbody tr #editar', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                    me.editarCompra(tableSalidaProductos.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // DEVOLVER SALIDA DE PRODUCTO

                    tableSalidaProductos.on('click', 'tbody tr #devolverProducto', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                    me.devolverSalida(tableSalidaProductos.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // GENERAR REPORTE PDF

                    tableSalidaProductos.on('click', 'tbody tr #imprimirReporte', function() {

	                    // *******************************************************************

	                    // ENVIAR A COMMON FUNCTION PARA GENERAR REPORTE PDF

	                   	var row  = $(this).parents('tr')[0];
	                   	me.generarReporte(tableSalidaProductos.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    tableSalidaProductos.on('click', 'tbody tr #mostrar', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA

	                   	 var row  = $(this).parents('tr')[0];
	                   	 me.mostrarModalDetalle(tableSalidaProductos.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

	                // ------------------------------------------------------------------------
	 });	
        }
    }
</script>