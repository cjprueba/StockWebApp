<template>
	<div class="container-fluid">
			<div class="modal fade" id="modalCompraProductos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-xl" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title text-primary" id="exampleModalLabel"><small>Detalle de Compra: {{codigo_compra}} </small></h5>
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
							<span class="float-left"><strong> Container: </strong> {{descripcionContainer}}</span><br/>
						</div>
			      		<div class="col-4">
							<span class="float-left"><strong> Sección: </strong> {{descripcionSeccion}} </span><br/>
						</div>
			      	</div>

			      	<div class="row mt-3 mb-3">	

			      		<div class="col-4">
							<span class="float-left"><strong class="ml-3"> Sector: </strong> {{descripcionSector}}</span><br/>
			      		</div>
						<div class="col-4">
							<span class="float-left"><strong> Góndola: </strong> {{descripcionGondola}}</span><br/>
						</div>
			      		<div class="col-4">
							<span class="float-left"><strong> Piso: </strong> {{piso}} </span><br/>
						</div>
					</div>
				</div>
		        <table id="CompraProductos" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
					            <thead>
					                <tr>
					                    <th>ITEM</th>
					                    <th>Código Producto</th>
					                    <th>Descripción</th>
					                    <th>Cantidad</th>
					                    <th>Costo</th>
					                    <th>Costo Total</th>
					                    <th>Lote</th>
					                    <th>Precio</th>
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
          codigo_compra: '',
          rack: false,
          descripcionContainer: '',
		  descripcionSeccion: '',
          descripcionSector: '',
		  descripcionGondola: '',
		  piso: '',
		  nro_caja:''
        }
      }, 
      methods: {
      		mostrarModal(codigo){

      			// ------------------------------------------------------------------------

      			// LLAMAR AJAX PARA CARGAR DATATABLE 

      			this.obtenerDatosCompra(codigo);

      			// ------------------------------------------------------------------------

      			// LLAMAR AJAX PARA CARGAR CABECERA 

      			this.cargarCabecera(codigo);

      			// ------------------------------------------------------------------------

      			// LLAMAR MODAL COMPRA PRODUCTOS

      			$('#modalCompraProductos').modal('show');

      			// ------------------------------------------------------------------------
            	
            }, 

            cargarCabecera(codigo){

            	let me = this;

				Common.obtenerCabeceraCompraCommon(codigo).then(data=> {
		        		
        			me.rack = data.SISTEMA_DEPOSITO;
        			if(data.SISTEMA_DEPOSITO === true){
        				me.nro_caja = data.NRO_FACTURA;
        				me.descripcionContainer = data.DATOS_DEPOSITO.DESCRIPCION;
        				me.descripcionSeccion = data.DATOS_DEPOSITO.DESC_SECCION;
        				me.descripcionGondola = data.DATOS_DEPOSITO.DESC_GONDOLA;
        				me.descripcionSector = data.DATOS_DEPOSITO.DESC_SECTOR;
        				me.piso = data.DATOS_DEPOSITO.DESC_PISO;
        			}
		       	});
			},

            obtenerDatosCompra(codigo){

            	// ------------------------------------------------------------------------

            	let me = this;

            	// ------------------------------------------------------------------------

            	// PREPARAR DATATABLE 

	 			var tableProductosCompra = $('#CompraProductos').DataTable({
	                 	"processing": true,
	                 	"serverSide": true,
	                 	"destroy": true,
	                 	"bAutoWidth": true,
	                 	"select": true,
	                 	"ajax":{
	                 			"data": {
	                 				codigoCompra: codigo
	                 			},
	                             "url": "/compra/mostrar_productos",
	                             "dataType": "json",
	                             "type": "GET",
	                             "contentType": "application/json; charset=utf-8"
	                           },
	                    "columns": [
	                            { "data": "ITEM" },
	                            { "data": "COD_PROD" },
	                            { "data": "DESCRIPCION" },
	                            { "data": "CANTIDAD" },
	                            { "data": "COSTO" },
	                            { "data": "COSTO_TOTAL" },
	                            { "data": "LOTE" },
	                            { "data": "PRECIO" }
	                        ]    
	                });
                    
	 				// ------------------------------------------------------------------------

	 				// CARGAR CODIGO COMPRA

      				this.codigo_compra = codigo;

      				// ------------------------------------------------------------------------
            }
      },
        mounted() {

        }
    }
</script>