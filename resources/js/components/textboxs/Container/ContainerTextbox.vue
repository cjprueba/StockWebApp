<template>
    <div>
            <div class="text-left"> 
                <label for="validationTooltip01">Codigo Container:</label>
            </div>
            <div class="input-group" id="validationTooltip01" >
                <div class="input-group-prepend">
                    <button  type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".container-table"><font-awesome-icon icon="search"/></button>
                </div>


                <input ref="codigo" id="codigo_usuario" class="custom-select custom-select-sm shadow-sm" type="text"  :value="nombre" @input="$emit('input', $event.target.value)" v-bind:class="{ 'is-invalid': validarContainer }" v-on:keyup.prevent.13="enterContainer($event.target.value)" v-on:blur="enterContainer($event.target.value)">

            </div>

            <!-- ******************************************************************* -->

            <!-- MODAL PRODUCTOS -->

                    <div class="modal fade container-table" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalCenterTitle">Container: </small></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                                <table id="tablaModalContainer" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Codigo</th>
                                            <th>Nombre</th>
                                            <th>Fecha Inicio</th>
                                            <th>Fecha Primera Compra</th>
                                            <th>Fecha Ultima Compra</th>


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
      props: ['nombre','validarContainer'],
      data(){
        return {
            productos: [],
            roles:[]
        }
      }, 
       
      methods: {
        recargar(){
          var tableContainer = $('#tablaModalContainer').DataTable();
          tableContainer.ajax.reload( null, false );
        },
       enterContainer(Codigo){

        // ------------------------------------------------------------------------
           let me =this;
        // LLAMAR FUNCION PARA FILTRAR PRODUCTOS
        
           Common.filtrarContainerCommon(Codigo).then(data => {

                        if(data.response===true){

                         me.enviarCodigoPadre(Codigo,data.container[0].CODIGO,data.container[0].DESCRIPCION,data.container[0].FECHA_INICIO);
        

                         }else{
                          
                           me.$emit('nombre_transporte',Codigo);
                           me.$emit('existe_container',false);
                         }
                       
                    })

        // ------------------------------------------------------------------------

      },
       enviarCodigoPadre(nombre,id,descripcion,fecha_inicio){

                // ------------------------------------------------------------------------

                // ENVIAR CODIGO
         this.$emit('nombre_container',nombre);
         this.$emit('id', id);
         this.$emit('descripcion', descripcion);
         this.$emit('fecha_inicio', fecha_inicio);

         this.$emit('existe_container',true);

                // ------------------------------------------------------------------------

            }, vaciarDevolver(){

                // ------------------------------------------------------------------------

                // VACIAR Y DEVOLVER FOCUS AL TEXTBOX

                $("#codigo_Producto").focus();
    
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
                
                var tableContainer = $('#tablaModalContainer').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy":true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/containerDatatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "DESCRIPCION" },
                            { "data": "FECHA_INICIO" },
                            { "data": "FECHAPRIME_C" },
                            { "data": "FECHULT_C" }
                        ]      
                    });
                

                // ------------------------------------------------------------------------
                
                // SELECCIONAR UNA FILA - SE PUEDE BORRAR - SIN USO


                $('#tablaModalContainer').on('click', 'tbody tr', function() {

                

                    // *******************************************************************

                    // CARGAR LOS VALORES A LAS VARIABLES DE PRODUCTO


                    me.codigo = tableContainer.row(this).data().CODIGO;
                    me.description = tableContainer.row(this).data().DESCRIPCION;  
                    Common.filtrarContainerCommon(me.codigo).then(data => {
                        
                      


                           me.enviarCodigoPadre(me.codigo,data.container[0].CODIGO,me.description,data.container[0].FECHA_INICIO);
                           
                    })
                  
                    // *******************************************************************

                    // CERRAR EL MODAL
                     
                     $('.container-table').modal('hide');

                    // *******************************************************************

                });

                //FIN TABLA MODAL PRODUCTOS
                // <<   
                // ------------------------------------------------------------------------
            });    
        }
    }
</script>