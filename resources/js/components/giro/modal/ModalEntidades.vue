<template>
	<div class="container-fluid">
			<div class="modal fade" id="modalEntidades" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-xl" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title text-primary" id="exampleModalLabel"><small>Tarjetas</small></h5>
		      </div>
		      <div class="modal-body">
		        <table id="entidades" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
					            <thead>
					                <tr>
					                    <th>Codigo</th>
					                    <th>Descripcion</th>
					                    <!-- <th>Imagen</th> -->
					                </tr>
					            </thead>
					            <tbody>
					            </tbody>
					        </table>
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
        }
      }, 
      methods: {
      		mostrarModal(codigo){

      			// ------------------------------------------------------------------------

      			// LLAMAR MODAL TRANSFERENCIA PRODUCTOS

      			$('#modalEntidades').modal('show');

      			// ------------------------------------------------------------------------
            	
            }, 
            enviarOpcionesPadre(codigo, descripcion){
              this.$emit('codigo', codigo);
              this.$emit('descripcion', descripcion);
            }
      },
        mounted() {

        	// ------------------------------------------------------------------------

            let me = this;

            // ------------------------------------------------------------------------

            // PREPARAR DATATABLE 

	 		var tableEntidades = $('#entidades').DataTable({
	                "processing": true,
	                "serverSide": true,
	                "destroy": true,
	                "bAutoWidth": true,
	                "select": true,
	                "ajax":{
	                    "url": "/giro/datatable/entidades",
	                    "dataType": "json",
	                    "type": "GET",
	                    "contentType": "application/json; charset=utf-8"
	                },
	                "columns": [
	                    { "data": "CODIGO" },
	                    { "data": "DESCRIPCION" }
	                ]    
	        });
                    
	 		// ------------------------------------------------------------------------

	 		// OBTENER DATOS DEUDA 

    		tableEntidades.on('click', 'tbody tr', function() {
    			me.enviarOpcionesPadre(tableEntidades.row(this).data().CODIGO, tableEntidades.row(this).data().DESCRIPCION);
    			$('#modalEntidades').modal('hide');
    		})

    		// ------------------------------------------------------------------------

        }
    }
</script>