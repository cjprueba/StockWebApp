<template>
  <div>
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Lotes</h5>
        <h6 class="card-subtitle mb-2 text-muted">Estos son los lotes que estan vencidos o por vencer</h6>

          <table id="tablaLote" class="table table-sm">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Producto</th>
                <th scope="col">Descripcion</th>
                <th scope="col">Lote</th>
                <th scope="col">Cant.</th>
                <th scope="col">Venc.</th>
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
  </div>  
</template>
<script>
    export default {
     
      data(){
        return {
        }
      }, 
      methods: {
      },  
        mounted() {


          // ------------------------------------------------------------------------

          // PREPARAR DATATABLE 

          var tableLote = $('#tablaLote').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/lote/vencidos",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "C" },
                            { "data": "CODIGO" },
                            { "data": "DESCRIPCION" },
                            { "data": "LOTE" },
                            { "data": "CANTIDAD" },
                            { "data": "VENCIMIENTO" },
                            { "data": "IMAGEN" },
                            { "data": "ACCION" },
                            { "data": "ESTATUS" }
                        ],
                        "columnDefs": [
                            {
                                "targets": [ 8 ],
                                "visible": false,
                                "searchable": false
                            }
                        ],
                        "createdRow": function( row, data, dataIndex){
                              $(row).addClass(data['ESTATUS']);
                        }      
          });
                    
          // ------------------------------------------------------------------------

          // CAMBIAR TAMAÑO FUENTE 

          $("#tablaLote").css("font-size", 12);
          tableLote.columns.adjust().draw();

          // ------------------------------------------------------------------------

        }
    }
</script>