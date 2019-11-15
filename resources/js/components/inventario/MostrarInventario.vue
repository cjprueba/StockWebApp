<template>
	<div class="container-fluid mt-4">

		<!-- ------------------------------------------------------------------------------------- -->

		<!-- TITULO  -->
		
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				 <li class="breadcrumb-item active" aria-current="page">Mostrar Inventario</li>
			</ol>
		</nav>

	
        <!-- ------------------------------------------------------------------------------------- -->

        <!-- TABLA CONTEO -->

		<table id="tablaConteo" class="table table-striped table-bordered table-sm" style="width:100%">
		    <thead>
		        <tr>
		            <th>ID</th>
		            <th>Observación</th>
		            <th>Sucursal</th>
		            <th>Fecha Creación</th>
		            <th>Fecha Modificación</th>
		            <th>Acción</th>
		        </tr>
		    </thead>	
		 </table>

		 <!-- ------------------------------------------------------------------------------------- -->

	</div>
</template>

<script>
	export default {
      props: ['moneda'],
      data(){
        return {
          menu : 0
        }
      }, 
      methods: {
            nuevaCategoria(){
                const params = {
                    description: this.title
                };
                axios.post('/categorias', params).then((response) => {
                    const categoria = response.data;
                     this.$emit('new', categoria);
                });
                
            },editarInventario(ID){

              // ------------------------------------------------------------------------

              // MANDAR ID INVENTARIO

               this.$router.push('/in2/'+ ID + '');

              // ------------------------------------------------------------------------
            }
      },
        mounted() {
            
            // ------------------------------------------------------------------------

            // INICIAR VARIABLES

            let me = this;

            // ------------------------------------------------------------------------

            $(document).ready( function () {

        		var table = $('#tablaConteo').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "url": "/inventarioMostrar",
                                 "dataType": "json",
                                 "type": "GET"
                               },
                        "columns": [
                            { "data": "ID" },
                            { "data": "OBSERVACION" },
                            { "data": "SUCURSAL" },
                            { "data": "FECALTAS" },
                            { "data": "FECMODIF" },
                            { "data": "ACCION" }
                        ]      
                    });

                  // ------------------------------------------------------------------------

                  // EDITAR TRANSFERENCIA

                    $('#tablaConteo').on('click', 'tbody tr #editarInventario', function() {

                      // *******************************************************************

                      // REDIRIGIR Y ENVIAR CODIGO Inventario
                      
                      var row  = $(this).parents('tr')[0];
                      me.editarInventario(table.row( row ).data().ID);

                      // *******************************************************************

                  });

                  // ------------------------------------------------------------------------
        	});
        }
    }
</script>