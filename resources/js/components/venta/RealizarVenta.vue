<template>
	<div v-if="$can('venta.realizarventas') && $can('venta')" class="container-fluid bg-light">

		<!-- ------------------------------------------------------------------ -->

		<!-- SIDEBAR -->

		<b-sidebar   id="sidebar-right" title="Opciones"  right shadow>
	      <div class="px-3 py-2">

	      				<!-- ------------------------------------------------------------------ -->

	      				<button class="btn btn-outline-primary btn-sm btn-block" data-toggle="modal"data-target=".modal-atajos"><small>Atajos de Teclado</small></button>

	      				<!-- ------------------------------------------------------------------ -->

						<div class="col-md-12">
							<hr>
						</div>
						
						<!-- ------------------------------------------------------------------ -->

						<form class="form-inline">

							<div class="row">

								<!-- ------------------------------------------------------------------ -->

							    <!-- HABILITAR MAYORISTA -->

								<div class="col-md-6">

									<div class="my-1">
										<div class="custom-control custom-switch mr-sm-3 ">
											<input type="checkbox" class="custom-control-input" v-model="checked.MAYORISTA" id="switchMayorista" v-on:change="validarMayorista" :disabled="mayorista">
											<label style="font-size: 12px" class="custom-control-label float-left" for="switchMayorista">Mayorista</label>
										</div>
									</div>

								</div>

								<!-- ------------------------------------------------------------------ -->

								<!-- MOSTRAR TICKET -->
								
								<div class="col-md-6">			
									<div class="my-1">
										<div class="custom-control custom-switch mr-sm-3">
											<input type="checkbox" class="custom-control-input" id="switchTicket" v-model="checked.TICKET">
											<label style="font-size: 12px" class="custom-control-label float-left" for="switchTicket">Most. Ticket</label>
										</div>
									</div>
								</div>

								<!-- ------------------------------------------------------------------ -->

								<!-- MOSTRAR FACTURA -->

								<div class="col-md-6">		
									<div class="my-1">
										<div class="custom-control custom-switch mr-sm-3">
											<input type="checkbox" class="custom-control-input" id="switchFactura" v-model="checked.FACTURA">
											<label style="font-size: 12px" class="custom-control-label float-left" for="switchFactura">Most. Factura</label>
										</div>
									</div>
								</div>

								<!-- ------------------------------------------------------------------ -->

								<!-- MOSTRAR FACTURA -->
								
								<div class="col-md-6">		
									<div class="my-1">
										<div class="custom-control custom-switch mr-sm-3">
											<input type="checkbox" class="custom-control-input" id="switchDescuento" v-model="checked.DESCUENTO">
											<label style="font-size: 12px" class="custom-control-label float-left" for="switchDescuento">Desc. Auto</label>
										</div>
									</div>
								</div>

								<!-- ------------------------------------------------------------------ -->

								<!-- DEVOLUCION -->
										
								<!-- <div class="my-1">
									<div class="custom-control custom-switch mr-sm-3">
										<input type="checkbox" class="custom-control-input" v-on:change="devolucionLlamar" id="switchDevolucion" v-model="checked.DEVOLUCION" data-target=".modal-devolucion">
										<label class="custom-control-label" for="switchDevolucion" >Devolución</label>
									</div>
								</div> -->

								<!-- ------------------------------------------------------------------ -->

								<!-- HABILITAR MAYORISTA AUTOMATICO -->
								
								<div class="col-md-6">		
									<div class="my-1">
										<div class="custom-control custom-switch mr-sm-3">
											<input type="checkbox" class="custom-control-input" id="switchMayoristaAut" v-model="checked.MAYORISTA_AUT">
											<label style="font-size: 12px" class="custom-control-label float-left" for="switchMayoristaAut">Mayorista Aut.</label>
										</div>
									</div>
								</div>

								<!-- ------------------------------------------------------------------ -->
								

							</div>

						</form>

						<!-- ------------------------------------------------------------------ -->

						<div class="col-md-12">
							<hr>
						</div>

						<!-- ------------------------------------------------------------------ -->
					  
	      	<button class="btn btn-dark btn-sm btn-block" v-on:click="resumen_test"><small>Resumen Caja</small></button>
			<button class="btn btn-dark btn-sm btn-block" v-on:click="nota_credito"><small>Nota Crédito</small></button>
			<button class="btn btn-dark btn-sm btn-block" v-on:click="agencia_seleccionar"><small>Agencia</small></button>
			<button class="btn btn-dark btn-sm btn-block" v-on:click="movimiento_caja"><small>Movimiento de Caja</small></button>
			<button class="btn btn-dark btn-sm btn-block" v-on:click="factura_test"><small>Última Factura</small></button>
	    </div>
	    </b-sidebar>
	    

	    <!-- ------------------------------------------------------------------ -->

		<div class="row">

			<div class="col-md-10">

				<div class="row">

					<!-- ------------------------------------------------------------------ -->

				    <!-- TITULO  -->
			
					<div class="col-md-6 mt-3 mb-0">
						<div class="section-title">
		                    <h4>Realizar Venta</h4>
		                    <p>Crear ventas de productos y servicios.</p>
		                </div>
					</div>
					
			        <!-- ------------------------------------------------------------------------------------- -->
				    
				    <div class="col-md-6 mt-3 mb-0">
						<div class="row mb-3">
							<div class="col-md-7 text-right">
								<h3 class="card-text text-primary">CAJA: {{caja.CODIGO}}</h3>
							</div>
							
							<div class="col-md-5 text-right">
								<button v-b-toggle.sidebar-right class=" btn-menu btn btn-outline-dark btn-toggle-menu" type="button">
			                        <i class=" fa fa-bars"></i>
			                    </button>
							</div>		    		
						</div>
					</div>

				    <!-- ------------------------------------------------------------------ -->

				    <div class="col-md-12 mb-3">
						<div class="invoice-price">

							<div class="invoice-price-left">
								<div class="invoice-price-row">
									<div class="sub-price">
										<form class="form-inline">
											<div class="form-group">
												<div style="font-size:20px">
											    	<span class="flag-icon flag-icon-py"></span>
											    </div>
											    <h5 class="mt-1">&nbsp; - {{cotizacion.GUARANIES}} </h5>
										    </div>
									    </form>
									</div>
									<div class="sub-price">
										<form class="form-inline">
											<div class="form-group">
											    <div style="font-size:20px">
											    	<span class="flag-icon flag-icon-us"></span>
											    </div>
											    <h5 class="mt-1">&nbsp; - {{cotizacion.DOLARES}}</h5>
											</div>
									    </form>	
									</div>
									<div class="sub-price">
										<form class="form-inline">
											<div class="form-group">
											    <div style="font-size:20px">
											    	<span class="flag-icon flag-icon-br"></span>
											    </div>
											    <h5 class="mt-1">&nbsp; - {{cotizacion.REALES}}</h5>
										    </div>
									    </form>
									</div>
									<div class="sub-price">
										<form class="form-inline">
											<div class="form-group">
											    <div style="font-size:20px">
											    	<span class="flag-icon flag-icon-ar"></span>
											    </div>
											    <h5 class="mt-1">&nbsp; - {{cotizacion.PESOS}}</h5>
									    	</div>
									    </form>	
									</div>
								</div>
							</div>

							<div class="invoice-price-right">
								<small>TOTAL {{moneda.DESCRIPCION}}</small> <span class="f-20 f-w-900" v-on:change="gravadas">{{venta.TOTAL}}</span>
							</div>

						</div>
					</div>

					<!-- <div class="col-md-2 mt-3 d-flex align-items-center">
						<form class="form-inline">
							<div class="form-group">
								<div style="font-size:20px">
							    	<span class="flag-icon flag-icon-py"></span>
							    </div>
							    <h5 class="mt-1">&nbsp; - {{cotizacion.GUARANIES}} </h5>
						    </div>
					    </form>
					</div>

					<div class="col-md-2 mt-3 d-flex align-items-center">
						<form class="form-inline">
							<div class="form-group">
							    <div style="font-size:20px">
							    	<span class="flag-icon flag-icon-us"></span>
							    </div>
							    <h5 class="mt-1">&nbsp; - {{cotizacion.DOLARES}}</h5>
							</div>
					    </form>	    
					</div>

					<div class="col-md-2 mt-3 d-flex align-items-center">
						<form class="form-inline">
							<div class="form-group">
							    <div style="font-size:20px">
							    	<span class="flag-icon flag-icon-br"></span>
							    </div>
							    <h5 class="mt-1">&nbsp; - {{cotizacion.REALES}}</h5>
						    </div>
					    </form>
					</div>

					<div class="col-md-2 mt-3 d-flex align-items-center">
						<form class="form-inline">
							<div class="form-group">
							    <div style="font-size:20px">
							    	<span class="flag-icon flag-icon-ar"></span>
							    </div>
							    <h5 class="mt-1">&nbsp; - {{cotizacion.PESOS}}</h5>
					    	</div>
					    </form>
					</div>

					<div class="col-md-4 text-right mt-3">
						<label class="mb-0">Total {{moneda.DESCRIPCION}}</label>
						<h1 v-on:change="gravadas">{{venta.TOTAL}}</h1>
					</div> -->
					
					<!-- ------------------------------------------------------------------ -->

					<!-- <div class="col-md-12">
						<hr>
					</div>
 -->
					<div class="col-md-3">
						<!-- <div>
							 -->
						<span class="text-primary"><small>Nro. Ticket / Nro. Caja</small></span><br>
						<h6 class="text-secondary">{{venta.CODIGO + 1}} / {{venta.CODIGO_CAJA}}</h6>	 
						<!-- <label><span class="text-primary">Nro. Ticket:</span> {{venta.CODIGO + 1}}</label><br>
						<label><span class="text-primary">Nro. Caja:</span> {{venta.CODIGO_CAJA}}</label> -->
					</div>

					<!-- <div class="project col-md-3">
					    <div class="row bg-white has-shadow">
					      <div class="left-col col-lg-12 d-flex align-items-center justify-content-between">
					        <div class="project-title d-flex align-items-center">
					          <div class="image has-shadow">
					          	<span v-html="cliente.imagen"></span>
					          </div>
					          <div class="text">
					            <h3 class="h4">{{cliente.NOMBRE}}</h3><small>{{cliente.NOMBRE}}</small>
					          </div>
					        </div>
					        <div class="project-date"><span class="hidden-sm-down">{{cliente.NOMBRE}}</span></div>
					      </div>
					    </div>
					</div> -->

					<div class="col-md-3">
						<span class="text-primary"><small>Cliente</small></span><br>
						<h6 class="text-secondary">{{cliente.NOMBRE}}</h6>
					</div> 

					<div class="col-md-3">
						<span class="text-primary"><small>Vendedor</small></span><br>
						<h6 class="text-secondary">{{vendedor.NOMBRE}}</h6>
					</div> 

					<div class="col-md-3">
						<span class="text-primary"><small>Agencia</small></span><br>
						<h6 class="text-secondary">{{agencia.NOMBRE}}</h6>
						<!-- <label><span class="text-primary">Agencia:</span> {{agencia.NOMBRE}}</label><br> -->
						
					</div> 

					<!-- <div class="col-md-2">
						<label class="text-primary">Exentas</label>
						<h6>2,300.00 USD</h6>
					</div> 

					<div class="col-md-2">
						<label class="text-primary">Gravadas</label>
						<h6>{{venta.GRAVADAS}}</h6>
					</div> 

					<div class="col-md-2">
						<label class="text-primary">I.V.A.</label>
						<h6>{{venta.IMPUESTO}}</h6>
					</div>   -->
					
					<div class="col-md-12">
						<hr>
					</div>

					<!-- ------------------------------------------------------------------------------------- -->
		   
				   	<!-- AGREGAR PRODUCTO -->

					<div class="col-md-12">
				
						<div class="mt-2">

							<div class="row">
								<div class="col-2">
									<codigo-producto :validar_codigo_producto="validar.COD_PROD" @codigo_producto="cargarProductos" ref="compontente_codigo_producto" v-model="producto.COD_PROD"></codigo-producto >
								</div>	

								<div class="col-md-1">
									<label for="validationTooltip01">Cantidad</label>
									<input v-model="producto.CANTIDAD" class="form-control form-control-sm" type="text">
								</div>

								<div class="col-md-1">
									<label for="validationTooltip01">Desc.</label>
									<input class="form-control form-control-sm" type="text" v-on:change='autorizarDescuento' v-model="producto.DESCUENTO">
								</div>

								<div class="col-md-2">
									<label for="validationTooltip01">Precio Venta</label>
									<div class="input-group input-group-sm mb-3" >
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroup-sizing-sm">{{moneda.DESCRIPCION}}</span>
										</div>
							    		<input ref="precio_venta" tabindex="1" v-model="producto.PREC_VENTA" class="form-control form-control-sm" type="text" v-on:blur="formatoPrecio">
									</div>
								</div>

								<div class="col-md-1">
									<label for="validationTooltip01">Stock</label>
									<input v-model="producto.STOCK" class="form-control form-control-sm" type="text" disabled>
								</div>

								<div class="col-md-5">
									<label for="validationTooltip01">Descripción</label>
									<input v-model="producto.DESCRIPCION" tabindex="2" class="form-control form-control-sm" type="text"  :disabled="deshabilitar.DESCRIPCION">
								</div>

							</div>

						</div>	
					</div>		

					<!-- FINAL AGREGAR PRODUCTO -->
					
					<!-- ------------------------------------------------------------------------ -->

					<div class="col-md-12 mt-4">

						<!-- ------------------------------------------------------------------------ -->

				        <!-- TABLA VENTA -->

							<table id="tablaVenta" class="display nowrap table table-hover table-bordered table-sm mb-3" style="width:100%">
				                <thead>
				                    <tr>
				                        <th>#</th>
				                        <th class="codigoDeclarados">Codigo</th>
				                        <th>Descripción</th>
				                        <th>L</th>
				                        <th>%</th>
				                        <th>Desc.</th>
				                        <th class="cantidadColumna">Cant.</th>
				                        <th class="impuestoColumna">Iva</th>
				                        <th class="precioColumna">Precio</th>
				                        <th class="precioTotalColumna">Total</th>
				                        <th>Acción</th>
				                        <th>IVA</th>
				                        <th>Codigo Real</th>
				                        <th>Precio Mayorista</th>
				                        <th>Descuento Unitario</th>
				                        <th>Tipo</th>
				                        <th>Id</th>
				                        <th>Tipo Descuento</th>
				                    </tr>
				                </thead>
				                <tbody>
				                    
				                </tbody>
				                <tfoot>
				                	<tr>
				                		<th></th>
				                		<th></th>
				                		<th></th>
					                	<th></th>
					                	<th></th>
					                	<th>TOTALES</th>
					                	<th></th>
					                	<th></th>
					                	<th></th>
					                	<th></th>
					                	<th></th>
					                	<th></th>
					                	<th></th>
					                	<th></th>
					                	<th></th>
					                	<th></th>
					                    <th></th>
					                	<th></th>
				                	</tr>
				                </tfoot>	
				            </table>

						<!-- ------------------------------------------------------------------------ -->

					</div>

					<div class="col-md-12 mt-3">
						<div class="text-right" v-if="cliente.RETENTOR === 1">
							RETENCIONES: {{venta.RETENCION}}
						</div>	
					</div>

				</div>

			</div>	

			<div class="col-md-2 mt-3">

				

				<!-- ------------------------------------------------------------------ -->

				<div>
					<div class="text-center" v-if="ajustes.LOGO !== 0">
						<span v-html="ajustes.LOGO"></span>
					</div>
				</div>

				<!-- ------------------------------------------------------------------ -->

				<div class="col-md-12 mb-2">
					<hr>
				</div>

				<!-- ------------------------------------------------------------------ -->

				<!-- IMAGEN -->

				<div>
					<div class="card">
					    <span v-html="imagen.RUTA"></span>
										  <!-- <img v-if="producto.COD_PROD === ''" :src="imagen.RUTA" class="card-img-top" alt="..."> -->
					</div>
				</div>

	        	<!-- ------------------------------------------------------------------------------------- -->

				<!-- TITULO  -->
			
				<div class="col-md-12">
					<vs-divider>
						Cliente
					</vs-divider>
				</div>
			
	        	<!-- ------------------------------------------------------------------------------------- -->

				<busqueda-cliente-modal @codigo="codigoCliente" @nombre="nombreCliente" @tipo="tipoCliente" @data="dataCliente" ref="componente_cliente_modal" ></busqueda-cliente-modal>

				<div class="col-md-12">
					<hr>
				</div>

				<busqueda-vendedor-modal @codigo="codigoVendedor" @nombre="nombreVendedor"></busqueda-vendedor-modal>
				
				<div class="row mt-3 mb-3">
					<div class="col-md-6">
						<button class="btn btn-outline-dark btn-sm btn-block" v-on:click="nuevo"><font-awesome-icon size="1x" icon="file-alt" /><br><small>(F1) Nuevo</small></button>
					</div>
					<div class="col-md-6">
						<button class="btn btn-outline-dark btn-sm btn-block" v-on:click="guardar"><font-awesome-icon size="1x" icon="cash-register" /><br><small>(Esc) Fact.</small></button>
					</div>	
				</div>
				<!-- <button class="btn btn-dark btn-sm btn-block" v-on:click="ticket_mostrar"><small>Último Ticket</small></button> -->
				
				
				<!-- <button class="btn btn-dark btn-sm btn-block" v-on:click="test_factura"><small>Test Factura</small></button> -->
			</div>

		</div>
		
		<!-- ------------------------------------------------------------------------ -->

		<!-- FORMA PAGO PROVEEDOR -->

		<forma-pago-textbox :total="venta.TOTAL" :total_crudo="venta.TOTAL_CRUDO" :moneda="moneda.CODIGO" :candec="moneda.DECIMAL" :customer="cliente.CODIGO" @datos="formaPago" :retencion="venta.RETENCION" ref="compontente_medio_pago"></forma-pago-textbox>

		<!-- ------------------------------------------------------------------------ -->	

		<!-- MODAL DEVOLUCION -->

		<div class="modal fade modal-devolucion" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">

                      <div class="modal-content">

                      	<!-- ------------------------------------------------------------------------ -->

                      	<!-- HEADER -->

                      	<div class="modal-header">
                          <h5 class="modal-title" id="exampleModalCenterTitle"><small>DEVOLUCION</small></h5>
                        </div>

                        <!-- ------------------------------------------------------------------------ -->

                        <div class="modal-body">  
                        	<ventas-global-textbox class="form-input-inline form-input-sm" ref="componente_textbox_Ventas" @codigo='codigo_devolucion'></ventas-global-textbox>

                        	<table id="devolucionProductos" class="table table-hover table-bordered table-sm mb-3 mt-3" style="width:100%">
					            <thead>
					                <tr>
					                	<th>ID</th>
					                    <th>ITEM</th>
					                    <th>Codigo Producto</th>
					                    <th>Descripción</th>
					                    <th>%</th>
					                    <th>Descuento</th>
					                    <th>Cantidad</th>
					                    <th>IVA</th>
					                    <th>Impuesto</th>
					                    <th>Precio</th>
					                    <th>Total</th>
					                </tr>
					            </thead>
					            <tbody>
					                <td></td>
					            </tbody>
					        </table>

                        </div>	
		      			
		      			<!-- ------------------------------------------------------------------------ -->

		      			<!-- FOOTER -->

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">CERRAR</button>
                        </div>

                        <!-- ------------------------------------------------------------------------ -->

		    		  </div>

		  			</div>
		</div>

		<!-- ------------------------------------------------------------------------ -->

		<!-- MOVIMIENTO CAJA -->

		<div class="modal fade modal-mov-caja" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">

                      <div class="modal-content">

                      	<!-- ------------------------------------------------------------------------ -->

                      	<!-- HEADER -->

                      	<div class="modal-header">
                          <h5 class="modal-title" id="exampleModalCenterTitle"><small>MOVIMIENTO CAJA</small></h5>
                        </div>

                        <!-- ------------------------------------------------------------------------ -->

                        <div class="modal-body">

                        	<div class="row">

	                            <div class="col-md-6">
	                                <label>Movimiento</label>
	                                <select v-model="movimiento.TIPO" class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validar.MOVIMIENTO.TIPO }">
	                                        <option :value="null">SELECCIONAR</option>
	                                        <option :value="1">Entrada</option>
	                                        <option :value="2">Salida</option>
	                                </select>       
	                            </div>

	                            <div class="col-md-6">
	                                <label>Tipo</label>
	                                <select v-model="movimiento.MEDIO" class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validar.MOVIMIENTO.MEDIO }">
	                                        <option :value="null">SELECCIONAR</option>
	                                        <option :value="1">Efectivo</option>
	                                        <option :value="2">Cheque</option>
	                                </select>       
	                            </div>

	                            <!-- ------------------------------------------------------------------------ -->

	                            <!-- MONEDA -->

	                            <div class="col-md-6 mt-3">
	                            	
									<select-moneda v-model="movimiento.MONEDA" @descripcion_moneda="descripcionMoneda" @cantidad_decimales="cantidadDecimal"></select-moneda>
									<span class="badge badge-danger" v-if="mostrar.SINCOTIZACION">No hay cotización</span>

	                            </div>

	                            <!-- ------------------------------------------------------------------------ -->

	                            <!-- IMPORTE -->

	                            <div class="col-md-6 mt-3">
				        			<label>Importe</label>
									<input class="form-control form-control-sm" type="text" v-model="movimiento.IMPORTE" v-bind:class="{ 'is-invalid': validar.MOVIMIENTO.IMPORTE }" v-on:blur="formatoImporte" :disabled="deshabilitar.movimiento.IMPORTE">
				        		</div>

				        		<!-- ------------------------------------------------------------------------ -->

				        		<div class="col-md-12 mt-3">
				        			<label for="comentarioTextArea">Comentario</label>
				        			<textarea v-model="movimiento.COMENTARIO" v-bind:class="{ 'is-invalid': validar.MOVIMIENTO.COMENTARIO }" class="form-control" id="comentarioTextArea" rows="3"></textarea>
				        		</div>

                            </div>

                        </div>	
		      			
		      			<!-- ------------------------------------------------------------------------ -->

		      			<!-- FOOTER -->

                        <div class="modal-footer">
                        	<button type="button" v-on:click="guardar_movimiento_caja()" class="btn btn-outline-dark"><font-awesome-icon size="1x" icon="save"  /> GUARDAR</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">CERRAR</button>
                        </div>

                        <!-- ------------------------------------------------------------------------ -->

		    		  </div>

		  			</div>
		</div>

		<!-- ------------------------------------------------------------------------ -->

		<!-- ATAJOS DE TECLADO -->

		<div class="modal fade modal-atajos" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">

                      <div class="modal-content">

                      	<!-- ------------------------------------------------------------------------ -->

                      	<!-- HEADER -->

                      	<div class="modal-header">
                          <h5 class="modal-title" id="exampleModalCenterTitle"><small>ATAJOS</small></h5>
                        </div>

                        <!-- ------------------------------------------------------------------------ -->

                        <div class="modal-body">

                        	<div class="row">

                        		

	                            <div class="col-md-6">
	                            	<div class="badge badge-dark text-wrap" style="width: 6rem;">
									  F1
									</div>
				        		</div>

				        		<div class="col-md-6">
	                            	<p class="text-monospace">Nueva Venta.</p>
				        		</div>

				        		<div class="col-md-12">
	                            	<hr/>
				        		</div>

				        		<div class="col-md-6">
	                            	<div class="badge badge-dark text-wrap" style="width: 6rem;">
									  Esc
									</div>
				        		</div>

				        		<div class="col-md-6">
	                            	<p class="text-monospace">Facturar Venta.</p>
				        		</div>

				        		<div class="col-md-12">
	                            	<hr/>
				        		</div>

				        		<div class="col-md-6">
	                            	<div class="badge badge-dark text-wrap" style="width: 6rem;">
									  Shift + p
									</div>
				        		</div>

				        		<div class="col-md-6">
	                            	<p class="text-monospace">Mostrar productos.</p>
				        		</div>

				        		<div class="col-md-12">
	                            	<hr/>
				        		</div>

				        		<div class="col-md-6">
	                            	<div class="badge badge-dark text-wrap" style="width: 6rem;">
									  Shift + c
									</div>
				        		</div>

				        		<div class="col-md-6">
	                            	<p class="text-monospace">Mostrar clientes.</p>
				        		</div>

				        		<div class="col-md-12">
	                            	<hr/>
				        		</div>

				        		<div class="col-md-6">
	                            	<div class="badge badge-dark text-wrap" style="width: 6rem;">
									  Shift + r
									</div>
				        		</div>

				        		<div class="col-md-6">
	                            	<p class="text-monospace">Registrar cliente.</p>
				        		</div>

				        		<div class="col-md-12">
	                            	<hr/>
				        		</div>

				        		<div class="col-md-6">
	                            	<div class="badge badge-dark text-wrap" style="width: 6rem;">
									  Shift + m
									</div>
				        		</div>

				        		<div class="col-md-6">
	                            	<p class="text-monospace">Movimiento caja.</p>
				        		</div>

				        		<div class="col-md-12">
	                            	<hr/>
				        		</div>

				        		<div class="col-md-6">
	                            	<div class="badge badge-dark text-wrap" style="width: 6rem;">
									  Shift + v
									</div>
				        		</div>

				        		<div class="col-md-6">
	                            	<p class="text-monospace">Mostrar vendedor.</p>
				        		</div>

				        		<div class="col-md-12">
	                            	<hr/>
				        		</div>

				        		<div class="col-md-6">
	                            	<div class="badge badge-dark text-wrap" style="width: 6rem;">
									  Shift + n
									</div>
				        		</div>

				        		<div class="col-md-6">
	                            	<p class="text-monospace">Mostrar nota de crédito.</p>
				        		</div>

                            </div>

                        </div>	
		      			
		      			<!-- ------------------------------------------------------------------------ -->

		      			<!-- FOOTER -->

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">CERRAR</button>
                        </div>

                        <!-- ------------------------------------------------------------------------ -->

		    		  </div>

		  			</div>
		</div>

		<!-- ------------------------------------------------------------------------ -->
		<!-- TOAST STOCK CERO -->

			<b-toast id="toast-stock-cero" variant="warning" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">Error !</strong>
		          <small class="text-muted mr-2">cero</small>
		        </div>
		      </template>
		      Este producto ya no tiene stock !
		    </b-toast>

		<!-- ------------------------------------------------------------------------ -->

		<!-- TOAST SERVICIO EXISTENTE -->

		<div aria-live="polite" aria-atomic="true" class="d-flex justify-content-center align-items-center" style="min-height: 200px;">
			<b-toast id="toast-servicio-repetido" variant="warning" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">Error !</strong>
		          <small class="text-muted mr-2">Existente</small>
		        </div>
		      </template>
		      Este servicio ya ha sido agregado !
		    </b-toast>
		</div>

		<!-- ------------------------------------------------------------------------ -->

		<!-- TOAST FALTA COMPLETAR -->

			<b-toast id="toast-falta-completar" variant="warning" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">Error !</strong>
		          <small class="text-muted mr-2">Incompleto</small>
		        </div>
		      </template>
		      Para realizar la venta se necesita añadir más datos !
		    </b-toast>

		<!-- ------------------------------------------------------------------------ -->

		<!-- TOAST FALTA COMPLETAR -->

			<b-toast id="toast-no-existe" variant="warning" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">Error !</strong>
		          <small class="text-muted mr-2">No existe</small>
		        </div>
		      </template>
		      Este código no existe !
		    </b-toast>

		<!-- ------------------------------------------------------------------------ -->

		<!-- TOAST DESCUENTO SUPERADO -->

			<b-toast id="toast-descuento-error" variant="warning" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">Error !</strong>
		          <small class="text-muted mr-2">Equivocado</small>
		        </div>
		      </template>
		      Error en la aplicación del descuento, favor verifque su valor !
		    </b-toast>

		<!-- ------------------------------------------------------------------------ -->

		<!-- TOAST STOCK SUPERADO -->

			<b-toast id="toast-cantidad-superada" variant="warning" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">Error !</strong>
		          <small class="text-muted mr-2">superado</small>
		        </div>
		      </template>
		      La cantidad ingresada ya supera el stock !
		    </b-toast>

		<!-- ------------------------------------------------------------------------ -->

		<!-- TOAST PRODUCTO TRANSFERENCIA MODIFICADO -->

			<b-toast id="toast-producto-modificado" variant="success" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <strong class="mr-auto">Éxito !</strong>
		          <small class="text-muted mr-2">modificado</small>
		        </div>
		      </template>
		      Este producto ha sido modificado con éxito !
		    </b-toast>

		<!-- ------------------------------------------------------------------------ -->

		<!-- TOAST PRODUCTO TRANSFERENCIA MODIFICADO -->

			<b-toast id="toast-total-negativo" variant="warning" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">Error !</strong>
		          <small class="text-muted mr-2">negativo</small>
		        </div>
		      </template>
		      El total no puede ser negativo !
		    </b-toast>

		<!-- ------------------------------------------------------------------------ -->

		<!-- TOAST  -->

			<b-toast id="toast-falta-completar-movimiento-caja" variant="warning" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">Error !</strong>
		          <small class="text-muted mr-2">completar</small>
		        </div>
		      </template>
		      Completar todos los datos por favor !
		    </b-toast>

		<!-- ------------------------------------------------------------------------ -->

		<!-- MODAL DETALLE PRODUCTO -->

		<producto-detalle ref="detalle_producto" :codigo="codigo_detalle"></producto-detalle>

		<!-- ------------------------------------------------------------------------ -->

		<!-- MODAL NOTA CREDITO -->

		<nota-credito-cliente-datatable @data="dataNotaCreditoTextbox"  ref="detalle_nota_credito_cliente"></nota-credito-cliente-datatable>

		<!-- ------------------------------------------------------------------------ -->

		<!-- MODAL AGENCIA -->

		<agencia-datatable-textbox @data="dataAgenciaTextbox"  ref="lista_agencia"></agencia-datatable-textbox>

		<!-- ------------------------------------------------------------------------ -->

		<!-- AUTORIZAR VENTA -->

		<autorizacion @data="autorizacionData" ref="autorizacion_componente"></autorizacion>

		<!-- ------------------------------------------------------------------------ -->

	</div>
	<div v-else>
		<cuatrocientos-cuatro></cuatrocientos-cuatro>
	</div>
</template>
<script>
	export default {
      data(){
        return {
         	moneda: {
         		DECIMAL: '',
         		CODIGO: '',
         		DESCRIPCION: '',
         		DESHABILITAR: false
         	},
         	cliente: {
         		CODIGO: 1,
         		CI: '',
         		NOMBRE: '',
         		TIPO: '',
         		RETENTOR: 0
         	},
         	vendedor: {
         		CODIGO: 1,
         		CI: '',
         		NOMBRE: ''
         	},
         	caja: {
         		CODIGO: null,
         		CANTIDAD_PERSONALIZADA: 1,
         		CANTIDAD_TICKET: 1,
         		RECARGAR: 0
         	},
         	producto: {
         		COD_PROD: '',
         		DESCRIPCION: '',
         		PREC_VENTA: '',
         		PREMAYORISTA: '',
         		PREC_AUX: '',
         		LOTE: '',
         		STOCK: '',
         		PRECIO_TOTAL: 0,
         		CANTIDAD: 1,
         		DESCUENTO: 0,
         		DESCUENTO_MONTO: 0,
         		DESCUENTO_UNITARIO: 0,
         		TIPO_DESCUENTO: 0,
         		IVA: 0,
         		IMPUESTO: 0,
         		CODIGO_REAL: '',
         		DESCUENTO_CATEGORIA: 0
         	},
         	validar: {
         		COD_PROD: false,
         		MOVIMIENTO: {
         			MEDIO: false,
         			MONTO: false,
         			TIPO: false,
         			COMENTARIO: false,
         			IMPORTE: false
         		},
         		IMPORTE: false
         	}, 
         	venta: {
         		CODIGO: '',
         		TOTAL: 0,
         		GRAVADAS: 0,
         		IMPUESTO: 0,
         		TOTAL_CRUDO: 0,
         		CODIGO_CAJA: '',
         		RETENCION: 0,
         	}, respuesta: {
         		cabecera: '',
         		moneda: '',
         		pago: ''
         	}, cotizacion: {
         		DOLARES: '',
         		GUARANIES: '',
         		PESOS: '',
         		REALES: ''
         	}, ajustes: {
         		LIMITE_MAYORISTA: 0,
         		IMPRESORA_TICKET: '',
         		IMPRESORA_MATRICIAL: '',
         		LOGO: ''
         	}, checked: {
         		MAYORISTA: false,
         		TICKET: false,
         		FACTURA: false,
         		DESCUENTO: true,
         		DEVOLUCION: false,
         		MAYORISTA_AUT: true
         	}, codigo_detalle: '',
         	impresion: {
         		TICKET: false,
         		FACTURA: false,
         		TIPO: ''
         	}, deshabilitar: {
         		DESCRIPCION: true,
         		movimiento: {
         			IMPORTE: false
         		}
         	}, servicio: {
         		COD_PROD: '',
			    DESCRIPCION: '',
			    LOTE: 0,
			    STOCK: 1,
			    IVA: 0
         	}, imagen: {
         		RUTA: "<img src='http://172.16.249.20:8080/storage/imagenes/productos/product.png'  class='card-img-top'>",
         		//RUTA: require('./../../../imagenes/SinImagen.png'),
         	}, tabla: {
         		ITEM: 0,
         		tableProductosDevolucion: ''
         	}, devolucion: {
         		CODIGO: 0,
         		PRODUCTO: {
         			CODIGO: ''
         		}
         	}, agencia: {
         		CODIGO: '',
         		NOMBRE: 'NINGUNO'
         	}, autorizacion: {
         		HABILITAR: 0,
         		CODIGO: 0,
         		ID_USUARIO: 0,
         		PERMITIDO: 0,
         		ID_USER_SUPERVISOR: 0
         	}, datos: '',
         	movimiento: {
         		TIPO: null,
         		MEDIO: null,
         		IMPORTE: 0,
         		DECIMAL: 0,
         		MONEDA: '0',
         		COMENTARIO: '',
         		CAJA: null,
         		MONEDA_DESCRIPCION: '',
         		MONEDA_SISTEMA: '0'
         	}, mostrar: {
         		SINCOTIZACION: ''
         	},
         	mayorista: true,
         	permiso: false,
         	descuento_lote:[],
         	descuento_lote_validar:false,
         	cantidad_restante:0

        }
      }, 
      methods: {

      		formaPago(datos) {

      			// ------------------------------------------------------------------------

	        	let me = this;
	        	var tableVenta = $('#tablaVenta').DataTable();

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

	        	this.datos = datos;

	        	// ------------------------------------------------------------------------

	        	// REVISAR AUTORIZACION 


	        	if(this.autorizacion.HABILITAR === 1){

		        	if ((datos.DESCUENTO_GENERAL_PORCENTAJE > 0 || this.venta.TOTAL === 0 || this.venta.TOTAL === '0' || this.venta.TOTAL === '0.00' || this.venta.TOTAL === 0.00 || datos.PAGO_AL_ENTREGAR === true || parseFloat(Common.quitarComaCommon(datos.CREDITO)) > 0 || parseFloat(Common.quitarComaCommon(datos.CUPON_TOTAL)) > 0 || parseFloat(Common.quitarComaCommon(datos.VALE)) > 0) && (this.autorizacion.PERMITIDO === 0)) {
		        		this.permiso = true;
		        		this.revisarAutorizacion();
		        		return;
		        	}

		        	if(this.autorizacion.ID_USUARIO !== 0 && this.permiso === false){
		         		this.autorizacion.PERMITIDO = 1;
		        	}
	        	}

	        	// ------------------------------------------------------------------------

	        	// GUARDAR 

	        	this.respuesta = {
	        		autorizacion: this.autorizacion,
	        		cabecera: this.venta,
	        		cliente: this.cliente,
	        		vendedor: this.vendedor,
	        		agencia: this.agencia,
	        		caja: this.caja,
	        		moneda: this.moneda.CODIGO,
	        		pago: datos,
	        		productos: tableVenta.rows().data().toArray(),
	        		mayorista: this.checked.MAYORISTA,
	        	}

	        	Swal.fire({
				  title: 'Guardando venta',
				  html: 'Cerrare en cuanto guarde la venta.',
				  onBeforeOpen: () => {

				  	// ------------------------------------------------------------------------

				  	// MOSTRAR CARGAR 

				    Swal.showLoading()

				    // ------------------------------------------------------------------------

				    Common.guardarVentaCommon(me.respuesta).then(data => {

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

								// GENERAR FACTURA Y TICKET

								me.impresion.TIPO = datos.TIPO_IMPRESION;

								if (me.checked.FACTURA === true && datos.TIPO_IMPRESION === "2") {
									Common.generarPdfFacturaVentaVisualizarCommon(data.CODIGO, data.CAJA);
								} 

								if (me.checked.TICKET === true) {
									Common.generarPdfTicketVentaVisualizarCommon(data.CODIGO, data.CAJA);
								} else {
									me.ticket(data.CODIGO, data.CAJA);
								}

								// ------------------------------------------------------------------------

								// VOLVER TRUE FACTURA SI ES QUE SOLO IMPRIMIO TICKET 

								if(datos.TIPO_IMPRESION === "1") {
									me.impresion.FACTURA = true;
								}

								// ------------------------------------------------------------------------ 

								me.recargar();
								
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

	   //      	Swal.fire({
				// 	title: 'Estas seguro ?',
				// 	text: "Guardar la venta !",
				// 	type: 'warning',
				// 	showLoaderOnConfirm: true,
				// 	showCancelButton: true,
				// 	confirmButtonColor: '#d33',
				// 	cancelButtonColor: '#3085d6',
				// 	confirmButtonText: 'Si, guardalo!',
				// 	cancelButtonText: 'Cancelar',
				// 	preConfirm: () => {
					    
				// 	}
				// }).then((result) => {
					
				// 	if (result.value.response) {

				// 		// ------------------------------------------------------------------------

				// 		Swal.fire(
				// 			'Guardado !',
				// 			 result.value.statusText,
				// 			'success'
				// 		)

				// 		// ------------------------------------------------------------------------

				// 		// GENERAR FACTURA Y TICKET

				// 		me.impresion.TIPO = datos.TIPO_IMPRESION;

				// 		if (me.checked.FACTURA === true && datos.TIPO_IMPRESION === "2") {
				// 			Common.generarPdfFacturaVentaVisualizarCommon(result.value.CODIGO, result.value.CAJA);
				// 		} 

				// 		if (me.checked.TICKET === true) {
				// 			Common.generarPdfTicketVentaVisualizarCommon(result.value.CODIGO, result.value.CAJA);
				// 		} else {
				// 			me.ticket(result.value.CODIGO, result.value.CAJA);
				// 		}
				// 		// ------------------------------------------------------------------------

				// 		// VOLVER TRUE FACTURA SI ES QUE SOLO IMPRIMIO TICKET 

				// 		if(datos.TIPO_IMPRESION === "1") {
				// 			me.impresion.FACTURA = true;
				// 		}

				// 		// ------------------------------------------------------------------------ 

				// 	}
				// })
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
	                	  	  me.caja.CANTIDAD_PERSONALIZADA  =   response.data.caja[0].CANTIDAD_PERSONALIZADA;
	                	  	  me.caja.CANTIDAD_TICKET = response.data.caja[0].CANTIDAD_TICKET;
	                	  	  me.caja.RECARGAR = response.data.caja[0].RECARGAR;
	                	  	  me.numeracion();
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
      		recargar(){

      			// ------------------------------------------------------------------------ 

      			// RECARGAR LA PAGINA 

      			//this.test();

      			if ((this.impresion.TICKET === true && this.impresion.FACTURA === true) || this.caja.RECARGAR === 1) {
      				window.location.href = '/vt2';
      			}

				// ------------------------------------------------------------------------ 

      		},
      		nuevo(){

      			// ------------------------------------------------------------------------ 

      			// RECARGAR LA PAGINA 

      			Swal.fire({
					title: '¿ Crear nueva venta ?',
					type: 'warning',
					showLoaderOnConfirm: true,
					showCancelButton: true,
					confirmButtonColor: '#d33',
					cancelButtonColor: '#3085d6',
					confirmButtonText: 'Aceptar',
					cancelButtonText: 'Cancelar'
				}).then((result) => {
					
					if (result.value === true) {
						window.location.href = '/vt2';
					}
					
				})

	        	// ------------------------------------------------------------------------

      		},
      		controlador(){

      			// ------------------------------------------------------------------------

	        	let me = this;
	        	var falta = false;
	        	var tableVenta = $('#tablaVenta').DataTable();

	        	// ------------------------------------------------------------------------

	        	// if (me.proveedor.length === 0) {
	         //        me.validar.PROVEEDOR = true;
	         //        falta = true;
	         //    } else {
	         //        me.validar.PROVEEDOR = false;
	         //    }

	            // ------------------------------------------------------------------------

	            // REVISAR TABLA VENTA 

	            if (tableVenta.rows().data().length === 0) {
	            	falta = true;
	            }

            	// ------------------------------------------------------------------------

	            // RETORNAR FALTA - SI ES TRUE SE DETIENE EL GUARDADO 
            	// SI ES FALSE CONTINUA LA OPERACION 

            	return falta;

        		// ------------------------------------------------------------------------

      		},
      		guardar() {

	        	// ------------------------------------------------------------------------
	        	
	        	// CONTROLADOR

	        	if (this.controlador() === true) {
	        		this.$bvToast.show('toast-falta-completar');
	        		return;
	        	}

	        	// QUITAR FORMATO TOTAL

	        	this.venta.TOTAL_CRUDO = Common.quitarComaCommon(this.venta.TOTAL);

	        	// ------------------------------------------------------------------------

	        	if (this.venta.TOTAL_CRUDO >= 0) {
	        		this.$refs.compontente_medio_pago.procesarFormas();
	        	} else {
	        		this.$bvToast.show('toast-total-negativo');
	        	}

	        	// ------------------------------------------------------------------------

        	}, formatoPrecio(){

	            // ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;
	            var impuesto = 0, tipo = 2, rowClass='table-primary';

	            // ------------------------------------------------------------------------

	            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A PRECIO

	            me.producto.PREC_VENTA = Common.darFormatoCommon(me.producto.PREC_VENTA, me.moneda.DECIMAL);

	            // ------------------------------------------------------------------------
	            
	            // IMPUESTO

	            impuesto = Common.calcularIVACommon(me.producto.PREC_VENTA, me.servicio.IVA, me.moneda.DECIMAL);

	            // ------------------------------------------------------------------------

	            // CONTROLADOR SERVICIO

	            if (me.producto.COD_PROD.length === 0) {
	                me.validar.COD_PROD = true;
	                return;
	            } else {
	                me.validar.COD_PROD = false;
	            }

	            if (me.producto.PREC_VENTA.length === 0 ) {
	                me.validar.PRECIO_UNITARIO = true;
	                return;
	            } else {
	                me.validar.PRECIO_UNITARIO = false;
	            }

	            if (me.producto.CANTIDAD.length === 0  || me.producto.CANTIDAD === '0') {
	                me.validar.CANTIDAD = true;
	                return;
	            } else {
	                me.validar.CANTIDAD = false;
	            }

	            // ------------------------------------------------------------------------

	            me.agregarFilaTabla(me.servicio.COD_PROD, me.servicio.DESCRIPCION, me.servicio.LOTE, 0, 1, impuesto, me.producto.PREC_VENTA, me.producto.PREC_VENTA, me.servicio.IVA, 0, '', me.producto.PREC_VENTA, 0, tipo, rowClass);

	            // ------------------------------------------------------------------------

	            // INICIAR AGREGAR PRODUCTO 

	            me.inivarAgregarProducto();

	            // ------------------------------------------------------------------------

	        },
        	ticket_mostrar(){

        		//this.ticket(8, 1);
        		Common.generarPdfTicketVentaVisualizarCommon(this.venta.CODIGO, this.caja.CODIGO);
        	},
        	factura_test(){
        		Common.generarPdfFacturaVentaVisualizarCommon(1, 1);
        		//this.factura(8, 1);
        	},
        	test_factura(){
        		this.factura(103, 1);
        	},
        	resumen_test(){
        		Common.generarPdfResumenCajaVentaCommon(this.caja.CODIGO);
        	},
      		gravadas(){

      			// ------------------------------------------------------------------------

      			this.venta.GRAVADAS = Common.darFormatoCommon(Common.quitarComaCommon(this.venta.TOTAL) - Common.quitarComaCommon(this.venta.IMPUESTO), 0);

      			// ------------------------------------------------------------------------

      		},
      		nombreCliente(cliente){

      			// ------------------------------------------------------------------------

      			// NOMBRE CLIENTE 

      			this.cliente.NOMBRE = cliente;

      			// ------------------------------------------------------------------------

      		},
      		codigoCliente(cliente){

      			// ------------------------------------------------------------------------

      			// CODIGO CLIENTE 

      			this.cliente.CODIGO = cliente;

      			// ------------------------------------------------------------------------

      			$('.registrar-cliente-modal').modal('hide');

      			// ------------------------------------------------------------------------

      		},
      		tipoCliente(cliente){

      			let me = this; 

      			// ------------------------------------------------------------------------

      			// TIPO CLIENTE 

      			me.cliente.TIPO = cliente;

      			// ------------------------------------------------------------------------

      			// MAYORISTA

      			if(me.cliente.NOMBRE === 'CLIENTE OCASIONAL' || me.cliente.NOMBRE === '' || me.cliente.NOMBRE === 'ONLINE'){
				    
				    me.mayorista = true;
				    me.checked.MAYORISTA = false;
				    return;

				}else{

				    me.mayorista = false;

	      			if (cliente === 'MAYORISTA' || cliente === 'FUNCIONARIO') {

	      				me.checked.MAYORISTA = true;

	      				if(me.autorizacion.HABILITAR === 1){

							me.revisarAutorizacion();
	      				}
	      			}else{

      					me.checked.MAYORISTA = false;
	      			}
				}

      			// ------------------------------------------------------------------------

      		},
      		dataCliente(data){

      			// ------------------------------------------------------------------------

      			// RETENTOR 

      			if (data.retentor === true) {

      				data.retentor = 1;
      			} else if (data.retentor === false) {
      				data.retentor = 0;
      			}

      			// ------------------------------------------------------------------------

      			this.cliente.RETENTOR = data.retentor;
      			this.calculoRetencion(this.venta.IMPUESTO);

      			// ------------------------------------------------------------------------

      		},
      		nombreVendedor(vendedor){

      			// ------------------------------------------------------------------------

      			this.vendedor.NOMBRE = vendedor;

      			// ------------------------------------------------------------------------

      		},
      		codigoVendedor(vendedor){

      			// ------------------------------------------------------------------------
      			
      			this.vendedor.CODIGO = vendedor;

      			// ------------------------------------------------------------------------

      		},
/*		  	async ExistenProductos (tableVenta,codigo,tipo){
	      			var productoExistentes = [];
	      			productoExistentes =  await Common.existenProductosDataTableCommon(tableVenta, codigo, tipo);
	      			return productoExistentes;
      		},*/
           async cargarProductos(codigo) {
        	
	            // ------------------------------------------------------------------------

	            // INICIAR VARIABLES

	            let me = this;
	            var tableVenta = $('#tablaVenta').DataTable();
	        	var productoExistente = [];
	        	var cantidadExistente = 0;


				productoExistente =  await Common.existenProductosDataTableCommon(tableVenta, codigo, 2);
  				
  				
  				 if (productoExistente.respuesta == true) {
  				 	
  				 		cantidadExistente = parseFloat(productoExistente.cantidad);
  				 }
	            // ------------------------------------------------------------------------

	            // CONTROLAR SI HAY DATOS EN EL TEXTBOX Y RETORNAR SI NO EXISTE DATO

	            if (codigo.length <= 0) {
	                me.inivarAgregarProducto();
	                return;
	            }	
		        
		        // ------------------------------------------------------------------------

		        // REVISAR SI ES PARA DESCUENTO O CANTIDAD 

		        if (codigo.substring(0, 1) === "+" && me.caja.CANTIDAD_PERSONALIZADA === 1) {

		        	me.producto.CANTIDAD = codigo.substring(1, 20);
		        	// ------------------------------------------------------------------------
		        	//SI LA CANTIDAD ES 0 ENTONCES PONER DE VUELTA 1 PARA EVITAR PROBLEMAS EN EJECUCION POSTERIORES.
		        	if(me.producto.CANTIDAD==='0'){
		        	
		        		me.producto.CANTIDAD=1;
		   
		        	}
		        	// ------------------------------------------------------------------------

		        	me.producto.COD_PROD = '';
		        	return;

		        } else if (codigo.substring(0, 1) === "*") {
		        	

		        	me.producto.DESCUENTO = codigo.substring(1, 20);

		        	// ------------------------------------------------------------------------

		        	// SI EL DESCUENTO SUPERA EL 100 O ESTA POR DEBAJO

		        	if (me.producto.DESCUENTO > 100 || me.producto.DESCUENTO < 0) {
		        		me.$bvToast.show('toast-descuento-error');
		        		me.producto.DESCUENTO = 0;
		        		return;
		        	}


		        	if(me.autorizacion.HABILITAR === 1){

			        	// ------------------------------------------------------------------------
			        	
			        	// REVISAR AUTORIZACION

			        	me.revisarAutorizacion();

			        	// ------------------------------------------------------------------------

		        	}else{

		        		me.producto.COD_PROD = '';
		        	}

		        	me.producto.TIPO_DESCUENTO = 3;
		        	return;
		        }

	            // ------------------------------------------------------------------------

	            // SERVICIO 

	            if (codigo.substring(0, 1) === "/") {

	            	// ------------------------------------------------------------------------

	            	// OBTENER SERVICIO 

	            	Common.obtenerServicioPOSCommon(codigo).then(data => {

	            		if (data.response === true) {

	            			if(data.servicio.MANUAL_PRECIO === 1) {
	            				me.$refs.precio_venta.focus();
	            			}
	            				
	            			if(data.servicio.MANUAL_DESCRIPCION === 1) {
	            				me.deshabilitar.DESCRIPCION = false;
	            			}

	            			// ------------------------------------------------------------------------

	            			// AGREGAR SERVICIO

	            			me.servicio.COD_PROD = data.servicio.CODIGO;
			            	me.servicio.DESCRIPCION = data.servicio.DESCRIPCION;
			            	me.servicio.LOTE = 0;
			            	me.servicio.STOCK = 1;
			            	me.servicio.IVA = data.servicio.IVA;
	            			
	            			// ------------------------------------------------------------------------

	            		}
	            		
	            	});

	            	// ------------------------------------------------------------------------

	            	return;
	            }

	            // ------------------------------------------------------------------------

	            // CONSULTAR PRODUCTO CON LOTE 


  			


	            Common.obtenerProductoPOSCommon(codigo, me.moneda.CODIGO,parseFloat(me.producto.CANTIDAD),cantidadExistente).then(data => {

	            	if (data.response  === true) {

		            	// ------------------------------------------------------------------------

		            	// REVISAR SI EL STOCK ES CERO 

		            	if (data.producto.STOCK === 0) {
		            		me.$bvToast.show('toast-stock-cero');
		            		return;
		            	} else if (me.producto.CANTIDAD > data.producto.STOCK) {
		            		me.$bvToast.show('toast-cantidad-superada');
		            		return;
		            	}

		            	// ------------------------------------------------------------------------

		            	// AGREGAR PRODUCTO 

		            	me.producto.COD_PROD = data.producto.CODIGO;
		            	me.producto.DESCRIPCION = data.producto.DESCRIPCION;
		            	me.producto.PREC_VENTA = data.producto.PREC_VENTA;
		            	me.producto.PREC_AUX = data.producto.PREC_VENTA;
		            	me.producto.PREMAYORISTA = data.producto.PREMAYORISTA;
		            	me.producto.LOTE = data.producto.LOTE;
		            	me.producto.STOCK = data.producto.STOCK;
		            	me.producto.IVA = data.producto.IMPUESTO;
		            	me.producto.CODIGO_REAL = data.producto.CODIGO_REAL;
		            	me.producto.DESCUENTO_CATEGORIA = data.producto.DESCUENTO_CATEGORIA;
		            	me.producto.TIPO_DESCUENTO = 0;

						// ------------------------------------------------------------------------

						// IMAGEN DEL PRODUCTO 

						me.imagen.RUTA = data.imagen;

						// ------------------------------------------------------------------------

						// DESCUENTO POR MARCA O CATEGORIA O LOTE

						if (data.descuento_marca !== false && me.checked.DESCUENTO === true) {
							me.producto.DESCUENTO = data.descuento_marca.DESCUENTO;
							me.producto.TIPO_DESCUENTO = 5;
						} else if (data.descuento_categoria > 0 && me.producto.DESCUENTO === 0 && me.checked.DESCUENTO === true) {
							me.producto.DESCUENTO = data.descuento_categoria;
							me.producto.TIPO_DESCUENTO = 6;
						}
						//SI EL DESCUENTO POR LOTE NO ES FALSO COMIENZA A INSERTAR EN EL DATATABLE POR LOTE
						if(data.descuento_lote!==false){
							//AUXILIAR DESCUENTO_LOTE_VALIDAR PARA UTILIZAR EN AGREGARPRODUCTO
							me.descuento_lote_validar=true;
							//PONEMOS EN FALSE EL MAYORISTA AUTOMATICO PARA QUE RESPETE EL DESCUENTO POR LOTE.
							me.checked.MAYORISTA_AUT=false;
							//RECORRIDO DE LOS LOTES CON SU DESCUENTO Y SU PORCENTAJE
							me.descuento_lote=data.descuento_lote.datos;
							me.cantidad_restante=me.producto.CANTIDAD;
								me.descuento_lote.map(function(x){
								me.producto.DESCUENTO=x.DESCUENTO;
								me.producto.CANTIDAD=x.CANTIDAD;
								me.cantidad_restante=me.cantidad_restante-x.CANTIDAD;
								me.producto.TIPO_DESCUENTO = 7;
								me.agregarProducto();
							});
								
								
								//SI SOLO ALGUNOS LOTES TIENEN CANTIDAD PERO SOBRO LOS QUE NO, SEPARAR EN EL DATATABLE.
								me.descuento_lote_validar=false;
							    me.checked.MAYORISTA_AUT=true;
								if(me.cantidad_restante>0){
									me.producto.DESCUENTO=0;
									me.producto.CANTIDAD=me.cantidad_restante;
									me.producto.TIPO_DESCUENTO = 0;
									me.agregarProducto();
								}

								//UNA VEZ QUE TERMINE DE RECORRER TODOS LOS LOTES CON CANTIDADES RESTANTES Y LOS QUE NO TIENEN DESCUENTO (SI NO TIENEN DESCUENTO) SE COMIENZA LA LIMPIEZA DE LAS VARIABLES
								me.inivarAgregarProducto();
								this.producto.COD_PROD = '';
		        				this.$refs.compontente_codigo_producto.vaciarDevolver();

						}else{
							me.descuento_lote_validar=data.descuento_lote;
							me.descuento_lote=[];
							me.agregarProducto();
						}

						// ------------------------------------------------------------------------

						// AGREGAR PRODUCTO 

						

						// ------------------------------------------------------------------------

					} else {

						// ------------------------------------------------------------------------

						// NO EXISTE PRODUCTO 

						me.$bvToast.show('toast-no-existe');

						// ------------------------------------------------------------------------

					}

	            })       

	    		// ------------------------------------------------------------------------
	    		

	        },
	        agregarProducto(){

	            // ------------------------------------------------------------------------

	            // INICIAR VARIABLES

	            let me = this;
	            var iva = 0, tipo = 1;
	            var tableVenta = $("#tablaVenta").DataTable();
	            var rowClass = "";

	            // ------------------------------------------------------------------------

	            // CONTROLADOR

	            if (me.producto.COD_PROD.length === 0) {
	                me.validar.COD_PROD = true;
	                return;
	            } else {
	                me.validar.COD_PROD = false;
	            }

	            if (me.producto.PREC_VENTA.length === 0) {
	                me.validar.PRECIO_UNITARIO = true;
	                return;
	            } else {
	                me.validar.PRECIO_UNITARIO = false;
	            }

	            if (me.producto.CANTIDAD.length === 0  || me.producto.CANTIDAD === '0') {
	                me.validar.CANTIDAD = true;
	                return;
	            } else {
	                me.validar.CANTIDAD = false;
	            }

	            // ------------------------------------------------------------------------

	            // SWITCH MAYORISTA

	            if (me.checked.MAYORISTA === false && me.producto.DESCUENTO < 50) {

		            // ------------------------------------------------------------------------

		            // SI LA CANTIDAD SUPERA LA CANTIDAD DE PRECIO MAYORISTA O LO IGUALA 

		            if ((Common.quitarComaCommon(me.producto.CANTIDAD) >= parseInt(me.ajustes.LIMITE_MAYORISTA)) && me.checked.MAYORISTA_AUT === true) {
		            	me.producto.PREC_VENTA = me.producto.PREMAYORISTA;
		            	me.producto.DESCUENTO = 0;
		            	rowClass = "table-secondary";
		            }

		            // ------------------------------------------------------------------------

		            // PRECIO MAYORISTA CODIGO REAL

		            if (Common.mayoristaCommon(me.producto.CODIGO_REAL,tableVenta, parseInt(me.ajustes.LIMITE_MAYORISTA), me.producto.PREMAYORISTA, me.producto.CANTIDAD, me.moneda.DECIMAL, 'table-secondary') === true && me.checked.MAYORISTA_AUT === true) {
		            	me.producto.PREC_VENTA = me.producto.PREMAYORISTA;
		            	me.producto.DESCUENTO = 0;
		            	rowClass = "table-secondary";
		            } 

		            // ------------------------------------------------------------------------

	            } else if (me.checked.MAYORISTA === true && me.producto.PREMAYORISTA !== 0 && me.producto.PREMAYORISTA !== 0.00 && me.producto.DESCUENTO < 50){

	            	// ------------------------------------------------------------------------

	            	
	            	me.producto.PREC_VENTA = me.producto.PREMAYORISTA;
	            	rowClass = "table-secondary";
	            	if (me.producto.DESCUENTO < 100) {
	            		me.producto.DESCUENTO = 0;
	            	}
	            	

	            	// ------------------------------------------------------------------------
	            }

	            // ------------------------------------------------------------------------

	            if (me.producto.DESCUENTO > 0 && me.producto.DESCUENTO < 100) {
	            	rowClass = "table-warning";
	            } else if (me.producto.DESCUENTO == 100) {
	            	rowClass = "table-danger";
	            }
	            
	            // ------------------------------------------------------------------------

	            //  QUITAR COMA DE PRECIO

	            me.producto.PRECIO_TOTAL = Common.multiplicarCommon(me.producto.CANTIDAD, me.producto.PREC_VENTA, me.moneda.DECIMAL);
	         
	            // ------------------------------------------------------------------------

	            //  DAR FORMATO AL RESULTADO FINAL PARA MOSTRAR EN DATATABLE 

	            me.producto.PRECIO_TOTAL = Common.darFormatoCommon(me.producto.PRECIO_TOTAL, me.moneda.DECIMAL);

	            // ------------------------------------------------------------------------

	            // DESCUENTO UNITARIO 

	            me.producto.DESCUENTO_UNITARIO = Common.descuentoCommon(me.producto.DESCUENTO, me.producto.PREC_VENTA, me.moneda.DECIMAL);

	            // ------------------------------------------------------------------------

	            // RESTAR DESCUENTO DE PRECIO UNITARIO 

	            me.producto.PREC_VENTA = Common.restarCommon(me.producto.PREC_VENTA, me.producto.DESCUENTO_UNITARIO, me.moneda.DECIMAL);

	            // ------------------------------------------------------------------------

	            // CALCULAR DESCUENTO

	            me.producto.DESCUENTO_MONTO = Common.descuentoCommon(me.producto.DESCUENTO, me.producto.PRECIO_TOTAL, me.moneda.DECIMAL);

	            // ------------------------------------------------------------------------

	            // RESTAR DESCUENTO 

	            me.producto.PRECIO_TOTAL = Common.restarCommon(me.producto.PRECIO_TOTAL, me.producto.DESCUENTO_MONTO, me.moneda.DECIMAL);

	            // ------------------------------------------------------------------------

	            // CALCULAR IVA

            	me.producto.IMPUESTO = Common.calcularIVACommon(me.producto.PRECIO_TOTAL, me.producto.IVA, me.moneda.DECIMAL);
            	
            	// ------------------------------------------------------------------------

	            // CARGAR DATO EN TABLA VENTAS

	            me.agregarFilaTabla(me.producto.COD_PROD, me.producto.DESCRIPCION, me.producto.LOTE, me.producto.DESCUENTO, me.producto.CANTIDAD, me.producto.IMPUESTO, me.producto.PREC_VENTA, me.producto.PRECIO_TOTAL, me.producto.IVA, me.producto.DESCUENTO_MONTO, me.producto.CODIGO_REAL, me.producto.PREMAYORISTA, me.producto.DESCUENTO_UNITARIO, tipo, rowClass);

	            // ------------------------------------------------------------------------

	            // VACIAR TEXTBOX AGREGAR PRODUCTO SI NO EXISTE DESCUENTO_POR_LOTE
	            if(!me.descuento_lote_validar){
	            	  me.inivarAgregarProducto();

	            // ------------------------------------------------------------------------

	            // VERIFICAR SI LA TABLA YA TIENE PRODUCTOS PARA DESHABILITAR MONEDA 

	            if (tableVenta.rows().data().length > 0) {
	    			me.moneda.DESHABILITAR = true;
	    		}

	    		// ------------------------------------------------------------------------

		        // LLAMAR AL METODO HIJO 

		        this.producto.COD_PROD = '';
		        this.$refs.compontente_codigo_producto.vaciarDevolver();

		        // ------------------------------------------------------------------------
	            }

	          

	        }, agregarFilaTabla(codigo, descripcion, lote, descuento, cantidad, impuesto, precio, precio_total, iva, descuento_total, codigo_real, premayorista, descuento_unitario, tipo, rowClass){

	        	// ------------------------------------------------------------------------
	        	
	        	//	INICIAR VARIABLES 

	        	let me = this;
	        	var tableVenta = $('#tablaVenta').DataTable();
	        	var productoExistente = [];
	        	var cantidadNueva = 0;

	        	// ------------------------------------------------------------------------

	            // REVISAR SI EXISTE VALORES REPETIDOS EN TABLA TRANSFERENCIAS 
	            // LA OPCION 1 ES PARA DEVOLVER SOLO TRUE O FALSE SI EXISTE O NO
	            // LA OPCION 2 ES PARA DEVOLVER MAS DATOS DEL PRODUCTO 
	            // LA OPCION 3 ES PARA DEVOLVER MAS DATOS DEL PRODUCTO COMPARANDO SU PORCENTAJE Y SOLO FUNCIONARA SI EL DESCUENTO_LOTE_VALIDAR ES TRUE
	            if(me.descuento_lote_validar){
	            	productoExistente = Common.existeProductoConDescuentoDataTableCommon(tableVenta, codigo, 3,descuento);
	            }else{
	            	 productoExistente = Common.existeProductoConDescuentoDataTableCommon(tableVenta, codigo, 2,0);
	            }

	           
	           	
	            if (productoExistente.respuesta == true && tipo !== 2) {

	            	// ------------------------------------------------------------------------

	            	// SUMAR LA CANTIDAD E IVA

	            	cantidadNueva = parseFloat(productoExistente.cantidad) + parseFloat(cantidad);

	            	// ------------------------------------------------------------------------

	            	// PRECIO MAYORISTA EN EDITAR CANTIDAD 

		            if (me.checked.MAYORISTA === false && descuento < 50 && me.checked.MAYORISTA_AUT === true) {

			            // SI LA CANTIDAD SUPERA LA CANTIDAD DE PRECIO MAYORISTA O LO IGUALA 

			            if ((cantidadNueva >= parseInt(me.ajustes.LIMITE_MAYORISTA))) {
			            	precio = premayorista;
			            	descuento = 0;
			            	rowClass = "table-secondary";
			            }

		            }

		            // ------------------------------------------------------------------------

	            	// REVISAR SI LA NUEVA CANTIDAD SUPERA AL STOCK

	            	if (cantidadNueva > me.producto.STOCK) {
	            		me.$bvToast.show('toast-cantidad-superada');
		            	return;
	            	}

	            	// ------------------------------------------------------------------------

	            	// EDITAR CANTIDAD PRODUCTO 
	            	
	            	me.editarCantidadProducto(tableVenta, cantidadNueva, impuesto, precio, productoExistente.row, descuento, descuento_total, descuento_unitario, rowClass);
	            	return;

	            	// ------------------------------------------------------------------------

	            } else if (productoExistente.respuesta == true){
	            	me.validarCodigoProducto = false;
	            	me.$bvToast.show('toast-servicio-repetido');
	            	return;
	            }

	            // ------------------------------------------------------------------------

	            // SUMAR FILAS TABLA 

	            me.tabla.ITEM = me.tabla.ITEM + 1;

	            // ------------------------------------------------------------------------

	        	// AGREGAR FILAS 

	        	tableVenta.rows.add( [ {
	        					"ID": 0,
			                    "ITEM": me.tabla.ITEM,
			                    "CODIGO":   codigo,
			                    "DESCRIPCION":     descripcion,
			                    "LOTE": lote,
			                    "DESCUENTO": descuento,
			                    "CANTIDAD": cantidad,
			                    "IMPUESTO": impuesto,
			                    "PRECIO":    precio,
			                    "PRECIO_TOTAL":    precio_total,
			                    "ACCION":    "&emsp;<a role='button' id='mostrarProductoFila' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a> &emsp;<a role='button' id='editarProducto' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>&emsp;<a role='button'  title='Eliminar'><i id='eliminarProducto' class='fa fa-trash text-danger' aria-hidden='true'></i></a>",
			                    "IVA": iva,
			                    "DESCUENTO_TOTAL": descuento_total,
			                    "CODIGO_REAL": codigo_real,
			                    "PREMAYORISTA": premayorista,
			                    "DESCUENTO_UNITARIO": descuento_unitario,
			                    "TIPO": tipo,
			                    "TIPO_DESCUENTO":me.producto.TIPO_DESCUENTO
			                } ] )
			     .draw()
			     .nodes()
    			 .to$()
			     .addClass(rowClass);

			    // ------------------------------------------------------------------------ 

	            // AGREGAR INDEX A LA TABLA TRANSFERNCIAS

	            // tableVenta.on( 'order.dt search.dt', function () {
	            //     tableVenta.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	            //         cell.innerHTML = i+1;
	            //     } );
	            // } ).draw();

	            // ------------------------------------------------------------------------

	            this.gravadas();

	            // ------------------------------------------------------------------------

	            // AJUSTAR COLUMNAS DE ACUERDO AL DATO QUE CONTIENEN

	            tableVenta.columns.adjust().draw();

	            // ------------------------------------------------------------------------

	        }, editarCantidadProducto(tabla, cantidad, impuesto, precio, row, descuento, descuento_total, descuento_unitario, rowClass){
	        	
	        	// ------------------------------------------------------------------------

	        	// INICIAR VARIABLES

	        	let me = this;
	        	var precio_total = 0;
	        	var iva = 0;

	        	// ------------------------------------------------------------------------

	            // PROHIBIR EDITADO SI CANTIDAD O PRECIO ES CERO

	            if (cantidad === '0') {
	                me.$bvToast.show('toast-editar-cero');
	                return;	
	            }

	            // ------------------------------------------------------------------------

	            // CARGAR DESCUENTO

	            tabla.cell(row, 4).data(descuento).draw();

	            // ------------------------------------------------------------------------

	            // CARGAR CANTIDAD

	            tabla.cell(row, 6).data(cantidad).draw();

	            // ------------------------------------------------------------------------
	            
	            // CARGAR IMPUESTO

	            tabla.cell(row, 7).data(impuesto).draw();

	            // ------------------------------------------------------------------------

	            // CARGAR PRECIO 

	            tabla.cell(row, 8).data(precio).draw();

	            // ------------------------------------------------------------------------

	            // DESCUENTO UNITARIO

	            tabla.cell(row, 14).data(descuento_unitario).draw();

	            // ------------------------------------------------------------------------

	            // CALCULAR PRECIO TOTAL

	            precio_total = Common.multiplicarCommon(cantidad, precio, me.moneda.DECIMAL);

			    // ------------------------------------------------------------------------

			    // CALCULAR DESCUENTO TOTAL

	            descuento_total = Common.descuentoCommon(descuento, (Common.quitarComaCommon(me.producto.PREC_AUX) * cantidad), me.moneda.DECIMAL);
	            
	            tabla.cell(row, 5).data(descuento_total).draw();

	            // ------------------------------------------------------------------------

			    // CARGAR PRECIO CALCULADO 

	            tabla.cell(row, 9).data(precio_total).draw();

	            // ------------------------------------------------------------------------

	            // EDITAR FILA COLOR 

	            tabla.row(row).draw()
	            .nodes()
    			.to$()
    			.removeClass('table-secondary')
    			.removeClass('table-info')
    			.removeClass('table-primary')
    			.removeClass('table-warning')
    			.removeClass('table-danger')
    			.addClass(rowClass);

	            // ------------------------------------------------------------------------

	            // LLAMAR TOAST MODIFICADO

	            me.$bvToast.show('toast-producto-modificado');

	            // ------------------------------------------------------------------------

	        }, inivarAgregarProducto(){

				// ------------------------------------------------------------------------

				// VACIAR TEXTBOX 

				this.producto = {
					COD_PROD: '',
					DESCRIPCION: '',
					LOTE: '',
					PREC_VENTA: '',
					CANTIDAD: '',
					PRECIO_TOTAL: '',
					PREC_VENTA: '',
					CANTIDAD: 1,
					DESCUENTO: 0
				}

	            // ------------------------------------------------------------------------   

			}, 
			numeracion() {

				let me = this;


				Common.numeracionVentaCommon(me.caja.CODIGO).then(data => {
					me.venta.CODIGO = data.CODIGO;
					me.venta.CODIGO_CAJA = data.CODIGO_CAJA;
				}).catch(error => {
					Swal.showValidationMessage(
					  `Request failed: ${error}`
					)
				});

			}, 
			inicio() {

				// ------------------------------------------------------------------------ 

				let me = this;
				//this.$refs.compontente_codigo_producto.$el.focus();
				//this.$refs.compontente_codigo_producto.focus();
				// ------------------------------------------------------------------------ 

				// OBTENER DATOS DE INICIO PARA VENTA

				Common.inicioVentaCommon().then(data => {

					// ------------------------------------------------------------------------ 

					me.cliente.NOMBRE = data.CLIENTE.NOMBRE;
					me.cliente.CODIGO = data.CLIENTE.CODIGO;
					me.vendedor.CODIGO = data.EMPLEADO.CODIGO;
					me.vendedor.NOMBRE = data.EMPLEADO.NOMBRE;
					me.moneda.DECIMAL = data.MONEDA.CANDEC;
					me.moneda.CODIGO = data.MONEDA.CODIGO;
					me.moneda.DESCRIPCION = data.MONEDA.DESCRIPCION;
					me.ajustes.LIMITE_MAYORISTA = data.LIMITE_MAYORISTA;
					me.ajustes.IMPRESORA_TICKET = data.IMPRESORA_TICKET;
					me.ajustes.IMPRESORA_MATRICIAL = data.IMPRESORA_MATRICIAL;
					me.ajustes.LOGO = data.LOGO;
					me.autorizacion.HABILITAR = data.SUPERVISOR;
					me.movimiento.MONEDA = data.MONEDA.CODIGO.toString();
					me.movimiento.DECIMAL = data.MONEDA.CANDEC.toString();

					// ------------------------------------------------------------------------ 

				}).catch(error => {
					Swal.showValidationMessage(
					  `Request failed: ${error}`
					)
				});

				// ------------------------------------------------------------------------ 

				// COTIZACION DIA 

				Common.obtenerCotizacionDia().then(data => {
					me.cotizacion.GUARANIES = Common.darFormatoCommon(data.Guaranies, 0);
					me.cotizacion.DOLARES = Common.darFormatoCommon(data.Dolares, 2);
					me.cotizacion.REALES = Common.darFormatoCommon(data.Reales, 2);
					me.cotizacion.PESOS = Common.darFormatoCommon(data.Pesos, 2);
				});

				// ------------------------------------------------------------------------ 

			},
			test() {

				// qz.printers.find("EPSON TM-U220 Receipt").then(function(found) {
				//    alert("Printer: " + found);
				// });

				qz.websocket.connect().then(function() { 
				   return qz.printers.find("TICKET");              // Pass the printer name into the next Promise
				}).then(function(printer) {
				   var config = qz.configs.create(printer);       // Create a default config for the found printer
				   var data = ['^XA^FO50,50^ADN,36,20^FDRAW ZPL EXAMPLE^FS^XZ'];   // Raw ZPL
				   return qz.print(config, data);
				}).catch(function(e) { console.error(e); });

			},
			factura(numero, caja) {

				// ------------------------------------------------------------------------ 

				let me = this;

				// ------------------------------------------------------------------------ 

				Common.generarPdfFacturaVentaCommon(numero, caja).then(response => {

						var reader = new FileReader();
						 reader.readAsDataURL(new Blob([response])); 
						reader.onloadend = function() {
						     var base64data = reader.result;
						     base64data = base64data.replace("data:application/octet-stream;base64,", "");
						     me.imprimir_factura(base64data);
						 }

				});

				// ------------------------------------------------------------------------ 

				// Common.generarPdfTicketVentaTestCommon();

			},
			ticket(numero, caja) {

				// ------------------------------------------------------------------------ 

				let me = this;

				// ------------------------------------------------------------------------ 

				Common.generarPdfTicketVentaCommon(numero, caja).then(response => {

						var reader = new FileReader();
						 reader.readAsDataURL(new Blob([response])); 
						reader.onloadend = function() {
						     var base64data = reader.result;
						     base64data = base64data.replace("data:application/octet-stream;base64,", "");
						     me.imprimir(base64data, numero, caja);
						 }

				});

				// ------------------------------------------------------------------------ 

				// Common.generarPdfTicketVentaTestCommon();

			}, imprimir(base64, numero, caja) {

				let me = this;

				qz.websocket.connect().then(function() { 
					   return qz.printers.find(me.ajustes.IMPRESORA_TICKET);              // Pass the printer name into the next Promise
					}).then(function(printer) {

						     var config = qz.configs.create(printer, { copies: me.caja.CANTIDAD_TICKET });
						var data = [{ 
						   type: 'pixel',
           					format: 'pdf',
           					flavor: 'base64',
						   data: base64
						}];

					   return qz.print(config, data).then(function() {

					   		// ------------------------------------------------------------------------ 

					   		// AVISAR QUE YA IMPRIMIO TICKET 

					   		me.impresion.TICKET = true;

					   		// ------------------------------------------------------------------------ 

					   		// CUANDO SE DESCONECTA TICKET ENVIAR IMPRESION FACTURA

					   		qz.websocket.disconnect();

					   		if (me.impresion.TIPO === "2" && me.checked.FACTURA === false) {
								me.factura(numero, caja);
							}

					   		// ------------------------------------------------------------------------ 

					   		// RECARGAR LA PAGINA 

					   		me.recargar();

					   		// ------------------------------------------------------------------------ 

					   });
						 
					   
					}).catch(function(e) { console.error(e); });

			}, imprimir_factura(base64) {

				let me = this;
				
				qz.websocket.connect().then(function() { 
					   return qz.printers.find(me.ajustes.IMPRESORA_MATRICIAL);              // Pass the printer name into the next Promise
					}).then(function(printer) {

						var config = qz.configs.create(printer);
						var data = [{ 
						   type: 'pixel',
           					format: 'pdf',
           					flavor: 'base64',
						   data: base64
						}];

					   return qz.print(config, data).then(function() {
					   		me.impresion.FACTURA = true;
					   		qz.websocket.disconnect();
					   		me.recargar();
					   });
						 
					   
					}).catch(function(e) { console.error(e); });

			}, devolucionLlamar() {

				// ------------------------------------------------------------------------

				// LLAMAR MODAL 

				if (this.checked.DEVOLUCION === true) {
					this.$refs.componente_textbox_Ventas.datatableMostrar(this.caja.CODIGO);
					$('.modal-devolucion').modal('show');
				}

            	// ------------------------------------------------------------------------

			}, codigo_devolucion(codigo) {

				// ------------------------------------------------------------------------

            	let me = this;

            	// ------------------------------------------------------------------------

            	// PREPARAR DATATABLE 
            	// alert(me.token);
            	// alert($('meta[name="csrf-token"]').attr('content'));
	 			this.tabla.tableProductosDevolucion = $('#devolucionProductos').DataTable({
	                 	"processing": true,
	                 	"serverSide": true,
	                 	"destroy": true,
	                 	"bAutoWidth": true,
	                 	"select": true,
	                 	"ajax":{
	                 			"data": {
	                 				"codigo": codigo,
	                 				"caja": me.caja.CODIGO
	                 			},
	                             "url": "venta/devolucion/productos",
	                             "type": "POST",
	                             'headers': {
						            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						          }
	                           },
	                    "columns": [
	                    		{ "data": "ID" },
	                            { "data": "ITEM" },
	                            { "data": "COD_PROD" },
	                            { "data": "DESCRIPCION" },
	                            { "data": "DESCUENTO" },
	                            { "data": "DESCUENTO_TOTAL" },
	                            { "data": "CANTIDAD" },
	                            { "data": "IVA_PORCENTAJE" },
	                            { "data": "IMPUESTO" },
	                            { "data": "PRECIO" },
	                            { "data": "TOTAL" }
	                        ]    
	                });
                    
	 			// ------------------------------------------------------------------------

			}, nota_credito() {
				
				// ------------------------------------------------------------------------

				// LLAMAR MODAL 

				this.$refs.detalle_nota_credito_cliente.mostrarModal(this.cliente.CODIGO);

				// ------------------------------------------------------------------------

			}, agencia_seleccionar() {
				
				// ------------------------------------------------------------------------

				// LLAMAR MODAL 

				this.$refs.lista_agencia.mostrarModal();
				this.agencia.CODIGO = '';
				this.agencia.NOMBRE = 'NINGUNO';

				// ------------------------------------------------------------------------

			}, dataNotaCreditoTextbox(data){

				// ------------------------------------------------------------------------
				
				// INICIAR VARIABLES 

				let me = this;
                var productoExistente = [];
                var tableVenta = $('#tablaVenta').DataTable();

                // *******************************************************************

                // OBTENER DATOS DEL PRODUCTO DATATABLE JS
                	
                	productoExistente = Common.existeProductoDataTableCommon(tableVenta, data.id+'-NC', 2);
	           	
	            	if (productoExistente.respuesta == false) {

	                	me.tabla.ITEM = me.tabla.ITEM + 1;
	                	
	                	tableVenta.rows.add( [ {
	                				"ID": data.id,
				                    "ITEM": me.tabla.ITEM,
				                    "CODIGO":  data.id+'-NC',
				                    "DESCRIPCION":  'NOTA CREDITO',
				                    "LOTE": 0,
				                    "DESCUENTO": 0,
				                    "DESCUENTO_TOTAL": 0,
				                    "CANTIDAD": 1,
				                    "IMPUESTO": data.iva * -1,
				                    "PRECIO": data.total * -1,
				                    "PRECIO_TOTAL": data.total * -1,
				                    "ACCION":    "&emsp;<a role='button'  title='Eliminar'><i id='eliminarProducto' class='fa fa-trash text-danger' aria-hidden='true'></i></a>",
				                    "IVA": data.iva * -1,
				                    "CODIGO_REAL": 0,
				                    "PREMAYORISTA": 0,
				                    "DESCUENTO_UNITARIO": 0,
				                    "TIPO": 4,
				                    "TIPO_DESCUENTO":0
				                } ] )
					     .draw()
					     .nodes()
		    			 .to$()
					     .addClass('table-info');

				    }

				// ------------------------------------------------------------------------

			}, calculoRetencion(total){

				// ------------------------------------------------------------------------

				// CALCULAR EL 30 % DE LA RETENCION \

				if (this.cliente.RETENTOR === 1) {
					this.venta.RETENCION = Common.multiplicarCommon(0.3, total, this.moneda.DECIMAL);
				}

				// ------------------------------------------------------------------------

			}, dataAgenciaTextbox(agencia){

				// ------------------------------------------------------------------------

				this.agencia.CODIGO = agencia.id;
				this.agencia.NOMBRE = agencia.nombre;

				// ------------------------------------------------------------------------

			}, revisarAutorizacion(){

				// ------------------------------------------------------------------------

				// LLAMAR MODAL 

				this.$refs.autorizacion_componente.mostrarModal();
				this.$refs.autorizacion_componente.enfocar();

				// ------------------------------------------------------------------------

			}, autorizacionData(data){

				// ------------------------------------------------------------------------

				// LLAMAR MODAL
				
				if (data.response === true) {

		        	this.producto.COD_PROD = '';
					this.autorizacion.ID_USUARIO = data.usuario;
					this.autorizacion.ID_USER_SUPERVISOR = data.id_user_supervisor;

					if(this.permiso === true){

						this.autorizacion.PERMITIDO = 1;
						this.formaPago(this.datos);
					}
				}

				// ------------------------------------------------------------------------

			}, movimiento_caja(){

			   // ------------------------------------------------------------------------
			   	
				$('.modal-mov-caja').modal('show');

			   // ------------------------------------------------------------------------	
			}, cantidadDecimal(decimal){

				// ------------------------------------------------------------------------	

				this.movimiento.DECIMAL = decimal;

				// ------------------------------------------------------------------------	

			}, descripcionMoneda(moneda){

				// ------------------------------------------------------------------------	

				this.movimiento.MONEDA_DESCRIPCION= moneda;

				// ------------------------------------------------------------------------	

			}, formatoImporte() {

	        	// ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;

	            // ------------------------------------------------------------------------

	            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A IMPORTE

	            me.movimiento.IMPORTE = Common.darFormatoCommon(me.movimiento.IMPORTE, me.movimiento.DECIMAL);

	            // ------------------------------------------------------------------------

	        }, 
		    controlador_movimiento() {

		            // ------------------------------------------------------------------------

		            let me = this;
		            var falta = false;

		            // ------------------------------------------------------------------------
		            
		            // CONTROLADOR

		            if (me.movimiento.TIPO === null) {
		                me.validar.MOVIMIENTO.TIPO = true;
		                falta = true;
		            } else {
		                me.validar.MOVIMIENTO.TIPO = false;
		            }

		            if (me.movimiento.MEDIO === null) {
		                me.validar.MOVIMIENTO.MEDIO = true;
		                falta = true;
		            } else {
		                me.validar.MOVIMIENTO.MEDIO = false;
		            }

		            if (me.movimiento.IMPORTE.length === 0 || me.movimiento.IMPORTE === '0' || me.movimiento.IMPORTE === '0.00' || me.movimiento.IMPORTE === 0 || me.movimiento.IMPORTE === 0.00 ) {
		                me.validar.MOVIMIENTO.IMPORTE = true;
		                falta = true;
		            } else {
		                me.validar.MOVIMIENTO.IMPORTE = false;
		            }

		            if (me.movimiento.COMENTARIO === '') {
		                me.validar.MOVIMIENTO.COMENTARIO = true;
		                falta = true;
		            } else {
		                me.validar.MOVIMIENTO.COMENTARIO = false;
		            }

		            // ------------------------------------------------------------------------

		            // RETORNAR FALTA - SI ES TRUE SE DETIENE EL GUARDADO 
		            // SI ES FALSE CONTINUA LA OPERACION 

		            return falta;

		            // ------------------------------------------------------------------------

		    }, limpiar_movimiento_caja(){

		    	this.movimiento.TIPO = null;

	         	this.movimiento.MEDIO = null;

	         	this.movimiento.IMPORTE = 0;

	         	this.movimiento.COMENTARIO = '';

		    },guardar_movimiento_caja(){

		    	// ------------------------------------------------------------------------
	        	
	        	// INICIAR VARIABLES

	        	let me = this;

	        	// ------------------------------------------------------------------------

	        	// CONTROLADOR

	        	if (this.controlador_movimiento() === true) {
	        		this.$bvToast.show('toast-falta-completar-movimiento-caja');
	        		return;
	        	}

	        	// ------------------------------------------------------------------------

	        	// MOVIMIENTO CAJA 

	        	this.movimiento.CAJA = this.caja.CODIGO;
	        	this.movimiento.MONEDA_SISTEMA = this.moneda.CODIGO;
	        	
	        	// ------------------------------------------------------------------------

	        	Common.guardarMovimientoCajaCommon(this.movimiento).then(data => {

	        		if (data.response === true){

	        			Swal.fire(
									'Guardado !',
									'Se ha guardado el movimiento correctamente',
									'success'
								)

	        			this.limpiar_movimiento_caja();


	        		} else if (data.response === false) {

	        			Swal.showValidationMessage(
					          `Request failed: ${data.statusText}`
					        )

	        		}

	        	});

	        	// ------------------------------------------------------------------------

		    }, validarMayorista(){

		    	let me = this;

				if(me.checked.MAYORISTA === true && me.autorizacion.HABILITAR === 1){

					// REVISAR AUTORIZACION

	        		// ------------------------------------------------------------------------

					me.revisarAutorizacion();

	        		// ------------------------------------------------------------------------
				}

		    }, 
		    autorizarDescuento(){

		    	let me = this;

		    	// ------------------------------------------------------------------------

		        // SI EL DESCUENTO SUPERA EL 100 O ESTA POR DEBAJO

		        if (me.producto.DESCUENTO > 100 || me.producto.DESCUENTO < 0) {
		        	me.$bvToast.show('toast-descuento-error');
		        	me.producto.DESCUENTO = 0;
		        	return;
		        }

		    	if(me.autorizacion.HABILITAR === 1){

					// REVISAR AUTORIZACION

	        		// ------------------------------------------------------------------------

					me.revisarAutorizacion();

	        		// ------------------------------------------------------------------------
		    	}

		        me.producto.TIPO_DESCUENTO = 3;
		        me.producto.COD_PROD = '';
		        return;
		    }

      },
        mounted() {

        	// ------------------------------------------------------------------------

        	let me = this;

        	// ------------------------------------------------------------------------

        	// OBTENER CAJA 

        	me.obtenerCaja();

        	// ------------------------------------------------------------------------

        	me.inicio();

        	// ------------------------------------------------------------------------

        	// ATAJOS DE TECLADO 

        	Mousetrap.bind('f1', function() { me.nuevo(); });

/*			Mousetrap.bind('esc', function() { 

				if ($("#modalImpresion").data('bs.modal')) {
	              if (($("#modalImpresion").data('bs.modal'))._isShown){
	                me.$refs.compontente_medio_pago.enviar();
	              }
	            };

				if ($("#staticBackdrop").data('bs.modal')) {
	              if (($("#staticBackdrop").data('bs.modal'))._isShown){
	              	if ($("#modalImpresion").data('bs.modal') === undefined) {
	                    me.$refs.compontente_medio_pago.aceptar();
	                };
	                
	              }
	            }; 

				if ($("#staticBackdrop").data('bs.modal') === undefined) {
	              me.guardar();
	            }; 


			});*/

			Mousetrap.bind('shift+m', function() { me.movimiento_caja(); });
			Mousetrap.bind('shift+c', function() { me.$refs.componente_cliente_modal.procesarFormas(); });
			Mousetrap.bind('shift+r', function() { $('.registrar-cliente-modal').modal('show'); });
			Mousetrap.bind('shift+v', function() { $('.busqueda-vendedor-modal').modal('show'); });
			Mousetrap.bind('shift+p', function() { 
				$('.producto-modal').modal('show'); 
			});

			Mousetrap.bind('shift+n', function() { me.nota_credito(); });
				
        	// ------------------------------------------------------------------------

        	// FIJAR CURSOR

        	me.$refs.compontente_codigo_producto.vaciarDevolver();
        	
            	// ------------------------------------------------------------------------
                // >>
                // INICIAR EL DATATABLE TRANSFERENCIA 
                // ------------------------------------------------------------------------

                var tableVenta = $('#tablaVenta').DataTable({
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "responsive": false,
                        "dom": "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
						"<'row'<'col-sm-12'tr>>" +
						"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
				        "buttons": [
				            { extend: 'copy', text: '<i class="fa fa-copy"></i>', titleAttr: 'Copiar', className: 'btn btn-secondary' },
				        	{ extend: 'excelHtml5', text: '<i class="fa fa-file"></i>', titleAttr: 'Excel', className: 'btn btn-success' },
				            { extend: 'pdfHtml5', text: '<i class="fa fa-file"></i>', titleAttr: 'Pdf', className: 'btn btn-danger' }, 
				            { extend: 'print', text: '<i class="fa fa-print"></i>', titleAttr: 'Imprimir', className: 'btn btn-secondary', title: 'Transferencia', messageTop: 'Productos registrados para Transferencia' }
				        ], 
                        "columns": [
                            { "data": "ITEM" },
                            { "data": "CODIGO" },
                            { "data": "DESCRIPCION" },
                            { "data": "LOTE" },
                            { "data": "DESCUENTO" },
                            { "data": "DESCUENTO_TOTAL" },
                            { "data": "CANTIDAD" },
                            { "data": "IMPUESTO" },
                            { "data": "PRECIO" },
                            { "data": "PRECIO_TOTAL" },
                            { "data": "ACCION" },
                            { "data": "IVA" },
                            { "data": "CODIGO_REAL" },
                            { "data": "PREMAYORISTA" },
                            { "data": "DESCUENTO_UNITARIO" },
                            { "data": "TIPO" },
                            { "data": "ID" },
                            { "data": "TIPO_DESCUENTO"}
                        ],
                        "columnDefs": [
				            {
				                "targets": [ 11 ],
				                "visible": false,
				                "searchable": false
				            },
				            {
				                "targets": [ 12 ],
				                "visible": false,
				                "searchable": false
				            },
				            {
				                "targets": [ 13 ],
				                "visible": false,
				                "searchable": false
				            },
				            {
				                "targets": [ 14 ],
				                "visible": false,
				                "searchable": false
				            },
				            {
				                "targets": [ 15 ],
				                "visible": false,
				                "searchable": false
				            },
				            {
				                "targets": [ 16 ],
				                "visible": false,
				                "searchable": false
				            },
				            {
				                "targets": [ 17 ],
				                "visible": false,
				                "searchable": false
				            },

				        ],
				        "order": [[ 0, "desc" ]],
                        "footerCallback": function(row, data, start, end, display) {
						  var api = this.api();
						 
						  // *******************************************************************

						  // CANTIDAD 

						  api.columns('.cantidadColumna', {
						    
						  }).every(function() {
						    var cantidad = this
						      .data()
						      .reduce(function(a, b) {

						      	// *******************************************************************

						      	// QUITAR LAS COMAS DE LOS VALORES

						      	a = Common.quitarComaCommon(a);
						      	b = Common.quitarComaCommon(b);
						      	
						      	// *******************************************************************

						        var x = parseFloat(a) || 0;
						        var y = parseFloat(b) || 0;

						        // *******************************************************************

						        // SUMAR VALORES

						        return x + y;

						        // *******************************************************************

						      }, 0);

						      // *******************************************************************

						      // CARGAR EN EL FOOTER

						      $( api.columns('.cantidadColumna').footer() ).html(
					                Common.darFormatoCommon(cantidad, 0)
					           );

						      // *******************************************************************

						  });

						  // *******************************************************************

						  // CANTIDAD 

						  api.columns('.impuestoColumna', {
						    
						  }).every(function() {
						    var impuesto = this
						      .data()
						      .reduce(function(a, b) {

						      	// *******************************************************************

						      	// QUITAR LAS COMAS DE LOS VALORES

						      	a = Common.quitarComaCommon(a);
						      	b = Common.quitarComaCommon(b);
						      	
						      	// *******************************************************************

						        var x = parseFloat(a) || 0;
						        var y = parseFloat(b) || 0;

						        // *******************************************************************

						        // SUMAR VALORES

						        return x + y;

						        // *******************************************************************

						      }, 0);

						      // *******************************************************************

						      // CARGAR EN EL FOOTER

						      $( api.columns('.impuestoColumna').footer() ).html(
					                me.venta.IMPUESTO = Common.darFormatoCommon(impuesto, me.moneda.DECIMAL)
					           );

						      // *******************************************************************

						  });

						  // *******************************************************************

						  // PRECIO

						  api.columns('.precioColumna', {
						    
						  }).every(function() {
						    var precio = this
						      .data()
						      .reduce(function(a, b) {

						      	// *******************************************************************

						      	// QUITAR LAS COMAS DE LOS VALORES

						      	a = Common.quitarComaCommon(a);
						      	b = Common.quitarComaCommon(b);
						      	
						      	// *******************************************************************

						        var x = parseFloat(a) || 0;
						        var y = parseFloat(b) || 0;

						        // *******************************************************************

						        // SUMAR VALORES

						        return x + y;

						        // *******************************************************************

						      }, 0);

						      // *******************************************************************

						      // CARGAR EN EL FOOTER

						      $( api.columns('.precioColumna').footer() ).html(
					                Common.darFormatoCommon(precio, me.moneda.DECIMAL)
					           );

						      // *******************************************************************

						  });

						  // *******************************************************************

						  // PRECIO TOTAL

						  api.columns('.precioTotalColumna', {
						    
						  }).every(function() {
						    var precio = this
						      .data()
						      .reduce(function(a, b) {

						      	// *******************************************************************

						      	// QUITAR LAS COMAS DE LOS VALORES

						      	a = Common.quitarComaCommon(a);
						      	b = Common.quitarComaCommon(b);
						      	
						      	// *******************************************************************

						        var x = parseFloat(a) || 0;
						        var y = parseFloat(b) || 0;

						        // *******************************************************************

						        // SUMAR VALORES

						        return x + y;

						        // *******************************************************************

						      }, 0);

						      // *******************************************************************

						      // CARGAR EN EL FOOTER

						      $( api.columns('.precioTotalColumna').footer() ).html(
					                me.venta.TOTAL = Common.darFormatoCommon(precio, me.moneda.DECIMAL)
					           );

						      // *******************************************************************

						  });

						  
						  // *******************************************************************
						  
						}      
                });
                
                // ------------------------------------------------------------------------

                // AJUSTAR TAMAÑO DE FUENTE DE TABLA

    			$("#tablaVenta").css("font-size", 12);
				tableVenta.columns.adjust().draw();

				// ------------------------------------------------------------------------

				// MOSTRAR SWEET ELIMINAR

                $('#tablaVenta').on('click', 'tbody tr #eliminarProducto', function() {

                    // *******************************************************************

                    // ABRIR EL SWEET ALERT

                    Swal.fire({
					  title: 'Estas seguro ?',
					  text: "Eliminar el producto " + tableVenta.row($(this).parents('tr')).data().CODIGO + " !",
					  type: 'warning',
					  showLoaderOnConfirm: true,
					  showCancelButton: true,
					  confirmButtonColor: '#d33',
					  cancelButtonColor: '#3085d6',
					  confirmButtonText: 'Si, eliminalo!',
					  cancelButtonText: 'Cancelar'

					}).then((result) => {

					  if (result.value) {

					  	// *******************************************************************

	                    // ELIMINAR 
	                    
		                tableVenta.row($(this).parents('tr')).remove().draw(); 

	                    // *******************************************************************

					  	Swal.fire(
							      'Eliminado!',
							      'Se ha eliminado el producto de la tabla !',
							      'success'
						)

						// *******************************************************************

					  }
					})

                    // *******************************************************************

                });

                // ------------------------------------------------------------------------

                // MOSTRAR MODAL PRODUCTO

                $('#tablaVenta').on('click', 'tbody tr #mostrarProductoFila', function() {

                	// *******************************************************************

                	// OBTENER DATOS DEL PRODUCTO DATATABLE JS

                	me.codigo_detalle = tableVenta.row($(this).parents('tr')).data().CODIGO;
                	me.$refs.detalle_producto.mostrar();
                	// OBTENER IMAGEN - UTIL
                	// me.imagen = $(tableProductos.row($(this).parents('tr')).data().IMAGEN).attr('src');

                    // *******************************************************************

                });

                // ------------------------------------------------------------------------

                // CODIGO PARA CAMBIAR EL FOCUS CON ENTER EN INPUT
        	
	        	$(document).on('keypress', 'input', function(e) {
	        		
				  if(e.keyCode == 13) {
				           
				           me.cb = parseInt($(this).attr('tabindex'));
				    	   
				           if ($(':input[tabindex=\'' + (me.cb + 1) + '\']') != null) {

				               $(':input[tabindex=\'' + (me.cb + 1) + '\']').focus();
				               $(':input[tabindex=\'' + (me.cb + 1) + '\']').select();
				               e.preventDefault();
				    
				               return false;
				           }
				  }

				});

				// -------------------------------------------------------------------------------------

				// MOSTRAR MODAL PRODUCTO

                $('#devolucionProductos').on('click', 'tbody tr', function() {

                	// *******************************************************************

                	// INICIAR VARIABLES 

                	var productoExistente = [];

                	// *******************************************************************

                	// OBTENER DATOS DEL PRODUCTO DATATABLE JS
                	
                	productoExistente = Common.existeProductoDataTableCommon(tableVenta, me.tabla.tableProductosDevolucion.row(this).data().COD_PROD, 2);
	           	
	            	if (productoExistente.respuesta == false) {

	                	me.tabla.ITEM = me.tabla.ITEM + 1;
	                	
	                	tableVenta.rows.add( [ {
	                				"ID": me.tabla.tableProductosDevolucion.row(this).data().ID,
				                    "ITEM": me.tabla.ITEM,
				                    "CODIGO":  me.tabla.tableProductosDevolucion.row(this).data().COD_PROD,
				                    "DESCRIPCION":  'DEVOLUCION: '+me.tabla.tableProductosDevolucion.row(this).data().DESCRIPCION,
				                    "LOTE": 0,
				                    "DESCUENTO": me.tabla.tableProductosDevolucion.row(this).data().DESCUENTO,
				                    "DESCUENTO_TOTAL": me.tabla.tableProductosDevolucion.row(this).data().DESCUENTO_TOTAL,
				                    "CANTIDAD": me.tabla.tableProductosDevolucion.row(this).data().CANTIDAD,
				                    "IMPUESTO": Common.multiplicarCommon(me.tabla.tableProductosDevolucion.row(this).data().IMPUESTO, -1, me.moneda.DECIMAL),
				                    "PRECIO": Common.multiplicarCommon(me.tabla.tableProductosDevolucion.row(this).data().PRECIO, -1, me.moneda.DECIMAL),
				                    "PRECIO_TOTAL": Common.multiplicarCommon(me.tabla.tableProductosDevolucion.row(this).data().TOTAL, -1, me.moneda.DECIMAL),
				                    "ACCION":    "&emsp;<a role='button' id='mostrarProductoFila' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a> &emsp;<a role='button' id='editarProducto' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>&emsp;<a role='button'  title='Eliminar'><i id='eliminarProducto' class='fa fa-trash text-danger' aria-hidden='true'></i></a>",
				                    "IVA": me.tabla.tableProductosDevolucion.row(this).data().IVA_PORCENTAJE,
				                    "CODIGO_REAL": 0,
				                    "PREMAYORISTA": 0,
				                    "DESCUENTO_UNITARIO": 0,
				                    "TIPO": 3,
				                    "TIPO_DESCUENTO":0
				                } ] )
					     .draw()
					     .nodes()
		    			 .to$()
					     .addClass('table-info');

				    }

                    // *******************************************************************

                    // CERRAR MODAL 

                    $('.modal-devolucion').modal('hide');

                    // *******************************************************************

                });

                // ------------------------------------------------------------------------

                $('#tablaVenta').on( 'draw.dt', function () {
				    me.calculoRetencion(me.venta.IMPUESTO);
				} );

				// ------------------------------------------------------------------------

        }
    }
				
</script>