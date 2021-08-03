<template>
  <div>
    <div class="card border-bottom-info">
      <div class="card-body">
        <h5 class="card-title">Stock Mínimo</h5>
        <h6 class="card-subtitle mb-2 text-muted">Estos son los productos que estan al limite de su stock</h6>

          <table id="tablaMinimo" class="table table-sm" style="width:100%">
            <thead>
              <tr>
                <th scope="col">Producto</th>
                <th scope="col">Descripcion</th>
                <th scope="col">Stock</th>
                <th scope="col">Mínimo</th>
                <th scope="col">Imagen</th>
                <th scope="col">Accion</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
      </div>
    </div>

    <!-- ------------------------------------------------------------------------ -->

  </div>  
</template>
<script>
    export default {
     
      data(){
        return {
        	codigo_producto: ''
        }
      }, 
      methods: {
      },  
        mounted() {
          console.log("entre");

          // ------------------------------------------------------------------------
          	
          let me = this;

          // ------------------------------------------------------------------------

          // PREPARAR DATATABLE 

          var tableMinimo = $('#tablaMinimo').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]],
                        "ajax":{
                                 "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/producto/minimo",
                                 "dataType": "json",
                                 "type": "GET"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "DESCRIPCION" },
                            { "data": "STOCK" },
                            { "data": "MINIMO" },
                            { "data": "IMAGEN" },
                            { "data": "ACCION" }
                        ],
                        "footerCallback": function(row, data, start, end, display) {
            }      
          });
                    
          // ------------------------------------------------------------------------

          // CAMBIAR TAMAÑO FUENTE 

          $("#tablaMinimo").css("font-size", 12);
         

          // ------------------------------------------------------------------------

          $('#tablaMinimo').on('click', 'tbody tr #Detalle', function() {

           	// *******************************************************************

           	// OBTENER COSTO DEL PRODUCTO DE LA TABLA 

           	me.$emit('codigo_producto', tableMinimo.row($(this).parents('tr')).data().CODIGO);

            // *******************************************************************

          });

          // ------------------------------------------------------------------------

          $('#tablaMinimo').on('click', 'tbody tr #Baja', function() {

           	// *******************************************************************

           	// DAR DE BAJA

      		Swal.fire({
				title: 'Estas seguro ?',
				text: "Dar de baja al producto "+tableMinimo.row($(this).parents('tr')).data().CODIGO+" !",
				type: 'warning',
				showLoaderOnConfirm: true,
				showCancelButton: true,
				confirmButtonColor: '#d33',
				cancelButtonColor: '#3085d6',
				confirmButtonText: 'Si, dar de baja !',
				cancelButtonText: 'Cancelar',
				preConfirm: () => {
				    return Common.darDeBajaProductoCommon(tableMinimo.row($(this).parents('tr')).data().CODIGO).then(data => {
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

				if (result.value.response) {

					Swal.fire(
						'Guardado!',
						'Se ha dado de baja correctamente !',
						'success'
					)

					// ------------------------------------------------------------------------

					// RECARGAR TABLA 
				  	
					tableMinimo.ajax.reload( null, false );

					// ------------------------------------------------------------------------

				}
			})

            // *******************************************************************

          });

          // ------------------------------------------------------------------------
        }
    }
</script>