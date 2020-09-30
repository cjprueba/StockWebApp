<template>
    <div>
           
                <label for="validationTooltip01">Codigo Venta:</label>
            
            <div class="input-group" id="validationTooltip01" >
                <div class="input-group-prepend">
                    <button  type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".ventas-table"><font-awesome-icon icon="search"/></button>
                </div>


                <input ref="codigo" id="codigo_usuario" class="form-control form-control-sm" type="text"  :value="nombre" @input="$emit('input', $event.target.value)" v-bind:class="{ 'is-invalid': validarVenta }" >

            </div>

            <!-- ******************************************************************* -->

            <!-- MODAL PRODUCTOS -->

                    <div class="modal fade ventas-table" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalCenterTitle">Ventas: </small></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                                <table id="tablaModalVentas" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Codigo</th>
                                            <th>Caja</th>
                                            <th>Cliente</th>
                                            <th>Fecha</th>
                                            <th>Hora</th>
                                            <th>Tipo</th>
                                            <th>Total</th>
                                            <th>Accion</th>
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
      props: ['nombre','validarVenta'],
      data(){
        return {
            productos: [],
            roles:[],
            tableVenta:''
        }
      }, 
       
      methods: {
        recargar(){
          var tableVenta = $('#tablaModalVentas').DataTable();
       tableVenta.ajax.reload( null, false );
        },

          enviarCodigoPadre(codigo){

                // ------------------------------------------------------------------------
           
                // ENVIAR CODIGO
                
                 this.$emit('codigo',codigo);

                // ------------------------------------------------------------------------

            }, vaciarDevolver(){

                // ------------------------------------------------------------------------

                // VACIAR Y DEVOLVER FOCUS AL TEXTBOX

                $("#codigo_Producto").focus();
    
                // ------------------------------------------------------------------------

            }, datatableMostrar(caja){

              let me=this;


              // ------------------------------------------------------------------------

              // PREPARAR DATATABLE 

              this.tableVenta = $('#tablaModalVentas').DataTable({
                  "processing": true,
                  "serverSide": true,
                  "destroy": true,
                  "bAutoWidth": true,
                  "select": true,
                  "ajax":{
                    "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                    },
                    "url": "/venta/datatable",
                    "dataType": "json",
                    "type": "POST"
                  },
                  "columns": [
                      { "data": "CODIGO" },
                      { "data": "CAJA" },
                      { "data": "CLIENTE" },
                      { "data": "FECHA" },
                      { "data": "HORA" },
                      { "data": "TIPO" },
                      { "data": "TOTAL" },
                      { "data": "ACCION" }
                  ],
                  "createdRow": function( row, data, dataIndex){
                      $(row).addClass(data['ESTATUS']);
                  }       
              });   

                // ------------------------------------------------------------------------
                // >>
                // INICIAR EL DATATABLE PRODUCTOS MODAL
                // ------------------------------------------------------------------------
                

                 $(document).ready( function () {

                // ------------------------------------------------------------------------
                
                // SELECCIONAR UNA FILA - SE PUEDE BORRAR - SIN USO


                $('#tablaModalVentas').on('click', 'tbody tr', function() {

                

                    // *******************************************************************

                    // CARGAR LOS VALORES A LAS VARIABLES DE PRODUCTO

                        
                    me.codigo = me.tableVenta.row(this).data().CODIGO;

                    me.enviarCodigoPadre(me.codigo);

                    // CERRAR EL MODAL
                     
                     $('.ventas-table').modal('hide');

                    // *******************************************************************

                });

                //FIN TABLA MODAL PRODUCTOS
                // <<   
                // ------------------------------------------------------------------------
            }); 
            }

      },


        mounted() {
            
            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

    
/*
          Common.hello(function(){*/
               /* axios.post('/cajaObtener', {'id': window.IPv}).then(function (response) {
              
              response.data.caja[0].CAJA;*/
      

  

            

        /* })*/
     /*});*/
            // ------------------------------------------------------------------------
   
        }
    }
</script>