<template>
  <div v-if="$can('clientes.credito') && $can('clientes')" class="container-fluid mt-4">
    <div class="row" >

      <!-- ------------------------------------------------------------------------------------- -->

      <!-- TITULO  -->
      
      <div class="col-md-12">
        <vs-divider>
          Clientes con Crédito
        </vs-divider>
      </div>
      
          <!-- ------------------------------------------------------------------------------------- -->

          <!-- MOSTRAR LOADING -->

          <div class="col-md-12">
        <div v-if="procesar" class="d-flex justify-content-center mt-3">
          <strong>Procesando...   </strong>
                  <div class="spinner-grow" role="status" aria-hidden="true"></div>
               </div>
            </div>

      <!-- ------------------------------------------------------------------------ -->

      <div class="col-md-12">

        <table id="tablaClienteCredito" class="table table-striped table-hover table-bordered table-sm mb-3" style="width:100%">
                <thead>
                    <tr>
                        <th>Codigo</th>
                        <th>Nombre</th>
                        <th>Celular</th>
                        <th>Telefono</th>
                        <th>Saldo</th>
                        <th>Pago</th>
                        <th>Total</th>
                        <th>Acción</th>
                        <th>Moneda</th>
                        <th>Candec</th>
                        <th>Deuda Crudo</th>
                    </tr>
                </thead>
                <tbody>
                    <td></td>
                </tbody>
            </table>

      </div>  
    </div>

    <!-- ------------------------------------------------------------------------ -->

    <!-- <div v-else>
      <cuatrocientos-cuatro></cuatrocientos-cuatro>
    </div>
 -->
    <!-- ------------------------------------------------------------------------ -->

    <!-- MODAL MOSTRAR DETALLE TRANSFERENCIA -->

    <modal-detalle-compra 
    ref="ModalMostrarDetalleCompra"
    ></modal-detalle-compra>

    <!-- ------------------------------------------------------------------------ -->

    <!-- MODAL PAGAR CUENTA -->

    <div class="modal fade producto-modal bg-dark" id="modalAbono" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
                      <div class="modal-content">
                        
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalCenterTitle"><small> MEDIOS DE PAGO </small></h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
            </div>
            
            <div class="modal-body">  
                          <div class="row">
                            <div class="col-md-12 mb-1">
                              <button class="btn btn-primary" v-on:click="abonar"><font-awesome-icon icon="plus" /> Abonar</button>
                              <button class="btn btn-danger"><font-awesome-icon icon="trash" /> Eliminar</button>
                              <button class="btn btn-primary"><font-awesome-icon icon="print" /> Recibo</button>
                              <button class="btn btn-info" v-on:click="nota_credito"><font-awesome-icon icon="document" /> Nota Credito</button>
                            </div>

                            <div class="col-md-12 mb-1">
                              <hr>
                            </div>
                            
                            <div class="col-md-12 mb-1">
                              <label>VENTAS</label>
                            </div>

                            <div class="col-md-12 mb-1">
                              <hr>
                            </div>

                            <div class="col-md-12">
                              <table id="tablaClienteCreditoDet" class="table table-striped table-hover table-bordered table-sm mb-3 mt-3" style="width:100%">
                          <thead>
                              <tr>
                                  <th>Codigo.</th>
                                  <th>Creacion</th>
                                  <th>Vencimiento</th>
                                  <th>Deuda</th>
                                  <th>Pago</th>
                                  <th>Saldo</th>
                                  <th>Acción</th>
                                  <th>Saldo Crudo</th>
                              </tr>
                          </thead>
                          <tbody>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>

                          </tbody>
                      </table> 
                            </div>  

                            <div class="col-md-12 mb-1">
                              <hr>
                            </div>

                            <div class="col-md-12 mb-1">
                              <label>ABONOS</label>
                            </div>

                            <div class="col-md-12 mb-1">
                              <hr>
                            </div>

                            <div class="col-md-12">
                              <table id="tablaClienteCreditoAbono" class="table table-striped table-hover table-bordered table-sm mb-3 mt-3" style="width:100%">
                          <thead>
                              <tr>
                                  <th>ID</th>
                                  <th>FECHA</th>
                                  <th>PAGO</th>
                                  <th>SALDO</th>
                                  <!-- <th>Acción</th> -->
                              </tr>
                          </thead>
                          <tbody>
                              <td></td>
                          </tbody>
                      </table> 
                            </div>

                            <div class="col-md-12 mb-1">
                              <hr>
                            </div>

                            <div class="col-md-12 mb-1">
                              <label>NOTA DE CREDITO</label>
                            </div>

                            <div class="col-md-12 mb-1">
                              <hr>
                            </div>

                            <div class="col-md-12 mb-4">
                              <table id="tablaClienteCreditoNotaCredito" class="table table-striped table-hover table-bordered table-sm mb-3 mt-3" style="width:100%">
                          <thead>
                              <tr>
                                  <th>ID</th>
                                  <th>FECHA</th>
                                  <th>VENTA ANTERIOR</th>
                                  <th>TOTAL</th>
                              </tr>
                          </thead>
                          <tbody>
                              <td></td>
                          </tbody>
                      </table> 
                            </div>
                          </div>  
                        </div>  
            
            </div>
          </div>
    </div>
    
    <!-- ------------------------------------------------------------------------ -->

    <!-- FORMA PAGO -->

      <forma-pago-credito-textbox :total="venta.TOTAL" :fk_venta="venta.FK_VENTA" :total_crudo="venta.TOTAL_CRUDO" :moneda="moneda.CODIGO" :candec="moneda.DECIMAL" :customer="cliente.CODIGO" :tipo="tipo_proceso"  @datos="formaPago" ref="compontente_medio_pago"></forma-pago-credito-textbox>

    <!-- ------------------------------------------------------------------------ --> 

    <!-- MODAL DETALLE PRODUCTO -->

    <nota-credito-cliente-datatable @data="aplicarEn"  ref="detalle_nota_credito_cliente"></nota-credito-cliente-datatable>

    <!-- ------------------------------------------------------------------------ -->
                      <!-- MODAL SELECCION TIPO DE MONEDA PARA APLICAR NOTA DE CREDITO-->

                  <div class="modal fade" id="modalAplicarNc" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">

                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Aplicar En</h5>
                        </div>

                        <div class="modal-body">


                          <div class="row">
                            <div class="col-md-12">
                              <hr>
                            </div>
                          </div>  
                          <div class="row">
                            <legend class="col-form-label col-sm-2 pt-0">Opciones:</legend>
                            <div class="col-sm-10">
                              <div class="form-check form-check-inline">
                                <input  v-model="radio.APLICAR" class="form-check-input" type="radio" name="radioAplicar" id="gridRadioGuarani" value="1" >
                                <label class="form-check-label" for="gridRadioGuarani">
                                  Guaranies
                                </label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input  v-model="radio.APLICAR" class="form-check-input" type="radio" name="radioAplicar" id="gridRadioDolar" value="2" >
                                <label class="form-check-label" for="gridRadioDolar">
                                  Dolares
                                </label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input  v-model="radio.APLICAR" class="form-check-input" type="radio" name="radioAplicar" id="gridRadioReal" value="4" >
                                <label class="form-check-label" for="gridRadioReal">
                                  Reales
                                </label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input  v-model="radio.APLICAR" class="form-check-input" type="radio" name="radioAplicar" id="gridRadioPeso" value="3">
                                <label class="form-check-label" for="gridRadioPeso">
                                  Pesos
                                </label>
                              </div>
                            </div>
                          </div>

                          <div class="row" >
                            <div class="col-md-12">
                              <hr>
                            </div>
                          </div> 

<!--                           <div class="row">
                            <div class="col-md-12">
                              <div class="text-center">
                                <h1 class="text-primary" >{{vuelto.seleccion}} </h1>
                              </div>  
                            </div>
                          </div> -->

                        </div>

                        <div class="modal-footer">
                          <button type="button" class="btn btn-primary" data-dismiss="modal" v-on:click="dataNotaCreditoTextbox">Aceptar</button>
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        </div>

                      </div>
                    </div>
                  </div>



  </div>
  <div v-else>
    <cuatrocientos-cuatro></cuatrocientos-cuatro>
  </div>
</template>
<script>


   export default {
    props: ['id_sucursal'], 
      data(){
        return {
            codigoVenta: '',
            procesar: false,
            caja: {
              CODIGO: ''
            },
            venta: {
              TOTAL: '',
              TOTAL_CRUDO: ''
            },
            moneda: {
              CODIGO: '',
              DECIMAL: ''
            },
            cliente: {
              CODIGO: ''
            }, nc: {
              TOTAL: '',
              SALDO: '',
              VUELTO: ''
            },
            radio:{
              APLICAR:1
            },
            tipo_proceso:0,
            datosNc:[],
            candec:''
        }
      }, 
      methods: {

          mostrarModalTranferencia(codigo) {

            // ------------------------------------------------------------------------

            // LLAMAR EL METODO DEL COMPONENTE HIJO

            this.$refs.ModalMostrarDetalleCompra.mostrarModal(codigo);

            // ------------------------------------------------------------------------

          }, detalleCredito(codigo) {

            // ------------------------------------------------------------------------

            // DETALLE CREDITOS VENTAS

            var tableClienteCreditoDet = $('#tablaClienteCreditoDet').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                             "data": {
                              codigo: codigo,
                              "_token": $('meta[name="csrf-token"]').attr('content')
                             },
                                 "url": "/cliente/credito/detalle/datatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "FECALTAS" },
                            { "data": "VENCIMIENTO" },
                            { "data": "DEUDA" },
                            { "data": "PAGO" },
                            { "data": "SALDO" },
                            { "data": "ACCION" },
                           { "data": "SALDO_CRUDO", "visible": false }
                        ],
                        "order": [[ 0, "desc" ]],
                    "createdRow": function( row, data, dataIndex){
                        $(row).addClass(data['ESTATUS']);
                    }       
                });

            // ------------------------------------------------------------------------

            $('#tablaClienteCreditoDet tbody').on( 'click', 'tr', function () {
              if ( $(this).hasClass('table-dark') ) {
                  $(this).removeClass('table-dark');
              }
              else {
                  tableClienteCreditoDet.$('tr.table-dark').removeClass('table-dark');
                  $(this).addClass('table-dark');
              }
          } );

          }, detalleAbono(codigo){

            // ------------------------------------------------------------------------

            // DETALLE CREDITOS VENTAS

            var tableClienteCreditoDetAbono = $('#tablaClienteCreditoAbono').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                             "data": {
                              codigo: codigo,
                              "_token": $('meta[name="csrf-token"]').attr('content')
                             },
                                 "url": "/cliente/credito/detalle/abono/datatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "FECHA" },
                            { "data": "PAGO" },
                            { "data": "SALDO" }
                        ],
                        "order": [[ 0, "desc" ]]         
                });

            // ------------------------------------------------------------------------

          }, detalleNotaCredito(codigo){

            // ------------------------------------------------------------------------

            // DETALLE CREDITOS VENTAS

            var tableClienteCreditoDetNotaCredito = $('#tablaClienteCreditoNotaCredito').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                             "data": {
                              codigo: codigo,
                              "_token": $('meta[name="csrf-token"]').attr('content')
                             },
                                 "url": "/cliente/credito/detalle/nc/datatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "ID" },
                            { "data": "FECALTAS" },
                            { "data": "VENTA_ANTERIOR" },
                            { "data": "TOTAL" }
                        ],
                        "order": [[ 0, "desc" ]]         
                });

            // ------------------------------------------------------------------------

          }, abonar(){

            // ------------------------------------------------------------------------

            let me = this;

            // ------------------------------------------------------------------------
            me.tipo_proceso=3;
            me.venta.FK_VENTA='';
            me.$refs.compontente_medio_pago.procesarFormas();

            // ------------------------------------------------------------------------

          },
          obtenerCaja() {

            let me=this;
              
              // ------------------------------------------------------------------------

              // OBTENER CAJA 

              Common.obtenerIPCommon(function(){

                if (window.IPv !== false) {
                  axios.post('/cajaObtener', {'id': window.IPv}).then(function (response) {
                      if (response.data.response === true) {
                          me.caja.CODIGO  =   response.data.caja[0].CAJA;
                          // me.caja.CANTIDAD_PERSONALIZADA  =   response.data.caja[0].CANTIDAD_PERSONALIZADA;
                          // me.caja.CANTIDAD_TICKET = response.data.caja[0].CANTIDAD_TICKET;
                          // me.numeracion();
                      } else {
                          
                            Swal.fire({
                  title: 'NO SE PUDO OBTENER CAJA',
                  type: 'warning',
                  confirmButtonColor: '#d33',
                  confirmButtonText: 'Aceptar',
                }).then((result) => {
                  
                  window.location.href = '/vt2';

                })  

                      }   
                    
                  })
                } else {


                  Swal.fire({
              title: 'NO SE PUDO OBTENER LA IP DE LA MAQUINA',
              type: 'warning',
              confirmButtonColor: '#d33',
              confirmButtonText: 'Aceptar',
            }).then((result) => {
                  
              window.location.href = '/vt2';

            })

                }
                  
                });

                // ------------------------------------------------------------------------

          },
          formaPago(datos){

            // ------------------------------------------------------------------------

            let me = this;

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES

            var tableClienteCredito = $('#tablaClienteCredito').DataTable();
            var tableClienteCreditoDet = $('#tablaClienteCreditoAbono').DataTable();
            var tableClienteCreditoDetAbono = $('#tablaClienteCreditoAbono').DataTable();
            var tableClienteCreditoDetNotaCredito = $('#tablaClienteCreditoNotaCredito').DataTable();

            // ------------------------------------------------------------------------

            if (me.caja.CODIGO === null) {
              Swal.fire({
            title: 'NO SE PUDO OBTENER CAJA',
            type: 'warning',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Aceptar',
          })
              return;
            }

            // ------------------------------------------------------------------------

            // GUARDAR 
            console.log("despues del modal: ",this.venta.FK_VENTA);
            console.log("despues del modal tipo: ",this.tipo_proceso);
            if(this.tipo_proceso===5){
               
               this.respuesta = {
                  cliente: this.cliente.CODIGO,
                  caja: this.caja.CODIGO,
                  moneda: this.moneda.CODIGO,
                  estatus: 2,
                  tipo_proceso:this.tipo_proceso,
                  fk_venta:this.venta.FK_VENTA,
                  pago: datos,
              }
            }else{
              this.respuesta = {
                  cliente: this.cliente.CODIGO,
                  caja: this.caja.CODIGO,
                  moneda: this.moneda.CODIGO,
                  estatus: 2,
                  tipo_proceso:this.tipo_proceso,
                  pago: datos,
              }
            }
            

            Swal.fire({
          title: 'Guardando Pago',
          html: 'Cerrare en cuanto modifique la venta.',
          onBeforeOpen: () => {

            // ------------------------------------------------------------------------

            // MOSTRAR CARGAR 

            Swal.showLoading()
            
            // ------------------------------------------------------------------------

            Common.guardarPagoCreditoCommon(me.respuesta).then(data => {

                // ------------------------------------------------------------------------

                if (!data.response === true) {
                    throw new Error(data.statusText);
                  }

                  if (data.response) {

                    Swal.close();

                // ------------------------------------------------------------------------

                Swal.fire(
                  'Guardado !',
                   data.statusText,
                  'success'
                )

                // ------------------------------------------------------------------------ 

                // RECARGAR TABLA 

                  $('#modalAbono').modal('toggle');
                tableClienteCredito.ajax.reload( null, false );
                tableClienteCreditoDet.ajax.reload( null, false );
                    tableClienteCreditoDetAbono.ajax.reload( null, false );
                    tableClienteCreditoDetNotaCredito.ajax.reload( null, false );

                // ------------------------------------------------------------------------

                me.$refs.compontente_medio_pago.limpiar();

                // ------------------------------------------------------------------------
                
              }

              // ------------------------------------------------------------------------

          }).catch(error => {
                  Swal.showValidationMessage(
                    `Request failed: ${error}`
                  )
           });
          }
        }).then((result) => {

            

        })
          }, nota_credito() {
        
        // ------------------------------------------------------------------------

        // LLAMAR MODAL 

        this.$refs.detalle_nota_credito_cliente.mostrarModal(this.cliente.CODIGO);

        // ------------------------------------------------------------------------

      },aplicarEn(data){
        let me=this;
        me.datosNc=data;
         $('#modalAplicarNc').modal('show');
      },
      dataNotaCreditoTextbox(){
       
        // ------------------------------------------------------------------------

        let me = this;

        // ------------------------------------------------------------------------

        // INICIAR VARIABLES

            var tableClienteCredito = $('#tablaClienteCredito').DataTable();
            var tableClienteCreditoDet = $('#tablaClienteCreditoAbono').DataTable();
            var tableClienteCreditoDetAbono = $('#tablaClienteCreditoAbono').DataTable();
            var tableClienteCreditoDetNotaCredito = $('#tablaClienteCreditoNotaCredito').DataTable();

            // ------------------------------------------------------------------------

        // NOTA DE CREDITO CALCULOS 

        this.nc.TOTAL = this.datosNc.total;
        this.nc.SALDO = Common.restarCommon(this.venta.TOTAL, this.datosNc.total, this.moneda.DECIMAL);

        if (Common.quitarComaCommon(this.nc.SALDO) < 0) {
          this.nc.VUELTO = this.nc.SALDO;
        } else if (Common.quitarComaCommon(this.nc.SALDO) >= 0) {
          this.nc.VUELTO = 0;
        }

        // ------------------------------------------------------------------------

        Swal.fire({
          title: 'Estas seguro ?',
          text: "Aplicar nota de crédito " + this.datosNc.id + " !",
          type: 'warning',
          showLoaderOnConfirm: true,
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Aplicar !',
          cancelButtonText: 'Cancelar',
          preConfirm: () => {
            return Common.notaCreditoClienteCreditoCommon({id_nota_credito: this.datosNc.id, cliente: me.cliente.CODIGO,
              caja: me.caja.CODIGO,
              moneda: me.moneda.CODIGO, total: this.datosNc.total, saldo: this.nc.SALDO, vuelto: this.nc.VUELTO, moneda_aplicar:this.radio.APLICAR,candec:this.candec}).then(data => {
              if (!data.response === true) {
                  throw new Error(data.statusText);
                }
              return data;
            }).catch(error => {
                Swal.showValidationMessage(
                  `Request failed: ${error}`
                )
            });
          }
        }).then((result) => {
          if (result.value) {
            Swal.fire(
                  'Aplicado !',
                  'Se ha aplicado la nota de crédito !',
                  'success'
          )

              tableClienteCredito.ajax.reload( null, false );
              tableClienteCreditoDet.ajax.reload( null, false );
              tableClienteCreditoDetAbono.ajax.reload( null, false );
              tableClienteCreditoDetNotaCredito.ajax.reload( null, false );
              
            // ------------------------------------------------------------------------

            // RECARGAR TABLA 
            
          //tableTransferencia.ajax.reload( null, false );

          // ------------------------------------------------------------------------

          } 
        })
      }
      },
        mounted() {
          
          let me = this;
                    Common.obtenerParametroCommon().then(data => {
                    

                                me.candec=data.parametros[0].CANDEC;
                               
                               
                            

                                

                         
    
            
                     });
          me.obtenerCaja();

            $(document).ready( function () {

                // ------------------------------------------------------------------------

                // PREPARAR DATATABLE 

          var tableClienteCredito = $('#tablaClienteCredito').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                            "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/cliente/credito/datatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "NOMBRE" },
                            { "data": "CELULAR" },
                            { "data": "TELEFONO" },
                            { "data": "DEUDA" },
                            { "data": "PAGO" },
                            { "data": "MONTO" },
                            { "data": "ACCION" },
                            { "data": "MONEDA", "visible": false },
                            { "data": "CANDEC", "visible": false },
                            { "data": "DEUDA_CRUDO", "visible": false },
                        ],
                        "order": [[ 0, "desc" ]]         
                    });
                    
          // ------------------------------------------------------------------------

          // AJUSTAR COLUMNAS DE ACUERDO AL DATO QUE CONTIENEN

                tableClienteCredito.columns.adjust().draw();

                    // ------------------------------------------------------------------------

                    // ELIMINAR TRANSFERENCIA

                    $('#tablaClienteCredito').on('click', 'tbody tr #eliminar', function() {

                      // *******************************************************************

                      // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
                      
                      var row  = $(this).parents('tr')[0];
                      me.eliminarCompra(tableClienteCredito.row( row ).data().CODIGO);

                      // *******************************************************************

                  });

                    // ------------------------------------------------------------------------

                    // GENERAR REPORTE PDF

                    $('#tablaClienteCredito').on('click', 'tbody tr #reporte', function() {

                      // *******************************************************************

                      // ENVIAR A COMMON FUNCTION PARA GENERAR REPORTE PDF

                      me.procesar = true;
                      var row  = $(this).parents('tr')[0];
                      Common.generarRptPdfCompraCommon(tableClienteCredito.row( row ).data().CODIGO).then( () => {
                        me.procesar = false;
                      });
                      

                      // *******************************************************************

                  });

                    // ------------------------------------------------------------------------

                    $('#tablaClienteCredito').on('click', 'tbody tr #abonar', function() {

                      // *******************************************************************

                      // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA

                       $('#modalAbono').modal('show');
                       var row  = $(this).parents('tr')[0];
                       me.detalleCredito(tableClienteCredito.row( row ).data().CODIGO);
                       me.detalleAbono(tableClienteCredito.row( row ).data().CODIGO);
                       me.detalleNotaCredito(tableClienteCredito.row( row ).data().CODIGO);
                       
                 me.venta.TOTAL = tableClienteCredito.row( row ).data().DEUDA;
                 me.venta.TOTAL_CRUDO = tableClienteCredito.row( row ).data().DEUDA_CRUDO;
                 me.moneda.CODIGO = tableClienteCredito.row( row ).data().MONEDA;
                 me.moneda.DECIMAL = tableClienteCredito.row( row ).data().CANDEC;
                 me.cliente.CODIGO = tableClienteCredito.row( row ).data().CODIGO;

                      // *******************************************************************

                  });
                    //MODAL DE FORMA PAGO CREDITO
                      $('#tablaClienteCreditoDet').on('click', 'tbody tr #abonar', function() {
                        var tableClienteCreditoDet = $('#tablaClienteCreditoDet').DataTable();

                         var row  = $(this).parents('tr')[0];
                         me.venta.TOTAL = tableClienteCreditoDet.row( row ).data().SALDO_CRUDO;
                         me.venta.TOTAL_CRUDO = tableClienteCreditoDet.row( row ).data().SALDO_CRUDO;
                         me.venta.FK_VENTA=tableClienteCreditoDet.row( row ).data().CODIGO;
                         console.log("antes del modal: ",me.venta.FK_VENTA);
                         me.tipo_proceso=5;
                         me.$refs.compontente_medio_pago.procesarFormas();
                      });

                  // ------------------------------------------------------------------------
   });  
        }
    }
</script>