<template>
	<div v-if="$can('ordenes.completados') && $can('ordenes')"  class="container-fluid mt-4">
		<div class="row" >

			<!-- -------------------------v-if="$can('orden.mostrar.completado')"------------------------------------------------------------ -->

			<!-- TITULO  -->
			
			<div class="col-md-12">
				<vs-divider>
					Mostrar Pedidos
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
				<table id="tablaOrdenMostrar" class="table table-striped table-hover table-bordered table-sm mb-3" style="width:100%">
		            <thead>
		                <tr>
		                    <th>Código</th>
		                    <th>Cliente</th>
		                    <th>Ciudad</th>
		                    <th>Fecha</th>
		                    <th>Hora</th>
		                    <th>Total</th>
		                    <th>Envío</th>
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

		<modal-detalle-orden 
		ref="ModalImportarOrden"
		></modal-detalle-orden>

		<!-- ------------------------------------------------------------------------ -->

		<!-- MODAL IMPRIMIR DIRECCION ORDEN -->

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

	      	mostrarModalOrden(codigo) {

	      		// ------------------------------------------------------------------------

	      		// LLAMAR EL METODO DEL COMPONENTE HIJO

	      		this.$refs.ModalImportarOrden.mostrarModal(codigo);

	      		// ------------------------------------------------------------------------

	      	},

	      	enviarOrden(codigo){
      			
      			// ------------------------------------------------------------------------

      			// INICIAR VARIABLES

      			var tableOrdenMostrar = $('#tablaOrdenMostrar').DataTable();

      			// ------------------------------------------------------------------------

      			// MOSTRAR LA PREGUNTA

      			Swal.fire({
				  title: '¿Estás seguro?',
				  text: "¡Enviar el Pedido " + codigo + "!",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  cancelButtonColor: '#d33',
				  confirmButtonColor: 'btn btn-success',
				  confirmButtonText: '¡Sí, envialo!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
				    return Common.enviarOrdenCommon(codigo).then(data => {
				    	if (!data.response === true) {
				          throw new Error(data.statusText);
				        }
				  		return data;
				  	}).catch(error => {
				        Swal.showValidationMessage(
				          `Request failed: ${error}`
				        )
				    });
				  }
				}).then((result) => {
				  if (result.value) {
				  	Swal.fire(
						      '¡Enviado!',
						      '¡Se ha enviado el pedido correctamente!',
						      'success'
					)

				  	// ------------------------------------------------------------------------

				  	// RECARGAR TABLA 
				  	
					tableOrdenMostrar.ajax.reload( null, false );

					// ------------------------------------------------------------------------

				  }
				})

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

    			Common.generarPdfFacturaOrdenCommon(ordenID).then(function(){
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

    			Common.generarPdfDireccionOrdenCommon(data).then(function(){
    				Swal.close();
    			});

	        }
  
      },
 	  mounted() {

        	
        	// ------------------------------------------------------------------------

        	let me = this;


        	// ------------------------------------------------------------------------

            // PREPARAR DATATABLE 

	 		var tableOrdenMostrar = $('#tablaOrdenMostrar').DataTable({
                "processing": true,
                "serverSide": true,
                "destroy": true,
                "bAutoWidth": true,
                "select": true,
                "ajax":{
                  "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },	
                  "url": "/orden/datatable",
                  "dataType": "json",
                  "type": "POST"
                },
                "columns": [
                    { "data": "ORDEN_ID" },
                    { "data": "CLIENTE" },
                    { "data": "CIUDAD" },
                    { "data": "FECHA" },
                    { "data": "HORA" },
                    { "data": "TOTAL" },
                    { "data": "ESTADO"},
                    { "data": "ACCION" }
                ]
                
            });

            // ------------------------------------------------------------------------

           	// ------------------------------------------------------------------------

           	$('#tablaOrdenMostrar').on('click', 'tbody tr #mostrarDetalle', function() {

		        // *******************************************************************

		        // REDIRIGIR Y ENVIAR CODIGO VENTA

		        var row  = $(this).parents('tr')[0];
		        me.mostrarModalOrden(tableOrdenMostrar.row( row ).data().ORDEN_ID);

		        // *******************************************************************

	        });

	        // ------------------------------------------------------------------------

            // GENERAR PDF

            $('#tablaOrdenMostrar').on('click', 'tbody tr #imprimirDireccion', function() {

	            // *******************************************************************

                // ABRIR EL MODAL
                     
                $('.imprimir-direccion-modal').modal('show');

                // *******************************************************************

	            // ENVIAR A COMMON FUNCTION PARA GENERAR PDF

	            var row  = $(this).parents('tr')[0];

	            me.ordenID = tableOrdenMostrar.row( row ).data().ORDEN_ID;

	            

	            // *******************************************************************

	            // *******************************************************************

	        });

            // ------------------------------------------------------------------------

            // GENERAR PDF

            $('#tablaOrdenMostrar').on('click', 'tbody tr #imprimirFactura', function() {

                // *******************************************************************

	            // ENVIAR A COMMON FUNCTION PARA GENERAR PDF

	            var row  = $(this).parents('tr')[0];

	            me.facturaPDF(tableOrdenMostrar.row( row ).data().ORDEN_ID);

	            // *******************************************************************

	            // *******************************************************************

	        });

	        // ------------------------------------------------------------------------

	        // ENVIAR

            $('#tablaOrdenMostrar').on('click', 'tbody tr #enviarOrden', function() {

	         	// *******************************************************************

	         	// REDIRIGIR Y ENVIAR CODIGO
	                   	
	        	var row  = $(this).parents('tr')[0];
	         	me.enviarOrden(tableOrdenMostrar.row( row ).data().ORDEN_ID);

	         	// *******************************************************************

	        });
	 
      }
    }
</script>