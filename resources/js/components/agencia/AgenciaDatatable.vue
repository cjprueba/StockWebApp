<template>
	<div class="container-fluid">
			<div class="modal fade" id="modalAgencias" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-xl" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title text-primary" id="exampleModalLabel"><small>Agencias </small></h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        <table id="agencias" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
					            <thead>
					                <tr>
					                    <th>Id</th>
					                    <th>Nombre</th>
					                    <th>Fecha</th>
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
        	tableAgencias: ''
        }
      }, 
      methods: {
      		mostrarModal(){

      			// ------------------------------------------------------------------------

      			// LLAMAR AJAX PARA CARGAR DATATABLE 

      			this.obtenerDatosAgencia();

      			// ------------------------------------------------------------------------

      			// LLAMAR MODAL VENTA PRODUCTOS

      			$('#modalAgencias').modal('show');

      			// ------------------------------------------------------------------------
            	
            }, 
            obtenerDatosAgencia(){

            	// ------------------------------------------------------------------------

            	let me = this;

            	// ------------------------------------------------------------------------

            	// PREPARAR DATATABLE 

	 			this.tableAgencias = $('#agencias').DataTable({
	                 	"processing": true,
	                 	"serverSide": true,
	                 	"destroy": true,
	                 	"bAutoWidth": true,
	                 	"select": true,
	                 	"ajax":{
	                             "url": "/agencia/datatable",
	                             "dataType": "json",
	                             "type": "GET",
	                             "contentType": "application/json; charset=utf-8"
	                           },
	                    "columns": [
	                            { "data": "ID" },
	                            { "data": "NOMBRE" },
	                            { "data": "FECALTAS" },
	                        ]    
	                });
                    

      				// ------------------------------------------------------------------------

            }, enviarPadre(data) {
            	
            	// ------------------------------------------------------------------------

            	// EMITIR DATOS 

            	this.$emit('data', data);

            	// ------------------------------------------------------------------------

            }
      },
        mounted() {

        	let me = this;

        	this.obtenerDatosAgencia(0);

        	// ------------------------------------------------------------------------

            	this.tableAgencias.on('click', 'tbody tr', function() {
	       		
	       		me.enviarPadre({
	        			'id': me.tableAgencias.row(this).data().ID,
	        			'nombre': me.tableAgencias.row(this).data().NOMBRE,
	        		})
	    			$('#modalAgencias').modal('hide');
	    	    })

            // ------------------------------------------------------------------------
        }
    }
</script>