<template>
	<div>

		<!--  ------------------------------------------------------------------------ -->

		<!-- BOTON CLIENTES  -->

		<button class="btn btn-primary btn-sm btn-block" data-toggle="modal" data-target=".busqueda-cliente-modal"><small>Cliente</small></button>

		<!--  ------------------------------------------------------------------------ -->

	        <!-- MODAL PRODUCTOS -->

	                <div class="modal fade busqueda-cliente-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	                  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
	                    <div class="modal-content">
	                      <div class="modal-header">
	                        <h5 class="modal-title" id="exampleModalCenterTitle">Clientes: </small></h5>
	                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                          <span aria-hidden="true">&times;</span>
	                        </button>
	                      </div>
	                      <div class="modal-body">
	                            <table id="tablaModalClientes" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
	                                <thead>
	                                    <tr>
	                                        <th>Codigo</th>
	                                        <th>CI</th>
	                                        <th>Nombre</th>
	                                        <th>RUC</th>
	                                        <th>Dirección</th>
	                                        <th>Ciudad</th>
	                                        <th>Telefono</th>
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
        	cliente: {
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

		  },
		  procesarFormas(){

          	// ------------------------------------------------------------------------

            // MOSTRAR LA PREGUNTA DE ELIMINAR 

            $('.busqueda-cliente-modal').modal('show');

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
                
                var tableClientes = $('#tablaModalClientes').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/cliente/datatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "CI" },
                            { "data": "NOMBRE" },
                            { "data": "RUC" },
                            { "data": "DIRECCION" },
                            { "data": "CIUDAD" },
                            { "data": "TELEFONO" }
                        ]      
                    });

                // ------------------------------------------------------------------------
                
                // CAMBIAR TAMAÑO FUENTE 

                $("#tablaModalClientes").css("font-size", 12);
                tableClientes.columns.adjust()
                .responsive.recalc()
                .draw();

                // ------------------------------------------------------------------------

                // SELECCIONAR UNA FILA - SE PUEDE BORRAR - SIN USO


                $('#tablaModalClientes').on('click', 'tbody tr', function() {

                    // *******************************************************************

                    // CARGAR LOS VALORES A LAS VARIABLES DE PRODUCTO

                    me.cliente.CODIGO = tableClientes.row(this).data().CODIGO;
                    me.cliente.NOMBRE = tableClientes.row(this).data().NOMBRE;

                    me.enviarPadre(me.cliente.CODIGO, me.cliente.NOMBRE);

                    // *******************************************************************

                    // CERRAR EL MODAL
                     
                    $('.busqueda-cliente-modal').modal('hide');

                    // *******************************************************************

                });

                //FIN TABLA MODAL PRODUCTOS
                // <<   
                // ------------------------------------------------------------------------
            });   
        }
    }
</script>