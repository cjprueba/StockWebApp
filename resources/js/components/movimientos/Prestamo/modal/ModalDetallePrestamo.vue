<template>
	<div class="container-fluid">
		<div class="modal fade" id="modalDetallePrestamoProductos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-xl" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title text-primary" id="exampleModalLabel"><small>Detalle de Préstamo: {{codigo}} </small></h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        <table id="detallePrestamoProductos" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
					<thead>
						<tr>
					        <th>Item</th>
					        <th>Código Producto</th>
					        <th>Descripción</th>
					        <th>Cantidad</th>
					    </tr>
					</thead>
				</table>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
		      </div>
		    </div>
		  </div>
		</div>
	</div>
</template>

<script>
	export default {
		props: [''],
	    data(){
	        return {
	          open: false,
	          codigo: ''
	        }
	    }, 
      	methods: {

      		mostrarModal(codigo){

      			// ------------------------------------------------------------------------

      			// LLAMAR AJAX PARA CARGAR DATATABLE 
      			
      			this.obtenerDatosDetallePrestamo(codigo);

      			// ------------------------------------------------------------------------

      			// LLAMAR MODAL PRODUCTOS

      			$('#modalDetallePrestamoProductos').modal('show');

      			// ------------------------------------------------------------------------
            	
            }, 

            obtenerDatosDetallePrestamo(codigo){

            	// ------------------------------------------------------------------------

            	let me = this;

            	// ------------------------------------------------------------------------

            	// PREPARAR DATATABLE 

	 			var tableDevolucionProductos = $('#detallePrestamoProductos').DataTable({
	                 	"processing": true,
	                 	"serverSide": true,
	                 	"destroy": true,
	                 	"bAutoWidth": true,
	                 	"select": true,
	                 	"ajax":{
	                 			"data": {
	                 				codigo: codigo
	                 			},
	                             "url": "/prestamo/producto/detalle",
	                             "dataType": "json",
	                             "type": "GET",
	                             "contentType": "application/json; charset=utf-8"
	                           },
	                    "columns": [
	                            { "data": "ITEM" },
	                            { "data": "COD_PROD" },
	                            { "data": "DESCRIPCION" },
	                            { "data": "CANTIDAD" }
	                        ]    
	                });
                    
	 			// ------------------------------------------------------------------------

	 			// CARGAR CODIGO

      			this.codigo = codigo;

      			// ------------------------------------------------------------------------
            }
      	},
        mounted() {

        }
    }
</script>