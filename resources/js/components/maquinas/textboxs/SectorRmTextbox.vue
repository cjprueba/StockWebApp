<template>
	<div>
		<div class="input-group">
			
      <div class="input-group-prepend">
				<button  type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".sector-rm-modal"><font-awesome-icon icon="search"/></button>
			</div>
      
      <input ref="componente_textbox_sector_rm" :value="id" id="id_sector" class="custom-select custom-select-sm" type="text" @input="$emit('input', $event.target.value)">
		</div>

	  <!-- MODAL SECTORES -->

    <div class="modal fade sector-rm-modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalCenterTitle">Sectores: </small></h5>
	          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	           <span aria-hidden="true">&times;</span>
	          </button>
          </div>
          <div class="modal-body">
            <table id="tablaModalSectoresRm" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>DESCRIPCIÓN</th>
                  <th>DESC_CORTA</th>
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
    props: ['id'],
    data(){
      return {
      }
    }, 

    methods: {

			enviarCodigoPadre(id, descripcion, desc_corta){

				// ------------------------------------------------------------------------

				// ENVIAR DATOS
                  
				this.$emit('id', id);
        this.$emit('descripcion', descripcion);
        this.$emit('desc_corta', desc_corta);

				// ------------------------------------------------------------------------

			},

      recargar(){
            var table = $('#tablaModalSectoresRm').DataTable();
            table.ajax.reload( null, false );
        },
    },
    mounted() {
      
      // ------------------------------------------------------------------------

      // INICIAR VARIABLES 

      let me = this;

      // ------------------------------------------------------------------------
                
      var tableSectoresRm = $('#tablaModalSectoresRm').DataTable({
                "processing": true,
                "serverSide": true,
                "destroy":true,
                "bAutoWidth": true,
                "select": true,
                "ajax":{
                  "data": {
                  },
                  "url": "/sectoresRmDatatable",
                  "dataType": "json",
                  "type": "GET",
                  "contentType": "application/json; charset=utf-8"
                },
                "columns": [
                  { "data": "ID" },
                  { "data": "DESCRIPCION" },
                  { "data": "DESC_CORTA" }
                ]      
              });
                
        // ------------------------------------------------------------------------
                
        // SELECCIONAR

        $('#tablaModalSectoresRm').on('click', 'tbody tr', function() {

          // *******************************************************************

          // CARGAR LOS VALORES

          me.id = tableSectoresRm.row(this).data().ID;

          Common.filtrarSectoresRmCommon(me.id).then(data => {
                          
            me.enviarCodigoPadre(
              me.id,
              data.sector[0].DESCRIPCION,
              data.sector[0].DESC_CORTA);
          })

        // CERRAR EL MODAL
                     
        $('.sector-rm-modal').modal('hide');

      });
    }
  }
</script>