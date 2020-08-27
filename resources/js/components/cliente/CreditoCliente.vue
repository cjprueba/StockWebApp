<template>
	<div class="container-fluid mt-4">
		<div class="row" v-if="$can('compra.mostrar')">

			<!-- ------------------------------------------------------------------------------------- -->

			<!-- TITULO  -->
			
			<div class="col-md-12">
				<vs-divider>
					Clientes con Crédito
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

				<table id="tablaClienteCredito" class="table table-striped table-hover table-bordered table-sm mb-3" style="width:100%">
		            <thead>
		                <tr>
		                    <th>Codigo</th>
		                    <th>Nombre</th>
		                    <th>Celular</th>
		                    <th>Telefono</th>
		                    <th>Monto</th>
		                    <!-- <th>Acción</th> -->
		                </tr>
		            </thead>
		            <tbody>
		                <td></td>
		            </tbody>
		        </table>

			</div>	
		</div>

		<!-- ------------------------------------------------------------------------ -->

		<div v-else>
			<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div>

		<!-- ------------------------------------------------------------------------ -->

		<!-- MODAL MOSTRAR DETALLE TRANSFERENCIA -->

		<modal-detalle-compra 
		ref="ModalMostrarDetalleCompra"
		></modal-detalle-compra>

		<!-- ------------------------------------------------------------------------ -->

		<!-- MODAL PAGAR CUENTA -->

		<div class="modal fade producto-modal" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
                      <div class="modal-content">
                        
                        <div class="modal-header">
                        	<h5 class="modal-title" id="exampleModalCenterTitle"><small> MEDIOS DE PAGO </small></h5>
                        	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					          <span aria-hidden="true">&times;</span>
					        </button>
						</div>
						
						<div class="modal-body">  
                        	<div class="row">
                        		<div class="col-md-12 mb-3">
                        			<button class="btn btn-primary"><font-awesome-icon icon="plus" /> Abonar</button>
                        			<button class="btn btn-danger"><font-awesome-icon icon="trash" /> Eliminar</button>
                        			<button class="btn btn-primary"><font-awesome-icon icon="print" /> Recibo</button>
                        		</div>
                        		<div class="col-md-12">
                        			<table id="tablaClienteCreditoDet" class="table table-striped table-hover table-bordered table-sm mb-3 mt-3" style="width:100%">
							            <thead>
							                <tr>
							                    <th>Doc.</th>
							                    <th>Folio</th>
							                    <th>Serie</th>
							                    <th>Fecha</th>
							                    <th>Vencimiento</th>
							                    <th>Crédito</th>
							                    <th>Abonos</th>
							                    <th>Saldo</th>
							                    <!-- <th>Acción</th> -->
							                </tr>
							            </thead>
							            <tbody>
							                <td></td>
							            </tbody>
							        </table> 
                        		</div>	

                        		<div class="col-md-12">
                        			<table id="tablaClienteCreditoDet" class="table table-striped table-hover table-bordered table-sm mb-3 mt-3" style="width:100%">
							            <thead>
							                <tr>
							                    <th>Fecha</th>
							                    <th>TP</th>
							                    <th>Mult. Pago</th>
							                    <th>Referencia</th>
							                    <th>Total</th>
							                    <!-- <th>Acción</th> -->
							                </tr>
							            </thead>
							            <tbody>
							                <td></td>
							            </tbody>
							        </table> 
                        		</div>
                        	</div>	
                        </div>	
					  
					  </div>
					</div>
		</div>
		
		<!-- ------------------------------------------------------------------------ -->

	</div>
</template>
<script>


	 export default {
	  props: ['id_sucursal'],	
      data(){
        return {
          	codigoTransferencia: '',
          	procesar: false
        }
      }, 
      methods: {

      		mostrarModalTranferencia(codigo) {

      			// ------------------------------------------------------------------------

      			// LLAMAR EL METODO DEL COMPONENTE HIJO

      			this.$refs.ModalMostrarDetalleCompra.mostrarModal(codigo);

      			// ------------------------------------------------------------------------

      		}, detalleCredito(){

      			var tableClienteCreditoDet = $('#tablaClienteCreditoDet').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "url": "/cliente/credito/datatable",
                                 "dataType": "json",
                                 "type": "GET",
                                 "contentType": "application/json; charset=utf-8"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "NOMBRE" },
                            { "data": "CELULAR" },
                            { "data": "TELEFONO" },
                            { "data": "MONTO" }
                        ]      
                });

      		}
      },
        mounted() {
        	
        	let me = this;

            $(document).ready( function () {

            		// ------------------------------------------------------------------------

            		// PREPARAR DATATABLE 

	 				var tableClienteCredito = $('#tablaClienteCredito').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "url": "/cliente/credito/datatable",
                                 "dataType": "json",
                                 "type": "GET",
                                 "contentType": "application/json; charset=utf-8"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "NOMBRE" },
                            { "data": "CELULAR" },
                            { "data": "TELEFONO" },
                            { "data": "MONTO" }
                        ]      
                    });
                    
	 				// ------------------------------------------------------------------------

	 				// AJUSTAR COLUMNAS DE ACUERDO AL DATO QUE CONTIENEN

            		tableClienteCredito.columns.adjust().draw();

            		// ------------------------------------------------------------------------

                	// EDITAR TRANSFERENCIA

                    $('#tablaClienteCredito').on('click', 'tbody tr', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	                   	$('#staticBackdrop').modal('show');
	                   	me.detalleCredito();
	                   	// var row  = $(this).parents('tr')[0];
	                    // me.editarCompra(tableClienteCredito.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // ELIMINAR TRANSFERENCIA

                    $('#tablaClienteCredito').on('click', 'tbody tr #eliminar', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                    me.eliminarCompra(tableClienteCredito.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // GENERAR REPORTE PDF

                    $('#tablaClienteCredito').on('click', 'tbody tr #reporte', function() {

	                    // *******************************************************************

	                    // ENVIAR A COMMON FUNCTION PARA GENERAR REPORTE PDF

	                   	me.procesar = true;
	                   	var row  = $(this).parents('tr')[0];
	                   	Common.generarRptPdfCompraCommon(tableClienteCredito.row( row ).data().CODIGO).then( () => {
	                   		me.procesar = false;
	                   	});
	                   	

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    $('#tablaClienteCredito').on('click', 'tbody tr #mostrar', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA

	                   	 var row  = $(this).parents('tr')[0];
	                   	 me.mostrarModalTranferencia(tableClienteCredito.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

	                // ------------------------------------------------------------------------
	 });	
        }
    }
</script>