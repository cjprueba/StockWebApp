<template>
	<div class="container-fluid">
		
    <div class="row">

      <div class="col-md-12 text-right">

      <!-- ------------------------------------------------------------------------ -->

      <!-- BOTON CAMARA -->

      <camara-bardcode @codigo_camara="codigo_camara"></camara-bardcode>
      
      <!-- ------------------------------------------------------------------------ -->

      </div>
    
    </div>
    
    <div class="mt-3" v-if="$can('producto.mostrar')">
			<table id="tablaModalProductos" class="table table-striped table-hover table-bordered table-sm mb-3" style="width:100%">
		        <thead>
		            <tr>
		                <th>Codigo</th>
		                <th>Descripcion</th>
		                <th>Precio Venta</th>
		                <th>Precio Costo</th>
		                <th>Precio Mayorista</th>
		                <th>Stock</th>
		                <th>Imagen</th>
		                <th>Accion</th>
		            </tr>
		        </thead>
		     </table>
		</div>

		<!-- ------------------------------------------------------------------------ -->

		<div v-else>
      <cuatrocientos-cuatro></cuatrocientos-cuatro>
    </div>

    <!-- ------------------------------------------------------------------------ -->

		<!-- MODAL DETALLE PRODUCTO -->

		<producto-detalle ref="detalle_producto" :codigo="codigo"></producto-detalle>

		<!-- ------------------------------------------------------------------------ -->

    

	</div>
</template>
<script>
	export default {
      data(){
        return {
          	productos: [],
          	codigo: ''
        }
      }, 
      methods: {
        codigo_camara(codigo){

          // ------------------------------------------------------------------------

          // CODIGO DE CAMARA RECIBIDO DEL COMPONENTE

          this.codigo = codigo;
          this.$refs.detalle_producto.mostrar();

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
                
                var tableProductos = $('#tablaModalProductos').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "responsive": true,
                        "rowReorder": {
                            "selector": 'td:nth-child(2)'
                        },
                        "ajax":{
                                 "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/productoDatatableGeneral",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "DESCRIPCION" },
                            { "data": "PREC_VENTA" },
                            { "data": "PRECOSTO" },
                            { "data": "PREMAYORISTA" },
                            { "data": "STOCK" },
                            { "data": "IMAGEN" },
                            { "data": "ACCION" }
                        ]      
                    });

                // ------------------------------------------------------------------------

            	// AJUSTAR COLUMNAS DE ACUERDO AL DATO QUE CONTIENEN
	
				$("table#tablaModalProductos").css("font-size", 12);
            	tableProductos.columns.adjust().draw();

            	// ------------------------------------------------------------------------
                
                // SELECCIONAR UNA FILA - SE PUEDE BORRAR - SIN USO


                $('#tablaModalProductos').on('click', 'tbody tr', function() {

                    // *******************************************************************

                    // CARGAR LOS VALORES A LAS VARIABLES DE PRODUCTO


                    // *******************************************************************

                });

                //FIN TABLA MODAL PRODUCTOS
                // <<   
                // ------------------------------------------------------------------------

                // MOSTRAR MODAL PRODUCTO

                $('#tablaModalProductos').on('click', 'tbody tr #mostrarDetalle', function() {

                	// *******************************************************************

                	// OBTENER DATOS DEL PRODUCTO DATATABLE JS

                	me.codigo = tableProductos.row($(this).parents('tr')).data().CODIGO;
                	me.$refs.detalle_producto.mostrar();
                	// OBTENER IMAGEN - UTIL
                	// me.imagen = $(tableProductos.row($(this).parents('tr')).data().IMAGEN).attr('src');

                    // *******************************************************************

                });

                // ------------------------------------------------------------------------

                // EDITAR PRODUCTO 

                $('#tablaModalProductos').on('click', 'tbody tr #editarProducto', function() {
                  me.$router.push('/pr2/'+ tableProductos.row($(this).parents('tr')).data().CODIGO + '');
                });

                // ------------------------------------------------------------------------

                // MOSTRAR MODAL PRODUCTO

                $('#tablaModalProductos').on('click', 'tbody tr #eliminarProducto', function() {

                    // *******************************************************************

                    // OBTENER DATOS DEL PRODUCTO DATATABLE JS
                    
                    Swal.fire({
                      title: '¿ Eliminar ?',
                      text: 'Eliminar el producto',
                      type: 'warning',
                      showLoaderOnConfirm: true,
                      showCancelButton: true,
                      confirmButtonColor: 'btn btn-success',
                      cancelButtonColor: '#d33',
                      confirmButtonText: 'Si !',
                      cancelButtonText: 'Cancelar',
                      preConfirm: () => {

                        return Common.eliminarProductoCommon(tableProductos.row($(this).parents('tr')).data().CODIGO).then(data => {

                            // ------------------------------------------------------------------------

                            // REVISAR SI HAY DATOS 

                            if (!data.response === true) {
                              throw new Error(data.statusText);
                            } 

                            // ------------------------------------------------------------------------

                            return true;

                            // ------------------------------------------------------------------------

                        }).catch(error => {
                            Swal.showValidationMessage(
                              `Request failed: ${error}`
                            )
                        });
                      }

                    }).then((result) => {
                      if (result.value) {
                        Swal.fire(
                                  'Eliminado !',
                                  'Se ha eliminado el producto correctamente !',
                                  'success'
                        )

                        // ------------------------------------------------------------------------

                      }
                    })
                    //me.codigo = tableProductos.row($(this).parents('tr')).data().CODIGO;

                    // *******************************************************************

                });

                // ------------------------------------------------------------------------
            });    
        }
    }
</script>