<template>
	<div v-if="$can('movimientos.mostrarnotadecredito') && $can('movimientos')" class="container-fluid mt-4">
		<div class="row mt-4">
			<!-- TITULO  -->
			
			<div class="col-md-6">
				<div class="section-title">
                    <h4>Listado de Notas de Crédito</h4>
                </div>
            </div>

            <!-- ------------------------------------ TABLA DE DATOS ------------------------------------ -->

			<div class="col-md-12">
				<table id="tablaNotaMostrar" class="table table-striped table-hover table-bordered table-sm mb-3" style="width:100%">
		            <thead>
		                <tr>
		                	<th>ID</th>
		                	<th>Venta_ID</th>
		                    <th>Cliente</th>
		                    <th>RUC</th>
		                    <th>Nro_Factura</th>
		                    <th>Sub_Total</th>
		                    <th>IVA</th>
		                    <th>Total</th>
		                    <th>Fecha</th>
		                    <th>Tipo</th>
		                    <th>Caja</th>
		                    <th>Acción</th>
		                </tr>
		            </thead>
		            <tbody>
		                <td></td>
		            </tbody>
		        </table> 
			</div>

		</div>
		<!-- MODAL MOSTRAR DETALLE VENTA -->

			<modal-detalle-nota-credito 
			ref="ModalDetalleNotaCredito"
			></modal-detalle-nota-credito>
	</div>
	<div v-else>
		<cuatrocientos-cuatro></cuatrocientos-cuatro>
	</div>	
</template>

<script>
	export default{
		data(){
			return {

			}
		},
		methods: {	
			  mostrarModalNotaCredito(codigo) {

      			// ------------------------------------------------------------------------

      			// LLAMAR EL METODO DEL COMPONENTE HIJO

      			this.$refs.ModalDetalleNotaCredito.mostrarModal(codigo);

      			// ------------------------------------------------------------------------

      		}

		},
		mounted(){

			let me = this;

            $(document).ready( function () {

            		// ------------------------------------------------------------------------

            		// PREPARAR DATATABLE 

	 				var tableNotaMostrar = $('#tablaNotaMostrar').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "url": "/nota/credito/mostrar",
                                 "dataType": "json",
                                 "type": "GET",
                                 "contentType": "application/json; charset=utf-8"
                               },
                        "columns": [
                            { "data": "ID" },
                            { "data": "ID_VENTA"},
                            { "data": "CLIENTE" },
                            { "data": "RUC" },
                            { "data": "NRO_FACTURA" },
                            { "data": "SUB_TOTAL" },
                            { "data": "IVA" },
                            { "data": "TOTAL" },
                            { "data": "FECHA" },
                            { "data": "TIPO" },
                            { "data": "CAJA" },
                            { "data": "ACCION" }
                        ], 
		                "createdRow": function( row, data, dataIndex){
		                    $(row).addClass(data['ESTATUS']);
		                },      

                    });
                $('#tablaNotaMostrar').on('click', 'tbody tr #imprimirReporte', function() {

	            // *******************************************************************

			            // IMPRIMIR Reporte 
			            var row  = $(this).parents('tr')[0];
	                   	
			          	Common.generarPdfNotaCreditoCommon(tableNotaMostrar.row( row ).data().ID).then(data => {
									// window.location.href = '/mov2';	
						});

	            // *******************************************************************

	       		 });
                $('#tablaNotaMostrar').on('click', 'tbody tr #mostrarCredito', function() {

			        // *******************************************************************

			        // REDIRIGIR Y ENVIAR CODIGO VENTA

			        var row  = $(this).parents('tr')[0];
			        me.mostrarModalNotaCredito(tableNotaMostrar.row( row ).data().ID);

			        // *******************************************************************

		        });
                 $('#tablaNotaMostrar').on('click', 'tbody tr #devolverCreditoNota', function() {

	            // *******************************************************************

			            // IMPRIMIR Reporte 
			            var row  = $(this).parents('tr')[0];
			            Swal.fire({
					          title: 'Estas seguro ?',
					          text: "cancelar la nota de credito " + tableNotaMostrar.row( row ).data().ID + " !",
					          type: 'warning',
					          showLoaderOnConfirm: true,
					          showCancelButton: true,
					          cancelButtonColor: '#3085d6',
					          confirmButtonText: 'Si!',
					          cancelButtonText: 'Cancelar',
					          preConfirm: () => {
					            return  Common.cancelarNotaCreditoCommon(tableNotaMostrar.row( row ).data().ID).then(data => {
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
					                  'Cancelado !',
					                  'Se ha Cancelado la nota de credito!',
					                  'success'
					          )
					           /* me.$refs.componente_textbox_Ventas.recargar();*/
					           tableNotaMostrar.ajax.reload( null, false );
			
					                }
					        })
	                   	

	            // *******************************************************************

	       		 });

	 				// ------------------------------------------------------------------------

	 				// AJUSTAR COLUMNAS DE ACUERDO AL DATO QUE CONTIENEN

            		tableNotaMostrar.columns.adjust().draw();

            		// ------------------------------------------------------------------------

            });
                    
		}
	}
</script>

<style>
	.section-title h4 {
	    font-weight: 700;
	    color: #002A3A;
	    font-size: 30px;
	    margin: 0 0 15px;
	    border-left: 5px solid #0A1FA3;
	    padding-left: 15px;
	    padding-bottom: 10px;
	}
</style>