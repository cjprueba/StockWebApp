<template>
	<div class="container-fluid">
			<div class="modal fade" id="modalOrdenProductos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-xl" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title text-primary" id="exampleModalLabel"><small>Detalle de Orden: {{codigo_orden}} </small></h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		      	<div class="row mt-2">
		      		<div class="col-6">
						<span class="float-left"><strong> Cliente: </strong> {{cliente.nombre}}</span><br/>
		      		</div>
		      		<div class="col-6">
						<span class="float-left"><strong> R.U.C/C.I: </strong> {{cliente.documento}}</span><br/>
		      		</div>
		      	</div>
		      	<div class="row mt-2">
					<div class="col-6">
						<span class="float-left"><strong> Departamento: </strong> {{cliente.estado}}</span><br/>
					</div>
					<div class="col-6">
						<span class="float-left"><strong> Teléfono: </strong> {{cliente.telefono}}</span><br/>
					</div>
		      	</div>
		      	<div class="row mt-2">
		      		<div class="col-6">
						<span><span class="float-left"><strong> Ciudad: </strong> {{cliente.ciudad}} </span></span><br/>
					</div>
		      	</div>

		      	<div class="row mt-2">
		      		<div class="col">
						<span class="float-left"><strong> Dirección: </strong> {{cliente.direccion}}</span><br/>
					</div>
				</div>

				<div class="row mt-2 mb-3">
		      		<div class="col">
						<span><span class="float-left"><strong> Nota del Cliente: </strong> {{cliente.nota}} </span></span><br/>
					</div>
				</div>

		        <table id="ordenProductos" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
					            <thead>
					                <tr>
					                    <th>ITEM</th>
					                    <th>Código</th>
					                    <th>Descripción</th>
					                    <th>Cantidad</th>
					                    <th>Precio</th>
					                    <th>Total</th>
					                </tr>
					            </thead>
					            <tbody>
					                <td></td>
					            </tbody>
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
          codigo_orden: '',
          cliente: {
          	nombre: '',
          	direccion: '',
          	ciudad: '',
          	telefono: '',
          	documento: '',
          	estado:'',
          	ruc:'',
          	nota: ''
          }
        }
      }, 
      methods: {

      		mostrarModal(codigo){

      			// ------------------------------------------------------------------------

      			// LLAMAR AJAX PARA CARGAR DATATABLE 

      			this.obtenerDatosOrden(codigo);

      			// ------------------------------------------------------------------------

      			// LLAMAR MODAL PRODUCTOS

      			$('#modalOrdenProductos').modal('show');

      			// ------------------------------------------------------------------------
            	
            }, 

            cargarCabecera(){

				Common.obtenerCabeceraOrdenCommon(this.codigo_orden).then(data=> {

		        		this.cliente.nombre = data.CLIENTE;
		        		this.cliente.direccion = data.DIRECCION_1;
		        		this.cliente.ciudad = data.CIUDAD;
		        		this.cliente.telefono = data.CELULAR;
		        		this.cliente.documento = data.DOCUMENTO;
		        		this.cliente.estado = data.ESTADO;
		        		this.cliente.ruc = data.RUC;	
		        		this.cliente.nota = data.NOTA;					
		        		// this.codigo_remision = 1;
				

		       	});
			},

            obtenerDatosOrden(codigo){

            	// ------------------------------------------------------------------------

            	let me = this;

            	// ------------------------------------------------------------------------

            	// PREPARAR DATATABLE 

	 			var tableProductosOrden = $('#ordenProductos').DataTable({
	                 	"processing": true,
	                 	"serverSide": true,
	                 	"destroy": true,
	                 	"bAutoWidth": true,
	                 	"select": true,
	                 	"ajax":{
	                 			"data": {
	                 				codigoOrden: codigo
	                 			},
	                             "url": "/ordenMostrarProductos",
	                             "dataType": "json",
	                             "type": "GET",
	                             "contentType": "application/json; charset=utf-8"
	                           },
	                    "columns": [
	                            { "data": "ITEM" },
	                            { "data": "SKU" },
	                            { "data": "DESCRIPCION" },
	                            { "data": "CANTIDAD" },
	                            { "data": "PRECIO" },
	                            { "data": "TOTAL" }
	                            // { "data": "PORC_DESCUENTO" },
	                            // { "data": "TOTAL_DESCUENTO" }
	                        ]    
	                });
                    
	 				// ------------------------------------------------------------------------

	 				// CARGAR CODIGO

      				this.codigo_orden = codigo;
      				this.cargarCabecera();

      				// ------------------------------------------------------------------------
            }
      },
      mounted() {

      }
    }
</script>