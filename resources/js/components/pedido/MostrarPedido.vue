<template>
	<div class="container-fluid mt-4">
		<!-- -->
		<div class="row"  v-if="$can('pedido.mostrar') && $can('pedidos')">

			<!-- ------------------------------------------------------------------------------------- -->

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
				<table id="tablaPedidos" class="table table-striped table-hover table-bordered table-sm mb-3" style="width:100%">
		            <thead>
		                <tr>
		                    <th>Codigo</th>
		                    <th>Cliente</th>
		                    <th>Creación</th>
		                    <th>Observación</th>
		                    <th>Usuario</th>
		                    <th>Total</th>
		                    <th>Estatus</th>
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
		
		<div v-else>
			<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div>

		<!-- ------------------------------------------------------------------------ -->

        <!-- MODAL ELECCION DE MONEDA -->

                  <div class="modal fade" id="modalMoneda" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">

                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Opciones</h5>
                        </div>

                        <div class="modal-body">

                          <div class="row">
                            <legend class="col-form-label col-sm-2 pt-0">Moneda</legend>
                            <div class="col-sm-10">
                              <div class="form-check form-check-inline">
                                <input  v-model="radio.moneda" class="form-check-input" type="radio" name="radioVuelto" id="gridRadios1" value="1">
                                <label class="form-check-label" for="gridRadios1">
                                  Guaranies
                                </label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input  v-model="radio.moneda" class="form-check-input" type="radio" name="radioVuelto" id="gridRadios2" value="2">
                                <label class="form-check-label" for="gridRadios2">
                                  Dolares
                                </label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input  v-model="radio.moneda" class="form-check-input" type="radio" name="radioVuelto" id="gridRadios3" value="3">
                                <label class="form-check-label" for="gridRadios3">
                                  Reales
                                </label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input  v-model="radio.moneda" class="form-check-input" type="radio" name="radioVuelto" id="gridRadios4" value="4">
                                <label class="form-check-label" for="gridRadios4">
                                  Pesos
                                </label>
                              </div>
                            </div>
                          </div>

                        </div>

                        <div class="modal-footer">
                          <button type="button" class="btn btn-primary" data-dismiss="modal" v-on:click="enviar">Aceptar</button>
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        </div>

                      </div>
                    </div>
                  </div>
                  
        <!-- ------------------------------------------------------------------------ -->

		<!-- <div v-else>
			<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div> -->

		<!-- ------------------------------------------------------------------------ -->

		<!-- MODAL MOSTRAR DETALLE TRANSFERENCIA -->

		<modal-detalle-compra 
		ref="ModalMostrarDetalleCompra"
		></modal-detalle-compra>

		<!-- ------------------------------------------------------------------------ -->

	</div>
</template>
<script>


	 export default {
	  props: ['id_sucursal'],	
      data(){
        return {
          	codigoTransferencia: '',
          	procesar: false,
          	radio: {
          		moneda: '1'
          	},
          	pedido: {
          		codigo: ''
          	}
        }
      }, 
      methods: {
      		editarCompra(codigo){

      			// ------------------------------------------------------------------------

      			// MANDAR CODIGO TRANSFERENCIA

      			 this.$router.push('/cr2/'+ codigo + '');

      			// ------------------------------------------------------------------------
      		}, mostrarModalTranferencia(codigo) {

      			// ------------------------------------------------------------------------

      			// LLAMAR EL METODO DEL COMPONENTE HIJO

      			this.$refs.ModalMostrarDetalleCompra.mostrarModal(codigo);

      			// ------------------------------------------------------------------------

      		}, eliminarCompra(codigo){

      			// ------------------------------------------------------------------------

      			// INICIAR VARIABLES

      			var tablePedidos = $('#tablaPedidos').DataTable();

      			// ------------------------------------------------------------------------

      			// MOSTRAR LA PREGUNTA DE ELIMINAR 

      			Swal.fire({
				  title: 'Estas seguro ?',
				  text: "Eliminar la Compra " + codigo + " !",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  confirmButtonColor: '#d33',
				  cancelButtonColor: '#3085d6',
				  confirmButtonText: 'Confirmar !',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
				    return Common.eliminarCompraCommon(codigo).then(data => {
				    	if (!data.response === true) {
				          throw new Error(data.statusText);
				        }
				  		return data.response;
				  	}).catch(error => {
				        Swal.showValidationMessage(
				          `Request failed: ${error}`
				        )
				    });
				  }
				}).then((result) => {
				  if (result.value) {
				  	Swal.fire(
						      'Eliminado!',
						      'Se ha eliminado la transferencia y devuelto el stock !',
						      'success'
					)

				  	// ------------------------------------------------------------------------

				  	// RECARGAR TABLA 
				  	
					tablePedido.ajax.reload( null, false );

					// ------------------------------------------------------------------------

				  }
				})

				// ------------------------------------------------------------------------

      		}, enviar() {

      			this.procesar = true;

      			Common.generarRptPdfPedidoCommon(this.pedido.codigo, this.radio.moneda).then( () => {
	                   		this.procesar = false;
	            });

      		}
      },
        mounted() {
        	
        	let me = this;

            $(document).ready( function () {

            		// ------------------------------------------------------------------------

            		// PREPARAR DATATABLE 

	 				var tablePedido = $('#tablaPedidos').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                        		 "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/pedido/mostrar/datatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "CLIENTE" },
                            { "data": "FECALTAS" },
                            { "data": "OBSERVACION" },
                            { "data": "USUARIO" },
                            { "data": "TOTAL" },
                            { "data": "ESTATUS" },
                            { "data": "ACCION" }
                        ]      
                    });
                    
	 				// ------------------------------------------------------------------------

	 				// AJUSTAR COLUMNAS DE ACUERDO AL DATO QUE CONTIENEN

            		tablePedido.columns.adjust().draw();

            		// ------------------------------------------------------------------------

                	// EDITAR TRANSFERENCIA

                    $('#tablaPedidos').on('click', 'tbody tr #editar', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                    me.editarCompra(tablePedido.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // ELIMINAR TRANSFERENCIA

                    $('#tablaPedidos').on('click', 'tbody tr #eliminar', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                    me.eliminarCompra(tablePedido.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // GENERAR REPORTE PDF

                    $('#tablaPedidos').on('click', 'tbody tr #reporte', function() {

	                    // *******************************************************************

	                    // ENVIAR A COMMON FUNCTION PARA GENERAR REPORTE PDF

	                   	$('#modalMoneda').modal('show');
	                   	var row  = $(this).parents('tr')[0];
	                   	me.pedido.codigo = tablePedido.row( row ).data().CODIGO;

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    $('#tablaPedidos').on('click', 'tbody tr #mostrar', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA

	                   	 var row  = $(this).parents('tr')[0];
	                   	 me.mostrarModalTranferencia(tablePedido.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

	                // ------------------------------------------------------------------------

	                // CONFIRMAR PEDIDO

                    $('#tablaPedidos').on('click', 'tbody tr #confirmarPedido', function() {

	                    // *******************************************************************

	                    // CONFIRMAR PEDIDO
	                    
	                   	var row  = $(this).parents('tr')[0];

	                   	Swal.fire({
						  title: 'Estas seguro ?',
						  text: "Confirmar pedido " + tablePedido.row( row ).data().CODIGO + " !",
						  type: 'warning',
						  showLoaderOnConfirm: true,
						  showCancelButton: true,
						  confirmButtonColor: '#d33',
						  cancelButtonColor: '#3085d6',
						  confirmButtonText: 'Confirmar !',
						  cancelButtonText: 'Cancelar',
						  preConfirm: () => {
						    return Common.cambiarEstatusPedidoCommon(tablePedido.row( row ).data().CODIGO, 3).then( data => {
						    	if (!data.response === true) {
						          throw new Error(data.statusText);
						        }
						  		return data.response;
						  	}).catch(error => {
						        Swal.showValidationMessage(
						          `Request failed: ${error}`
						        )
						    });
						  }
						}).then((result) => {
						  if (result.value) {
						  	Swal.fire(
								      'Cambiado !',
								      'Se ha confirmado el pedido !',
								      'success'
							)

						  	// ------------------------------------------------------------------------

						  	// RECARGAR TABLA 
						  	
							tablePedido.ajax.reload( null, false );

							// ------------------------------------------------------------------------

						  }
						})
	                   	

	                    // *******************************************************************

	                });
	 });	
        }
    }
</script>