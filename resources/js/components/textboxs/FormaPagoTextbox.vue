<template>
	<div>
            <!-- <label for="validationTooltip01">Forma de Pago</label>
            <select class="custom-select custom-select-sm" v-on:change="llamarPadre($event.target.value)" v-on:change="" @input="$emit('input', $event.target.value)">
                    <option v-for="forma in formas" :selected="forma.CODIGO === parseInt(value)" :value="forma.CODIGO">{{ forma.CODIGO }} - {{ forma.DESCRIPCION }}</option>
            <v-select :options="formas"  label="DESCRIPCION" track-by="CODIGO" :multiple="true" @input="llamarPadre" v-model="opciones" ></v-select>
            </select>
			        -->

              <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
                      <div class="modal-content">

                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalCenterTitle"><small> MEDIOS DE PAGO </small></h5>

                          <!-- ------------------------------------------------------------------------ -->

                          <!-- PAGO AL ENTREGAR -->
                  
                          <div class="my-1" v-if="tipo !== 2 && tipo !== 3 && tipo !== 4">
                            <div class="custom-control custom-switch mr-sm-3">
                              <input type="checkbox" class="custom-control-input" id="switchPagoEntrega" v-on:change="pagoAlEntregar" v-model="checked.PAGO_AL_ENTREGAR">
                              <label class="custom-control-label" for="switchPagoEntrega" >PAGO AL ENTREGAR</label>
                            </div>
                          </div>

                          <!-- ------------------------------------------------------------------------ -->

                          <div class="text-right" v-if="cliente.credito.total_agregado > 0">
                            <span class="badge badge-primary">Crédito: {{cliente.credito.total_agregado}} </span>
                          </div>

                          <div class="float-right" v-if="deshabilitar.credito && tipo !== 3 && tipo !== 4">
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalCredito" v-on:click="calcularCredito">Agregar Crédito</button>
                          </div>

                        </div>

                        <div class="modal-body">  
                            <div class="row">

                              <!-- ------------------------------------------------------------------------ -->

                              <!-- RETENCION -->

                              <div class="col-md-12 mb-3 mt-1">
                                <div class="text-left" v-if="retencion > 0">
                                  <span class="badge badge-warning">Retencion: {{retencion}} </span>
                                </div>
                              </div>

                              <!-- ------------------------------------------------------------------------ -->

                              <div class="col-md-12">

                                <!-- ------------------------------------------------------------------------ -->

                                <div class="card  bg-dark card-body border-left-info">

                                  <div class="row">

                                    <!-- ------------------------------------------------------------------------ -->
                                      
                                    <div class="col-md-3 mt-3"">
                                      <label for="validationTooltip01" class="text-light">TOTAL</label>
                                      <h1 class="text-success"><b>{{total}}</b></h1>
                                    </div>

                                    <div class="col-md-3 mt-3"">
                                      <label for="validationTooltip01" class="text-light">SALDO</label>
                                      <h2 class="text-light">{{medios.SALDO}}</h2>
                                    </div>

                                    <div class="col-md-3 mt-3"">
                                      <label for="validationTooltip01" class="text-light">MEDIOS</label>
                                      <h2 class="text-info">{{medios.MEDIOS}}</h2>
                                    </div>

                                    <div class="col-md-3 mt-3"">
                                      <label for="validationTooltip01" class="text-light">VUELTO</label>
                                      <h2 class="text-light">{{medios.VUELTO}}</h2>
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

                              <div v-if="checked.PAGO_AL_ENTREGAR === false" class="col-md-6 mt-3">
                                  <div class="row">
                                    <div class="col-md-12">

                                      <!-- ------------------------------------------------------------------------ -->

                                      <!-- GUARANIES -->

                                        <div class="row">

                                          <div class="col-md-2">
                                            <label for="validationTooltip01">Guaranies:</label>
                                          </div>  

                                          <!-- <div class="col-md-3">
                                            <input class="form-control form-control-sm" type="text" v-model="totales.GUARANIES" v-on:blur="formatoGuaranies" disabled>
                                          </div> -->

                                          <div class="col-md-4">
                                            <div class="input-group input-group-sm mb-3" >
                                              <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">{{cotizacion.descripcion_gs}}</span>
                                              </div>
                                              <input class="form-control form-control-sm" type="text" id="guaranies_input"  v-model="monedas.GUARANIES" v-on:blur="formatoGuaranies" :disabled="cotizacion.deshabilitar_gs">
                                            </div>
                                          </div>

                                          <div class="col-md-6">
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

                                          <!-- <div class="col-md-3">
                                            <input class="form-control form-control-sm" type="text"  v-model="totales.DOLARES"  v-on:blur="formatoDolares" disabled>
                                          </div> -->

                                          <div class="col-md-4">
                                            <div class="input-group input-group-sm mb-3" >
                                              <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">{{cotizacion.descripcion_$}}</span>
                                              </div>
                                              <input class="form-control form-control-sm" type="text" id="dolares_input"  v-model="monedas.DOLARES"  v-on:blur="formatoDolares" :disabled="cotizacion.deshabilitar_$">
                                            </div>
                                          </div>

                                          <div class="col-md-6">
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

                                          <!-- <div class="col-md-3">
                                            <input class="form-control form-control-sm" type="text"  v-model="totales.PESOS" v-on:blur="formatoPesos" disabled>
                                          </div> -->

                                          <div class="col-md-4">
                                            <div class="input-group input-group-sm mb-3" >
                                              <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">{{cotizacion.descripcion_ps}}</span>
                                              </div>
                                              <input class="form-control form-control-sm" type="text" id="pesos_input"  v-model="monedas.PESOS" v-on:blur="formatoPesos" :disabled="cotizacion.deshabilitar_ps">
                                            </div>
                                          </div>

                                          <div class="col-md-6">
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

                                          <!-- <div class="col-md-3">
                                            <input class="form-control form-control-sm" type="text" v-model="totales.REALES" v-on:blur="formatoReales" disabled>
                                          </div> -->

                                          <div class="col-md-4">
                                            <div class="input-group input-group-sm mb-3" >
                                              <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">{{cotizacion.descripcion_rs}}</span>
                                              </div>
                                              <input class="form-control form-control-sm" type="text" id="reales_input"  v-model="monedas.REALES" v-on:blur="formatoReales" :disabled="cotizacion.deshabilitar_rs">
                                            </div>
                                          </div>

                                          <div class="col-md-6">
                                            <input class="form-control form-control-sm" type="text" v-model="vuelto.reales" disabled>
                                          </div>

                                        </div>

                                      <!-- ------------------------------------------------------------------------ -->

                                    </div> 

                                    <div class="col-md-12">

                                      <!-- ------------------------------------------------------------------------ -->

                                      <!-- REALES -->

                                        <div class="row" v-if="tipo !== 3 && tipo !== 4">

                                          <div class="col-md-2">
                                            <label for="validationTooltip01">Descuento</label>
                                          </div> 

                                          <div class="col-md-7">
                                            <div class="input-group input-group-sm mb-3" >
                                              <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">%</span>
                                              </div>
                                              <input class="form-control form-control-sm" type="number" max="100" min="0" v-model="descuento.PORCENTAJE" v-on:blur="formatoDescuento" >
                                            </div>
                                          </div>

                                          <div class="col-md-3">
                                            <input class="form-control form-control-sm" type="text" v-model="medios.DESCUENTO" disabled>
                                          </div>

                                        </div>

                                      <!-- ------------------------------------------------------------------------ -->

                                    </div> 
                                    <!-----------------------CUPONES----------------------------------------------- -->

                                      <div class="col-md-12">
                                        <div class="row">

                                          <div class="col-md-9 input-group mb-4 border rounded-pill p-2">
                                            <input type="text" placeholder="Aplicar cupón" v-model='cupon.codigo' v-on:blur="obtener_cupon" aria-describedby="button-addon3" class="form-control form-control-sm border-0">
                                            <div class="input-group-append border-0">
                                              <button id="button-addon3" type="button" v-on:click="quitar_cupon" class="btn btn-sm btn-dark px-4 rounded-pill"><i class="fa fa-gift mr-2"></i>Quitar cupón</button>
                                            </div>
                                              
                                          </div>

                                          <div class="col-md-3">
                                            <input class="form-control form-control-sm" type="text" v-model="medios.CUPON"  disabled>
                                          </div>

                                       </div>
                                          
                                     
                                    
                                    </div>
                                  </div>

                                  

                              </div> 

                              
                                
                              <div class="col-md-6 mt-3" v-if="checked.PAGO_AL_ENTREGAR === false">
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
                                          <input class="form-control form-control-sm" placeholder="Solo en guaranies." type="text" v-model="medios.TARJETA" v-on:blur="formatoTarjeta">
                                        </div>

                                        <small id="passwordHelpBlock" class="form-text text-muted" v-if="mostrar.TARJETA_SELECCION">
                                            <p><a href="#" v-on:click="formatoTarjeta" class="text-primary">{{tarjeta.descripcion}}</a></p>
                                        </small>

                                      <!-- ------------------------------------------------------------------------ -->

                                    </div>

                                    <!-- ------------------------------------------------------------------------ -->

                                    <!-- TRANSFERENCIA -->

                                    <div class="col-md-2">
                                      <label for="validationTooltip01">Transf.</label>
                                    </div>  

                                    <div class="col-md-10">

                                      <!-- ------------------------------------------------------------------------ -->

                                      <!-- MONTO PAGADO -->

                                        
                                        <div class="input-group input-group-sm mb-3" >
                                          <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><font-awesome-icon icon="university"/></span>
                                          </div>
                                          <input class="form-control form-control-sm" type="text" v-model="medios.TRANSFERENCIA" v-on:blur="formatoTransferencia">
                                        </div>

                                        <small id="passwordHelpBlock" class="form-text text-muted" v-if="mostrar.BANCO_SELECCION">
                                            <p><a href="#" v-on:click="formatoTransferencia" class="text-primary">{{banco.descripcion}}</a></p>
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

                                    <div class="col-md-2" v-if="tipo !== 4">
                                      <label for="validationTooltip01">Vales:</label>
                                    </div>  

                                    <!-- ------------------------------------------------------------------------ -->

                                    <div class="col-md-10" v-if="tipo !== 4">

                                      <!-- ------------------------------------------------------------------------ -->

                                      <!-- MONTO PAGADO -->

                                        <div class="input-group input-group-sm mb-3" >
                                          <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><font-awesome-icon icon="address-card"/></span>
                                          </div>
                                          <input class="form-control form-control-sm" type="text" v-model="medios.VALES" v-on:blur="formatoVale">
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
                                          <input class="form-control form-control-sm" type="text" placeholder="Solo en guaranies." v-model="medios.GIROS" v-on:blur="formatoGiro">
                                        </div>

                                        <small id="passwordHelpBlock" class="form-text text-muted" v-if="mostrar.GIROS_SELECCION">
                                            <p><a href="#" v-on:click="formatoGiro" class="text-primary">{{giro.descripcion}}</a></p>
                                        </small>

                                      <!-- ------------------------------------------------------------------------ -->

                                    </div>
                                  </div>  
                              </div>

                            </div>  
                        </div>

                        <!-- ------------------------------------------------------------------------ -->

                        <!-- FOOTER -->

                        <div class="modal-footer">
                                <button type="button" class="btn btn-primary btn-sm" v-on:click="aceptar">(Esc) Aceptar</button>
                            
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
                        </div>

                        <!-- ------------------------------------------------------------------------ -->

                      </div>
                    </div>
                  </div> 

                  <tarjeta-modal ref="compontente_modal_tarjeta" @codigo="codigoTarjeta" @descripcion="descripcionTarjeta"></tarjeta-modal> 
                  <giro-modal ref="compontente_modal_giro" @codigo="codigoGiro" @descripcion="descripcionGiro"></giro-modal> 
                  <banco-modal ref="compontente_modal_banco" @codigo="codigoBanco" @descripcion="descripcionBanco"></banco-modal> 
                  <cheque-modal ref="compontente_modal_cheque" @data="sumarCheques" :cotizacion="cotizacion" :moneda_principal="moneda"></cheque-modal> 

                  <!-- ------------------------------------------------------------------------ -->

                  <!-- MODAL IMPRESORA - TICKET - VUELTO -->

                  <div class="modal fade" id="modalImpresion" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">

                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Opciones</h5>
                        </div>

                        <div class="modal-body">

                          <div class="row" v-if="tipo !== 2 && tipo !== 4">
                            <legend class="col-form-label col-sm-2 pt-0">Impresión</legend>
                            <div class="col-sm-10">
                              <div class="form-check form-check-inline">
                                <input v-model="radio.impresion" v-on:change="seleccionar" class="form-check-input" type="radio" name="gridRadios" id="radioImpresion1" value="1">
                                <label class="form-check-label" for="radioImpresion1">
                                  Ticket
                                </label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input v-model="radio.impresion" v-on:change="seleccionar" class="form-check-input" type="radio" name="gridRadios" id="radioImpresion2" value="2">
                                <label class="form-check-label" for="radioImpresion2">
                                  Factura / Ticket
                                </label>
                              </div>
                            </div>
                          </div>

                          <div class="row" v-if="checked.PAGO_AL_ENTREGAR === false">
                            <div class="col-md-12">
                              <hr>
                            </div>
                          </div>  
                          <div class="row" v-if="checked.PAGO_AL_ENTREGAR === false">
                            <legend class="col-form-label col-sm-2 pt-0">Vuelto</legend>
                            <div class="col-sm-10">
                              <div class="form-check form-check-inline">
                                <input v-on:change="seleccionar" v-model="radio.vuelto" class="form-check-input" type="radio" name="radioVuelto" id="gridRadios1" value="1" :disabled="cotizacion.deshabilitar_gs">
                                <label class="form-check-label" for="gridRadios1">
                                  Guaranies
                                </label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input v-on:change="seleccionar" v-model="radio.vuelto" class="form-check-input" type="radio" name="radioVuelto" id="gridRadios2" value="2" :disabled="cotizacion.deshabilitar_$">
                                <label class="form-check-label" for="gridRadios2">
                                  Dolares
                                </label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input v-on:change="seleccionar" v-model="radio.vuelto" class="form-check-input" type="radio" name="radioVuelto" id="gridRadios3" value="3" :disabled="cotizacion.deshabilitar_rs">
                                <label class="form-check-label" for="gridRadios3">
                                  Reales
                                </label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input v-on:change="seleccionar" v-model="radio.vuelto" class="form-check-input" type="radio" name="radioVuelto" id="gridRadios4" value="4" :disabled="cotizacion.deshabilitar_ps">
                                <label class="form-check-label" for="gridRadios4">
                                  Pesos
                                </label>
                              </div>
                            </div>
                          </div>

                          <div class="row" v-if="checked.PAGO_AL_ENTREGAR === false">
                            <div class="col-md-12">
                              <hr>
                            </div>
                          </div> 

                          <div class="row" v-if="checked.PAGO_AL_ENTREGAR === false">
                            <div class="col-md-12">
                              <div class="text-center">
                                <h1 class="text-primary" >{{vuelto.seleccion}} </h1>
                              </div>  
                            </div>
                          </div>

                        </div>

                        <div class="modal-footer">
                          <button type="button" class="btn btn-primary" data-dismiss="modal" v-on:click="enviar">Aceptar</button>
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        </div>

                      </div>
                    </div>
                  </div>
                  
                  <!-- ------------------------------------------------------------------------ -->

                  <!-- TOAST MONTO INSUFICIENTE -->

                  <b-toast id="toast-monto-insuficiente" variant="warning" solid>
                      <template v-slot:toast-title>
                        <div class="d-flex flex-grow-1 align-items-baseline">
                          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
                          <strong class="mr-auto">Error !</strong>
                          <small class="text-muted mr-2">insuficiente</small>
                        </div>
                      </template>
                      El total supera al pago ingresado !
                  </b-toast>

                  <!-- ------------------------------------------------------------------------ -->

                  <!-- TOAST CREDITO SUPERADO -->

                  <b-toast id="toast-credito-superado" variant="warning" solid>
                      <template v-slot:toast-title>
                        <div class="d-flex flex-grow-1 align-items-baseline">
                          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
                          <strong class="mr-auto">Error !</strong>
                          <small class="text-muted mr-2">insuficiente</small>
                        </div>
                      </template>
                      El total del credito supera lo disponible !
                  </b-toast>

                  <!-- ------------------------------------------------------------------------ -->

                 <!-- MODAL CREDITO -->

                  <div class="modal fade" id="modalCredito" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                      <div class="modal-content">

                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel"><small>CREDITO</small></h5>
                        </div>

                        <div class="modal-body">

                          <div class="row">

                            <div class="col-md-4">
                              <label class="form-check-label">
                                  Días de Crédito
                              </label>
                              <input v-model="cliente.credito.defaultDia" class="form-control form-control-sm mt-2" type="text" disabled>
                            </div>

                            <div class="col-md-4">
                              <label class="form-check-label">
                                  Límite de Crédito
                              </label>
                              <input v-model="cliente.credito.limite" class="input-sm form-control form-control-sm mt-2" type="text" disabled>
                            </div>

                            <div class="col-md-4">
                              <label class="form-check-label">
                                  Crédito Disponible
                              </label>
                              <input v-model="cliente.credito.disponible" class="input-sm form-control form-control-sm mt-2" type="text" disabled>
                            </div>
                            
                            <div class="col-md-12">
                              <hr>
                            </div>
                             
                            <div class="col-md-4">
                              <label class="form-check-label">
                                  Total a Crédito
                              </label>
                              <input v-model="cliente.credito.total" v-on:blur="formatoTotalCredito"  class="input-sm form-control form-control-sm mt-2" type="text" >
                            </div>

                            <div class="col-md-4">
                              <label class="form-check-label">
                                  Días de Crédito
                              </label>
                              <input v-model="cliente.credito.dias" v-on:change="formatoDias" class="input-sm form-control form-control-sm mt-2" type="number" >
                            </div>

                            <!-- FECHA COBRO -->

                            <div class="col-md-4">
                              <label>Vencimiento</label>
                              <div id="sandbox-container">
                                <div class="input-daterange input-group input-group-sm date">
                                  <div class="input-group-prepend ">
                                    <span class="input-group-text" id="inputGroup-sizing-sm"><font-awesome-icon icon="calendar" /></span>
                                  </div>
                                  <input type="text" class="input-sm form-control form-control-sm" id="credito_vencimiento" v-model="cliente.credito.vencimiento" data-date-format="yyyy-mm-dd" v-bind:class="{  }" disabled />
                                </div>
                              </div>  
                            </div>


                            <div class="col-md-12 mt-3" v-if="cliente.credito.total_agregado > 0">
                              <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <strong>Crédito agregado: </strong> {{cliente.credito.total_agregado}}
                                <button type="button" class="close" v-on:click="cliente.credito.total_agregado = 0; sumarMonedas();">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                            </div>
                              
                          </div> 

                        </div>

                        <div class="modal-footer">
                          <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" v-on:click="agregarCredito">Agregar</button>
                          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
                        </div>

                      </div>
                    </div>
                  </div>
                  
                  <!-- ------------------------------------------------------------------------ --> 
	</div>	
</template>
<script>
	export default {
      props: ['shadow', 'total', 'procesar', 'moneda', 'total_crudo', 'candec', 'customer', 'tipo', 'retencion'],
      watch: { 
        total_crudo: function(newVal, oldVal) {
            this.sumarMonedas();
            this.formatoDias();

        }, 
        total: function(newVal,oldVal){

             if(this.texto===''){
            
              this.textopc=0;
            }else{
              
               this.textopc=1;
            }
           
            this.obtener_cupon();
        },
        customer: function(newVal, oldVal) {
            this.formatoDias();
            this.datosCliente(newVal);
        }, 
        tipo: function(newVal, oldVal) {
            //alert(newVal);
        },
        moneda: function(newVal, oldVal) {
            this.radio.vuelto = String(newVal);
        },
        retencion: function(newVal, oldVal) {
            this.sumarMonedas();
        }
      },
      data(){
        return {
            formas: [
	            {'CODIGO': 1, 'DESCRIPCION': 'EFECTIVO'},
	            {'CODIGO': 2, 'DESCRIPCION': 'CHEQUE'},
	            {'CODIGO': 3, 'DESCRIPCION': 'TARJETA'},
	            {'CODIGO': 4, 'DESCRIPCION': 'VALE'},
            ],
            opciones: [],
            texto:'',
            opcion: '',
            textopc:0,
            medios: {
              SALDO: '0',
              MEDIOS: '0',
              VUELTO: '0',
              TARJETA: '0',
              EFECTIVO: '0',
              CHEQUE: '0',
              TRANSFERENCIA: '0',
              GIROS: '0',
              VALES: '0',
              DESCUENTO: '0',
              CUPON:'0'
            },
            cupon:{
              codigo: '',
              total:0,
              porcentaje:0,
              tipo:0,
              id:0
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
              candec: '',
              formula_gs_id: '',
              formula_usd_id: '',
              formula_ps_id: '',
              formula_rs_id: '',
              formula_gs_reves_id: '',
              formula_usd_reves_id: '',
              formula_ps_reves_id: '',
              formula_rs_reves_id: '',
            },
            totales: {
              GUARANIES: '',
              DOLARES: '',
              PESOS: '',
              REALES: ''
            },
            total_modificable: 0,
            vuelto: {
              guaranies: '',
              dolares: '',
              pesos: '',
              reales: '',
              seleccion: ''
            },
            tarjeta: {
              codigo: '',
              descripcion: 'Ninguno'
            },
            banco: {
              codigo: '',
              descripcion: 'Ninguno'
            },
            giro: {
              codigo: '',
              descripcion: 'Ninguno'
            },
            mostrar: {
              TARJETA_SELECCION: false,
              BANCO_SELECCION: false
            },
            cheque: '',
            seleccion: {
              impresion: '1',
              vuelto: '1'
            },
            radio: {
              impresion: '1',
              vuelto: '1'
            }, respuesta: {
              COTIZACION: '',
              EFECTIVO: '',
              CODIGO_TARJETA: '',
              TARJETA: '',
              CODIGO_BANCO: '',
              TRANSFERENCIA: '',
              CODIGO_ENT: '',
              GIRO: '',
              VUELTO: '',
              SALDO: '',
              GUARANIES: '',
              DOLARES: '',
              PESOS: '',
              REALES: '',
              CHEQUE: '',
              TIPO_IMPRESION: '',
              OPCION_VUELTO: '',
              PAGO_AL_ENTREGAR: false
            }, descuento: {
              PORCENTAJE: '0'
            }, cliente: {
              credito: {
                defaultDia: '',
                limite: '',
                disponible: '',
                total: '0',
                dias: '90',
                vencimiento: '',
                total_agregado: '0'
              }
            }, deshabilitar: {
              credito: true
            }, checked: {
              PAGO_AL_ENTREGAR: false
            }
        }
      }, 
      methods: {
          datosCliente(codigo){

            // ------------------------------------------------------------------------

            let me = this;

            // ------------------------------------------------------------------------

            // SI CODIGO ES NULO RETORNAR 
            
            if (codigo === '') {
              return;
            }

            // ------------------------------------------------------------------------

            // OBTENER DATOS DEL CLIENTE 

            Common.obtenerCreditoClienteCommon(codigo).then(data => {

              // ------------------------------------------------------------------------

              // INICIAR VARIABLES 

              me.cliente.credito.limite = Common.darFormatoCommon(data.cliente.LIMITE_CREDITO, me.cotizacion.candec);
              me.cliente.credito.dias = data.cliente.DIAS_CREDITO;
              me.cliente.credito.disponible = Common.darFormatoCommon(data.cliente.CREDITO_DISPONIBLE, me.cotizacion.candec);
              me.cliente.credito.defaultDia = data.cliente.DIAS_CREDITO;

              // ------------------------------------------------------------------------

              // DESHABILITAR CREDITO DE ACUERDO A LO DISPONIBLE 
              
              if (Common.quitarComaCommon(me.cliente.credito.limite) === '0' || me.cliente.credito.dias === 0 || me.cliente.credito.disponible === 0 || Common.quitarComaCommon(me.cliente.credito.disponible) === '0.00') {
                me.deshabilitar.credito = false;
              } else {
                me.deshabilitar.credito = true;
              }

              // ------------------------------------------------------------------------

              me.formatoDias();

              // ------------------------------------------------------------------------

            })

            // ------------------------------------------------------------------------

          },
          agregarCredito(){

            // ------------------------------------------------------------------------

            // SELECCIONAR

            this.cliente.credito.total_agregado = this.cliente.credito.total;

            // ------------------------------------------------------------------------

            this.sumarMonedas();

            // ------------------------------------------------------------------------

            // CONSULTAR CREDITO DE CLIENTE 


            // ------------------------------------------------------------------------

          },
          seleccionar(){

            // ------------------------------------------------------------------------

            // SELECCIONAR 

            this.respuesta.TIPO_IMPRESION = this.radio.impresion;
            this.respuesta.OPCION_VUELTO = this.radio.vuelto;

            // ------------------------------------------------------------------------

            // MOSTRAR VUELTO SELECCIONADO 

            if (this.radio.vuelto === "1") {
              this.vuelto.seleccion = this.vuelto.guaranies;
            } else if (this.radio.vuelto === "2") {
              this.vuelto.seleccion = this.vuelto.dolares;
            } else if (this.radio.vuelto === "3") {
              this.vuelto.seleccion = this.vuelto.reales;
            } else if (this.radio.vuelto === "4") {
              this.vuelto.seleccion = this.vuelto.pesos;
            } 

            // ------------------------------------------------------------------------
          },
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

            // CAMBIAR EL DEFAULT DE VUELTO POR LA MONEDA 

            this.radio.vuelto = String(this.moneda);
            this.respuesta.OPCION_VUELTO = String(this.moneda);

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

            // DAR FORMATO A TARJETA
            
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

          }, formatoGiro(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // DAR FORMATO A TARJETA
            
            me.medios.GIROS = Common.darFormatoCommon(me.medios.GIROS, me.cotizacion.candec_gs);

            // ------------------------------------------------------------------------

            if (me.medios.GIROS !== '0') {
              this.$refs.compontente_modal_giro.mostrarModal();
              this.mostrar.GIROS_SELECCION = true;
            } else {
              this.giro.codigo = '';
              this.giro.descripcion = 'Ninguno';
              this.mostrar.GIROS_SELECCION = false;
            }
            
            // ------------------------------------------------------------------------

            // CALCULAR VALORES 
            
            this.sumarMonedas();

            // ------------------------------------------------------------------------

          }, formatoVale(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // DAR FORMATO A TARJETA
            
            me.medios.VALES = Common.darFormatoCommon(me.medios.VALES, me.cotizacion.candec);
            
            // ------------------------------------------------------------------------

            // CALCULAR VALORES 
            
            this.sumarMonedas();

            // ------------------------------------------------------------------------

          }, formatoTransferencia(){
           
            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // DAR FORMATO A TARJETA
            
            me.medios.TRANSFERENCIA = Common.darFormatoCommon(me.medios.TRANSFERENCIA, me.cotizacion.candec_gs);

            // ------------------------------------------------------------------------

            if (me.medios.TRANSFERENCIA !== '0') {
              this.$refs.compontente_modal_banco.mostrarModal();
              this.mostrar.BANCO_SELECCION = true;
            } else {
              this.banco.codigo = '';
              this.banco.descripcion = 'Ninguno';
              this.mostrar.BANCO_SELECCION = false;
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
            
            me.medios.CHEQUE = Common.darFormatoCommon(me.medios.CHEQUE, me.cotizacion.candec);

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

          }, formatoTotalCredito(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // SUPERA LO DISPONIBLE

            if(
              (parseFloat(Common.quitarComaCommon(me.cliente.credito.total)) > parseFloat(Common.quitarComaCommon(me.cliente.credito.disponible)))
               || (parseFloat(Common.quitarComaCommon(me.medios.SALDO)) < parseFloat(Common.quitarComaCommon(me.cliente.credito.total)))) 
               {
              me.cliente.credito.total = Common.darFormatoCommon(0, me.cotizacion.candec);
              this.$bvToast.show('toast-credito-superado');
              return;
            }

            // ------------------------------------------------------------------------

            // DAR FORMATO A CANTIDAD

            me.cliente.credito.total = Common.darFormatoCommon(me.cliente.credito.total, me.cotizacion.candec);

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

          }, formatoDescuento(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // DAR FORMATO A PORCENTAJE
            
            me.descuento.PORCENTAJE = Common.darFormatoCommon(me.descuento.PORCENTAJE, 0);

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
                me.total_modificable = me.total_crudo;
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
            
            var dolares = 0, guaranies = 0, pesos = 0, reales = 0, total = 0, vuelto = 0, tarjeta = 0, transferencia = 0, giro = 0;

            // ------------------------------------------------------------------------

            // TOTALES MONEDAS 

            guaranies = Common.formulaCommon(this.cotizacion.formula_gs_reves, this.monedas.GUARANIES, this.cotizacion.guaranies, this.cotizacion.candec, this.moneda, this.cotizacion.moneda_gs);

            dolares =  Common.formulaCommon(this.cotizacion.formula_usd_reves, this.monedas.DOLARES, this.cotizacion.dolares, this.cotizacion.candec, this.moneda, this.cotizacion.moneda_$);

            pesos =  Common.formulaCommon(this.cotizacion.formula_ps_reves, this.monedas.PESOS, this.cotizacion.pesos, this.cotizacion.candec, this.moneda, this.cotizacion.moneda_ps);

            reales = Common.formulaCommon(this.cotizacion.formula_rs_reves, this.monedas.REALES, this.cotizacion.reales, this.cotizacion.candec, this.moneda, this.cotizacion.moneda_rs);

            // ------------------------------------------------------------------------

            // TOTAL MONEDAS 
            // alert("total guaranies "+this.monedas.GUARANIES);
            // alert("guaranies "+ guaranies);
            total = (Common.sumarCommon(Common.sumarCommon(pesos, reales, this.candec), Common.sumarCommon(dolares, guaranies, this.candec), this.candec));

            // ------------------------------------------------------------------------

            // TARJETA

            tarjeta = Common.formulaCommon(this.cotizacion.formula_gs_reves, this.medios.TARJETA, this.cotizacion.guaranies, this.cotizacion.candec, this.moneda, this.cotizacion.moneda_gs);
            total = Common.sumarCommon(tarjeta, total, this.candec);

            // ------------------------------------------------------------------------

            // TRANSFERENCIA

            transferencia = Common.formulaCommon(this.cotizacion.formula_gs_reves, this.medios.TRANSFERENCIA, this.cotizacion.guaranies, this.cotizacion.candec, this.moneda, this.cotizacion.moneda_gs);
            total = Common.sumarCommon(transferencia, total, this.candec);

            // ------------------------------------------------------------------------

            // GIROS

            giro = Common.formulaCommon(this.cotizacion.formula_gs_reves, this.medios.GIROS, this.cotizacion.guaranies, this.cotizacion.candec, this.moneda, this.cotizacion.moneda_gs);
            total = Common.sumarCommon(giro, total, this.candec);

            // ------------------------------------------------------------------------

            // VALE

            total = Common.sumarCommon(Common.darFormatoCommon(this.medios.VALES, this.candec), total, this.candec);

            // ------------------------------------------------------------------------

            // CUPON

            total = Common.sumarCommon(Common.darFormatoCommon(this.medios.CUPON, this.candec), total, this.candec);

            // ------------------------------------------------------------------------

            // RETENCION

            total = Common.sumarCommon(Common.darFormatoCommon(this.retencion, this.candec), total, this.candec);

            // ------------------------------------------------------------------------

            // CREDITO

            total = Common.sumarCommon(Common.darFormatoCommon(this.cliente.credito.total_agregado, this.candec), total, this.candec);

            // ------------------------------------------------------------------------

            // CHEQUE 

            total = Common.sumarCommon(this.medios.CHEQUE, total, this.candec);

            // ------------------------------------------------------------------------

            // CALCULAR DESCUENTO

            this.medios.DESCUENTO = Common.descuentoCommon(this.descuento.PORCENTAJE, this.total_crudo, this.cotizacion.candec);

            // ------------------------------------------------------------------------

            // DESCUENTO GENERAL

            total = Common.sumarCommon(this.medios.DESCUENTO, total, this.candec);

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
                
                valor = Common.formulaCommon(me.cotizacion.formula_gs_reves, x.IMPORTE, me.cotizacion.guaranies, me.cotizacion.candec, me.moneda, me.cotizacion.moneda_gs);
                guaranies = Common.sumarCommon(guaranies, valor, me.candec);

              } else if (x.MONEDA === "2") {

                valor =  Common.formulaCommon(me.cotizacion.formula_usd_reves, x.IMPORTE, me.cotizacion.dolares, me.cotizacion.candec, me.moneda, me.cotizacion.moneda_$);
                dolares = Common.sumarCommon(dolares, valor, me.candec);

              } else if (x.MONEDA === "3") {

                valor =  Common.formulaCommon(me.cotizacion.formula_ps_reves, x.IMPORTE, me.cotizacion.pesos, me.cotizacion.candec, me.moneda, me.cotizacion.moneda_ps);
                pesos = Common.sumarCommon(pesos, valor, me.candec);

              } else if (x.MONEDA === "4") {

                valor = Common.formulaCommon(me.cotizacion.formula_rs_reves, x.IMPORTE, me.cotizacion.reales, me.cotizacion.candec, me.moneda, me.cotizacion.moneda_rs);
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

          }, codigoGiro(valor) {

            // ------------------------------------------------------------------------

            // OBTENER CODIGO DE GIRO

            this.giro.codigo = valor;

            // ------------------------------------------------------------------------

          }, descripcionGiro(valor) {

            // ------------------------------------------------------------------------

            // OBTENER DESCRIPCION DE GIRO

            this.giro.descripcion = valor;

            // ------------------------------------------------------------------------

          }, codigoBanco(valor) {

            // ------------------------------------------------------------------------

            // OBTENER CODIGO DE BANCO

            this.banco.codigo = valor;

            // ------------------------------------------------------------------------

          }, descripcionBanco(valor) {

            // ------------------------------------------------------------------------

            // OBTENER DESCRIPCION DE BANCO

            this.banco.descripcion = valor;

            // ------------------------------------------------------------------------

          }, enviar(){

            // ------------------------------------------------------------------------
            
            let me = this;
            
            me.$emit('datos', me.respuesta);
            //me.limpiar();
            $('#modalImpresion').modal('hide');

            // ------------------------------------------------------------------------

          }, controlarSeleccinMedios() {

            // ------------------------------------------------------------------------

            // SI NO ELIGE LAS ENTIDADES DE LOS MEDIOS NO PERMITIR ACEPTAR 

            // ------------------------------------------------------------------------

            if (this.medios.TARJETA !== '0' && this.tarjeta.codigo === '') {
              return false;
            }

            // ------------------------------------------------------------------------

            if (this.medios.TRANSFERENCIA !== '0' && this.banco.codigo === '') {
              return false;
            }

            // ------------------------------------------------------------------------

            if (this.medios.GIROS !== '0' && this.mostrar.GIROS_SELECCION === false) {
              return false;
            }

            // ------------------------------------------------------------------------

            return true;

            // ------------------------------------------------------------------------

          }, aceptar(){

            // ------------------------------------------------------------------------

            let me = this;
            var total = 0;
            var medios = 0;
            
            // ------------------------------------------------------------------------

            // RETORNAR SI ES FALSE 

            if (this.controlarSeleccinMedios() === false) {
              return; 
            }

            // ------------------------------------------------------------------------

            // ENVIAR DATOS

            me.respuesta = {
              COTIZACION: me.cotizacion,
              EFECTIVO: me.medios.EFECTIVO,
              CODIGO_TARJETA: me.tarjeta.codigo,
              TARJETA: me.medios.TARJETA,
              CODIGO_BANCO: me.banco.codigo,
              TRANSFERENCIA: me.medios.TRANSFERENCIA,
              CODIGO_ENT: me.giro.codigo,
              GIRO: me.medios.GIROS,
              VALE: me.medios.VALES,
              CREDITO: me.cliente.credito.total_agregado,
              DIAS_CREDITO: me.cliente.credito.dias,
              CREDITO_FIN: me.cliente.credito.vencimiento,
              DESCUENTO_GENERAL_PORCENTAJE: me.descuento.PORCENTAJE,
              DESCUENTO_GENERAL: me.medios.DESCUENTO,
              VUELTO: me.vuelto,
              SALDO: me.medios.SALDO,
              GUARANIES: me.monedas.GUARANIES,
              DOLARES: me.monedas.DOLARES,
              PESOS: me.monedas.PESOS,
              REALES: me.monedas.REALES,
              CHEQUE: me.cheque,
              TIPO_IMPRESION: me.seleccion.impresion,
              OPCION_VUELTO: me.radio.vuelto,
              PAGO_AL_ENTREGAR: me.checked.PAGO_AL_ENTREGAR,
              CUPON_CODIGO:me.cupon.codigo,
              CUPON_ID:me.cupon.id,
              CUPON_TOTAL:me.cupon.total,
              CUPON_PORCENTAJE:me.cupon.porcentaje,
              CUPON_TIPO:me.cupon.tipo
            }

            // ------------------------------------------------------------------------

            // EMITIR DATOS 

            // if (me.medios.MEDIOS !== '0') {

            total = parseFloat(Common.quitarComaCommon(me.total));
            medios = parseFloat(Common.quitarComaCommon(me.medios.MEDIOS));

            // ------------------------------------------------------------------------

            // REVISAR SI EL PAGO ES MAYOR AL TOTAL 

            if (total > medios && this.checked.PAGO_AL_ENTREGAR === false && this.tipo !== 3) {
              this.$bvToast.show('toast-monto-insuficiente');
              return;
            }

            // ------------------------------------------------------------------------

            $('#staticBackdrop').modal('hide');
            $('#modalImpresion').modal('show');
            me.seleccionar();
              
            // ------------------------------------------------------------------------

            // } else {
              
            //   // ------------------------------------------------------------------------

            //   this.$bvToast.show('toast-monto-insuficiente');
            //   return;

            //   // ------------------------------------------------------------------------

            // }

            // ------------------------------------------------------------------------

          }, limpiar() {

            // ------------------------------------------------------------------------

            this.medios = {
              SALDO: '0',
              MEDIOS: '0',
              VUELTO: '0',
              TARJETA: '0',
              EFECTIVO: '0',
              CHEQUE: '0',
              TRANSFERENCIA: '0',
              GIROS: '0',
              VALES: '0',
              DESCUENTO: '0',
            }

            // ------------------------------------------------------------------------

            this.monedas = {
              GUARANIES: '0',
              DOLARES: '0',
              PESOS: '0',
              REALES: '0'
            }

            // ------------------------------------------------------------------------

            this.cliente.credito.total_agregado = 0;

            // ------------------------------------------------------------------------

            this.tarjeta.codigo = '';
            this.cheque = '';
            this.medios.TRANSFERENCIA = '0';
            this.medios.TARJETA = '0';
            this.banco.codigo = '';

            // ------------------------------------------------------------------------

            this.formatoTarjeta();
            this.formatoTransferencia();
            this.sumarMonedas();

            // ------------------------------------------------------------------------

          },
          formatoDias(){
                //alert("entre");
                // ------------------------------------------------------------------------

                // INICIAR VARIABLES 

                let me = this;

                // ------------------------------------------------------------------------

                // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A COSTO

                me.cliente.credito.dias = Common.darFormatoCommon(me.cliente.credito.dias , 0);

                // ------------------------------------------------------------------------

                // OBTENER FECHA A PARTIR DE DIAS

                if (parseFloat(me.cliente.credito.dias) !== 0) {
                  var fecha = new Date();
                  fecha.setDate(fecha.getDate() + parseFloat(me.cliente.credito.dias));
                  me.cliente.credito.vencimiento = Common.formatDateCommon(fecha); 
                }

                // ------------------------------------------------------------------------

          }, 
          calcularCredito(){

            // ------------------------------------------------------------------------
            
            if ((Common.quitarComaCommon(this.medios.SALDO) !== '0' && Common.quitarComaCommon(this.medios.SALDO) !== '0.00') && (
                Common.quitarComaCommon(this.cliente.credito.total) === '0' || Common.quitarComaCommon(this.cliente.credito.total) === '0.00') && (Common.quitarComaCommon(this.cliente.credito.disponible) >= Common.quitarComaCommon(this.medios.SALDO))) 
            {
              this.cliente.credito.total = this.medios.SALDO;
            } else {
              this.cliente.credito.total = Common.darFormatoCommon(0, this.cotizacion.candec);
            }

            // ------------------------------------------------------------------------

          },
          pagoAlEntregar() {

            // ------------------------------------------------------------------------

            // ENTREGA 

            if (this.checked.PAGO_AL_ENTREGAR === true) {
              this.limpiar();
            }
            
            // ------------------------------------------------------------------------

          },
          obtener_cupon(){
            let me=this;
                if(me.textopc===0){
                  this.texto="Se aplicara el cupon: " + this.cupon.codigo + " !";
                }else{
                  this.texto="Desea recalcular  el cupon: " + this.cupon.codigo + " ?";
                }
    
            if(me.cupon.codigo!==''){
              var data={
                cupon:me.cupon.codigo,
                total:me.total_crudo,
                candec:me.cotizacion.candec,
                cliente:me.customer
              }
             Swal.fire({
          title: 'Estas seguro ?',
          text: this.texto,
          type: 'warning',
          showLoaderOnConfirm: true,
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Si, Aplicar!',
          cancelButtonText: 'Cancelar',
          preConfirm: () => {
            return Common.aplicarCuponCommon(data).then(data => {
              if (!data.response === true) {
                  throw new Error(data.statusText);
                }
              me.medios.CUPON=parseFloat(data.total);
              me.cupon.total=me.medios.CUPON;
              me.cupon.porcentaje=data.porcentaje;
              me.cupon.tipo=data.tipo;
              me.cupon.id=data.id;
              console.log(me.cupon);
              me.medios.CUPON=Common.darFormatoCommon(me.medios.CUPON, me.cotizacion.candec);
          
            
            me.sumarMonedas();
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
                  'Se ha aplicado el cupon !',
                  'success'
          )

            // ------------------------------------------------------------------------

            

          // ------------------------------------------------------------------------

          }else{
            me.quitar_cupon();
          } 
        })

            }
           
          },
          quitar_cupon(){
              let me=this;
              me.medios.CUPON=0.00;
              me.cupon.codigo='';
              me.cupon.total=0;
              me.cupon.porcentaje =0;
              me.cupon.tipo=0;
              me.cupon.id=0;
              me.textopc=0;

              me.sumarMonedas();
          }
      },
     
        mounted() {
        	
        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES 

        	let me = this;
            

        	// ------------------------------------------------------------------------
        	
          me.obtenerCotizacionyMoneda();
        	me.datosCliente(me.customer);
          
          // ------------------------------------------------------------------------

          // FOCUS EN SEARCH DEL DATATABLE DESPUES DE ABRIR EL MODAL 
            
          $('#staticBackdrop').on('shown.bs.modal', function() {

            if (me.moneda === 1) {
              $('#guaranies_input').focus();
            } else if (me.moneda === 2) {
              $('#dolares_input').focus();
            } else if (me.moneda === 3) {
              $('#pesos_input').focus();
            } else if (me.moneda === 4) {
              $('#reales_input').focus();
            }
            
          })

          // ------------------------------------------------------------------------

          $("#credito_vencimiento").datepicker().on(
                "changeDate", () => {me.cliente.credito.vencimiento = $('#credito_vencimiento').val()}
          );

          // ------------------------------------------------------------------------

          Mousetrap.bind('esc', function() { 
            if ($("#modalImpresion").data('bs.modal')) {
              if (($("#modalImpresion").data('bs.modal'))._isShown){
                me.enviar();
              }
            }; 

            if ($("#staticBackdrop").data('bs.modal')) {
              if (($("#staticBackdrop").data('bs.modal'))._isShown){
                if ($("#modalImpresion").data('bs.modal') === undefined) {
                    me.aceptar();
                }; 
              }
            }; 
          });

          // ------------------------------------------------------------------------

        }
    }
</script>