<template>
	<div class="container-fluid">
			<div class="modal fade" id="modalTransferenciaProductosDevolucion" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-xl" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title text-primary" id="exampleModalLabel"><small>Detalle de Transferencia para devolucion: {{codigo_transferencia}} </small></h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        <table id="transferenciaProductosDevolucion" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
					            <thead>
					                <tr>
					                    <th>ITEM</th>
					                    <th>Codigo Producto</th>
					                    <th>Descripción</th>
					                    <th>Cantidad</th>
					                    <th>Salida</th>
					                    <th>Precio</th>
					                    <th>Total</th>
					                    <th>Accion</th>

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

      			$('#modalTransferenciaProductosDevolucion').modal('show');

      			// ------------------------------------------------------------------------
            	
            }, 
            obtenerDatosTranferencia(codigo, codigo_origen){

            	// ------------------------------------------------------------------------

            	let me = this;

            	// ------------------------------------------------------------------------

            	// PREPARAR DATATABLE 

	 			var tableProductosTransferencia = $('#transferenciaProductosDevolucion').DataTable({
	                 	"processing": true,
	                 	"serverSide": true,
	                 	"destroy": true,
	                 	"bAutoWidth": true,
	                 	"select": true,
	                 	"ajax":{
	                 			"data": {
	                 				codigoTransferencia: codigo,
	                 				codigo_origen: codigo_origen
	                 			},
	                             "url": "/transferenciasMostrarProductosDevolucion",
	                             "dataType": "json",
	                             "type": "GET",
	                             "contentType": "application/json; charset=utf-8"
	                           },
	                    "columns": [
	                            { "data": "ITEM" },
	                            { "data": "COD_PROD" },
	                            { "data": "DESCRIPCION" },
	                            { "data": "CANTIDAD" },
	                            { "data": "SALIDA" },
	                            { "data": "PRECIO" },
	                            { "data": "TOTAL" },
	                            { "data": "TOTAL" },
	                            { "data": "ACCION" }
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