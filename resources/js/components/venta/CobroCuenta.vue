<template>
	<div class="container-fluid mt-4">
		<div class="row">

			<!-- ------------------------------------------------------------------------------------- -->

			<!-- TITULO  -->
			
			<div class="col-md-12">
				<vs-divider>
					Mostrar Cuentas
				</vs-divider>
			</div>
			
	        <!-- ------------------------------------------------------------------------------------- -->

	        <!-- MOSTRAR LOADING -->

	        <div class="col-md-12">
				<div v-if="procesar" class="d-flex justify-content-center mt-3">
					<strong>Procesando...   </strong>
	                <div class="spinner-grow" role="status" aria-hidden="true"></div>
	             </div>
            </div>

			<!-- ------------------------------------------------------------------------ -->

			<div class="col-md-12">
				<table id="tablaCuentas" class="table table-striped table-hover table-bordered table-sm mb-3" style="width:100%">
		            <thead>
		                <tr>
		                    <th>Codigo</th>
		                    <th>Cliente</th>
		                    <th>Venta</th>
		                    <th>Pago</th>
		                    <th>Vencimiento</th>
		                    <th>Total</th>
		                    <th>Ult. Pago</th>
		                    <th>Total Pago</th>
		                    <th>Saldo</th>
		                    <th>Desc.</th>
		                    <th>% Desc.</th>
		                    <th>Plazo Crédito</th>
		                    <th>Items</th>
		                    <th>Acción</th>
		                </tr>
		            </thead>
		            <tbody>
		                <td></td>
		            </tbody>
		        </table> 


			</div>	
		</div>

		<!-- ------------------------------------------------------------------------ -->

		<!-- <div v-else>
			<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div>
 -->
		<!-- ------------------------------------------------------------------------ -->

	</div>
</template>
<script>
	 export default {
	  props: [''],	
      data(){
        return {
          	codigoCobro: '',
          	procesar: false
        }
      }, 
      methods: {
      		editarCompra(codigo){

      			// ------------------------------------------------------------------------

      			// MANDAR CODIGO TRANSFERENCIA

      			 this.$router.push('/cr2/'+ codigo + '');

      			// ------------------------------------------------------------------------
      		}
      },
        mounted() {
        	
        	let me = this;

            $(document).ready( function () {

            		// ------------------------------------------------------------------------

            		// PREPARAR DATATABLE 

	 				var tableCuentas = $('#tablaCuentas').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "url": "/venta/cuenta/datatable",
                                 "dataType": "json",
                                 "type": "GET",
                                 "contentType": "application/json; charset=utf-8"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "CLIENTE" },
                            { "data": "VENTA" },
                            { "data": "PAGO" },
                            { "data": "VENCIMIENTO" },
                            { "data": "TOTAL" },
                            { "data": "ULT_PAGO" },
		                    { "data": "TOTAL_PAGO" },
		                    { "data": "SALDO" },
		                    { "data": "DESCUENTO" },
		                    { "data": "PORCENTAJE_DESCUENTO" },
		                    { "data": "PLAZO" },
		                    { "data": "ITEMS" },
                            { "data": "ACCION" }
                        ]      
                    });
                    
	 				// ------------------------------------------------------------------------

	 				// AJUSTAR COLUMNAS DE ACUERDO AL DATO QUE CONTIENEN

            		tableCuentas.columns.adjust().draw();

            		// ------------------------------------------------------------------------

                	// EDITAR TRANSFERENCIA

                    $('#tablaCompras').on('click', 'tbody tr #editar', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                    me.editarCompra(tableCuentas.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // ELIMINAR TRANSFERENCIA

                    $('#tablaCompras').on('click', 'tbody tr #eliminar', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                    me.eliminarCompra(tableCuentas.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // GENERAR REPORTE PDF

                    $('#tablaCompras').on('click', 'tbody tr #reporte', function() {

	                    // *******************************************************************

	                    // ENVIAR A COMMON FUNCTION PARA GENERAR REPORTE PDF

	                   	me.procesar = true;
	                   	var row  = $(this).parents('tr')[0];
	                   	Common.generarRptPdfCompraCommon(tableCuentas.row( row ).data().CODIGO).then( () => {
	                   		me.procesar = false;
	                   	});
	                   	

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    $('#tablaCompras').on('click', 'tbody tr #mostrar', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA

	                   	 var row  = $(this).parents('tr')[0];
	                   	 me.mostrarModalTranferencia(tableCuentas.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

	                // ------------------------------------------------------------------------
	 });	
        }
    }
</script>