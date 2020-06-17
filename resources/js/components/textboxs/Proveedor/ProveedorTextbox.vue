<template>
    <div>
            <div class="text-left"> 
                <label for="validationTooltip01">Codigo Proveedor:</label>
            </div>
            <div class="input-group" id="validationTooltip01" >
                <div class="input-group-prepend">
                    <button  type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".proveedor-table"><font-awesome-icon icon="search"/></button>
                </div>


                <input ref="codigo" id="codigo_usuario" class="custom-select custom-select-sm shadow-sm" type="text"  :value="nombre" @input="$emit('input', $event.target.value)" v-bind:class="{ 'is-invalid': validarProveedor }" :tabindex="tabIndexPadre" v-on:keyup.prevent.13="enterProveedor($event.target.value)" v-on:blur="enterProveedor($event.target.value)">

            </div>

            <!-- ******************************************************************* -->

            <!-- MODAL PRODUCTOS -->

                    <div class="modal fade proveedor-table" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalCenterTitle">Telas: </small></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                                <table id="tablaModalProveedor" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Codigo</th>
                                            <th>Nombre</th>
                                            <th>RUC</th>
                                            <th>Direccion</th>
                                            <th>Telefono</th>
                                            <th>Contacto</th>
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
      props: ['nombre','validarProveedor', 'tabIndexPadre'],
      data(){
        return {
            productos: [],
            roles:[]
        }
      }, 
       
      methods: {
        recargar(){
          var tableProveedor = $('#tablaModalProveedor').DataTable();
       tableProveedor.ajax.reload( null, false );
        },
       enterProveedor(Codigo){

        // ------------------------------------------------------------------------
           let me =this;
        // LLAMAR FUNCION PARA FILTRAR PRODUCTOS
        
           Common.filtrarProveedorCommon(Codigo).then(data => {

                        if(data.response===true){

                         me.enviarCodigoPadre(Codigo,data.proveedor[0].CODIGO,data.proveedor[0].NOMBRE,data.proveedor[0].RUC,data.proveedor[0].DIRECCION,data.proveedor[0].TELEFONO,data.proveedor[0].CELULAR,data.proveedor[0].EMAIL,data.proveedor[0].CONTACTO);
        

                         }else{
                          
                           me.$emit('nombre_proveedor',Codigo);
                           me.$emit('existe_proveedor',false);
                         }
                       
                    })

        // ------------------------------------------------------------------------

      },
       enviarCodigoPadre(nombre,id,descripcion,ruc,direccion,telefono,celular,email,contacto){

                // ------------------------------------------------------------------------

                // ENVIAR CODIGO
         this.$emit('nombre_proveedor',nombre);
         this.$emit('id', id);
         this.$emit('descripcion', descripcion);
         this.$emit('ruc', ruc);
         this.$emit('direccion', direccion);
         this.$emit('telefono', telefono);
         this.$emit('celular', celular);
         this.$emit('email', email);
         this.$emit('contacto', contacto);
         this.$emit('existe_proveedor',true);

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
                
                var tableProveedor = $('#tablaModalProveedor').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy":true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/proveedorDatatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "NOMBRE" },
                            { "data": "RUC" },
                            { "data": "DIRECCION" },
                            { "data": "TELEFONO" },
                            { "data": "CONTACTO" },
                            { "data": "CELULAR" },
                            { "data": "EMAIL" }
                        ]      
                    });
                

                // ------------------------------------------------------------------------
                
                // SELECCIONAR UNA FILA - SE PUEDE BORRAR - SIN USO


                $('#tablaModalProveedor').on('click', 'tbody tr', function() {

                

                    // *******************************************************************

                    // CARGAR LOS VALORES A LAS VARIABLES DE PRODUCTO


                    me.codigo = tableProveedor.row(this).data().CODIGO;
                    me.description = tableProveedor.row(this).data().NOMBRE;  
                    Common.filtrarProveedorCommon(me.codigo).then(data => {
                        
                      


                           me.enviarCodigoPadre(me.codigo,data.proveedor[0].CODIGO,me.description,data.proveedor[0].RUC,data.proveedor[0].DIRECCION,data.proveedor[0].TELEFONO,data.proveedor[0].CELULAR,data.proveedor[0].EMAIL,data.proveedor[0].CONTACTO);
                           
                    })
                  
                    // *******************************************************************

                    // CERRAR EL MODAL
                     
                     $('.proveedor-table').modal('hide');

                    // *******************************************************************

                });

                //FIN TABLA MODAL PRODUCTOS
                // <<   
                // ------------------------------------------------------------------------
            });    
        }
    }
</script>