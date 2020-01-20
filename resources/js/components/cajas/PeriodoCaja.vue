<template>
  <div>
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Periodos</h5>
        <h6 class="card-subtitle mb-2 text-muted">Estos son los productos cuyos periodos de ventas han superado su limite</h6>

          <table id="tablaPeriodo" class="table table-sm">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Producto</th>
                <th scope="col">Descripcion</th>
                <th scope="col">Venta</th>
                <th scope="col">Imagen</th>
                <th scope="col">Accion</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>

        <a href="#" class="card-link">Card link</a>
        <a href="#" class="card-link">Another link</a>
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

          var tableLote = $('#tablaPeriodo').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "url": "/ventas/periodos",
                                 "dataType": "json",
                                 "type": "GET",
                                 "contentType": "application/json; charset=utf-8"
                               },
                        "columns": [
                            { "data": "C" },
                            { "data": "CODIGO" },
                            { "data": "DESCRIPCION" },
                            { "data": "FEC_VENTA" },
                            { "data": "IMAGEN" },
                            { "data": "ACCION" }
                        ]     
          });
                    
          // ------------------------------------------------------------------------

          // CAMBIAR TAMAÑO FUENTE 

          $("#tablaPeriodo").css("font-size", 12);
          tableLote.columns.adjust().draw();

          // ------------------------------------------------------------------------

        }
    }
</script>