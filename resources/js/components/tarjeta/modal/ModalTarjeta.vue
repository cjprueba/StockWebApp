<template>
	<div class="container-fluid">
			<div class="modal fade" id="modalTarjetas" tabindex="-1"  data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-xl" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title text-primary" id="exampleModalLabel"><small>Tarjetas</small></h5>
		      </div>
		      <div class="modal-body">
		        <table id="tarjetas" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
					            <thead>
					                <tr>
					                    <th>Codigo</th>
					                    <th>Descripcion</th>
					                    <th>Emisor</th>
					                    <th>Entidad</th>
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

      			$('#modalTarjetas').modal('show');

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

	 		var tableTarjetas = $('#tarjetas').DataTable({
	                "processing": true,
	                "serverSide": true,
	                "destroy": true,
	                "bAutoWidth": true,
	                "select": true,
	                "ajax":{
	                    "url": "/tarjeta/datatable",
	                    "dataType": "json",
	                    "type": "GET",
	                    "contentType": "application/json; charset=utf-8"
	                },
	                "columns": [
	                    { "data": "CODIGO" },
	                    { "data": "DESCRIPCION" },
	                    { "data": "EMISOR" },
	                    { "data": "ENTIDAD_PAG" }
	                ]    
	        });
                    
	 		// ------------------------------------------------------------------------

	 		// OBTENER DATOS DEUDA 

    		tableTarjetas.on('click', 'tbody tr', function() {
    			me.enviarOpcionesPadre(tableTarjetas.row(this).data().CODIGO, tableTarjetas.row(this).data().DESCRIPCION);
    			$('#modalTarjetas').modal('hide');
    		})

    		// ------------------------------------------------------------------------

        }
    }
</script>