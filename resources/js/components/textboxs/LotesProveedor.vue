<template>
	<div>
			<div class="text-left"> 
                <label for="validationTooltip01">Lotes</label>
            </div>
			<div class="input-group" id="validationTooltip01" >
				<div class="input-group-prepend">
					<button  type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".lote-modal"><font-awesome-icon icon="search"/></button>
				</div>

                <input id="lote" class="custom-select custom-select-sm shadow-sm" type="text"  @input="$emit('input', $event.target.value)" v-bind:class="{ 'is-invalid': validar_lote }" :value="value" disabled>

			</div>

			<!-- ******************************************************************* -->

	        <!-- MODAL LOTES PRODUCTO -->

	                <div class="modal fade lote-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	                  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
	                    <div class="modal-content">
	                      <div class="modal-header">
	                        <h5 class="modal-title" id="exampleModalCenterTitle">lotes: </small></h5>
	                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                          <span aria-hidden="true">&times;</span>
	                        </button>
	                      </div>
	                      <div class="modal-body">
	                            <table id="tablaModalLotes" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
	                                <thead>
	                                    <tr>
	                                        <th>Codigo</th>
	                                        <th>Descripción</th>
	                                        <th>Lote</th>
	                                        <th>Precio Costo</th>
	                                        <th>Inicial</th>
	                                        <th>Stock</th>
	                                        <th>Vencimiento</th>
	                                        <th>Moneda</th>
	                                        <th>Decimal</th>
	                                        <th>ID LOTE</th>
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

	        <!-- ******************************************************************* -->
	</div>	
</template>
<script>
	export default {
      props: ['value'],
      data(){
        return {
          	productos: [],
          	loteProducto: '',
          	validar_lote: false,
          	tableModalLotes: ''
        }
      }, 
      methods: {

      		enviarCodigoPadre(codigo){

				// ------------------------------------------------------------------------

				// ENVIAR CODIGO

				this.$emit('lote_producto', codigo);

				// ------------------------------------------------------------------------

			}, 
			obtenerDatosLote(codigo, proveedor, moneda){

					// ------------------------------------------------------------------------

	        		// INICIAR VARIABLES 

	        		let me = this;

	        		// ------------------------------------------------------------------------
	        	

	        	 	// ------------------------------------------------------------------------
	                // >>
	                // INICIAR EL DATATABLE PRODUCTOS MODAL
	                // ------------------------------------------------------------------------
	                
	                this.tableModalLotes = $('#tablaModalLotes').DataTable({
	                        "processing": true,
	                        "serverSide": true,
	                        "destroy": true,
	                        "bAutoWidth": true,
	                        "select": true,
	                        "ajax":{
	                                 "data": {
	                                 	codigo: codigo,
	                                 	proveedor: proveedor,
	                                 	moneda: moneda,
	                                    "_token": $('meta[name="csrf-token"]').attr('content')
	                                 },
	                                 "url": "/proveedor/lote",
	                                 "dataType": "json",
	                                 "type": "POST"
	                               },
	                        "columns": [
	                            { "data": "COD_PROD" },
	                            { "data": "DESCRIPCION" },
	                            { "data": "LOTE" },
	                            { "data": "COSTO" },
	                            { "data": "INICIAL" },
	                            { "data": "STOCK" },
	                            { "data": "VENCIMIENTO" },
	                            { "data": "MONEDA" },
	                            { "data": "DECIMAL" },
	                            { "data": "LOTE_ID" }
	                        ],
	                        "columnDefs": [
	                        	{
					                "targets": [ 7 ],
					                "visible": false,
					                "searchable": false
					            },
					            {
					                "targets": [ 8 ],
					                "visible": false,
					                "searchable": false
					            },
					            {
					                "targets": [ 9 ],
					                "visible": false,
					                "searchable": false
					            }
					        ]          
	                    });

	                // ------------------------------------------------------------------------
	                
	                // SELECCIONAR UNA FILA - SE PUEDE BORRAR - SIN USO


	                $('#tablaModalLotes').on('click', 'tbody tr', function() {

	                    // *******************************************************************

	                    // CARGAR LOS VALORES A LAS VARIABLES DE PRODUCTO

	                    //me.loteProducto = me.tableModalLotes.row(this).data().LOTE;

	                    var data = {
	                    	descripcion: me.tableModalLotes.row(this).data().DESCRIPCION,
	                    	lote: me.tableModalLotes.row(this).data().LOTE,
	                    	cantidad: me.tableModalLotes.row(this).data().STOCK,
	                    	costo: me.tableModalLotes.row(this).data().COSTO,
	                    	inicial: me.tableModalLotes.row(this).data().INICIAL,
	                    	vencimiento: me.tableModalLotes.row(this).data().VENCIMIENTO,
	                    	moneda: me.tableModalLotes.row(this).data().MONEDA,
	                    	decimal: me.tableModalLotes.row(this).data().DECIMAL,
	                    	lote_id: me.tableModalLotes.row(this).data().LOTE_ID
	                    }

	                    me.enviarCodigoPadre(data);

	                    // *******************************************************************

	                    // CERRAR EL MODAL
	                     
	                     $('.lote-modal').modal('hide');

	                    // *******************************************************************

	                });

	                //FIN TABLA MODAL PRODUCTOS
	                // <<   
	                // ------------------------------------------------------------------------
	           
			}
      },
        mounted() {
        	
        	let me = this;
        	this.obtenerDatosLote();

        }
    }
</script>