<template>
	<div>
            <!-- <label for="validationTooltip01">Forma de Pago</label>
            <select class="custom-select custom-select-sm" v-on:change="llamarPadre($event.target.value)" v-on:change="" @input="$emit('input', $event.target.value)">
                    <option v-for="forma in formas" :selected="forma.CODIGO === parseInt(value)" :value="forma.CODIGO">{{ forma.CODIGO }} - {{ forma.DESCRIPCION }}</option>
            <v-select :options="formas"  label="DESCRIPCION" track-by="CODIGO" :multiple="true" @input="llamarPadre" v-model="opciones" ></v-select>
            </select>
			        -->

              <div class="modal fade producto-modal" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
                      <div class="modal-content">

                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalCenterTitle"><small> MEDIOS DE PAGO </small></h5>
                        </div>

                        <div class="modal-body">  
                            <div class="row">
                              <div class="col-md-12">

                                <div class="card  bg-light card-body border-left-info">

                                  <div class="row">

                                    <!-- ------------------------------------------------------------------------ -->

                                    <div class="col-md-3 mt-3"">
                                      <label for="validationTooltip01">TOTAL</label>
                                      <h2 class="text-success">{{total}}</h2>
                                    </div>

                                    <div class="col-md-3 mt-3"">
                                      <label for="validationTooltip01">SALDO</label>
                                      <h2 class="text-secondary">{{medios.SALDO}}</h2>
                                    </div>

                                    <div class="col-md-3 mt-3"">
                                      <label for="validationTooltip01">MEDIOS</label>
                                      <h2 class="text-info">{{medios.MEDIOS}}</h2>
                                    </div>

                                    <div class="col-md-3 mt-3"">
                                      <label for="validationTooltip01">VUELTO</label>
                                      <h2 class="text-muted">{{medios.VUELTO}}</h2>
                                    </div>

                                    <!-- ------------------------------------------------------------------------ -->

                                    <div class="col-md-12">
                                      <hr/>
                                    </div>

                                    <!-- ------------------------------------------------------------------------ -->

                                    <div class="col-md-3">
                                        <span class="badge badge-pill badge-light">Guaranies</span><br/>
                                                {{cotizacion.guaranies}}
                                    </div> 
                                           
                                    <div class="col-md-3">
                                          <span class="badge badge-pill badge-light">Dolares</span><br/>
                                                {{cotizacion.dolares}}
                                    </div> 
                                    
                                    <div class="col-md-3">
                                          <span class="badge badge-pill badge-light">Pesos</span><br/>
                                                {{cotizacion.pesos}}
                                    </div> 
                                    
                                    <div class="col-md-3">
                                          <span class="badge badge-pill badge-light">Reales</span><br/>
                                                {{cotizacion.reales}}
                                    </div> 

                                    <!-- ------------------------------------------------------------------------ -->

                                  </div>
                                
                                </div>

                              </div> 

                              <!-- ------------------------------------------------------------------------ -->

                              <div class="col-md-6 mt-3">
                                  <div class="row">
                                    <div class="col-md-12">

                                      <!-- ------------------------------------------------------------------------ -->

                                      <!-- GUARANIES -->

                                        <div class="row">

                                          <div class="col-md-2">
                                            <label for="validationTooltip01">Guaranies:</label>
                                          </div>  

                                          <div class="col-md-3">
                                            <input class="form-control form-control-sm" type="text" v-model="totales.GUARANIES" v-on:blur="formatoGuaranies" disabled>
                                          </div>

                                          <div class="col-md-4">
                                            <div class="input-group input-group-sm mb-3" >
                                              <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">{{cotizacion.descripcion_gs}}</span>
                                              </div>
                                              <input class="form-control form-control-sm" type="text"  v-model="monedas.GUARANIES" v-on:blur="formatoGuaranies" :disabled="cotizacion.deshabilitar_gs">
                                            </div>
                                          </div>

                                          <div class="col-md-3">
                                            <input class="form-control form-control-sm" type="text" v-model="vuelto.guaranies" disabled>
                                          </div>

                                        </div>

                                      <!-- ------------------------------------------------------------------------ -->

                                    </div>  

                                    <div class="col-md-12">

                                      <!-- ------------------------------------------------------------------------ -->

                                      <!-- DOLARES -->

                                        <div class="row">
                                          
                                          <div class="col-md-2">
                                            <label for="validationTooltip01">Dolares</label>
                                          </div> 

                                          <div class="col-md-3">
                                            <input class="form-control form-control-sm" type="text"  v-model="totales.DOLARES"  v-on:blur="formatoDolares" disabled>
                                          </div>

                                          <div class="col-md-4">
                                            <div class="input-group input-group-sm mb-3" >
                                              <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">{{cotizacion.descripcion_$}}</span>
                                              </div>
                                              <input class="form-control form-control-sm" type="text" v-model="monedas.DOLARES"  v-on:blur="formatoDolares" :disabled="cotizacion.deshabilitar_$">
                                            </div>
                                          </div>

                                          <div class="col-md-3">
                                            <input class="form-control form-control-sm" type="text"  v-model="vuelto.dolares" disabled>
                                          </div>

                                        </div>

                                      <!-- ------------------------------------------------------------------------ -->

                                    </div>  

                                    <div class="col-md-12">

                                      <!-- ------------------------------------------------------------------------ -->

                                      <!-- PESOS -->

                                        <div class="row">

                                          <div class="col-md-2">
                                            <label for="validationTooltip01">Pesos</label>
                                          </div> 

                                          <div class="col-md-3">
                                            <input class="form-control form-control-sm" type="text"  v-model="totales.PESOS" v-on:blur="formatoPesos" disabled>
                                          </div>

                                          <div class="col-md-4">
                                            <div class="input-group input-group-sm mb-3" >
                                              <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">{{cotizacion.descripcion_ps}}</span>
                                              </div>
                                              <input class="form-control form-control-sm" type="text" v-model="monedas.PESOS" v-on:blur="formatoPesos" :disabled="cotizacion.deshabilitar_ps">
                                            </div>
                                          </div>

                                          <div class="col-md-3">
                                            <input class="form-control form-control-sm" type="text" v-model="vuelto.pesos" disabled>
                                          </div>

                                        </div>

                                      <!-- ------------------------------------------------------------------------ -->

                                    </div>  

                                    <div class="col-md-12">

                                      <!-- ------------------------------------------------------------------------ -->

                                      <!-- REALES -->

                                        <div class="row">

                                          <div class="col-md-2">
                                            <label for="validationTooltip01">Reales</label>
                                          </div> 

                                          <div class="col-md-3">
                                            <input class="form-control form-control-sm" type="text" v-model="totales.REALES" v-on:blur="formatoReales" disabled>
                                          </div>

                                          <div class="col-md-4">
                                            <div class="input-group input-group-sm mb-3" >
                                              <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">{{cotizacion.descripcion_rs}}</span>
                                              </div>
                                              <input class="form-control form-control-sm" type="text" v-model="monedas.REALES" v-on:blur="formatoReales" :disabled="cotizacion.deshabilitar_rs">
                                            </div>
                                          </div>

                                          <div class="col-md-3">
                                            <input class="form-control form-control-sm" type="text" v-model="vuelto.reales" disabled>
                                          </div>

                                        </div>

                                      <!-- ------------------------------------------------------------------------ -->

                                    </div> 
                                  </div>  
                              </div> 

                              
                                
                              <div class="col-md-6 mt-3">
                                  <div class="row">

                                     <!-- ------------------------------------------------------------------------ -->

                                     <!-- EFECTIVO -->

                                    <div class="col-md-2">
                                      <label for="validationTooltip01">Pagado:</label>
                                    </div>  
                                    
                                    <div class="col-md-10">

                                      <!-- ------------------------------------------------------------------------ -->
                                        
                                        <div class="input-group input-group-sm mb-3" >
                                          <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><font-awesome-icon icon="money-bill-alt"/></span>
                                          </div>
                                          <input class="form-control form-control-sm" type="text" v-model="medios.EFECTIVO" disabled>
                                        </div>

                                      <!-- ------------------------------------------------------------------------ -->

                                    </div>  

                                    <!-- ------------------------------------------------------------------------ -->

                                    <!-- TARJETA -->

                                    <div class="col-md-2">
                                      <label for="validationTooltip01">Tarjeta</label>
                                    </div>  

                                    <div class="col-md-10">

                                      <!-- ------------------------------------------------------------------------ -->

                                      <!-- MONTO PAGADO -->

                                        
                                        <div class="input-group input-group-sm mb-3" >
                                          <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><font-awesome-icon icon="credit-card"/></span>
                                          </div>
                                          <input class="form-control form-control-sm" type="text" v-model="medios.TARJETA" v-on:blur="formatoTarjeta">
                                        </div>

                                        <small id="passwordHelpBlock" class="form-text text-muted" v-if="mostrar.TARJETA_SELECCION">
                                            <p><a href="#" v-on:click="formatoTarjeta" class="text-primary">{{tarjeta.descripcion}}</a></p>
                                        </small>

                                      <!-- ------------------------------------------------------------------------ -->

                                    </div>  
                                    <!-- ------------------------------------------------------------------------ -->

                                    <!-- CHEQUE -->

                                    <div class="col-md-2">
                                      <label for="validationTooltip01">Cheque:</label>
                                    </div>  

                                    <!-- ------------------------------------------------------------------------ -->

                                    <div class="col-md-10">

                                      <!-- ------------------------------------------------------------------------ -->

                                      <!-- MONTO PAGADO -->

                                        <div class="input-group input-group-sm mb-3" >

                                          <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><font-awesome-icon icon="money-check-alt"/></span>
                                          </div>
                                            <input class="form-control form-control-sm" type="text" v-model="medios.CHEQUE" disabled>
                                          <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm" v-on:click="formatoCheque"><a href="#"><font-awesome-icon icon="external-link-alt"/></a></span> 
                                          </div>

                                        </div>

                                      <!-- ------------------------------------------------------------------------ -->

                                    </div>  

                                    <!-- ------------------------------------------------------------------------ -->

                                    <!-- CHEQUE -->

                                    <div class="col-md-2">
                                      <label for="validationTooltip01">Vales:</label>
                                    </div>  

                                    <!-- ------------------------------------------------------------------------ -->

                                    <div class="col-md-10">

                                      <!-- ------------------------------------------------------------------------ -->

                                      <!-- MONTO PAGADO -->

                                        <div class="input-group input-group-sm mb-3" >
                                          <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><font-awesome-icon icon="address-card"/></span>
                                          </div>
                                          <input class="form-control form-control-sm" type="text" disabled>
                                        </div>

                                      <!-- ------------------------------------------------------------------------ -->

                                    </div>

                                    <!-- ------------------------------------------------------------------------ -->

                                    <!-- GIROS -->

                                    <div class="col-md-2">
                                      <label for="validationTooltip01">Giros</label>
                                    </div>  

                                    <!-- ------------------------------------------------------------------------ -->

                                    <div class="col-md-10">

                                      <!-- ------------------------------------------------------------------------ -->

                                      <!-- MONTO PAGADO -->

                                        <div class="input-group input-group-sm mb-3" >
                                          <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><font-awesome-icon icon="sync-alt"/></span>
                                          </div>
                                          <input class="form-control form-control-sm" type="text" disabled>
                                        </div>

                                      <!-- ------------------------------------------------------------------------ -->

                                    </div>
                                  </div>  
                              </div>

                            </div>  
                        </div>

                        <!-- ------------------------------------------------------------------------ -->

                        <!-- FOOTER -->

                        <div class="modal-footer">
                          <div class="col-md-6 mb-3">
                              <button type="button" class="btn btn-primary btn-lg btn-block" v-on:click="aceptar">Aceptar</button>
                          </div>

                          <div class="col-md-6 mb-3">
                              <button type="button" class="btn btn-secondary btn-lg btn-block" data-dismiss="modal">Cancelar</button>
                          </div> 
                        </div>

                        <!-- ------------------------------------------------------------------------ -->

                      </div>
                    </div>
                  </div> 

                  <tarjeta-modal ref="compontente_modal_tarjeta" @codigo="codigoTarjeta" @descripcion="descripcionTarjeta"></tarjeta-modal> 
                  <cheque-modal ref="compontente_modal_cheque" @data="sumarCheques" :cotizacion="cotizacion" :moneda_principal="moneda"></cheque-modal> 
	</div>	
</template>
<script>
	export default {
      props: ['shadow', 'total', 'procesar', 'moneda', 'total_crudo', 'candec'],
      data(){
        return {
            formas: [
	            {'CODIGO': 1, 'DESCRIPCION': 'EFECTIVO'},
	            {'CODIGO': 2, 'DESCRIPCION': 'CHEQUE'},
	            {'CODIGO': 3, 'DESCRIPCION': 'TARJETA'},
	            {'CODIGO': 4, 'DESCRIPCION': 'VALE'},
            ],
            opciones: [],
            opcion: '',
            medios: {
              SALDO: '0',
              MEDIOS: '0',
              VUELTO: '0',
              TARJETA: '0',
              EFECTIVO: '0',
              CHEQUE: '0'
            },
            monedas: {
              GUARANIES: '',
              DOLARES: '',
              PESOS: '',
              REALES: ''
            },
            cotizacion: {
              guaranies: '', 
              dolares: '',
              pesos: '',
              reales: '',
              deshabilitar_gs: '',
              deshabilitar_$: '',
              deshabilitar_ps: '',
              deshabilitar_rs: '',
              formula_gs: '',
              formula_$: '',
              formula_ps: '',
              formula_rs: '',
              formula_gs_reves: '',
              formula_usd_reves: '',
              formula_ps_reves: '',
              formula_rs_reves: '',
              candec_gs: '',
              candec_$: '',
              candec_ps: '',
              candec_rs: '',
              moneda_gs: '',
              moneda_$: '',
              moneda_ps: '',
              moneda_rs: '',
              moneda: '',
              candec: ''
            },
            totales: {
              GUARANIES: '',
              DOLARES: '',
              PESOS: '',
              REALES: ''
            },
            total_modificable: 0,
            respuesta: {
              EFECTIVO: '0',
              CODIGO_TARJETA: ''
            },
            vuelto: {
              guaranies: '',
              dolares: '',
              pesos: '',
              reales: ''
            },
            tarjeta: {
              codigo: '',
              descripcion: 'Ninguno'
            },
            mostrar: {
              TARJETA_SELECCION: false
            },
            cheque: ''
        }
      }, 
      methods: {
      		llamarPadre(valor){

              // ------------------------------------------------------------------------

              // VERIFICAR OPCIONES 

              // ------------------------------------------------------------------------

              // ENVIAR DESCRIPCION TELA A PADRE

              this.$emit('codigo_forma', valor);

              // ------------------------------------------------------------------------

          }, 
          procesarFormas(){

            // ------------------------------------------------------------------------

            // MOSTRAR LA PREGUNTA DE ELIMINAR 

            $('#staticBackdrop').modal('show');

            // ------------------------------------------------------------------------

          }, formatoGuaranies(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // DAR FORMATO A CANTIDAD
            
            me.monedas.GUARANIES = Common.darFormatoCommon(me.monedas.GUARANIES, me.cotizacion.candec_gs);

            // ------------------------------------------------------------------------

            // CALCULAR VALORES 
            
            this.sumarMonedas();

            // ------------------------------------------------------------------------

          }, formatoTarjeta(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // DAR FORMATO A CANTIDAD
            
            me.medios.TARJETA = Common.darFormatoCommon(me.medios.TARJETA, me.cotizacion.candec_gs);

            // ------------------------------------------------------------------------

            if (me.medios.TARJETA !== '0') {
              this.$refs.compontente_modal_tarjeta.mostrarModal();
              this.mostrar.TARJETA_SELECCION = true;
            } else {
              this.tarjeta.codigo = '';
              this.tarjeta.descripcion = 'Ninguno';
              this.mostrar.TARJETA_SELECCION = false;
            }
            
            // ------------------------------------------------------------------------

            // CALCULAR VALORES 
            
            this.sumarMonedas();

            // ------------------------------------------------------------------------

          }, formatoCheque(){
            
            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // DAR FORMATO A CANTIDAD
            
            me.medios.CHEQUE = Common.darFormatoCommon(me.medios.CHEQUE, me.cotizacion.candec_gs);

            // ------------------------------------------------------------------------

            this.$refs.compontente_modal_cheque.mostrarModal();

            // ------------------------------------------------------------------------

            // CALCULAR VALORES 
            
            this.sumarMonedas();

            // ------------------------------------------------------------------------

          }, formatoDolares(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // DAR FORMATO A CANTIDAD
            
            me.monedas.DOLARES = Common.darFormatoCommon(me.monedas.DOLARES, me.cotizacion.candec_$);

            // ------------------------------------------------------------------------

            // CALCULAR VALORES 
            
            this.sumarMonedas();

            // ------------------------------------------------------------------------

          }, formatoPesos(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // DAR FORMATO A CANTIDAD
            
            me.monedas.PESOS = Common.darFormatoCommon(me.monedas.PESOS, me.cotizacion.candec_ps);

            // ------------------------------------------------------------------------

            // CALCULAR VALORES 
            
            this.sumarMonedas();

            // ------------------------------------------------------------------------

          }, formatoReales(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // DAR FORMATO A CANTIDAD
            
            me.monedas.REALES = Common.darFormatoCommon(me.monedas.REALES, me.cotizacion.candec_rs);

            // ------------------------------------------------------------------------

            // CALCULAR VALORES 

            this.sumarMonedas();

            // ------------------------------------------------------------------------

          }, obtenerCotizacionyMoneda(){

            // ------------------------------------------------------------------------

            let me = this;

            // ------------------------------------------------------------------------

            Common.cotizacionyMonedaFormaPagoCommon().then(data => {
                me.cotizacion = data;
                this.total_modificable = this.total_crudo;
                me.calcularTotalesMoneda();
            });

            // ------------------------------------------------------------------------

          }, calcularTotalesMoneda(){

            // ------------------------------------------------------------------------

            // GUARANIES 

            this.totales.GUARANIES = Common.formulaCommon(this.cotizacion.formula_gs, this.total_modificable, this.cotizacion.guaranies, this.cotizacion.candec_gs, this.moneda, this.cotizacion.moneda_gs);

            // ------------------------------------------------------------------------

            // DOLARES 

            this.totales.DOLARES = Common.formulaCommon(this.cotizacion.formula_$, this.total_modificable, this.cotizacion.dolares, this.cotizacion.candec_$, this.moneda, this.cotizacion.moneda_$);

            // ------------------------------------------------------------------------

            // PESOS

            this.totales.PESOS = Common.formulaCommon(this.cotizacion.formula_ps, this.total_modificable, this.cotizacion.pesos, this.cotizacion.candec_ps, this.moneda, this.cotizacion.moneda_ps);

            // ------------------------------------------------------------------------

            // REALES

            this.totales.REALES = Common.formulaCommon(this.cotizacion.formula_rs, this.total_modificable, this.cotizacion.reales, this.cotizacion.candec_rs, this.moneda, this.cotizacion.moneda_rs);

            // ------------------------------------------------------------------------

          }, calculoResta(){

            // ------------------------------------------------------------------------

            // TOTAL

            me.totales.GUARANIES = Common.calcularCotizacionRestaCommon(this.total_crudo, me.monedas.GUARANIES, this.cotizacion.candec_gs, this.moneda, this.cotizacion.moneda_gs, this.cotizacion.guaranies)

            // ------------------------------------------------------------------------

            me.total_modificable = me.totales.GUARANIES;

            // ------------------------------------------------------------------------

            me.calcularTotalesMoneda();

            // ------------------------------------------------------------------------

          }, sumarMonedas() {

            var dolares = 0, guaranies = 0, pesos = 0, reales = 0, total = 0, vuelto = 0;

            // ------------------------------------------------------------------------

            // TOTALES MONEDAS 

            guaranies = Common.formulaCommon(this.cotizacion.formula_gs_reves, this.monedas.GUARANIES, this.cotizacion.guaranies, this.cotizacion.candec_gs, this.moneda, this.cotizacion.moneda_gs);

            dolares =  Common.formulaCommon(this.cotizacion.formula_usd_reves, this.monedas.DOLARES, this.cotizacion.dolares, this.cotizacion.candec_$, this.moneda, this.cotizacion.moneda_$);

            pesos =  Common.formulaCommon(this.cotizacion.formula_ps_reves, this.monedas.PESOS, this.cotizacion.pesos, this.cotizacion.candec_ps, this.moneda, this.cotizacion.moneda_ps);

            reales = Common.formulaCommon(this.cotizacion.formula_rs_reves, this.monedas.REALES, this.cotizacion.reales, this.cotizacion.candec_rs, this.moneda, this.cotizacion.moneda_rs);

            // ------------------------------------------------------------------------

            // TOTAL MONEDAS 

            total = (Common.sumarCommon(Common.sumarCommon(pesos, reales, this.candec), Common.sumarCommon(dolares, guaranies, this.candec), this.candec));

            // ------------------------------------------------------------------------

            // TARJETA 

            total = Common.sumarCommon(this.medios.TARJETA, total, this.candec);

            // ------------------------------------------------------------------------

            // CHEQUE 

            total = Common.sumarCommon(this.medios.CHEQUE, total, this.candec);

            // ------------------------------------------------------------------------

            // VUELTOS MONEDAS 
            
            vuelto = Common.restarCommon(total, this.total_crudo, this.candec);

            this.vuelto.guaranies = Common.formulaCommon(this.cotizacion.formula_gs, vuelto, this.cotizacion.guaranies, this.cotizacion.candec_gs, this.moneda, this.cotizacion.moneda_gs);

            this.vuelto.dolares = Common.formulaCommon(this.cotizacion.formula_$, vuelto, this.cotizacion.dolares, this.cotizacion.candec_$, this.moneda, this.cotizacion.moneda_$);

            this.vuelto.pesos = Common.formulaCommon(this.cotizacion.formula_ps, vuelto, this.cotizacion.pesos, this.cotizacion.candec_ps, this.moneda, this.cotizacion.moneda_ps);

            this.vuelto.reales = Common.formulaCommon(this.cotizacion.formula_rs, vuelto, this.cotizacion.reales, this.cotizacion.candec_rs, this.moneda, this.cotizacion.moneda_rs);

            // ------------------------------------------------------------------------

            this.medios.EFECTIVO = total;
            this.medios.VUELTO = vuelto;
            this.medios.MEDIOS = total;
            this.medios.SALDO = Common.saldoCommon(this.total_crudo, total, this.candec);

            // ------------------------------------------------------------------------

          }, sumarCheques(data) {

            // ------------------------------------------------------------------------

            let me = this;
            var dolares = 0, guaranies = 0, pesos = 0, reales = 0, valor = 0, total = 0;

            // ------------------------------------------------------------------------

            // RECORRER DATOS DEL CHEQUE 

            data.map(function(x) {

              // ------------------------------------------------------------------------

              // TOTALES MONEDAS CHEQUE
              
              if (x.MONEDA === "1") {

                valor = Common.formulaCommon(me.cotizacion.formula_gs_reves, x.IMPORTE, me.cotizacion.guaranies, me.cotizacion.candec_gs, me.moneda, me.cotizacion.moneda_gs);
                guaranies = Common.sumarCommon(guaranies, valor, me.candec);

              } else if (x.MONEDA === "2") {

                valor =  Common.formulaCommon(me.cotizacion.formula_usd_reves, x.IMPORTE, me.cotizacion.dolares, me.cotizacion.candec_$, me.moneda, me.cotizacion.moneda_$);
                dolares = Common.sumarCommon(dolares, valor, me.candec);

              } else if (x.MONEDA === "3") {

                valor =  Common.formulaCommon(me.cotizacion.formula_ps_reves, x.IMPORTE, me.cotizacion.pesos, me.cotizacion.candec_ps, me.moneda, me.cotizacion.moneda_ps);
                pesos = Common.sumarCommon(pesos, valor, me.candec);

              } else if (x.MONEDA === "4") {

                valor = Common.formulaCommon(me.cotizacion.formula_rs_reves, x.IMPORTE, me.cotizacion.reales, me.cotizacion.candec_rs, me.moneda, me.cotizacion.moneda_rs);
                reales = Common.sumarCommon(reales, valor, me.candec);

              }

              // ------------------------------------------------------------------------

            });

            // ------------------------------------------------------------------------

            // TOTAL CHEQUE 

            total = (Common.sumarCommon(Common.sumarCommon(pesos, reales, me.candec), Common.sumarCommon(dolares, guaranies, me.candec), me.candec));

            // ------------------------------------------------------------------------

            this.medios.CHEQUE = total;

            // ------------------------------------------------------------------------

            // CARGAR CHEQUE 

            this.cheque = data;

            // ------------------------------------------------------------------------

            // RECALCULAR TODO 

            this.sumarMonedas();

            // ------------------------------------------------------------------------

          }, codigoTarjeta(valor) {

            // ------------------------------------------------------------------------

            // OBTENER CODIGO DE TARJETA 

            this.tarjeta.codigo = valor;

            // ------------------------------------------------------------------------

          }, descripcionTarjeta(valor) {

            // ------------------------------------------------------------------------

            // OBTENER DESCRIPCION DE TARJETA 

            this.tarjeta.descripcion = valor;

            // ------------------------------------------------------------------------

          }, aceptar(){

            // ------------------------------------------------------------------------

            let me = this;
            
            // ------------------------------------------------------------------------

            // ENVIAR DATOS
            
            this.respuesta = {
              EFECTIVO: this.medios.EFECTIVO,
              CODIGO_TARJETA: this.tarjeta.codigo,
              TARJETA: this.medios.TARJETA,
              VUELTO: this.medios.VUELTO,
              SALDO: this.medios.SALDO,
              GUARANIES: this.monedas.GUARANIES,
              DOLARES: this.monedas.DOLARES,
              PESOS: this.monedas.PESOS,
              REALES: this.monedas.REALES,
              CHEQUE: this.cheque
            }

            // ------------------------------------------------------------------------

            // EMITIR DATOS 

            if (me.medios.EFECTIVO !== '0') {
              this.$emit('datos', this.respuesta);
              this.limpiar();
              $('#staticBackdrop').modal('hide');
            } else {
              alert("NO HAY NADA");
            }

            // ------------------------------------------------------------------------

          }, limpiar() {

            // ------------------------------------------------------------------------

            this.medios = {
              EFECTIVO: '0',
              TARJETA: '0',
              VUELTO: '0',
              SALDO: '0',
              MEDIOS: '0'
            }

            this.monedas = {
              GUARANIES: '',
              DOLARES: '',
              PESOS: '',
              REALES: ''
            }

            this.tarjeta.codigo = '';
            this.cheque = '';

            // ------------------------------------------------------------------------

          }
      },
        mounted() {
        	
        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES 

        	let me = this;
            

        	// ------------------------------------------------------------------------
        	
          me.obtenerCotizacionyMoneda();
        	
          // ------------------------------------------------------------------------

        }
    }
</script>