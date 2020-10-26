<template>
    <div>
           
                <label for="validationTooltip01">TICKET ID:</label>
            
            <div class="input-group mb-3" id="validationTooltip01" >
                <div class="input-group-prepend">
                    <button  type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".ventas-table"><font-awesome-icon icon="search"/></button>
                </div>


                <input ref="codigo" v-model="venta.ID" class="form-control form-control-sm" type="text"  @input="$emit('input', $event.target.value)" v-bind:class="{ 'is-invalid': validarVenta }" >

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

                                <!-- ------------------------------------------------------------------------ -->

                                <!-- <div class="row">
                                  <div class="col-md-11 mb-3">  
                                        <div id="sandbox-container" class="input-daterange input-group input-group-sm">
                                          <input id='selectedInicialFecha' data-date-format="yyyy-mm-dd" class=" form-control " v-model="selectedInicialFecha" v-bind:class="{ 'is-invalid': validar.inicialFecha }"/>
                                          <div class="input-group-append ">
                                            <span class="input-group-text">a</span>
                                          </div>
                                          <input name='end' id='selectedFinalFecha' data-date-format="yyyy-mm-dd" class=" form-control " v-model="selectedFinalFecha" v-bind:class="{ 'is-invalid': validar.finalFecha }"/>
                                          <div class="invalid-feedback">
                                                {{mensaje.fechaInvalida}}
                                            </div>
                                        </div>

                                        
                                  </div>

                                  <div class="col-md-1 mb-0 text-right">
                                    <label></label>
                                    <button class="btn btn-primary btn-sm" v-on:click="obtenerDatatable2()">Buscar</button>
                                  </div>
                                </div> -->

                                <!-- ------------------------------------------------------------------------ -->

                                <table id="tablaModalVentas" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
                                    <thead>
                                        <tr> 
                                            <th>ID</th>
                                            <th>Codigo</th>
                                            <th>Caja</th>
                                            <th>Cliente</th>
                                            <th>Fecha</th>
                                            <th>Hora</th>
                                            <th>Tipo</th>
                                            <th>Total Crudo</th>
                                            <th>Total</th>
                                            <th>Candec</th>
                                            <th>Moneda</th>
                                            <th>Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <td></td>
                                    </tbody>
                                </table> 

                                <!-- ------------------------------------------------------------------------ -->

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
            tableVenta:'',
            selectedInicialFecha: '',
            selectedFinalFecha: '',
            validar: {
              inicialFecha: '',
              finalFecha: ''
            },
            mensaje: {
              fechaInvalida: ''
            },
            venta: {
              ID: ''
            },
            open: false
        }
      }, 
       
      methods: {
        recargar(){
          var tableVenta = $('#tablaModalVentas').DataTable();
       tableVenta.ajax.reload( null, false );
        },

          enviarCodigoPadre(codigo, caja, data){

                // ------------------------------------------------------------------------
           
                // ENVIAR CODIGO
                
                 this.$emit('codigo',codigo);
                 this.$emit('caja',caja);
                 this.$emit('data', data);

                // ------------------------------------------------------------------------

            }, vaciarDevolver(){

                // ------------------------------------------------------------------------

                // VACIAR Y DEVOLVER FOCUS AL TEXTBOX

                $("#codigo_Producto").focus();
    
                // ------------------------------------------------------------------------

            }, obtenerDatatable2(){

                //$('#tablaModalVentas').DataTable().clear();
                //$('#tablaModalVentas').DataTable().destroy();
                $('#tablaModalVentas').DataTable().clear().destroy();
                let me = this;

              // ------------------------------------------------------------------------

              

              this.tableVenta = $('#tablaModalVentas').DataTable({
                  "processing": true,
                  "serverSide": true,
                  "destroy": true,
                  "bAutoWidth": true,
                  "select": true,
                  "ajax":{
                    "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content'),
                                    inicial: me.selectedInicialFecha,
                                    final: me.selectedFinalFecha,
                    },
                    "url": "/venta/datatable",
                    "dataType": "json",
                    "type": "POST"
                  },
                  "columns": [
                      { "data": "ID" },
                      { "data": "CODIGO" },
                      { "data": "CAJA" },
                      { "data": "CLIENTE" },
                      { "data": "FECHA" },
                      { "data": "HORA" },
                      { "data": "TIPO" },
                      { "data": "TOTAL" },
                      { "data": "TOTAL_CRUDO", "visible": false },
                      { "data": "CANDEC", "visible": false },
                      { "data": "MONEDA", "visible": false },
                      { "data": "ACCION" }
                  ],
                  "createdRow": function( row, data, dataIndex){
                      $(row).addClass(data['ESTATUS']);
                  }       
              });   

              // ------------------------------------------------------------------------

            }, obtenerDatatable(){

              let me = this;

              // ------------------------------------------------------------------------

              

              this.tableVenta = $('#tablaModalVentas').DataTable({
                  "processing": true,
                  "serverSide": true,
                  "destroy": true,
                  "bAutoWidth": true,
                  "select": true,
                  "ajax":{
                    "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content'),
                                    inicial: me.selectedInicialFecha,
                                    final: me.selectedFinalFecha,
                    },
                    "url": "/venta/datatable",
                    "dataType": "json",
                    "type": "POST"
                  },
                  "columns": [
                      { "data": "ID" },
                      { "data": "CODIGO" },
                      { "data": "CAJA" },
                      { "data": "CLIENTE" },
                      { "data": "FECHA" },
                      { "data": "HORA" },
                      { "data": "TIPO" },
                      { "data": "TOTAL" },
                      { "data": "TOTAL_CRUDO", "visible": false },
                      { "data": "CANDEC", "visible": false },
                      { "data": "MONEDA", "visible": false },
                      { "data": "ACCION" }
                  ],
                  "createdRow": function( row, data, dataIndex){
                      $(row).addClass(data['ESTATUS']);
                  }       
              });   

              // ------------------------------------------------------------------------

              

            }, buscadores(){

              let me = this;

              // ------------------------------------------------------------------------

              var tablaVenta = $('#tablaModalVentas').DataTable();

              // ------------------------------------------------------------------------

              $('#tablaModalVentas thead tr').clone(true).appendTo( '#tablaModalVentas thead' );
                    $('#tablaModalVentas thead tr:eq(1) th').each( function (i) {
                        var title = $(this).text();
                        $(this).html( '<input type="text" class="form-control form-control-sm" placeholder="Buscar '+title+'" />' );
                 
                        $( 'input', this ).on( 'keyup change', function () {
                            if ( me.tableVenta.column(i).search() !== this.value ) {
                                me.tableVenta
                                    .column(i)
                                    .search( this.value )
                                    .draw();
                            }
                        } );
                } );

                // ------------------------------------------------------------------------
                
                // SELECCIONAR UNA FILA - SE PUEDE BORRAR - SIN USO


                

            }, datatableMostrar(caja){

              let me=this;

              if (this.open === false) {
                this.open = true;

                // ------------------------------------------------------------------------

                // PREPARAR DATATABLE 

                // this.tableVenta = $('#tablaModalVentas').DataTable({
                //     "processing": true,
                //     "serverSide": true,
                //     "destroy": true,
                //     "bAutoWidth": true,
                //     "select": true,
                //     "ajax":{
                //       "data": {
                //                       "_token": $('meta[name="csrf-token"]').attr('content'),
                //                       inicial: me.selectedInicialFecha,
                //                       final: me.selectedFinalFecha,
                //       },
                //       "url": "/venta/datatable",
                //       "dataType": "json",
                //       "type": "POST"
                //     },
                //     "columns": [
                //         { "data": "ID" },
                //         { "data": "CODIGO" },
                //         { "data": "CAJA" },
                //         { "data": "CLIENTE" },
                //         { "data": "FECHA" },
                //         { "data": "HORA" },
                //         { "data": "TIPO" },
                //         { "data": "TOTAL" },
                //         { "data": "TOTAL_CRUDO", "visible": false },
                //         { "data": "CANDEC", "visible": false },
                //         { "data": "MONEDA", "visible": false },
                //         { "data": "ACCION" }
                //     ],
                //     "createdRow": function( row, data, dataIndex){
                //         $(row).addClass(data['ESTATUS']);
                //     }       
                // });   

                // ------------------------------------------------------------------------

                // MARCAR LA FECHA DE HOY

                var today = new Date();
                var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();

                // ------------------------------------------------------------------------

                  me.selectedInicialFecha = date;
                  me.selectedFinalFecha = date;

                // ------------------------------------------------------------------------
                
                // FECHAS 

                $(function(){
                        $('#sandbox-container .input-daterange').datepicker({
                              keyboardNavigation: false,
                          forceParse: false,
                      });
                      $("#selectedInicialFecha").datepicker().on(
                        "changeDate", () => {me.selectedInicialFecha = $('#selectedInicialFecha').val()}
                    );
                    $("#selectedFinalFecha").datepicker().on(
                        "changeDate", () => {me.selectedFinalFecha = $('#selectedFinalFecha').val()}
                    );

                });

                // ------------------------------------------------------------------------
                
                this.obtenerDatatable(function(){
                  
                });

                this.buscadores(function() {
                    
                  
                  });
              }
              
              $(document).ready( function () {
                     $('#tablaModalVentas').on('click', 'tbody tr', function() {

                    // *******************************************************************

                    // CARGAR LOS VALORES A LAS VARIABLES DE PRODUCTO

                        
                    me.codigo = me.tableVenta.row(this).data().CODIGO;
                    me.caja = me.tableVenta.row(this).data().CAJA;

                    me.enviarCodigoPadre(
                      me.codigo, 
                      me.caja, 
                      {
                      'TOTAL': me.tableVenta.row(this).data().TOTAL,
                      'TOTAL_CRUDO': me.tableVenta.row(this).data().TOTAL_CRUDO,
                      'MONEDA': me.tableVenta.row(this).data().MONEDA,
                      'CANDEC': me.tableVenta.row(this).data().CANDEC
                      }
                      
                      );

                    // CERRAR EL MODAL
                     
                     $('.ventas-table').modal('hide');

                    // *******************************************************************

                });
            });
            
              // this.tableVentaMostrar = $('#tablaModalVentas').DataTable();
              // $('#tablaModalVentas thead tr').clone(true).appendTo( '#tablaModalVentas thead' );
              //       $('#tablaModalVentas thead tr:eq(1) th').each( function (i) {
              //           var title = $(this).text();
              //           $(this).html( '<input type="text" class="form-control form-control-sm" placeholder="Buscar '+title+'" />' );
                 
              //           $( 'input', this ).on( 'keyup change', function () {
              //               if ( me.tableVenta.column(i).search() !== this.value ) {
              //                   me.tableVenta
              //                       .column(i)
              //                       .search( this.value )
              //                       .draw();
              //               }
              //           } );
              //   } );

              //   // ------------------------------------------------------------------------

      },


        mounted() {
            
            // ------------------------------------------------------------------------

            let me = this;

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            

            // PREPARAR DATATABLE 

            //this.obtenerDatatable();
            //this.datatableMostrar(1);
            

            
                // >>
                // INICIAR EL DATATABLE PRODUCTOS MODAL
                // ------------------------------------------------------------------------
                

                 
            }
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