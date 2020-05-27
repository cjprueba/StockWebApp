<template>
	<div class="container-fluid">
		  <div class="modal fade" id="modalBancos" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-xl" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title text-primary" id="exampleModalLabel"><small>Bancos</small></h5>
		      </div>
		      <div class="modal-body">
		        <table id="bancosModal" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
					            <thead>
					                <tr>
					                    <th>Codigo</th>
					                    <th>Descripcion</th>
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
          codigo_compra: ''
        }
      }, 
      methods: {
      		mostrarModal(codigo){

      			// ------------------------------------------------------------------------

      			// LLAMAR MODAL TRANSFERENCIA PRODUCTOS

      			$('#modalBancos').modal('show');

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

	 		var tableBancos = $('#bancosModal').DataTable({
	                "processing": true,
	                "serverSide": true,
	                "destroy": true,
	                "bAutoWidth": true,
	                "select": true,
	                "ajax":{
	                    "url": "/banco/datatable",
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

    		$('#bancosModal').on('click', 'tbody tr', function() {
    			me.enviarOpcionesPadre(tableBancos.row(this).data().CODIGO, tableBancos.row(this).data().DESCRIPCION);
    			$('#modalBancos').modal('hide');
    		})

    		// ------------------------------------------------------------------------

        }
    }
</script>