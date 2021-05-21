<template>
	<div v-if="$can('cotizacion.mostrar') && $can('cotizacion')" class="container-fluid mt-4">
		<div class="row">

			<!---------------------------v-if="$can('orden.mostrar.completado')"-------------------------------------------- -->

			<!-- TITULO  -->
			
			<div class="col-md-12">
				<vs-divider>
					Mostrar Cotización
				</vs-divider>
			</div>

			<!-- ------------------------------------------------------------------------ -->

			<div class="col-md-12">
				<table id="tablaCotizacionMostrar" class="table table-striped table-hover table-bordered table-sm mb-3" style="width:100%">
		            <thead>
		                <tr>
		                    <th>ID</th>
		                    <th>DE</th>
		                    <th>A</th>
		                    <th>FECHA</th>
		                    <th>VALOR</th>
		                    <th>FECALTA</th>
		                </tr>
		            </thead>
		        </table> 
			</div>	
		</div>

		<!-- ------------------------------------------------------------------------ -->
		
		<!-- <div v-else>
			<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div> -->

		<!-- ------------------------------------------------------------------------ -->

	</div>
	<div v-else>
		<cuatrocientos-cuatro></cuatrocientos-cuatro>
	</div>
</template>
<script>
	export default {
	  props: ['id_sucursal'],	
      data(){
        return {
        }
      }, 

      methods: {
      },

 	  mounted() {

        	
        	// ------------------------------------------------------------------------

        	let me = this;


        	// ------------------------------------------------------------------------

            // PREPARAR DATATABLE 

	 		var tableCotizacionMostrar = $('#tablaCotizacionMostrar').DataTable({
                "processing": true,
                "serverSide": true,
                "destroy": true,
                "bAutoWidth": true,
                "select": true,
                "ajax":{
                  "data": {
                  },
                  "url": "/cotizacionDatatable",
                  "dataType": "json",
                  "type": "GET",
                  "contentType": "application/json; charset=utf-8"
                },
                "columns": [
                    { "data": "ID" },
                    { "data": "DE" },
                    { "data": "A" },
                    { "data": "FECHA" },
                    { "data": "VALOR" },
                    { "data": "FECALTAS" }
                ]
                
            });
      	}
    }
</script>