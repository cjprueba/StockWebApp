<template>
  <div>
    <div class="card border-bottom-info">
      <div class="card-body">
        <h5 class="card-title">Lotes</h5>
        <h6 class="card-subtitle mb-2 text-muted">Estos son los lotes que estan vencidos o por vencer</h6>

          <table id="tablaLote" class="table table-sm" style="width:100%">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Producto</th>
                <th scope="col">Descripcion</th>
                <th scope="col">Lote</th>
                <th scope="col">Cant.</th>
                <th scope="col">Venc.</th>
                <th scope="col">Imagen</th>
                <th scope="col">Estatus</th>
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

          var tableLote = $('#tablaLote').DataTable({
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
                                 "url": "/lote/vencidos",
                                 "dataType": "json",
                                 "type": "GET"
                               },
                        "columns": [
                            { "data": "C" },
                            { "data": "CODIGO" },
                            { "data": "DESCRIPCION" },
                            { "data": "LOTE" },
                            { "data": "CANTIDAD" },
                            { "data": "VENCIMIENTO" },
                            { "data": "IMAGEN" },
                            { "data": "ESTATUS" },
                            { "data": "ACCION" }
                        ],
                        "footerCallback": function(row, data, start, end, display) {
                          } 

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

          $("#tablaLote").css("font-size", 12);
         

          // ------------------------------------------------------------------------

          $('#tablaLote').on('click', 'tbody tr #mostrarDetalle', function() {

            // *******************************************************************

            // OBTENER COSTO DEL PRODUCTO DE LA TABLA 

            me.$emit('codigo_producto', tableLote.row($(this).parents('tr')).data().CODIGO);

            // *******************************************************************

          });

          // ------------------------------------------------------------------------

        }
    }
</script>