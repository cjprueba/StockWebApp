<template>
	<div class="container-fluid">
			<div class="modal fade" id="modalCompraProductos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-xl" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title text-primary" id="exampleModalLabel"><small>Detalle de Compra: {{codigo_compra}} </small></h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        <table id="CompraProductos" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
					            <thead>
					                <tr>
					                    <th>ITEM</th>
					                    <th>Codigo Producto</th>
					                    <th>Descripción</th>
					                    <th>Cantidad</th>
					                    <th>Costo</th>
					                    <th>Costo Total</th>
					                    <th>Lote</th>
					                    <th>Precio</th>
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
          codigo_compra: ''
        }
      }, 
      methods: {
      		mostrarModal(codigo){

      			// ------------------------------------------------------------------------

      			// LLAMAR AJAX PARA CARGAR DATATABLE 

      			this.obtenerDatosCompra(codigo);

      			// ------------------------------------------------------------------------

      			// LLAMAR MODAL TRANSFERENCIA PRODUCTOS

      			$('#modalCompraProductos').modal('show');

      			// ------------------------------------------------------------------------
            	
            }, 
            obtenerDatosCompra(codigo){

            	// ------------------------------------------------------------------------

            	let me = this;

            	// ------------------------------------------------------------------------

            	// PREPARAR DATATABLE 

	 			var tableProductosCompra = $('#CompraProductos').DataTable({
	                 	"processing": true,
	                 	"serverSide": true,
	                 	"destroy": true,
	                 	"bAutoWidth": true,
	                 	"select": true,
	                 	"ajax":{
	                 			"data": {
	                 				codigoCompra: codigo
	                 			},
	                             "url": "/compra/mostrar_productos",
	                             "dataType": "json",
	                             "type": "GET",
	                             "contentType": "application/json; charset=utf-8"
	                           },
	                    "columns": [
	                            { "data": "ITEM" },
	                            { "data": "COD_PROD" },
	                            { "data": "DESCRIPCION" },
	                            { "data": "CANTIDAD" },
	                            { "data": "COSTO" },
	                            { "data": "COSTO_TOTAL" },
	                            { "data": "LOTE" },
	                            { "data": "PRECIO" }
	                        ]    
	                });
                    
	 				// ------------------------------------------------------------------------

	 				// CARGAR CODIGO TRANSFERENCIA

      				this.codigo_compra = codigo;

      				// ------------------------------------------------------------------------
            }
      },
        mounted() {

        }
    }
</script>