<template>
	<div>
    <div class="input-group" id="validationTooltip01" >
			<div class="input-group-prepend">
				<button  type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".empresa-modal"><font-awesome-icon icon="search"/></button>
			</div>

      <input ref="codigo" id="codigoEmpresa" :disabled="true" class="custom-select custom-select-sm shadow-sm" type="text"  :value="nombre" @input="$emit('input', $event.target.value)" v-bind:class="{ 'is-invalid': validarEmpresa }" v-on:keyup.prevent.13="enviarCodigoPadre($event.target.value)" v-on:blur="enviarCodigoPadre($event.target.value)">
    </div>

		<!-- ******************************************************************* -->

	  <!-- MODAL PRODUCTOS -->

	  <div class="modal fade empresa-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
	      <div class="modal-content">
	        <div class="modal-header">
	          <h5 class="modal-title" id="exampleModalCenterTitle">Empresas: </small></h5>
	          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	           <span aria-hidden="true">&times;</span>
	          </button>
	        </div>
	        <div class="modal-body">
	          <table id="tablaModalEmpresas" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
	            <thead>
	              <tr>
	               <th>Codigo</th>
	               <th>Nombre</th>
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
	  <!-- ******************************************************************* -->
	</div>	
</template>
<script>
	export default {
    props: ['nombre', 'validarEmpresa'],
    data(){
      return {
      }
    }, 
    
    methods: {

      enviarCodigoPadre(id, nombre){

				// ------------------------------------------------------------------------

				// ENVIAR CODIGO
                  
				this.$emit('nombre',nombre);
        this.$emit('id', id);
				
        // ------------------------------------------------------------------------

			}
    },
    
    mounted() {
        	
      // ------------------------------------------------------------------------

      // INICIAR VARIABLES 

      let me = this;

      // ------------------------------------------------------------------------
        	
      $(document).ready( function () {

        // ------------------------------------------------------------------------
        // >>
        // INICIAR EL DATATABLE PRODUCTOS MODAL
        // ------------------------------------------------------------------------
                
        var tableEmpresas = $('#tablaModalEmpresas').DataTable({
              "processing": true,
              "serverSide": true,
              "destroy":true,
              "bAutoWidth": true,
              "select": true,
              "ajax":{
                "data": {
                  "_token": $('meta[name="csrf-token"]').attr('content')
                },
                "url": "/empresasDatatable",
                "dataType": "json",
                "type": "POST"
              },
              "columns": [
                  { "data": "ID" },
                  { "data": "NOMBRE" }
              ]      
            });
                
        // ------------------------------------------------------------------------
                
        // SELECCIONAR UNA FILA

        $('#tablaModalEmpresas').on('click', 'tbody tr', function() {

          // *******************************************************************

          // CARGAR LOS VALORES A LAS VARIABLES DE PRODUCTO

          me.enviarCodigoPadre(tableEmpresas.row(this).data().ID, tableEmpresas.row(this).data().NOMBRE);
          
          // *******************************************************************

         // CERRAR EL MODAL
                     
          $('.empresa-modal').modal('hide');

            // *******************************************************************

        });

        //FIN TABLA MODAL 
        // <<   
        // ------------------------------------------------------------------------
      });    
    }
  }
</script>