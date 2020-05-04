<template>
	<div>

		<!--  ------------------------------------------------------------------------ -->

		<!-- BOTON CLIENTES  -->

		<button class="btn btn-primary btn-sm btn-block mt-2" data-toggle="modal" data-target=".busqueda-vendedor-modal"><small>Vendedor</small></button>

		<!--  ------------------------------------------------------------------------ -->

	        <!-- MODAL PRODUCTOS -->

	                <div class="modal fade busqueda-vendedor-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	                  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
	                    <div class="modal-content">
	                      <div class="modal-header">
	                        <h5 class="modal-title" id="exampleModalCenterTitle">Vendedores: </small></h5>
	                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                          <span aria-hidden="true">&times;</span>
	                        </button>
	                      </div>
	                      <div class="modal-body">
	                            <table id="tablaModalVendedores" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
	                                <thead>
	                                    <tr>
	                                        <th>Codigo</th>
	                                        <th>CI</th>
	                                        <th>Nombre</th>
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

	        <!--  ------------------------------------------------------------------------ -->

	</div>	
</template>
<script>
	export default {
      data(){
        return {
        	vendedor: {
        		CODIGO: '',
        		NOMBRE: ''
        	}
        }
      }, 
      methods: {
      	  enviarPadre(cedula, nombre){

				// ------------------------------------------------------------------------

				// ENVIAR CEDULA

				this.$emit('ci', cedula);
                this.$emit('nombre', nombre);

				// ------------------------------------------------------------------------

		  }
      },
        mounted() {
        	
        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES 

        	let me = this;

        	// ------------------------------------------------------------------------
        	
        	$(document).ready( function () {

        	 	// ------------------------------------------------------------------------
                // >>
                // INICIAR EL DATATABLE PRODUCTOS MODAL
                // ------------------------------------------------------------------------
                
                var tableVendedores = $('#tablaModalVendedores').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/vendedor/datatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "CI" },
                            { "data": "NOMBRE" }
                        ]      
                    });

                // ------------------------------------------------------------------------
                
                // CAMBIAR TAMAÑO FUENTE 

                $("#tablaModalVendedores").css("font-size", 12);
                tableVendedores.columns.adjust()
                .responsive.recalc()
                .draw();

                // ------------------------------------------------------------------------

                // SELECCIONAR UNA FILA - SE PUEDE BORRAR - SIN USO

                $('#tablaModalVendedores').on('click', 'tbody tr', function() {

                    // *******************************************************************

                    // CARGAR LOS VALORES A LAS VARIABLES DE PRODUCTO

                    me.vendedor.CODIGO = tableVendedores.row(this).data().CODIGO;
                    me.vendedor.NOMBRE = tableVendedores.row(this).data().NOMBRE;
                    me.enviarPadre(me.vendedor.CODIGO, me.vendedor.NOMBRE);

                    // *******************************************************************

                    // CERRAR EL MODAL
                     
                    $('.busqueda-vendedor-modal').modal('hide');

                    // *******************************************************************

                });

                //FIN TABLA MODAL PRODUCTOS
                // <<   
                // ------------------------------------------------------------------------
            });   
        }
    }
</script>