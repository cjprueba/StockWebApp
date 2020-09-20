<template>
	<div class="container-fluid">
			<div class="modal fade" id="modalTransferenciaProductos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-xl" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title text-primary" id="exampleModalLabel"><small>Detalle de Transferencia a devolver: {{codigo_transferencia}} </small></h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        <table id="transferenciaProductos" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
					            <thead>
					                <tr>
					                 
					                    <th>Codigo Producto</th>
					                    <th>Lote</th>
					                    <th>Descripción</th>
					                    <th>Cantidad</th>
					                    <th>Costo Unitario</th>
					                    <th>Costo Total</th>
					                </tr>
					            </thead>
					            <tbody>
					                <td></td>
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
          codigo_transferencia: ''
        }
      }, 
      methods: {
      		mostrarModal(codigo, codigo_origen){

      			// ------------------------------------------------------------------------

      			// LLAMAR AJAX PARA CARGAR DATATABLE 

      			this.obtenerDatosTranferencia(codigo, codigo_origen);

      			// ------------------------------------------------------------------------

      			// LLAMAR MODAL TRANSFERENCIA PRODUCTOS

      			$('#modalTransferenciaProductos').modal('show');

      			// ------------------------------------------------------------------------
            	
            }, 
            obtenerDatosTranferencia(codigo, codigo_origen){

            	// ------------------------------------------------------------------------

            	let me = this;

            	// ------------------------------------------------------------------------

            	// PREPARAR DATATABLE 

	 			var tableProductosTransferencia = $('#transferenciaProductos').DataTable({
	                 	"processing": true,
	                 	"serverSide": true,
	                 	"destroy": true,
	                 	"bAutoWidth": true,
	                 	"select": true,
	                 	"ajax":{
	                 			"data": {
	                 				codigoDevTransferencia: codigo,
	                 				codigo_origen: codigo_origen
	                 			},
	                             "url": "/transferenciasMostrarProductosDevImp",
	                             "dataType": "json",
	                             "type": "GET",
	                             "contentType": "application/json; charset=utf-8"
	                           },
	                    "columns": [
	                            { "data": "COD_PROD" },
	                            { "data": "LOTE" },
	                            { "data": "DESCRIPCION" },
	                            { "data": "CANTIDAD" },
	                            { "data": "COSTO_UNIT" },
	                            { "data": "COSTO_TOTAL" }
	                        ]    
	                });
                    
	 				// ------------------------------------------------------------------------

	 				// CARGAR CODIGO TRANSFERENCIA

      				this.codigo_transferencia = codigo;

      				// ------------------------------------------------------------------------
            }
      },
        mounted() {

           		 $(document).ready( function () {

           		 

           		 });

           		 
        }
    }
</script>