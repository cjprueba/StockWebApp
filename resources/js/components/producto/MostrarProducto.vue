<template>
	<div class="container-fluid">
		
    <div class="row">
      <!-- -->
        
      

    
    
    <!-- v-if="$can('producto.mostrar')" -->
    <div class="mt-3 d-none d-xl-block d-none d-lg-block d-xl-none col-lg-12" >
			<table id="tablaModalProductos" class="table table-striped table-hover table-bordered table-lg mb-3" style="width:100%">
		    <thead>
		      <tr>
		        <th>Codigo</th>
		        <th>Descripcion</th>
            <th>Categoria</th>
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
    
    <div class="mt-3 d-none d-md-block d-lg-none" >
			<div class="container">
			<div class="row">
              <div class="col-xs-12 col-sm-4 col-md-1 col-lg-1 col-xl-1 text-left">

        <!-- ------------------------------------------------------------------------ -->

        <!-- BOTON CAMARA -->
        
        <camara-bardcode @codigo_camara="codigo_camara"></camara-bardcode>

        <!-- ------------------------------------------------------------------------ -->

      </div>
      <div class="mt-4 col-md-11 mb-4">
        <input class="form-control  " type="text" placeholder="Buscar...">
      </div>
      </div>

        <!-- Cabecera -->
        <div class="shadow mb-4 bg-success rounded text-white text-center">
          BERSHKA ROPA PANTALON FEMENINO 32 NEGRO JEANS
          <br>
          Código: 00001211800328
        </div>
      
        <!-- Portfolio Item Row --> 
      <div class="row">

        <div class="col-md-7">
          <img class="img-fluid rounded" src="http://placehold.it/750x500" alt="">
        </div>

        <div class="col-md-5">
          <table class="table  table-sm"> <!-- Tabla de Precios --> 
            <thead class="bg-success text-white">
              <tr>
                <th colspan="2" class="text-center"><h5>Precios</h5></th>
              </tr>
            </thead>
            <tbody class="table-warning text-dark">
              <tr>
                <th scope="row">Venta</th>
                <td>$21.00</td>
              </tr>
              <tr>
                <th scope="row">Mayorista</th>
                <td>$21.00</td>
              </tr>
              <tr>
                <th scope="row">Costo</th>
                <td>$21.00</td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
      <br>
      <table class="table  table-sm"> <!-- Tabla de Información --> 
        <thead class="bg-success text-white">
          <tr>
            <th colspan="2" class="text-center"><h5>Información</h5></th>
          </tr>
        </thead>
        <tbody class="table-warning text-dark">
          <tr>
            <th scope="row">Stock</th>
            <td>25</td>
          </tr>
          <tr>
            <th scope="row">Cod. Interno</th>
            <td>8005535</td>
          </tr>
          <tr>
            <th scope="row">Categoría</th>
            <td>Ropa</td>
          </tr>
          <tr>
            <th scope="row">Sub. Categoría</th>
            <td>Pantalón</td>
          </tr>
        </tbody>
      </table>
    </div>

    </div>
  </div>
		<!-- ------------------------------------------------------------------------ -->

		<!-- <div v-else>
      <cuatrocientos-cuatro></cuatrocientos-cuatro>
    </div> -->

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

            var tableProductos = $('#tablaModalProductos').DataTable();

        	 	// ------------------------------------------------------------------------
                // >>
                // INICIAR EL DATATABLE PRODUCTOS MODAL
                // ------------------------------------------------------------------------
                
                // FILTRO POR COLUMNA 

                $('#tablaModalProductos thead tr').clone(true).appendTo( '#tablaModalProductos thead' );
                $('#tablaModalProductos thead tr:eq(1) th').each( function (i) {
                    var title = $(this).text();
                    $(this).html( '<input type="text" class="form-control form-control-sm" placeholder="Buscar '+title+'" />' );
             
                    $( 'input', this ).on( 'keyup change', function () {
                        if ( tableProductos.column(i).search() !== this.value ) {
                            tableProductos
                                .column(i)
                                .search( this.value )
                                .draw();
                        }
                    } );
                } );

                // ------------------------------------------------------------------------

                // 

                var tableProductos = $('#tablaModalProductos').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "orderCellsTop": true,
                        "fixedHeader": true,
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
                            { "data": "CATEGORIA" },
                            { "data": "PREC_VENTA" },
                            { "data": "PRECOSTO" },
                            { "data": "PREMAYORISTA" },
                            { "data": "STOCK", "searchable": false },
                            { "data": "IMAGEN", "searchable": false },
                            { "data": "ACCION", "searchable": false }
                        ],
                        "columnDefs": [
                            { "targets": [0, 1, 2, 3, 4, 5], "searchable": true},
                            { "targets": '_all', "searchable": false },
                             
                            //{ width: "40%", "targets": [5] }
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