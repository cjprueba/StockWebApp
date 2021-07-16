<template>
	<div class="container-fluid">
			<div class="modal fade" id="modalTransferenciaProductos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-xl" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title text-primary" id="exampleModalLabel"><small>Detalle de Transferencia: {{codigo_transferencia}} </small></h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		      	<div v-if="rack === true">
			      	<div class="row mt-2">
			      		<div class="col-4">
							<span class="float-left"><strong class="ml-3"> Nro. Caja: </strong> {{nro_caja}}</span><br/>
			      		</div>
						<div class="col-4">
							<span class="float-left"><strong> Sección: </strong> {{descripcionSeccion}} </span><br/>
						</div>
			      		<div class="col-4">
							<span class="float-left"><strong> Sector: </strong> {{descripcionSector}}</span><br/>
						</div>
			      	</div>

			      	<div class="row mt-3 mb-3">	

			      		<div class="col-4">
							<span class="float-left"><strong class="ml-3"> Góndola: </strong> {{descripcionGondola}}</span><br/>
			      		</div>
						<div class="col-4">
							<span class="float-left"><strong> Piso: </strong> {{piso}} </span><br/>
						</div>
					</div>
				</div>
		        <table id="transferenciaProductos" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
					            <thead>
					                <tr>
					                    <th>ITEM</th>
					                    <th>Codigo Producto</th>
					                    <th>Descripción</th>
					                    <th>Cantidad</th>
					                    <th>Precio</th>
					                    <th>Total</th>
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
	</div>
</template>

<script>
	 export default {
	  props: [''],
      data(){
        return {
          open: false,
          codigo_transferencia: '',
          rack: false,
		  descripcionSeccion: '',
          descripcionSector: '',
		  descripcionGondola: '',
		  piso: '',
		  nro_caja:''
        }
      }, 
      methods: {
      		mostrarModal(codigo, codigo_origen){

      			// ------------------------------------------------------------------------

      			// LLAMAR AJAX PARA CARGAR DATATABLE 

      			this.obtenerDatosTranferencia(codigo, codigo_origen);

      			// ------------------------------------------------------------------------
      			// ------------------------------------------------------------------------

      			// LLAMAR AJAX PARA CARGAR CABECERA 

      			this.cargarCabeceraTransferencia(codigo, codigo_origen);

      			// LLAMAR MODAL TRANSFERENCIA PRODUCTOS

      			$('#modalTransferenciaProductos').modal('show');

      			// ------------------------------------------------------------------------
            	
            }, 
            cargarCabeceraTransferencia(codigo, codigo_origen){

            	let me = this;

            	Common.obtenerCabeceraTransferenciaCommon(codigo, codigo_origen).then(data => {
            		
        			me.rack = data.SISTEMA_DEPOSITO;

        			if(data.SISTEMA_DEPOSITO === true){

        				me.nro_caja = data.DATOS_DEPOSITO.NRO_CAJA;
        				me.descripcionSeccion = data.DATOS_DEPOSITO.DESC_SECCION;
        				me.descripcionGondola = data.DATOS_DEPOSITO.DESC_GONDOLA;
        				me.descripcionSector = data.DATOS_DEPOSITO.DESC_SECTOR;
        				me.piso = data.DATOS_DEPOSITO.DESC_PISO;
        			}
				});
            },
            obtenerDatosTranferencia(codigo, codigo_origen){

            	// ------------------------------------------------------------------------

            	let me = this;

            	// ------------------------------------------------------------------------

            	// PREPARAR DATATABLE 

	 			var tableProductosTransferencia = $('#transferenciaProductos').DataTable({
	                 	"processing": true,
	                 	"serverSide": true,
	                 	"destroy": true,
	                 	"bAutoWidth": true,
	                 	"select": true,
	                 	"ajax":{
	                 			"data": {
	                 				codigoTransferencia: codigo,
	                 				codigo_origen: codigo_origen
	                 			},
	                             "url": "/transferenciasMostrarProductos",
	                             "dataType": "json",
	                             "type": "GET",
	                             "contentType": "application/json; charset=utf-8"
	                           },
	                    "columns": [
	                            { "data": "ITEM" },
	                            { "data": "COD_PROD" },
	                            { "data": "DESCRIPCION" },
	                            { "data": "CANTIDAD" },
	                            { "data": "PRECIO" },
	                            { "data": "TOTAL" }
	                        ]    
	                });
                    
	 				// ------------------------------------------------------------------------

	 				// CARGAR CODIGO TRANSFERENCIA

      				this.codigo_transferencia = codigo;

      				// ------------------------------------------------------------------------
            }
      },
        mounted() {

           		 $(document).ready( function () {

           		 

           		 });

           		 
        }
    }
</script>