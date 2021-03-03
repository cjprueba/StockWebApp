<template>
	<div class="container-fluid mt-4">
		<!-- <div class="row" v-if="$can('proveedor.mostrar.devolucion')"> -->

			<!-- ------------------------------------------------------------------------------------- -->

			<!-- TITULO  -->
            
            <div class="col-md-12 mt-3">
                <div class="section-title">
                    <h4>LISTADO DE PRÉSTAMOS</h4>
                </div>
            </div>

			<!-- ------------------------------------------------------------------------ -->

			<div class="col-md-12">
				<table id="tablaPrestamoProductos" class="table table-striped table-hover table-bordered table-sm mb-3" style="width:100%">
		            <thead>
		                <tr>
		                    <th>Código</th>
		                    <th>Observación</th>
		                    <th>Cliente</th>
		                    <th>Garantía</th>
		                    <th>Estado</th>
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
		
		<modal-detalle-prestamo-productos ref="componente_modal_detalle_prestamo"></modal-detalle-prestamo-productos>

		<!-- ------------------------------------------------------------------------ -->

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
      			
      			this.$refs.componente_modal_detalle_prestamo.mostrarModal(codigo);

      			// ------------------------------------------------------------------------

      		},  
      		devolverProducto(id){

      			// ------------------------------------------------------------------------

      			var tablePrestamoProductos = $('#tablaPrestamoProductos').DataTable();

      			Swal.fire({
					title: '¿Estas seguro?',
					text: "¡Devolver los productos!",
					type: 'warning',
					showLoaderOnConfirm: true,
					showCancelButton: true,
					confirmButtonColor: '#d33',
					cancelButtonColor: '#3085d6',
					confirmButtonText: '¡Sí, devolver!',
					cancelButtonText: 'Cancelar',
					preConfirm: () => {
					    return Common.devolverPrestamoCommon(id).then(data => {
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
							'¡Devuelto!',
							 result.value.statusText,
							'success'
						)

						tablePrestamoProductos.ajax.reload( null, false );

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

	 				var tablePrestamoProductos = $('#tablaPrestamoProductos').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "url": "/prestamo/mostrar",
                                 "dataType": "json",
                                 "type": "GET",
                                 "contentType": "application/json; charset=utf-8"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "OBSERVACION" },
                            { "data": "CLIENTE" },
                            { "data": "GARANTIA" },
                            { "data": "ESTADO" },
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

            		tablePrestamoProductos.columns.adjust().draw();

                    // ------------------------------------------------------------------------

                    // DEVOLVER SALIDA DE PRODUCTO

                    tablePrestamoProductos.on('click', 'tbody tr #devolverProducto', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                    me.devolverProducto(tablePrestamoProductos.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    tablePrestamoProductos.on('click', 'tbody tr #mostrar', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO

	                   	 var row  = $(this).parents('tr')[0];
	                   	 me.mostrarModalDetalle(tablePrestamoProductos.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

	                // ------------------------------------------------------------------------
	 });	
        }
    }
</script>