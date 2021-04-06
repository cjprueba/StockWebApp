<template>	
	<div class="container-fluid bg-light">
		<div class="row mt-4">
			<!--   -->
			<!-- ------------------------------------------------------------------------------------- -->

			<!-- TITULO  -->
			
			<div class="col-md-6">
				<div class="section-title">
                    <h4>Nota de Crédito</h4>
                    <p>Las notas de crédito se pueden crear por producto y por descuento.</p>
                </div>
			</div>
			
	        <!-- ------------------------------------------------------------------------------------- -->

	        <!-- BOTON NUEVO -->

	        <div class="col-md-6 text-right">
				<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTipo">Nuevo</button>
			</div>

			<!-- ------------------------------------------------------------------------ -->

			<!-- CLIENTE -->

			<div class="project col-md-12" v-if="respuesta.productos.length > 0">
			    <div class="row bg-white has-shadow">
			      <div class="left-col col-lg-12 d-flex justify-content-between">
			        <div class="col-4">
			        		
			        	<div class="project-title d-flex align-items-center">
				          <div class="image has-shadow">
				          	<span v-html="respuesta.imagen_cliente"></span>
				          </div>
				          <div class="text">
				            <h3 class="h4">{{respuesta.nombre}}</h3><small>{{respuesta.razon_social}}</small>
				          </div>
			        	</div>
			        </div>
				        <div class="col-4 align-items-center">
				          	<label>Nro. Factura</label>
				          	<input type="text" v-model="numero_factura" class="form-control form-control-sm" v-bind:class="{ 'is-invalid': validar.numero_factura }">
				        </div>
			        <div class="col-4 project-date"><span class="hidden-sm-down">{{respuesta.ruc}}</span></div>
			      </div>
			    </div>
			</div>

			<!-- ------------------------------------------------------------------------ -->

			<div class="col-md-12">
				<table id="tablaNotaFinal" class="table table-striped table-hover table-bordered table-sm mb-3" style="width:100%">
		            <thead>
		                <tr>
		                	<th>ID</th>
		                    <th>Código</th>
		                    <th>Cantidad</th>
		                    <th>Descripcion</th>
		                    <th>Base 5</th>
		                    <th>Base 10</th>
		                    <th>Exentas</th>
		                    <th>Gravadas</th>
		                    <th>Impuesto</th>
		                    <th>Precio Unitario</th>
		                    <th>Total</th>
		                </tr>
		            </thead>
		            <tbody>
		                <td></td>
		            </tbody>
		        </table> 
			</div>

			<!-- ------------------------------------------------------------------------ -->

			<!-- TOTALES -->

			<div class="col-md-12" v-if="respuesta.productos.length > 0">
				<div class="invoice-price mt-3">

					<div class="invoice-price-left">
						<div class="invoice-price-row">
							<div class="sub-price">
								<small>GRAVADAS</small>
								<span class="text-inverse">{{respuesta.gravadas}}</span>
							</div>
							<div class="sub-price">
								<i class="fa fa-plus text-muted"></i>
							</div>
							<div class="sub-price">
								<small>IMPUESTO</small>
								<span class="text-inverse">{{respuesta.impuesto}}</span>
							</div>
						</div>
					</div>

					<div class="invoice-price-right">
						<small>TOTAL</small> <span class="f-w-600">{{respuesta.total}}</span>
					</div>

				</div>
			</div>

			<!-- ------------------------------------------------------------------------ -->

			<!-- BOTON GUARDAR -->

	        <div class="col-md-12 text-right mt-3" v-if="respuesta.productos.length > 0">
				<button class="btn btn-dark btn-sm" data-toggle="modal" data-target="#modalDevolver">Guardar</button>
			</div>

			<!-- ------------------------------------------------------------------------ -->

                  <!-- MODAL TIPO NOTA -->

                  <div class="modal fade" id="modalTipo" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	                    <div class="modal-dialog" role="document">
	                      <div class="modal-content">

	                        <div class="modal-header">
	                          <h5 class="modal-title" id="exampleModalLabel">Opciones</h5>
	                        </div>

	                        <div class="modal-body">

	                          <div class="row">
	                            <div class="col-sm-10">
	                              <div class="form-check form-check-inline">
	                                <input v-model="nota.tipo" class="form-check-input" type="radio" name="gridRadios" id="radioImpresion1" value="1">
	                                <label class="form-check-label" for="radioImpresion1">
	                                  Nota de Crédito por devolución de producto.
	                                </label>
	                              </div>
	                              <div class="form-check form-check-inline">
	                                <input v-model="nota.tipo" class="form-check-input" type="radio" name="gridRadios" id="radioImpresion2" value="2">
	                                <label class="form-check-label" for="radioImpresion2">
	                                  Nota de Crédito por descuento.
	                                </label>
	                              </div>
	                            </div>
	                          </div>

	                      </div>
	                      <div class="modal-footer">
	                          <button type="button" class="btn btn-primary" data-dismiss="modal" v-on:click="tipo">Aceptar</button>
	                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
	                        </div>
	                    </div>
	                  </div>
                  </div>

                  <!-- ------------------------------------------------------------------------ -->

                  <!-- MODAL DEVOLUCION -->

					<div class="modal fade modal-contructor-nota-credito" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
			                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">

			                      <div class="modal-content">

			                      	<!-- ------------------------------------------------------------------------ -->

			                      	<!-- HEADER -->

			                      	<div class="modal-header">
			                          <h5 class="modal-title" id="exampleModalCenterTitle"><small>SELECCIONE VENTA</small></h5>
			                        </div>

			                        <!-- ------------------------------------------------------------------------ -->

			                        <div class="modal-body">

			                        	<ventas-global-textbox class="form-input-inline form-input-sm" ref="componente_textbox_Ventas" @codigo='codigo_venta' @caja='caja_venta' @data="total_venta"></ventas-global-textbox>

			                        	<!-- ------------------------------------------------------------------ -->

			                        	<!-- POR DEVOLUCION DE PRODUCTOS  -->

			                        	<!-- ------------------------------------------------------------------ -->

			                        	<!-- HABILITAR TODO -->
									
										<div class="my-1 mb-2" :class="{ hide: ocultar.tipo1 }">
											<div class="custom-control custom-switch mr-sm-3">
												<input type="checkbox" class="custom-control-input" id="switchMayorista" v-model="checked.todo">
												<label class="custom-control-label" for="switchMayorista">Todo</label>
											</div>
										</div>

										<!-- ------------------------------------------------------------------ -->

										<div :class="{ hide: ocultar.tipo1 }">

				                        	<table  id="tableConstructorNota" class="table table-hover table-bordered table-sm mb-3 mt-6" style="width:100%">
									            <thead>
									                <tr>
									                	<!-- <th>Sel.</th> -->
									                    <th>Codigo Producto</th>
									                    <th>Descripción</th>
									                    <th>Cantidad</th>
									                    <th>Devuelto</th>
									                    <th>Precio</th>
									                    <th>Total</th>
									                    <th>Accion</th>
									                </tr>
									            </thead>
									            <tbody>
									                <td></td>
									            </tbody>
									        </table>

								    	</div>

								        <div class="row" v-if="nota.tipo === '2'">
									        <div class="col-md-12">

												<div class="invoice-price">

													<div class="invoice-price-left">
														<div class="invoice-price-row">
															<div class="sub-price">
																<small>VENTA</small>
																<span class="text-inverse">{{venta.codigo}}</span>
															</div>
														</div>
													</div>
												

													<div class="invoice-price-right">
														<small>TOTAL</small> <span class="f-w-600">{{nota.total_show}}</span>
													</div>
												</div>

											</div>	

											<div class="col-md-12">
												<hr>
											</div>
											
											<div class="col-md-12">
												<div class="form-check form-check-inline">
												  <input class="form-check-input" v-on:change="recalcularIVA" type="radio" v-model="radio.iva" name="inlineRadioOptions" id="inlineRadio1" value="1">
												  <label class="form-check-label" for="inlineRadio1">I.V.A. 10%</label>
												</div>
												<div class="form-check form-check-inline">
												  <input class="form-check-input" v-on:change="recalcularIVA" type="radio" v-model="radio.iva" name="inlineRadioOptions" id="inlineRadio2" value="2">
												  <label class="form-check-label" for="inlineRadio2">I.V.A. 5%</label>
												</div>
												<div class="form-check form-check-inline">
												  <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="1">
												  <label class="form-check-label" for="inlineCheckbox1">Devolver productos</label>
												</div>
											</div>

											<div class="col-md-12">
												<hr>
											</div>

											<div class="col-md-3 mt-2 mb-3">
												<label>Descuento %</label>
												<input type="text" v-on:change="porcentaje_calculo" class="form-control" v-model="nota.descuento">
											</div>
											<div class="col-md-3 mt-2 mb-3">
												<label>I.V.A.</label>
												<input type="text" class="form-control" v-model="nota.iva" disabled>
											</div>
											<div class="col-md-3 mt-2 mb-3">
												<label>Subtotal</label>
												<input type="text" class="form-control" v-model="nota.subtotal" disabled>
											</div>	
											<div class="col-md-3 mt-2 mb-3">
												<label>Total</label>
												<input type="text" class="form-control" v-model="nota.total_pago" v-on:change="calculo_total">
											</div>
										</div>
			                        </div>	
					      			
					      			<!-- ------------------------------------------------------------------------ -->

					      			<!-- FOOTER -->

			                        <div class="modal-footer" v-if="nota.tipo === '1'">
			                            <button type="button" class="btn btn-dark" data-dismiss="modal" v-on:click="generar()">ACEPTAR</button>
			                        </div>

			                        <!-- ------------------------------------------------------------------------ -->

			                        <div class="modal-footer" v-if="nota.tipo === '2'">
					                    <button type="button" class="btn btn-primary" data-dismiss="modal"  v-on:click="generar()">Aceptar</button>
					                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					                </div>

			                        <!-- ------------------------------------------------------------------------ -->

					    		  </div>

					  			</div>
					</div>								  
                         			
                   <!-- ------------------------------------------------------------------------ -->

                   <!-- MODAL DEVOLVER CREDITO O PLATA -->

                  <div class="modal fade" id="modalDevolver" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	                    <div class="modal-dialog" role="document">
	                      <div class="modal-content">

	                        <div class="modal-header">
	                          <h5 class="modal-title" id="exampleModalLabel">Total Nota de Crédito: <b>{{mostrar.total}}</b></h5>
	                        </div>

	                        <div class="modal-body">

	                          <div class="row">
	                            <div class="col-sm-12">
	                              <div class="form-check form-check-inline">
	                                <input v-model="devolver.tipo" class="form-check-input" type="radio" name="gridRadiosDevolver" id="radioDevolver1" value="1">
	                                <label class="form-check-label" for="radioDevolver1">
	                                  Reservar la nota de crédito al cliente para usarse en futuras ventas.
	                                </label>
	                              </div>
	                              <hr>
	                              <div class="form-check form-check-inline">
	                                <input v-model="devolver.tipo" class="form-check-input" type="radio" name="gridRadiosDevolver" id="radioDevolver2" value="2">
	                                <label class="form-check-label" for="radioDevolver2">
	                                  Pago por Caja.
	                                </label>
	                              </div>
	                            </div>

	                            <div class="col-md-12" v-if="devolver.tipo === '2'">
	                            	<hr>
	                        	</div>

	                            <div class="col-md-4 mt-2" v-if="devolver.tipo === '2'">
	                            	<div class="form-check form-check-inline">
									  <input class="form-check-input" type="radio" v-model="radio.medio" name="inlineRadioOptions" id="medios1" value="1">
									  <label class="form-check-label" for="medios1">Guaranies</label>
									</div>
	                            </div>
	                            <!-- <div class="col-md-8 mt-2" v-if="devolver.tipo === '2'">
	                            	<input type="text" v-on:change="formatoGuaranies" class="form-control form-control-sm" v-model="medios.guaranies" :disabled="radio.medio !== '1'" name="">
	                            </div> -->
	                            <div class="col-md-8 mt-2" v-if="devolver.tipo === '2'">
	                            	<input type="text" class="form-control form-control-sm" v-model="totales.guaranies" :disabled="true" name="">
	                            </div>

	                            <div class="col-md-4 mt-2" v-if="devolver.tipo === '2'">
	                            	<div class="form-check form-check-inline">
									  <input class="form-check-input" type="radio" v-model="radio.medio" name="inlineRadioOptions" id="medios2" value="2" >
									  <label class="form-check-label" for="medios2">Dolares</label>
									</div>
	                            </div>
	                            <!-- <div class="col-md-8 mt-2" v-if="devolver.tipo === '2'">
	                            	<input type="text" v-on:change="formatoDolares" class="form-control form-control-sm" v-model="medios.dolares" :disabled="radio.medio !== '2'" name="">
	                            </div> -->
	                            <div class="col-md-8 mt-2" v-if="devolver.tipo === '2'">
	                            	<input type="text" class="form-control form-control-sm" v-model="totales.dolares" :disabled="true" name="">
	                            </div>

	                            <div class="col-md-4 mt-2" v-if="devolver.tipo === '2'">
	                            	<div class="form-check form-check-inline">
									  <input class="form-check-input" type="radio" v-model="radio.medio" name="inlineRadioOptions" id="medios3" value="4" >
									  <label class="form-check-label" for="medios3">Reales</label>
									</div>
	                            </div>
	                            <!-- <div class="col-md-8 mt-2" v-if="devolver.tipo === '2'">
	                            	<input type="text" v-on:change="formatoReales" class="form-control form-control-sm" v-model="medios.reales" :disabled="radio.medio !== '3'" name="">
	                            </div> -->
	                            <div class="col-md-8 mt-2" v-if="devolver.tipo === '2'">
	                            	<input type="text" class="form-control form-control-sm" v-model="totales.reales" :disabled="true" name="">
	                            </div>

	                            <div class="col-md-4 mt-2" v-if="devolver.tipo === '2'">
	                            	<div class="form-check form-check-inline">
									  <input class="form-check-input" type="radio" v-model="radio.medio" name="inlineRadioOptions" id="medios4" value="3" >
									  <label class="form-check-label" for="medios4">Pesos</label>
									</div>
	                            </div>
	                            <!-- <div class="col-md-8 mt-2" v-if="devolver.tipo === '2'">
	                            	<input type="text" v-on:change="formatoPesos" class="form-control form-control-sm" v-model="medios.pesos" :disabled="radio.medio !== '4'" name="">
	                            </div> -->
	                            <div class="col-md-8 mt-2" v-if="devolver.tipo === '2'">
	                            	<input type="text" class="form-control form-control-sm" v-model="totales.pesos" :disabled="true" name="">
	                            </div>

	                            <div class="col-md-4 mt-2" v-if="devolver.tipo === '2'">
	                            	<div class="form-check form-check-inline">
									  <input class="form-check-input" type="radio" v-model="radio.medio" name="inlineRadioOptions" id="medios5" value="5" >
									  <label class="form-check-label" for="medios5">Cheque</label>
									</div>
	                            </div>
	                            <!-- <div class="col-md-8 mt-2" v-if="devolver.tipo === '2'">
	                            	<input type="text" v-on:change="formatoCheque" class="form-control form-control-sm" v-model="medios.cheque" :disabled="radio.medio !== '5'" name="">
	                            </div> -->
	                            <div class="col-md-8 mt-2" v-if="devolver.tipo === '2'">
	                            	<input type="text" class="form-control form-control-sm" v-model="totales.cheque" :disabled="true" name="">
	                            </div>

	                            <div class="col-md-4 mt-2" v-if="devolver.tipo === '2'">
	                            	<div class="form-check form-check-inline">
									  <input class="form-check-input" type="radio" v-model="radio.medio" name="inlineRadioOptions" id="medios6" value="6" >
									  <label class="form-check-label" for="medios6">Transferencia</label>
									</div>
	                            </div>
	                            <!-- <div class="col-md-8 mt-2" v-if="devolver.tipo === '2'">
	                            	<input type="text" v-on:change="formatoTransferencia" class="form-control form-control-sm" v-model="medios.transferencia" :disabled="radio.medio !== '6'" name="">
	                            </div> -->
	                            <div class="col-md-8 mt-2" v-if="devolver.tipo === '2'">
	                            	<input type="text" class="form-control form-control-sm" v-model="totales.transferencia" :disabled="true" name="">
	                            </div>				
	                          </div>

	                      </div>
	                      <div class="modal-footer">
	                          <button type="button" class="btn btn-primary" data-dismiss="modal" v-on:click="guardar(1)">Aceptar</button>
	                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
	                        </div>
	                    </div>
	                  </div>
                  </div>
     			  
                  <!-- ------------------------------------------------------------------------ -->
                  <!-- ------------------------------------------------------------------------ -->

		</div>
	</div>	
</template>
<script>


	 export default {
	  props: [],	
      data(){
        return {
          	nota: {
          		tipo: '1',
          		total: 0,
          		iva: 0,
          		subtotal: 0,
          		descuento: 0,
          		total_pago: 0,
          		total_show: 0, 
          		candec: 0,
          		moneda: 0,
          	},
          	tabla: {
          		constructor: ''
          	}, 
          	caja: {
          		codigo: '',
          		caja_proceso: ''
          	},
          	venta: {
          		codigo: ''
          	},
          	checked: {
          		todo: false
          	},
          	nuevoMarcados: [],
          	datos: [],
          	actualizar: false,
          	data: {
          		codigo: '',
        		caja: '',
        		datos: ''
          	},
          	validar: {
          		numero_factura: false
          	},
          	numero_factura: '',
          	respuesta: {
          		cliente_codigo: '',
          		nombre: '',
          		razon_social: '',
          		ruc: '',
          		productos: [],
          		total: 0,
          		total_crudo: 0,
          		base5: 0,
          		base10: 0,
          		exentas: 0,
          		gravadas: 0,
          		impuesto: 0,
          		moneda: 0,
          		imagen_cliente: ''
          	},
          	devolver: {
          		tipo: '1'
          	},
          	medios: {
          		guaranies: '',
          		dolare: '',
          		reales: '',
          		pesos: '',
          		cheque: '',
          		transferencia: ''
          	},
          	envio: {
          		cliente_codigo: '',
          		productos: [],
          		total: 0,
          		base5: 0,
          		base10: 0,
          		exentas: 0,
          		gravadas: 0,
          		impuesto: 0,
          		tipo: '',
          		venta_codigo: '',
          		caja_codigo: '',
          		moneda: ''
          	}, ocultar: {
          		tipo1: true
          	},
          	radio: {
          		iva: '1',
          		medio: '1'
          	},
          	checkbox: {
          		devolver: false
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
          		guaranies: '',
          		dolares: '',
          		reales: '',
          		pesos: '',
          		cheque: '',
          		transferencia: ''
          	},
          	mostrar: {
          		total: 0
          	}
        }
      }, 
      methods: {
      	venta_datatable(){

      		this.$refs.componente_textbox_Ventas.datatableMostrar(1);
			$('.modal-contructor-nota-credito').modal('show');

      	},
      	codigo_venta(codigo){

      		// ------------------------------------------------------------------------

            let me = this;

            // ------------------------------------------------------------------------

            me.venta.codigo = codigo;

            // ------------------------------------------------------------------------

      	},
      	total_venta(data){

      		// ------------------------------------------------------------------------

            let me = this;

            // ------------------------------------------------------------------------

            me.nota.descuento = 100;

            // ------------------------------------------------------------------------

            me.nota.total = Common.darFormatoCommon(data.TOTAL_CRUDO, data.CANDEC);

            // ------------------------------------------------------------------------

            me.nota.total_pago =  Common.darFormatoCommon(data.TOTAL_CRUDO, data.CANDEC);

            // ------------------------------------------------------------------------

            me.nota.candec = data.CANDEC;
            me.nota.moneda = data.MONEDA;

            // ------------------------------------------------------------------------

            me.nota.total_show = data.TOTAL;

            // ------------------------------------------------------------------------

            // LLAMAR CALCULO IVA 

        	this.calculo_iva();
        	
        	// ------------------------------------------------------------------------

      	},
      	caja_venta(codigo){
      		
      		// ------------------------------------------------------------------------

      		let me = this;

      		// ------------------------------------------------------------------------

      		this.caja.codigo = codigo;

      		// ------------------------------------------------------------------------

      		if (this.nota.tipo === '1') {

      			this.tabla.contructor = $('#tableConstructorNota').DataTable({
	                 	"processing": true,
	                 	"serverSide": true,
	                 	"destroy": true,
	                 	"bAutoWidth": true,
	                 	"select": true,
	                 	"ajax":{
	                 			"data": {
	                 				"codigo": me.venta.codigo,
	                 				"caja": me.caja.codigo
	                 			},
	                             "url": "venta/devolucion/productos",
	                             "type": "POST",
	                             'headers': {
						            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						          }
	                           },
	                    "columns": [
	                            { "data": "COD_PROD" },
	                            { "data": "DESCRIPCION" },
	                            { "data": "CANTIDAD" },
	                            { "data": "CANTIDAD_DEVUELTA" },
	                            { "data": "PRECIO" },
	                            { "data": "TOTAL" },
	                            { "data": "ACCION" }
	                        ], "fnDrawCallback": function( oSettings ) {

	                        	// ------------------------------------------------------------------------

                       			me.Marcar();

                       			// ------------------------------------------------------------------------
                        	},
			                "createdRow": function( row, data, dataIndex){
			                    $(row).addClass(data['ESTATUS']);
			                }       
	        	});

      		} 
      		
                    
	 	    // ------------------------------------------------------------------------


      	},
      	tipo(){

      		// ------------------------------------------------------------------------

      		// LIMPIAR DATOS 
	 	    
			this.nuevoMarcados = [];
			this.datos = [];
			this.Desmarcar();
			this.caja_venta(this.caja.codigo);

			// ------------------------------------------------------------------------

			// var x = document.getElementById("tipo1");
			
			if (this.nota.tipo === '1') {
				this.ocultar.tipo1 = false;
			} else if (this.nota.tipo === '2') {
				this.ocultar.tipo1 = true;
			}

      		this.venta_datatable();
      		

      		// ------------------------------------------------------------------------

      	}, 
        marcar_desmarcar(){
            
            let me=this;

            if(me.checked_todo===true){
            	   var codigo_tr={
                     codigo:me.codigotr,
                     origen:me.codigo_origen
                   }
                   me.datos=[];
                   me.nuevoMarcados=[];
                      Common.marcarTodoTransferenciaCommon(codigo_tr).then(data => {
                      	 	for (var i=0; i<data.productos.length; i++){
                      	 		 me.nuevoMarcados.push(data.productos[i]["COD_PROD"]);
                      	 		 me.datos.push({
								  "CODIGO": data.productos[i]["COD_PROD"],
								  "CANTIDAD":data.productos[i]["CANTIDAD"],
								 });
                      	 	}
							me.Marcar();
							 me.marcar_dev();
              			});
            }else{
            		me.Desmarcar();
            		me.nuevoMarcados=[];
            		me.datos=[];
            }

        }, Marcar(){

		    let me=this;
		           
		    me.nuevoMarcados.map(function (x) {
		        $("#"+x).prop('checked', true);
		    });
		    

		    me.datos.map(function (x) {

		        if (document.getElementsByName($("#"+x.CODIGO).closest('input').attr('id'))[0] !== undefined) {
		        	document.getElementsByName($("#"+x.CODIGO).closest('input').attr('id'))[0].value = x.CANTIDAD;
		        }
		        
		    });
		           
		            
        }, Desmarcar(){

		    let me=this;
		           
		    me.nuevoMarcados.map(function (x) {
		        $("#"+x).prop('checked', false);
		    });
		            
        }, marcar_dev(){
         
            this.tableContructorNota = $('#tableConstructorNota').DataTable();
                    
            let me=this;
                  //RECORRE TODOS LOS CHECKBOX EXISTENTES EN EL DATATABLE
            $( me.tableContructorNota.$('input[type="checkbox"]').map(function ()
                           {
                   	 
                                        //PREGUNTA SI ESTA MARCADO
                                       if  ($(this).prop("checked"))  {
                                         // SI ESTA MARCADO AGREGAR 
                                            if (me.nuevoMarcados.includes($(this).closest('input').attr('id')) === false) { 
                                         	  //PREGUNTA SI ESTE CODIGO NO EXISTE EN EL ARRAY AUXILIAR
                                         	
                                         	    me.nuevoMarcados.push($(this).closest('input').attr('id'));
                                         	   //ACA SE GUARDA EL CODIGO EN UN ARRAY AUXILIAR
		 											   $( me.tableContructorNota.$('input[type="number"]').map(function () {
		 											  	//SE RECORRE TODOS LOS INPUT TIPO NUMERO PARA GUARDAR LA CANTIDAD 
				 												if (me.nuevoMarcados.includes($(this).closest('input').attr('id')) ===true) {
				 													//PREGUNTA SI ESTE CODIGO YA EXISTE EN EL ARRAY AUXILIAR
				 													 	for (var i=0; i<me.datos.length; i++) { 
				 													 		//RECORREMOS EL ARRAY ORIGINAL SI TIENE DATOS
				 													 	if(me.datos[i]["CODIGO"]===$(this).closest('input').attr('id')){
				 													 		//PREGUNTAMOS SI EXISTE UN CODIGO IGUAL EN NUESTRO ARRAY ORIGINAL PARA ACTUALIZAR LA CANTIDAD
 																			console.log("maximo: "+$(this).closest('input').attr('max'));
 																			if (parseInt($(this).closest('input').attr('max')) < parseInt(document.getElementsByName($(this).closest('input').attr('id'))[0].value)) {
 																				console.log("entre maximo: "+$(this).closest('input').attr('max'));
 																				document.getElementsByName($(this).closest('input').attr('id'))[0].value = $(this).closest('input').attr('max');
 																			}

				 													 		me.datos[i]["CANTIDAD"]=document.getElementsByName($(this).closest('input').attr('id'))[0].value;
				                                                             
				                                                             me.actualizar=true;
				                                                             //ACTUALIZAR ES LA VARIABLE QUE DECIDE SI EXISTE O NO EN NUESTRO ARRAY ORIGINAL EL PRODUCTO, SI ES TRUE ES POR QUE ENCONTRO UNO IGUAL
				                                                             i=me.datos.length;
				                                                             //CERRAMOS EL FOR
				 													 	}else{
				 													 		//SI NO ENCUENTRA NINGUN CODIGO IGUAL ENTONCES ES FALSE Y SE DEBE INSERTAR
				 													 		  me.actualizar=false;
				 													 	}
				 													 }
				 													 if(me.actualizar===false){
				 													 	//PREGUNTAMOS SI ES FALSE PARA PODER INSERTAR EN NUESTRO ARRAY ORIGINAL EL DATO

				 													 	 if (parseInt($(this).closest('input').attr('max')) < parseInt(document.getElementsByName($(this).closest('input').attr('id'))[0].value)) {

 																				document.getElementsByName($(this).closest('input').attr('id'))[0].value = $(this).closest('input').attr('max');
 																			}

				 													 	 me.datos.push({
																	    "CODIGO": $(this).closest('input').attr('id'),
																	    "CANTIDAD":document.getElementsByName($(this).closest('input').attr('id'))[0].value,
																	     });
				 													 }
				 													
				 													 	
				 													 
				 													 
						                                                me.actualizar=false;
						                                                //PONEMOS FALSE NUEVAMENTE LA VARIABLE ACTUALIZAR PARA PROXIMOS PRODUCTOS
						                                                  }
		                                                } ) );
	                                           
	                                            }else{
	                                            	me.checked_todo=false;
	                                         	//ACA ENTRA YA QUE NUESTRO ARRAY ORIGINAL DEVUELVE QUE YA EXISTE EL PRODUCTO EN NUESTROS ARRAYS
	                                         	  $( me.tableContructorNota.$('input[type="number"]').map(function () {
	                                         	  	//RECORREMOS TODOS LOS INPUT TIPO NUMEROS
	                                         	  		 for (var i=0; i<me.datos.length; i++) { 
	                                         	  		 	//RECORREMOS NUESTRO ARRAY ORIGINAL
	 													 	if(me.datos[i]["CODIGO"]===$(this).closest('input').attr('id')){

	 													 		if (parseInt($(this).closest('input').attr('max')) < parseInt(document.getElementsByName($(this).closest('input').attr('id'))[0].value)) {

 																				document.getElementsByName($(this).closest('input').attr('id'))[0].value = $(this).closest('input').attr('max');
 																			}

	 													 		//PREGUNTAMOS SI EXISTE ALGUN CODIGO IGUAL PARA PODER ACTUALIZAR LA CANTIDAD AL PRODUCTO EN ESPECIFICO
	 													 		me.datos[i]["CANTIDAD"]=document.getElementsByName($(this).closest('input').attr('id'))[0].value;
	                                                             
	                                                           
	                                                             i=me.datos.length;
	                                                             //CERRAMOS EL FOR
	 													 	}
	 													 }
		                                         } ) );
		        										
		                                         }
                                         
                                        } else {
                                      //ACA ENTRA CUANDO SE DESMARCA UN PRODUCTO ENTONCES PROCEDEMOS A ELIMINAR DE LOS DOS ARRAYS EL ELEMENTO
                                        for (var i=0; i<me.datos.length; i++) { 
                                        	//SE RECORRE EL ARRAY
                                        	if(me.datos[i]["CODIGO"]===$(this).closest('input').attr('id')){
                                        		//PREGUNTAMOS POR EL CODIGO QUE DESEAMOS ELIMINAR (PUEDEN SER VARIOS A LA VEZ YA QUE ES UN WHILE POR CADA CHECK) 
 								
                                                             
                                                              me.datos.splice(i, 1); 
                                                              i--;
                                                              //FORMULA QUE ELIMINA EL ELEMENTO DE NUESTRO ARRAY ORIGINAL
                                                        
                                                              
                                                             
 										  }
                                        	}
                                        me.Eliminar_Array($(this).closest('input').attr('id'));
                                        //EN ESTA FUNCION ENVIAMOS EL CODIGO DEL PRODUCTO EN UNA FUNCION QUE ELIMINA EL ELEMENTO DE NUESTRO ARRAY AUXILIAR
                                       
                                       };
                             } ) );
            },
                Eliminar_Array(element){
                Array.prototype.removeItem = function (a) {
                for (var i = 0; i < this.length; i++) {
              if (this[i] == a) {
               for (var i2 = i; i2 < this.length - 1; i2++) {
                this[i2] = this[i2 + 1];
               }
               this.length = this.length - 1;
               return;
              }
             }
            };
            if (this.nuevoMarcados.length > 0) {
              this.nuevoMarcados.removeItem(element);
            }
            //console.log(this.marcado); //
         
        },
                        Eliminar_Array_datos(element){
                        	console.log(element);
                Array.prototype.removeItem = function (a) {
                for (var i = 0; i < this.length; i++) {
              if (this[i] == a) {
               for (var i2 = i; i2 < this.length - 1; i2++) {
                this[i2] = this[i2 + 1];
               }
               this.length = this.length - 1;
               return;
              }
             }
            };
            if (this.datos.length > 0) {
              this.datos.removeItem(element);
            }
            //console.log(this.marcado); //
         
        },
        generar(){

        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES

        	let me = this;
        	var tablaNotaFinal = $('#tablaNotaFinal').DataTable();

        	// ------------------------------------------------------------------------

        	if (this.nota.tipo === '1') {

	        	// ------------------------------------------------------------------------

	        	// CARGAR DATOS 

	        	this.data = {
	        		codigo: this.venta.codigo,
	        		caja: this.caja.codigo,
	        		datos: this.datos,
	        		tipo: this.nota.tipo
	        	}

	        	// ------------------------------------------------------------------------

	        	// GENERAR CUERPO CON EL RESULTADO

	        	Common.generarCuerpoNotaCreditoCommon(this.data).then(data => {

	        		if (data.response !== false) {

	        			// ------------------------------------------------------------------------
	        		
		        		me.respuesta.productos = data.data;
		        		me.respuesta.total = data.total;
		        		me.respuesta.total_crudo = data.total_crudo;
		        		me.respuesta.base5 = data.base5;
		        		me.respuesta.base10 = data.base10;
		        		me.respuesta.exentas = data.exentas;
		        		me.respuesta.gravadas = data.gravadas;
		        		me.respuesta.impuesto = data.impuesto;
		        		me.respuesta.moneda = data.moneda;

		        		me.respuesta.cliente_codigo = data.cliente_codigo;
		          		me.respuesta.nombre = data.cliente_nombre;
		          		me.respuesta.razon_social = data.razon_social;
		          		me.respuesta.ruc = data.ruc;
		          		me.respuesta.imagen_cliente = data.imagen_cliente;

		        		tablaNotaFinal.clear();
		                tablaNotaFinal.rows.add(data.data);
		                tablaNotaFinal.draw(); 
		        		//tablaNotaFinal.ajax.reload( null, false );

		        		me.calcularTotalesMoneda();

		        		// ------------------------------------------------------------------------

		        		// MOSTRAR TOTAL

		        		me.mostrar.total = me.respuesta.total;

		        		// ------------------------------------------------------------------------

	        		} else {

	        			// ------------------------------------------------------------------------

	        			Swal.fire(
							'Error !',
							 data.statusText,
							'error'
						)

	        			// ------------------------------------------------------------------------

	        		}

	        	}).catch(error => {
					        Swal.showValidationMessage(
					          `Request failed: ${error}`
					        )
				});

	        	// ------------------------------------------------------------------------

        	} else if (this.nota.tipo === '2') {
        		me.calcularTotalesMoneda();
        		$('#modalDevolver').modal('show');

        		// ------------------------------------------------------------------------

        		// MOSTRAR TOTAL
		        		
		        me.mostrar.total = me.nota.total;

		        // ------------------------------------------------------------------------

        	}

        }, guardar(tipo) {

        	// ------------------------------------------------------------------------

        	let me = this;

        	if(me.numero_factura === '' || me.numero_factura.length === 0){
        		me.validar.numero_factura = true;
        		return;
        	}

        	me.validar.numero_factura = false;

        	// ------------------------------------------------------------------------

        	if (this.nota.tipo === '1') {

        		// ------------------------------------------------------------------------

        		this.envio = {
	          		cliente_codigo: this.respuesta.cliente_codigo,
	          		productos: this.respuesta.productos,
	          		total: this.respuesta.total_crudo,
	          		base5: this.respuesta.base5,
	          		base10: this.respuesta.base10,
	          		exentas: this.respuesta.exentas,
	          		gravadas: this.respuesta.gravadas,
	          		impuesto: this.respuesta.impuesto,
	          		venta_codigo: this.venta.codigo,
	          		caja_codigo: this.caja.codigo,
	          		tipo: this.nota.tipo,
	          		moneda: this.respuesta.moneda,
	          		tipo_iva: this.radio.iva,
	          		tipo_proceso: this.devolver.tipo,
	          		tipo_medio: this.radio.medio,
	          		totales: this.totales,
	          		caja_proceso: this.caja.caja_proceso,
	          		numero_factura: this.numero_factura
	          	}

	          	// ------------------------------------------------------------------------

	          } else if (this.nota.tipo === '2') {

	          	// ------------------------------------------------------------------------

	          	this.envio = {
	          		cliente_codigo: this.respuesta.cliente_codigo,
	          		productos: this.respuesta.productos,
	          		total: this.nota.total_pago,
	          		base5: 0,
	          		base10: 0,
	          		exentas: 0,
	          		gravadas: this.nota.subtotal,
	          		impuesto: this.nota.iva,
	          		venta_codigo: this.venta.codigo,
	          		caja_codigo: this.caja.codigo,
	          		tipo: this.nota.tipo,
	          		moneda: this.nota.moneda,
	          		tipo_iva: this.radio.iva,
	          		tipo_proceso: this.devolver.tipo,
	          		tipo_medio: this.radio.medio,
	          		totales: this.totales,
	          		descuento: this.nota.descuento,
	          		caja_proceso: this.caja.caja_proceso,
	          		numero_factura: this.numero_factura
	          	}

	          	// ------------------------------------------------------------------------

	          }
        	
        	// ------------------------------------------------------------------------

        	Swal.fire({
				title: 'Estas seguro ?',
				text: "Guardar la nota de crédito !",
				type: 'warning',
				showLoaderOnConfirm: true,
				showCancelButton: true,
				confirmButtonColor: '#d33',
				cancelButtonColor: '#3085d6',
				confirmButtonText: 'Si, guardalo!',
				cancelButtonText: 'Cancelar',
				preConfirm: () => {
				    return Common.guardarNotaCreditoCommon(me.envio).then(data => {
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

				if (result.value.response) {

					Swal.fire(
						'Guardado!',
						 result.value.statusText,
						'success'
					)

					
					//window.location.href = '/mov2';
					

					// ------------------------------------------------------------------------

					me.generarPdf(result.value.ID);

					// ------------------------------------------------------------------------

					// REDIRIGIR A MOSTRAR NOTA DE CREDITO
					
					
					//me.$router.push('/mov2');

					// ------------------------------------------------------------------------

				}
			})
        			

        	// ------------------------------------------------------------------------

        }, porcentaje_calculo() {

        	// ------------------------------------------------------------------------

        	this.nota.descuento = parseFloat(this.nota.descuento);

        	if (isNaN(this.nota.descuento)) {
        		this.nota.descuento = 0;
        	}

        	if (this.nota.descuento > 100) {
        		this.nota.descuento = 100;
        	} else if (this.nota.descuento < 0) {
        		this.nota.descuento = 0;
        	} else {
        		this.nota.total_pago = Common.darFormatoCommon(((this.nota.total * this.nota.descuento) / 100), this.nota.candec);
        	}

        	// ------------------------------------------------------------------------

        	// LLAMAR CALCULO IVA 

        	this.calculo_iva();

        	// ------------------------------------------------------------------------

        }, calculo_total() {

        	// ------------------------------------------------------------------------

        	if (this.nota.total_pago > this.nota.total) {
        		this.nota.total_pago = this.nota.total;
        	} else if (this.nota.total_pago < 0) {
        		this.nota.total_pago = 0;
        	} else {
        		this.nota.descuento = Common.darFormatoCommon(((this.nota.total_pago / this.nota.total) * 100), 2);
        	}

        	// ------------------------------------------------------------------------

        	// LLAMAR CALCULO IVA 

        	this.calculo_iva();
        	
        	// ------------------------------------------------------------------------

        }, calculo_iva(){

        	// ------------------------------------------------------------------------

        	if (this.radio.iva === '1') {
        		this.nota.iva = Common.darFormatoCommon(this.nota.total_pago / 11, this.nota.candec);
        	} else if (this.radio.iva === '2') {
        		this.nota.iva = Common.darFormatoCommon(this.nota.total_pago / 21, this.nota.candec);
        	}

        	// ------------------------------------------------------------------------

        	// CALCULO SUBTOTAL

        	this.nota.subtotal = Common.darFormatoCommon(this.nota.total_pago - this.nota.iva, this.nota.candec);

        	// ------------------------------------------------------------------------

        }, recalcularIVA() {

        	this.calculo_iva();

        }, formatoGuaranies(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // DAR FORMATO A CANTIDAD
            
            me.medios.guaranies = Common.darFormatoCommon(me.medios.guaranies, 0);

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
            
            me.medios.dolares = Common.darFormatoCommon(me.medios.dolares, 2);

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
            
            me.medios.pesos = Common.darFormatoCommon(me.medios.pesos, 2);

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
            
            me.medios.reales = Common.darFormatoCommon(me.medios.reales, 2);

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
            
            me.medios.cheque = Common.darFormatoCommon(me.medios.cheque, me.nota.candec);

            // ------------------------------------------------------------------------

            // CALCULAR VALORES 
            
            this.sumarMonedas();

            // ------------------------------------------------------------------------

         }, formatoTransferencia(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // DAR FORMATO A CANTIDAD
            
            me.medios.transferencia = Common.darFormatoCommon(me.medios.transferencia, me.nota.candec);

            // ------------------------------------------------------------------------

            // CALCULAR VALORES 
            
            this.sumarMonedas();

            // ------------------------------------------------------------------------

         }, sumarMonedas(){

         	// ------------------------------------------------------------------------

         	// var dolares = 0, guaranies = 0, pesos = 0, reales = 0, total = 0, vuelto = 0, tarjeta = 0, transferencia = 0, giro = 0;

          //   // ------------------------------------------------------------------------

          //   // TOTALES MONEDAS 

          //   guaranies = Common.formulaCommon(this.cotizacion.formula_gs_reves, this.monedas.GUARANIES, this.cotizacion.guaranies, this.cotizacion.candec, this.moneda, this.cotizacion.moneda_gs);

          //   dolares =  Common.formulaCommon(this.cotizacion.formula_usd_reves, this.monedas.DOLARES, this.cotizacion.dolares, this.cotizacion.candec, this.moneda, this.cotizacion.moneda_$);

          //   pesos =  Common.formulaCommon(this.cotizacion.formula_ps_reves, this.monedas.PESOS, this.cotizacion.pesos, this.cotizacion.candec, this.moneda, this.cotizacion.moneda_ps);

          //   reales = Common.formulaCommon(this.cotizacion.formula_rs_reves, this.monedas.REALES, this.cotizacion.reales, this.cotizacion.candec, this.moneda, this.cotizacion.moneda_rs);

          //   // ------------------------------------------------------------------------

          //   // TOTAL MONEDAS 

          //   total = (Common.sumarCommon(Common.sumarCommon(pesos, reales, this.candec), Common.sumarCommon(dolares, guaranies, this.candec), this.candec));

            // ------------------------------------------------------------------------

         }, opcionRadio(){


         }, calcularTotalesMoneda(){

         	if (this.nota.tipo === '1') {

	            // ------------------------------------------------------------------------

	            // GUARANIES 

	            this.totales.guaranies = Common.formulaCommon(this.cotizacion.formula_gs, this.respuesta.total_crudo, this.cotizacion.guaranies, this.cotizacion.candec_gs, this.nota.moneda, this.cotizacion.moneda_gs);

	            // ------------------------------------------------------------------------

	            // DOLARES 

	            this.totales.dolares = Common.formulaCommon(this.cotizacion.formula_$, this.respuesta.total_crudo, this.cotizacion.dolares, this.cotizacion.candec_$, this.nota.moneda, this.cotizacion.moneda_$);

	            // ------------------------------------------------------------------------

	            // PESOS

	            this.totales.pesos = Common.formulaCommon(this.cotizacion.formula_ps, this.respuesta.total_crudo, this.cotizacion.pesos, this.cotizacion.candec_ps, this.nota.moneda, this.cotizacion.moneda_ps);

	            // ------------------------------------------------------------------------

	            // REALES

	            this.totales.reales = Common.formulaCommon(this.cotizacion.formula_rs, this.respuesta.total_crudo, this.cotizacion.reales, this.cotizacion.candec_rs, this.nota.moneda, this.cotizacion.moneda_rs);

	            // ------------------------------------------------------------------------

	            // REALES

	            this.totales.cheque = Common.formulaCommon(this.cotizacion.formula_gs, this.respuesta.total_crudo, this.cotizacion.guaranies, this.cotizacion.candec_gs, this.nota.moneda, this.cotizacion.moneda_gs);

	            // ------------------------------------------------------------------------

	            // REALES

	            this.totales.transferencia = Common.formulaCommon(this.cotizacion.formula_gs, this.respuesta.total_crudo, this.cotizacion.guaranies, this.cotizacion.candec_gs, this.nota.moneda, this.cotizacion.moneda_gs);

	            // ------------------------------------------------------------------------

            } else if (this.nota.tipo === '2') {

	            // ------------------------------------------------------------------------

	            // GUARANIES 

	            this.totales.guaranies = Common.formulaCommon(this.cotizacion.formula_gs, this.nota.total, this.cotizacion.guaranies, this.cotizacion.candec_gs, this.nota.moneda, this.cotizacion.moneda_gs);

	            // ------------------------------------------------------------------------

	            // DOLARES 

	            this.totales.dolares = Common.formulaCommon(this.cotizacion.formula_$, this.nota.total, this.cotizacion.dolares, this.cotizacion.candec_$, this.nota.moneda, this.cotizacion.moneda_$);

	            // ------------------------------------------------------------------------

	            // PESOS

	            this.totales.pesos = Common.formulaCommon(this.cotizacion.formula_ps, this.nota.total, this.cotizacion.pesos, this.cotizacion.candec_ps, this.nota.moneda, this.cotizacion.moneda_ps);

	            // ------------------------------------------------------------------------

	            // REALES

	            this.totales.reales = Common.formulaCommon(this.cotizacion.formula_rs, this.nota.total, this.cotizacion.reales, this.cotizacion.candec_rs, this.nota.moneda, this.cotizacion.moneda_rs);

	            // ------------------------------------------------------------------------

	            // REALES

	            this.totales.cheque = Common.formulaCommon(this.cotizacion.formula_gs, this.nota.total, this.cotizacion.guaranies, this.cotizacion.candec_gs, this.nota.moneda, this.cotizacion.moneda_gs);

	            // ------------------------------------------------------------------------

	            // REALES

	            this.totales.transferencia = Common.formulaCommon(this.cotizacion.formula_gs, this.nota.total, this.cotizacion.guaranies, this.cotizacion.candec_gs, this.nota.moneda, this.cotizacion.moneda_gs);

	            // ------------------------------------------------------------------------

            }

          }, pruebaNota() {

          	// ------------------------------------------------------------------------

          	Common.generarPdfNotaCreditoCommon(49);

          	// ------------------------------------------------------------------------

          }, generarPdf(id) {

          	// ------------------------------------------------------------------------

          	Common.generarPdfNotaCreditoCommon(id).then(data => {
						window.location.href = '/mov2';	
			});

          	// ------------------------------------------------------------------------

          }, obtenerCaja() {

          		// ------------------------------------------------------------------------

      			let me=this;
          		
          		// ------------------------------------------------------------------------

          		// OBTENER CAJA 

          		Common.obtenerIPCommon(function(){

          			if (window.IPv !== false) {
          				axios.post('/cajaObtener', {'id': window.IPv}).then(function (response) {
	                	  if (response.data.response === true) {

	                	  	  me.caja.caja_proceso  =   response.data.caja[0].CAJA;

	                	  } else {
	                	  		
	                	  	  	Swal.fire({
									title: 'NO SE PUDO OBTENER CAJA',
									type: 'warning',
									confirmButtonColor: '#d33',
									confirmButtonText: 'Aceptar',
								}).then((result) => {
									
									window.location.href = '/mov2';

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
									
							window.location.href = '/vt0';

						})

          			}
                	
                });

                // ------------------------------------------------------------------------

      		} 
      },
        mounted() {
        	
        	// ------------------------------------------------------------------------

        	let me=this;
           	
           	// ------------------------------------------------------------------------

           	// OBTENER CAJA 

           	me.obtenerCaja();

           	// ------------------------------------------------------------------------

           	// LLAMAR FORMATOS 

           	me.formatoGuaranies();
           	me.formatoDolares();
           	me.formatoPesos();
           	me.formatoReales();
           	me.formatoTransferencia();
           	me.formatoCheque();

           	// ------------------------------------------------------------------------

           	// COTIZACION NOTA 

           	Common.cotizacionyMonedaFormaPagoCommon().then(data => {
                me.cotizacion = data;
            });

            // ------------------------------------------------------------------------

           	$('#tablaNotaFinal').DataTable ({
		        "data" : me.respuesta.productos,
		        "columns" : [
		        	{ "data" : "ID", "visible" : false },
		            { "data" : "CODIGO" },
		            { "data" : "CANTIDAD" },
		            { "data" : "DESCRIPCION" },
		            { "data" : "BASE5" },
		            { "data" : "BASE10" },
		            { "data" : "EXENTAS" },
		            { "data" : "GRAVADAS" },
		            { "data" : "IMPUESTO", "visible": false },
		            { "data" : "PRECIO_UNIT" },
		            { "data" : "TOTAL" }
		        ]
		    });

           	$(document).ready( function () {
 				$('#tableConstructorNota').on('change', 'tbody tr', function() {
                		me.marcar_dev();
                });
           	});		

        }
    }
</script>
<style>
	.hide {
		display: none;
	}

	.section {
	    padding: 100px 0;
	    position: relative;
	}

	.section-title {
	    padding-bottom: 10px;
	}

	.section-title h4 {
	    font-weight: 700;
	    color: #002A3A;
	    font-size: 30px;
	    margin: 0 0 15px;
	    border-left: 5px solid #fc5356;
	    padding-left: 15px;
	}

	.invoice-price {
	    background: #f0f3f4;
	    display: table;
	    width: 100%
	}

	.invoice-price .invoice-price-left,
	.invoice-price .invoice-price-right {
	    display: table-cell;
	    padding: 20px;
	    font-size: 20px;
	    font-weight: 600;
	    width: 75%;
	    position: relative;
	    vertical-align: middle
	}

	.invoice-price .invoice-price-left .sub-price {
	    display: table-cell;
	    vertical-align: middle;
	    padding: 0 20px
	}

	.invoice-price small {
	    font-size: 12px;
	    font-weight: 400;
	    display: block
	}

	.invoice-price .invoice-price-row {
	    display: table;
	    float: left
	}

	.invoice-price .invoice-price-right {
	    width: 25%;
	    background: #2d353c;
	    color: #fff;
	    font-size: 28px;
	    text-align: right;
	    vertical-align: bottom;
	    font-weight: 300
	}

	.invoice-price .invoice-price-right small {
	    display: block;
	    opacity: .6;
	    position: absolute;
	    top: 10px;
	    left: 10px;
	    font-size: 12px
	}

	/* CLIENTE */

	.project .row {
	    margin: 0;
	    padding: 15px 0;
	    margin-bottom: 15px
	}

	.project div[class*='col-'] {
	    border-right: 1px solid #eee
	}

	.project .text h3 {
	    margin-bottom: 0;
	    color: #555
	}

	.project .text small {
	    color: #aaa;
	    font-size: 0.75em
	}

	.project .project-date span {
	    font-size: 0.9em;
	    color: #999
	}

	.project .image {
	    max-width: 50px;
	    min-width: 50px;
	    height: 50px;
	    margin-right: 15px
	}

	.project .time,
	.project .comments,
	.project .project-progress {
	    color: #999;
	    font-size: 0.9em;
	    margin-right: 20px
	}

	.project .time i,
	.project .comments i,
	.project .project-progress i {
	    margin-right: 5px
	}

	.project .project-progress {
	    width: 200px
	}

	.project .project-progress .progress {
	    height: 4px
	}

	.project .card {
	    margin-bottom: 0
	}

</style>