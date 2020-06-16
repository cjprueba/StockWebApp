<template>
    <div>
            <div class="text-left"> 
                <label for="validationTooltip01">Codigo Transporte:</label>
            </div>
            <div class="input-group" id="validationTooltip01" >
                <div class="input-group-prepend">
                    <button  type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".transporte-table"><font-awesome-icon icon="search"/></button>
                </div>


                <input ref="codigo" id="codigo_usuario" class="custom-select custom-select-sm shadow-sm" type="text"  :value="nombre" @input="$emit('input', $event.target.value)" v-bind:class="{ 'is-invalid': validarTransporte }" v-on:keyup.prevent.13="enterProveedor($event.target.value)" v-on:blur="enterProveedor($event.target.value)">

            </div>

            <!-- ******************************************************************* -->

            <!-- MODAL PRODUCTOS -->

                    <div class="modal fade transporte-table" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalCenterTitle">Transporte: </small></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                                <table id="tablaModalTransporte" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Codigo</th>
                                            <th>Nombre</th>
                                            <th>RUC</th>
                                            <th>Direccion</th>
                                            <th>Telefono</th>43
                                            <th>Celular</th>
                                            <th>Email</th>


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
      props: ['nombre','validarTransporte'],
      data(){
        return {
            productos: [],
            roles:[]
        }
      }, 
       
      methods: {
        recargar(){
          var tableTransporte = $('#tablaModalTransporte').DataTable();
       tableTransporte.ajax.reload( null, false );
        },
       enterProveedor(Codigo){

        // ------------------------------------------------------------------------
           let me =this;
        // LLAMAR FUNCION PARA FILTRAR PRODUCTOS
        
           Common.filtrarTransporteCommon(Codigo).then(data => {

                        if(data.response===true){

                         me.enviarCodigoPadre(Codigo,data.transporte[0].CODIGO,data.transporte[0].NOMBRE,data.transporte[0].RUC,data.transporte[0].DIRECCION,data.transporte[0].TELEFONO,data.transporte[0].CELULAR,data.transporte[0].EMAIL);
        

                         }else{
                          
                           me.$emit('nombre_transporte',Codigo);
                           me.$emit('existe_transporte',false);
                         }
                       
                    })

        // ------------------------------------------------------------------------

      },
       enviarCodigoPadre(nombre,id,descripcion,ruc,direccion,telefono,celular,email){

                // ------------------------------------------------------------------------

                // ENVIAR CODIGO
         this.$emit('nombre_transporte',nombre);
         this.$emit('id', id);
         this.$emit('descripcion', descripcion);
         this.$emit('ruc', ruc);
         this.$emit('direccion', direccion);
         this.$emit('telefono', telefono);
         this.$emit('celular', celular);
         this.$emit('email', email);
         this.$emit('existe_transporte',true);

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
                
                var tableTransporte = $('#tablaModalTransporte').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy":true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/transporteDatatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "NOMBRE" },
                            { "data": "RUC" },
                            { "data": "DIRECCION" },
                            { "data": "TELEFONO" },
                            { "data": "CELULAR" },
                            { "data": "EMAIL" }
                        ]      
                    });
                

                // ------------------------------------------------------------------------
                
                // SELECCIONAR UNA FILA - SE PUEDE BORRAR - SIN USO


                $('#tablaModalTransporte').on('click', 'tbody tr', function() {

                

                    // *******************************************************************

                    // CARGAR LOS VALORES A LAS VARIABLES DE PRODUCTO


                    me.codigo = tableTransporte.row(this).data().CODIGO;
                    me.description = tableTransporte.row(this).data().NOMBRE;  
                    Common.filtrarTransporteCommon(me.codigo).then(data => {
                        
                      


                           me.enviarCodigoPadre(me.codigo,data.transporte[0].CODIGO,me.description,data.transporte[0].RUC,data.transporte[0].DIRECCION,data.transporte[0].TELEFONO,data.transporte[0].CELULAR,data.transporte[0].EMAIL);
                           
                    })
                  
                    // *******************************************************************

                    // CERRAR EL MODAL
                     
                     $('.transporte-table').modal('hide');

                    // *******************************************************************

                });

                //FIN TABLA MODAL PRODUCTOS
                // <<   
                // ------------------------------------------------------------------------
            });    
        }
    }
</script>