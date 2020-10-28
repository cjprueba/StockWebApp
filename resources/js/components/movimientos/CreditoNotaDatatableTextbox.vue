<template>
	<div class="container-fluid">
			<div class="modal fade" id="modalNotaCreditoCliente" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-xl" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title text-primary" id="exampleModalLabel"><small>Notas de Crédito para Cliente </small></h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        <table id="notaCreditoCliente" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
					            <thead>
					                <tr>
					                    <th>Id</th>
					                    <th>Venta</th>
					                    <th>Fecha</th>
					                    <th>Hora</th>
					                    <th>Base 5</th>
					                    <th>Base 10</th>
					                    <th>IVA</th>
					                    <th>Total</th>
					                    <th>Total Crudo</th>
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
        	tableNotaCreditoClientes: ''
        }
      }, 
      methods: {
      		mostrarModal(codigo){

      			// ------------------------------------------------------------------------

      			// LLAMAR AJAX PARA CARGAR DATATABLE 

      			this.obtenerDatosNotaCredito(codigo);

      			// ------------------------------------------------------------------------

      			// LLAMAR MODAL VENTA PRODUCTOS

      			$('#modalNotaCreditoCliente').modal('show');

      			// ------------------------------------------------------------------------
            	
            }, 
            obtenerDatosNotaCredito(codigo){

            	// ------------------------------------------------------------------------

            	let me = this;

            	// ------------------------------------------------------------------------

            	// PREPARAR DATATABLE 

	 			this.tableNotaCreditoClientes = $('#notaCreditoCliente').DataTable({
	                 	"processing": true,
	                 	"serverSide": true,
	                 	"destroy": true,
	                 	"bAutoWidth": true,
	                 	"select": true,
	                 	"ajax":{
	                 			"data": {
	                 				cliente: codigo,
	                 			},
	                             "url": "/nota/credito/obtener/credito/cliente",
	                             "dataType": "json",
	                             "type": "GET",
	                             "contentType": "application/json; charset=utf-8"
	                           },
	                    "columns": [
	                            { "data": "ID" },
	                            { "data": "FK_VENTA" },
	                            { "data": "FECALTAS" },
	                            { "data": "HORA" },
	                            { "data": "BASE5" },
	                            { "data": "BASE10" },
	                            { "data": "IVA" },
	                            { "data": "TOTAL" },
	                            { "data": "TOTAL_CRUDO", "visible": false },
	                        ]    
	                });
                    
	 				// ------------------------------------------------------------------------

	 				// CARGAR CODIGO VENTA

      				this.codigo_venta = codigo;

      				// ------------------------------------------------------------------------

      				// 

      				this.datatablellamar();

      				// ------------------------------------------------------------------------

            }, enviarPadre(data) {
            	
            	// ------------------------------------------------------------------------

            	// EMITIR DATOS 

            	this.$emit('data', data);

            	// ------------------------------------------------------------------------

            }, datatablellamar() {

            	let me = this;

            	

            }
      },
        mounted() {

        	let me = this;

        	this.obtenerDatosNotaCredito(0);

        	// ------------------------------------------------------------------------

            	this.tableNotaCreditoClientes.on('click', 'tbody tr', function() {
	       		
	       		me.enviarPadre({
	        			'total': me.tableNotaCreditoClientes.row(this).data().TOTAL_CRUDO,
	        			'id': me.tableNotaCreditoClientes.row(this).data().ID,
	        			'base5': me.tableNotaCreditoClientes.row(this).data().BASE5,
	        			'base10': me.tableNotaCreditoClientes.row(this).data().BASE10,
	        			'iva': me.tableNotaCreditoClientes.row(this).data().IVA,
	        		})
	    			$('#modalNotaCreditoCliente').modal('hide');
	    	    })

            // ------------------------------------------------------------------------
        }
    }
</script>