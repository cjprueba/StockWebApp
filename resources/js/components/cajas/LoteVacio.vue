<template>
  <div>
    <div class="card border-bottom-danger">
      <div class="card-body">
        <div class="card-head">
          <h5 class="card-title">Stock Cero Hoy</h5>
          <h6 class="card-subtitle mb-2 text-muted">Estos son los productos que se quedaron sin stock el día de hoy</h6>
          <button class="btn btn-primary float-right btn-sm ">Ver más</button>
        </div>
          <table id="tablaLoteVacio" class="table table-sm" style="width:100%">
            <thead>
              <tr>
                <th scope="col">Producto</th>
                <th scope="col">Descripcion</th>
                <th scope="col">Stock</th>
                <th scope="col">Imagen</th>
                <th scope="col">Accion</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>

        <!-- <a href="#" class="card-link">Card link</a>
        <a href="#" class="card-link">Another link</a> -->
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

          // ------------------------------------------------------------------------
            
          let me = this;

          // ------------------------------------------------------------------------

          // PREPARAR DATATABLE 

          var tableLote = $('#tablaLoteVacio').DataTable({
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
                                 "url": "/lote/terminados",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "COD_PROD" },
                            { "data": "DESCRIPCION" },
                            { "data": "STOCK" },
                            { "data": "IMAGEN" },
                            { "data": "ACCION" }
                        ]

                        // PARA COLOREAR UNA FILA 
                        // ,
                        // "columnDefs": [
                        //     {
                        //         "targets": [ 8 ],
                        //         "visible": false,
                        //         "searchable": false
                        //     }
                        // ],
                        // "createdRow": function( row, data, dataIndex){
                        //       $(row).addClass(data['ESTATUS']);
                        // }      
          });
                    
          // ------------------------------------------------------------------------

          // CAMBIAR TAMAÑO FUENTE 

          $("#tablaLoteVacio").css("font-size", 12);
          tableLote.columns.adjust().draw();

          // ------------------------------------------------------------------------

          $('#tablaLoteVacio').on('click', 'tbody tr #mostrarDetalle', function() {

            // *******************************************************************

            // OBTENER COSTO DEL PRODUCTO DE LA TABLA 

            me.$emit('codigo_producto', tableLote.row($(this).parents('tr')).data().COD_PROD);

            // *******************************************************************

          });

          // ------------------------------------------------------------------------

        }
    }
</script>