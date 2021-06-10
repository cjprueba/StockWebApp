<template>
	<div class="container-fluid">
			<div class="modal fade" id="modalDevolucionProductos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-xl" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title text-primary" id="exampleModalLabel"><small>Detalle de Devolución: {{codigo}} </small></h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        <table id="devolucionProductos" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
					            <thead>
					                <tr>
					                    <th>Item</th>
					                    <th>Codigo Producto</th>
					                    <th>Descripción</th>
					                    <th>Cantidad</th>
					                    <th>Costo</th>
					                    <th>Costo Total</th>
					                    <th>Lote</th>
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
      			
      			this.obtenerDatosDelucionProveedor(codigo);

      			// ------------------------------------------------------------------------

      			// LLAMAR MODAL TRANSFERENCIA PRODUCTOS

      			$('#modalDevolucionProductos').modal('show');

      			// ------------------------------------------------------------------------
            	
            }, 
            obtenerDatosDelucionProveedor(codigo){

            	// ------------------------------------------------------------------------

            	let me = this;

            	// ------------------------------------------------------------------------

            	// PREPARAR DATATABLE 

	 			var tableDevolucionProductos = $('#devolucionProductos').DataTable({
	                 	"processing": true,
	                 	"serverSide": true,
	                 	"destroy": true,
	                 	"bAutoWidth": true,
	                 	"select": true,
                        "dom": "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
						"<'row'<'col-sm-12'tr>>" +
						"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
				        "buttons": [
				            { extend: 'copy', text: '<i class="fa fa-copy"></i>', titleAttr: 'Copiar', className: 'btn btn-secondary' },
				        	{ extend: 'excelHtml5', text: '<i class="fa fa-file"></i>', titleAttr: 'Excel', className: 'btn btn-success' },
				            { extend: 'pdfHtml5', text: '<i class="fa fa-file"></i>', titleAttr: 'Pdf', className: 'btn btn-danger' }, 
				            { extend: 'print', text: '<i class="fa fa-print"></i>', titleAttr: 'Imprimir', className: 'btn btn-secondary', title: 'Devoluciones', messageTop: 'Productos devueltos a proveedores' }
				        ],
	                 	"ajax":{
	                 			"data": {
	                 				codigo: codigo
	                 			},
	                             "url": "/proveedor/devolucion/detalle",
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
	                            { "data": "LOTE" }
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