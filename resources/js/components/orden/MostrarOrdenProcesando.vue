<template>
	<div v-if="$can('ordenes.procesando') && $can('ordenes')" class="container-fluid mt-4">
		<div class="row" >
			<!--   -->
			<!-- -------------------------------------------v-if="$can('orden.mostrar.procesando')"------------------------------------------ -->

			<!-- TITULO  -->
			
			<div class="col-md-12">
				<vs-divider>
					Procesando
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
				<table id="tablaOrdenProcesando" class="table table-striped table-hover table-bordered table-sm mb-3" style="width:100%">
		            <thead>
		                <tr>
		                    <th>Código</th>
		                    <th>Cliente</th>
		                    <th>Ciudad</th>
		                    <th>Fecha</th>
		                    <th>Hora</th>
		                    <th>Total</th>
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
		</div> -->

		<!-- ------------------------------------------------------------------------ -->
		
		<!-- MODAL MOSTRAR DETALLE ORDEN -->

		<modal-detalle-orden-pendiente 
		ref="ModalImportarOrdenPendiente"
		></modal-detalle-orden-pendiente>

		<!-- ------------------------------------------------------------------------ -->

		<div class="modal fade imprimir-direccion-modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
	            <div class="modal-content">
	                <div class="modal-header">
	                    <h5 class="modal-title text-primary text-center" >PEDIDO: {{ordenID}}</h5>
	                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                          <span aria-hidden="true">&times;</span>
	                    </button>
	                </div>
	                <div class="modal-body">
	                    <div class="row">
	                        <div class="col-md-12">
	                            <label>Tipo de Envío: </label>
	                            <select v-model="tipoEnvio" class="form-control form-control-sm">
	                            	<option>Puerta a Puerta</option>
		      						<option>Pickup</option>
	                            </select>
	                        </div>
	                        <div class="col-md-12 mt-3">
	                            <label>Tamaño de Caja: </label>
	                            <select v-model="cajaTamanho" class="form-control form-control-sm">
	                            	<option>Pequeño</option>
		      						<option>Mediano</option>
		      						<option>Grande</option>
	                            </select>
	                        </div>
	                    </div>      
	                </div>
	                <div class="modal-footer">
	                    <button type="button" class="btn btn-primary" v-on:click="direccionPDF()">Imprimir</button>
	                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
	                </div>
	            </div>
	        </div>
	    </div> 
 
	</div>
</template>
<script>
	export default {
	  props: ['id_sucursal'],	
      data(){
        return {
        	ordenID: '',
          	codigoOrden: '',
          	cajaTamanho: 'Pequeño',
          	tipoEnvio: 'Puerta a Puerta',
          	procesar: false,
          	ajustes: {
          		IMPRESORA_TICKET: '',
          		IMPRESORA_MATRICIAL: ''
          	}
        }
      }, 
      methods: {

	      	mostrarModalOrdenProcesando(codigo) {

	      		// ------------------------------------------------------------------------

	      		// LLAMAR EL METODO DEL COMPONENTE 

	      		this.$refs.ModalImportarOrdenPendiente.mostrarModalOrdenPendiente(codigo);

	      		// ------------------------------------------------------------------------

	      	},
  			
  			facturaPDF(ordenID){

	      		Swal.fire({
					title: '¡Imprimiendo Factura!',
					html: 'Por favor espere...',
					onBeforeOpen: () => {
						Swal.showLoading()
					}
				})

    			Common.generarPdfFacturaOrdenPendienteCommon(ordenID).then(function(){
    				Swal.close();
    			});
    						
	        },

	        direccionPDF(){

	        	var data = {

    				codigo: this.ordenID,
    				tamanho: this.cajaTamanho,
    				tipo: this.tipoEnvio
    			}
    			
				Swal.fire({
					title: '¡Imprimiendo Dirección!',
					html: 'Por favor espere...',
					onBeforeOpen: () => {
						Swal.showLoading()
					}
				})

    			Common.generarPdfDireccionOrdenPendienteCommon(data).then(function(){
    				Swal.close();
    			});

	        }
      },
 	  mounted() {

        	
        	// ------------------------------------------------------------------------

        	let me = this;


        	// ------------------------------------------------------------------------

            // PREPARAR DATATABLE 

	 		var tableOrdenProcesando = $('#tablaOrdenProcesando').DataTable({
                "processing": true,
                "serverSide": true,
                "destroy": true,
                "bAutoWidth": true,
                "searching": false,
                "select": true,
	            "paging": false,
                "ajax":{
                  "url": "/orden/datatableProcesando",
                  "dataType": "json",
                  "type": "GET",
                  "contentType": "application/json; charset=utf-8"
                },
                "columns": [
                    { "data": "ORDEN_ID" },
                    { "data": "CLIENTE" },
                    { "data": "CIUDAD" },
                    { "data": "FECHA" },
                    { "data": "HORA" },
                    { "data": "TOTAL" },
                    { "data": "ACCION" }
                ]
                
            });

            // ------------------------------------------------------------------------

           	// ------------------------------------------------------------------------

           	$('#tablaOrdenProcesando').on('click', 'tbody tr #mostrarDetalle', function() {

		        // *******************************************************************

		        // REDIRIGIR Y ENVIAR CODIGO VENTA

		        var row  = $(this).parents('tr')[0];
		        me.mostrarModalOrdenProcesando(tableOrdenProcesando.row( row ).data().ORDEN_ID);

		        // *******************************************************************

	        });

	        // ------------------------------------------------------------------------

            // GENERAR PDF

            $('#tablaOrdenProcesando').on('click', 'tbody tr #imprimirDireccion', function() {

	            // *******************************************************************

                // ABRIR EL MODAL
                     
                $('.imprimir-direccion-modal').modal('show');

                // *******************************************************************

	            // ENVIAR A COMMON FUNCTION PARA GENERAR PDF

	            var row  = $(this).parents('tr')[0];

	            me.ordenID = tableOrdenProcesando.row( row ).data().ORDEN_ID;

	            

	            // *******************************************************************

	            // *******************************************************************

	        });

            // ------------------------------------------------------------------------

            // GENERAR PDF

            $('#tablaOrdenProcesando').on('click', 'tbody tr #imprimirFactura', function() {

                // *******************************************************************

	            // ENVIAR A COMMON FUNCTION PARA GENERAR PDF

	            var row  = $(this).parents('tr')[0];

	            me.facturaPDF(tableOrdenProcesando.row( row ).data().ORDEN_ID);

	            // *******************************************************************

	            // *******************************************************************

	        });
      }
    }
</script>