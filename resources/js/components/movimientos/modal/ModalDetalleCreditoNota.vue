<template>
	<div class="container-fluid">
			<div class="modal fade" id="modalDetalleCreditoNota" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-xl" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title text-primary" id="exampleModalLabel"><small>Detalle de Nota Credito: {{codigo}} </small></h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        <table id="detalleCreditoNota" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
					            <thead>
					                <tr>
					                    <th>Item</th>
					                    <th>Codigo Producto</th>
					                    <th>Descripción</th>
					                    <th>Cantidad</th>
					                    <th>Precio</th>
					                    <th>Iva</th>
					                    <th>Total</th>
					                </tr>
					            </thead>
					            <tbody>
					            </tbody>
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
      			
      			this.obtenerDatosDetalleCreditoNota(codigo);

      			// ------------------------------------------------------------------------

      			// LLAMAR MODAL TRANSFERENCIA PRODUCTOS

      			$('#modalDetalleCreditoNota').modal('show');

      			// ------------------------------------------------------------------------
            	
            }, 
            obtenerDatosDetalleCreditoNota(codigo){

            	// ------------------------------------------------------------------------

            	let me = this;

            	// ------------------------------------------------------------------------

            	// PREPARAR DATATABLE 

	 			var tableDetalleProductosCreditoNota = $('#detalleCreditoNota').DataTable({
	                 	"processing": true,
	                 	"serverSide": true,
	                 	"destroy": true,
	                 	"bAutoWidth": true,
	                 	"select": true,
	                 	"ajax":{
	                 			"data": {
	                 				codigo: codigo
	                 			},
	                             "url": "/credito/nota/producto/detalle",
	                             "dataType": "json",
	                             "type": "GET",
	                             "contentType": "application/json; charset=utf-8"
	                           },
	                    "columns": [
	                            { "data": "ITEM" },
	                            { "data": "COD_PROD" },
	                            { "data": "DESCRIPCION" },
	                            { "data": "CANTIDAD" },
	                            { "data": "PRECIO" },
	                            { "data": "IVA" },
	                            { "data": "TOTAL" }
	                        ]    
	                });
                    
	 				// ------------------------------------------------------------------------

	 				// CARGAR CODIGO TRANSFERENCIA

      				this.codigo = codigo;

      				// ------------------------------------------------------------------------
            }
      },
        mounted() {

        }
    }
</script>