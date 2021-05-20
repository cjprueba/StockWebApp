<template>
	<div class="container-fluid mt-4">
		<div  v-if="$can('cupones.mostrar') && $can('cupones')" class="row">
			<!--   -->
			<!-- ------------------------------------------------------------------------------------- -->

			<!-- TITULO  -->
			
			<div class="col-md-12">
				<vs-divider>
					Mostrar Cupones
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
			<li class="nav-item">
				 <div class="col-md-12 col text-center">
             
                  <router-link class="collapse-item btn btn-outline-success btn-block" :to="{name: 'cuponCrear'}">Crear Cupones</router-link>
              	
              </div>
			</li>
			
             <div class="col-md-12">
             	 <hr>
             </div>

			<div class="col-md-12">
				<table id="tablaCuponMostrar" class="table table-striped table-hover table-bordered table-sm mb-3" style="width:100%">
		            <thead>
		                <tr>
		                	
		                    <th>Codigo</th>
		                    <th>Tipo de Cupon</th>
		                    <th>Importe</th>
		                    <th>Descripcion</th>
		                    <th>Uso Limite</th>
		                    <th>Fecha Caducidad</th>
		                    <th>Gasto Minimo</th>
		                    <th>Gasto Maximo</th>
		                    <th>Excluir Promociones</th>
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

		

		<!-- ------------------------------------------------------------------------ -->

	</div>
</template>
<script>


	 export default {
	  props: ['id_sucursal'],	
      data(){
        return {
          	codigoVenta: '',
          	procesar: false,
          	ajustes: {
          		IMPRESORA_TICKET: '',
          		IMPRESORA_MATRICIAL: ''
          	}
        }
      }, 
      methods: {
      	      editarCompra(codigo){

      			// ------------------------------------------------------------------------

      			// MANDAR CODIGO TRANSFERENCIA

      			 this.$router.push('/cup2/'+ codigo + '');

      			// ------------------------------------------------------------------------
      		},
			inicio() {

				// ------------------------------------------------------------------------ 

				let me = this;

				// ------------------------------------------------------------------------ 

				// OBTENER DATOS DE INICIO PARA VENTA

				Common.inicioVentaCommon().then(data => {

					// ------------------------------------------------------------------------ 

					me.ajustes.IMPRESORA_TICKET = data.IMPRESORA_TICKET;
					me.ajustes.IMPRESORA_MATRICIAL = data.IMPRESORA_MATRICIAL;

					// ------------------------------------------------------------------------ 

				}).catch(error => {
					Swal.showValidationMessage(
					  `Request failed: ${error}`
					)
				});

				// ------------------------------------------------------------------------ 

			}, factura(numero, caja) {

					// ------------------------------------------------------------------------ 

					let me = this;

					// ------------------------------------------------------------------------ 

					Common.generarPdfFacturaVentaCommon(numero, caja).then(response => {

							var reader = new FileReader();
							 reader.readAsDataURL(new Blob([response])); 
							reader.onloadend = function() {
							     var base64data = reader.result;
							     base64data = base64data.replace("data:application/octet-stream;base64,", "");
							     me.imprimir_factura(base64data);
							 }

					});

					// ------------------------------------------------------------------------ 

					// Common.generarPdfTicketVentaTestCommon();

			},
		     recargar(){
		     
		     	var tableCuponMostrar = $('#tablaCuponMostrar').DataTable();

              tableCuponMostrar.ajax.reload( null, false );
		     },
			deshabilitar(codigo){

      			// ------------------------------------------------------------------------
				let me=this;
      			// INICIAR VARIABLES
                
      			var tablaCuponMostrar = $('#tablaCuponMostrar').DataTable();

      			// ------------------------------------------------------------------------

      			// MOSTRAR LA PREGUNTA DE ELIMINAR 

      			Swal.fire({
				  title: 'Estas seguro ?',
				  text: "deshabilitar el cupón " + codigo + " !",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  confirmButtonColor: '#d33',
				  cancelButtonColor: '#3085d6',
				  confirmButtonText: 'Si, deshabilitalo!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
				    return Common.deshabilitarCuponCommon(codigo).then(data => {
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
						      'deshabilitado!',
						      'Se ha deshabilitado el cupón. Este cupón ya no tendra efecto. !',
						      'success'
					)

				  	// ------------------------------------------------------------------------

				  	// RECARGAR TABLA 
				  	
					me.recargar();
 				
					// ------------------------------------------------------------------------

				  }
				})

				// ------------------------------------------------------------------------

      		},
      		habilitar(codigo){

      			// ------------------------------------------------------------------------

      			// INICIAR VARIABLES

      			var tableCuponMostrar = $('#tableCuponMostrar').DataTable();

      			// ------------------------------------------------------------------------
				let me=this;
      			// MOSTRAR LA PREGUNTA DE ELIMINAR 

      			Swal.fire({
				  title: 'Estas seguro ?',
				  text: "Habilitar el cupón " + codigo + " !",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  confirmButtonColor: '#d33',
				  cancelButtonColor: '#3085d6',
				  confirmButtonText: 'Si, Habilitalo!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
				    return Common.habilitarCuponCommon(codigo).then(data => {
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
						      'Habilitado!',
						      'Se ha Habilitado nuevamente el cupón. Este cupón tendra efecto cuando sea utilizado si no esta caducado. !',
						      'success'
					)

				  	// ------------------------------------------------------------------------

				  	// RECARGAR TABLA 
				  	
					me.recargar();

					// ------------------------------------------------------------------------

				  }
				})

				// ------------------------------------------------------------------------

      		},
      		ticket(numero, caja) {

				// ------------------------------------------------------------------------ 

				let me = this;

				// ------------------------------------------------------------------------ 

				Common.generarPdfTicketVentaCommon(numero, caja).then(response => {

						var reader = new FileReader();
						 reader.readAsDataURL(new Blob([response])); 
						reader.onloadend = function() {
						     var base64data = reader.result;
						     base64data = base64data.replace("data:application/octet-stream;base64,", "");
						    return me.imprimir(base64data);
						 }

				});

				// ------------------------------------------------------------------------ 

				// Common.generarPdfTicketVentaTestCommon();

			}, imprimir(base64) {

				let me = this;

				qz.websocket.connect().then(function() { 
					return qz.printers.find(me.ajustes.IMPRESORA_TICKET);              // Pass the printer name into the next Promise
				}).then(function(printer) {

					var config = qz.configs.create(printer);
					var data = [{ 
						type: 'pixel',
           				format: 'pdf',
           				flavor: 'base64',
						data: base64
					}];

					return qz.print(config, data).then(function() {
					   	qz.websocket.disconnect();
					   	Swal.close();
					});
						 
					   
				}).catch(function(e) { console.error(e); });

			}, imprimir_factura(base64) {

				let me = this;
				
				qz.websocket.connect().then(function() { 

					return qz.printers.find(me.ajustes.IMPRESORA_MATRICIAL);              // Pass the printer name into the next Promise

				}).then(function(printer) {

					var config = qz.configs.create(printer);
					var data = [{ 
						type: 'pixel',
           				format: 'pdf',
           				flavor: 'base64',
						data: base64
					}];

					return qz.print(config, data).then(function() {
						qz.websocket.disconnect();
						Swal.close();
					});

				}).catch(function(e) { console.error(e); });

			}
      },
        mounted() {
        	
        	// ------------------------------------------------------------------------

        	let me = this;

        	// ------------------------------------------------------------------------

        	// LLAMAR LOS DATOS DE LA IMPREOSRA 

        	me.inicio();

        	// ------------------------------------------------------------------------

            // PREPARAR DATATABLE 

	 		var tableCuponMostrar = $('#tablaCuponMostrar').DataTable({
                "processing": true,
                "serverSide": true,
                "destroy": true,
                "bAutoWidth": true,
                "select": true,
                "ajax":{
                  "data": {
                        "_token": $('meta[name="csrf-token"]').attr('content')
                  },
                  "url": "/cupon/datatable",
                  "dataType": "json",
                  "type": "POST"
                },
                "columns": [
                    { "data": "CODIGO" },
                    { "data": "TIPO_CUPON" },
                    { "data": "IMPORTE" },
                    { "data": "DESCRIPCION" },
                    { "data": "USO_LIMITE" },
                    { "data": "FECHA_CADUCIDAD" },
                    { "data": "GASTO_MIN" },
                    { "data": "GASTO_MAX" },
                    { "data": "EXCLUIR_ARTICULOS_CON_DESCUENTO" },
                    { "data": "ACCION" }
                ],
                "createdRow": function( row, data, dataIndex){
                    $(row).addClass(data['ESTATUS']);
                }       
            });

            // ------------------------------------------------------------------------

            // 

            $('#tablaCuponMostrar').on('click', 'tbody tr #eliminarTransferencia', function() {

	          	// *******************************************************************

	          	// REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	          	var row  = $(this).parents('tr')[0];
	          	me.eliminarTransferencia(tableCuponMostrar.row( row ).data().CODIGO);

	          	// *******************************************************************

	      	});

            // ------------------------------------------------------------------------

            // GENERAR FACTURA

            $('#tablaCuponMostrar').on('click', 'tbody tr #imprimirTicket', function() {

	            // *******************************************************************

	            // IMPRIMIR TICKET 
	                   	
	            var row  = $(this).parents('tr')[0];
	                   	
	            me.ticket(tableCuponMostrar.row( row ).data().CODIGO, tableCuponMostrar.row( row ).data().CAJA);

	            Swal.fire({
					title: '¡ Imprimiendo Ticket !',
					html: 'Por favor espere...',
					onBeforeOpen: () => {
						Swal.showLoading()
					}
				})

	            // *******************************************************************

	        });

            // ------------------------------------------------------------------------

            // GENERAR REPORTE PDF

            $('#tablaCuponMostrar').on('click', 'tbody tr #editar', function() {

	            // *******************************************************************

	            // ENVIAR A COMMON FUNCTION PARA GENERAR REPORTE PDF

	     
	           	     var row  = $(this).parents('tr')[0];
	                    me.editarCompra(tableCuponMostrar.row( row ).data().CODIGO);
	

	            // *******************************************************************

	            // *******************************************************************

	        });

           	// ------------------------------------------------------------------------

           	$('#tablaCuponMostrar').on('click', 'tbody tr #deshabilitar', function() {

		        // *******************************************************************

		        // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
		     


		        var row  = $(this).parents('tr')[0];
		        me.deshabilitar(tableCuponMostrar.row( row ).data().CODIGO);

		        // *******************************************************************

	        });
	         $('#tablaCuponMostrar').on('click', 'tbody tr #habilitar', function() {

		        // *******************************************************************

		        // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
		     


		        var row  = $(this).parents('tr')[0];
		        me.habilitar(tableCuponMostrar.row( row ).data().CODIGO);

		        // *******************************************************************

	        });

	        // ------------------------------------------------------------------------
	 
        }
    }
</script>