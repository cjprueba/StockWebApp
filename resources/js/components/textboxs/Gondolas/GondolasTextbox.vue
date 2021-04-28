<template>
  <div>
      <div class="text-left"> 
                <label for="validationTooltip01">Codigo Gondola:</label>
            </div>
      <div class="input-group" id="validationTooltip01" >
        <div class="input-group-prepend">
          <button  type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".gondola-modal"><font-awesome-icon icon="search"/></button>
        </div>


                <input ref="codigo" id="codigo_usuario" class="custom-select custom-select-sm shadow-sm" type="text"  :value="nombre" @input="$emit('input', $event.target.value)" v-bind:class="{ 'is-invalid': validarGondola }" v-on:keyup.prevent.13="enterGondola($event.target.value)" v-on:blur="enterGondola($event.target.value)">

      </div>

      <!-- ******************************************************************* -->

          <!-- MODAL PRODUCTOS -->

                  <div class="modal fade gondola-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalCenterTitle">Telas: </small></h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                              <table id="tablaModalGondola" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
                                  <thead>
                                      <tr>
                                          <th>Codigo</th>
                                          <th>Descripcion</th>
                                          <th>Seccion</th>
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
    props: ['nombre','validarGondola'],
    data(){
      return {
          productos: [],
          roles:[]
      }
    }, 
     
    methods: {
      
      enviarCodigoPadre(nombre,id,descripcion,id_seccion){
        // ------------------------------------------------------------------------

        // ENVIAR CODIGO
        this.$emit('nombre_gondola',nombre);
        this.$emit('id', id);
        this.$emit('descripcion', descripcion);
        this.$emit('seccion', id_seccion);
        this.$emit('existe_gondola',true);
        // ------------------------------------------------------------------------
      },
      enterGondola(Codigo){
      // ------------------------------------------------------------------------
         let me =this;
      // LLAMAR FUNCION PARA FILTRAR PRODUCTOS

      
         Common.filtrarGondolasCommon(Codigo).then(data => {

                      if(data.response===true){

                       me.enviarCodigoPadre(Codigo,data.Gondolas[0].CODIGO,data.Gondolas[0].DESCRIPCION, data.Gondolas[0].ID_SECCION);
      

                       }else{
                        
                        me.$emit('nombre_gondola',Codigo); 
                        me.$emit('id', id);
                        me.$emit('descripcion', descripcion);
                        me.$emit('seccion', id_seccion);
                        me.$emit('existe_gondola',false);
                       }
                     
                  })

        // ------------------------------------------------------------------------
      },

      recargar(){
        var table = $('#tablaModalGondola').DataTable();   
        table.ajax.reload( null, false );
      },

    },




    mounted() {          
      // ------------------------------------------------------------------------

      // INICIAR VARIABLES 
      let me = this;

      // ------------------------------------------------------------------------
        
      

      // INICIAR EL DATATABLE PRODUCTOS MODAL
       var table= $('#tablaModalGondola').DataTable({
          "processing": true,
          "serverSide": true,
          "destroy":true,
          "bAutoWidth": true,
          "select": true,
          "ajax":{
                   "data": {
                      "_token": $('meta[name="csrf-token"]').attr('content')
                   },
                   "url": "/gondolasDatatable",
                   "dataType": "json",
                   "type": "POST"
                 },
          "columns": [
              { "data": "CODIGO" },
              { "data": "DESCRIPCION" },
              { "data": "SECCION" }
          ]      
        });
        // ------------------------------------------------------------------------
                
        // SELECCIONAR UNA FILA - SE PUEDE BORRAR - SIN USO
        $('#tablaModalGondola').on('click', 'tbody tr', function() {
          // *******************************************************************
          // CARGAR LOS VALORES A LAS VARIABLES DE PRODUCTO
          // me.codigo = tableGondola.row(this).data().CODIGO;
          // me.description = tableGondola.row(this).data().DESCRIPCION; 
          // console.log(tableGondola.row(this).data());

          me.CODIGO = table.row(this).data().CODIGO;
          Common.filtrarGondolasCommon(me.CODIGO).then(data => { 
            me.enviarCodigoPadre(me.CODIGO,
              data.Gondolas[0].CODIGO,
              data.Gondolas[0].DESCRIPCION,
              data.Gondolas[0].ID_SECCION);      
          })

            // CERRAR EL MODAL
          $('.gondola-modal').modal('hide');
        });
        //FIN TABLA MODAL PRODUCTOS

          
    }
  }
</script>