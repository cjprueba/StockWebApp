<template>
	<!-- ******************************************************************* -->

		<!-- MODAL DETALLE PRODUCTO -->
		<div class="container-fluid">
				<div class="modal fade modal-detalle" tabindex="-1" role="dialog"  aria-hidden="true">
				  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalCenterTitle"><small>Detalle</small></h5>
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				          <span aria-hidden="true">&times;</span>
				        </button>
				      </div>
				      <div class="modal-body">
				        	<div class="row">
			                    <div class="col-md-4">
			                        <div class="profile-img">
			                        	<span v-on:click="agrandarImagen" v-html="producto.IMAGEN"></span>
			                            <!-- <img id="myImg" :src="producto.IMAGEN" alt="" class="img-thumbnail"/> -->
			                        </div>
			                        <div class="row">
			                        	<div class="col-md-12">
					                        <div class="profile-work">
					                            <p>PRECIOS</p>
					                            <span class="detalle"><strong>Venta:</strong> <span class="float-right">{{producto.PREC_VENTA}} </span></span><br/>
					                            <span class="detalle"><strong>Mayorista:</strong> <span class="float-right">{{producto.PREMAYORISTA}}</span></span><br/>
					                            <span class="detalle"><strong>VIP:</strong> <span class="float-right">{{producto.PREVIP}} </span></span><br/>
					                            <span class="detalle"><strong>Costo:</strong> <span class="float-right">{{producto.PRECOSTO}} </span></span>
					                            <p>FECHAS</p>
					                            <span class="detalle"><strong>Creación:</strong> <span class="float-right">{{producto.FECALTAS}} </span></span><br/>
					                            <span class="detalle"><strong>Modificación:</strong> <span class="float-right">{{producto.FECMODIF}} </span></span><br/>
					                            <span class="detalle"><strong>Ult. Venta:</strong> <span class="float-right">{{producto.FECHULT_V}}</span></span><br/>
					                            <span class="detalle"><strong>Ult. Compra:</strong> <span class="float-right">{{producto.FECHULT_C}}</span></span>
					                            <p>STOCK</p>
					                            <span class="detalle"><strong>Minimo:</strong> <span class="float-right">{{producto.STOCK_MIN}} </span></span><br/>
					                            <span class="detalle"><strong>Máximo:</strong> <span class="float-right">2,000</span></span><br/>
					                            <table class="table-borderless detalle">
													<tr v-for="deposito in deposito" >
														<td align="left" width="100%"><strong>{{deposito.DESCRIPCION}}</strong></td>
														<td align="right">{{deposito.CANTIDAD}}</td>
													</tr>
												</table>
					                        </div>
			                    		</div>
			                        </div>	
			                    </div>
			                    <div class="col-md-8">
			                        <div class="profile-head">
			                                    <h5>
			                                        {{codigo}}
			                                    </h5>
			                                    <h6>
			                                        {{producto.DESCRIPCION}}
			                                    </h6>
			                                    <h6>
			                                       <span v-html="producto.BAJA"></span>
			                                    </h6>
			                                    <p class="proile-rating">STOCK : <span>{{producto.STOCK}}</span> 
			                                    </p>
			                            <ul class="nav nav-tabs" id="myTab" role="tablist">
			                                <li class="nav-item">
			                                    <a class="nav-link active" id="informacion-tab" data-toggle="tab" href="#informacion" role="tab" aria-controls="informacion" aria-selected="true" :selected="seleccion.informacion">Info</a>
			                                </li>
			                                <li class="nav-item">
			                                    <a class="nav-link" id="lotes-tab" data-toggle="tab" href="#lotes" role="tab" aria-controls="lotes" aria-selected="true" v-on:click="obtenerLotes" :selected="seleccion.lotes">Lotes</a>
			                                </li>
			                                <li class="nav-item">
			                                    <a class="nav-link" id="gondola-tab" data-toggle="tab" href="#gondola" role="tab" aria-controls="gondola" aria-selected="true" v-on:click="obtenerGondolas">Góndolas</a>
			                                </li>
			                                <li class="nav-item">
			                                    <a class="nav-link" id="proveedores-tab" data-toggle="tab" href="#proveedores" role="tab" aria-controls="proveedor" aria-selected="true" v-on:click="obtenerProveedores">Compras</a>
			                                </li>
			                                <!-- <li class="nav-item">
			                                    <a class="nav-link" id="gondola-tab" data-toggle="tab" href="#gondola" role="tab" aria-controls="gondola" aria-selected="true">Depósitos</a>aria-controls="movivmientos" aria-selected="true" v-on:click="obtenerMovimientosProductos" :selected="seleccion.movimientos"
			                                </li> -->
			                                <!-- <li class="nav-item">
			                                    <a class="nav-link" id="ubicacion-tab" data-toggle="tab" href="#ubicacion" role="tab" aria-controls="ubicacion" aria-selected="true" v-on:click="obtenerUbicacion">Ubicación</a>
			                                </li> -->
			                                <li class="nav-item">
			                                    <a class="nav-link" id="transferencias-tab" data-toggle="tab" href="#transferencias" role="tab" aria-controls="transferencias" aria-selected="true" v-on:click="obtenerTransferenciaProductos" :selected="seleccion.transferencias">Transferencias</a>
			                                </li>
			                                <li class="nav-item">
			                                    <a class="nav-link" id="movimientos-tab" data-toggle="tab" href="#movimientos" role="tab"  aria-controls="movimientos" aria-selected="true" v-on:click="obtenerMovimientosProductos">Movimientos</a>
			                                </li>

			                                <li class="nav-item">
			                                    <a class="nav-link" id="inventarios-tab" data-toggle="tab" href="#inventarios" role="tab" aria-controls="inventarios" aria-selected="true" v-on:click="obtenerInventario">Inventarios</a>
			                                </li>
			                            </ul>
			                            <div class="row">
			                            	<div class="col-md-12">
						                        <div class="tab-content informacion-tab" id="myTabContent">
						                            <div class="tab-pane fade show active" id="informacion" role="tabpanel" aria-labelledby="informacion-tab">
						                                        <div class="row">
						                                            <div class="col-md-6">
						                                                <label>Código Interno</label>
						                                            </div>
						                                            <div class="col-md-6">
						                                                <p>{{producto.CODIGO_INTERNO}}</p>
						                                            </div>
						                                        </div>
						                                        <div class="row">
						                                            <div class="col-md-6">
						                                                <label>Categoría</label>
						                                            </div>
						                                            <div class="col-md-6">
						                                                <p>{{producto.CATEGORIA}}</p>
						                                            </div>
						                                        </div>
						                                        <div class="row">
						                                            <div class="col-md-6">
						                                                <label>Sub Categoría</label>
						                                            </div>
						                                            <div class="col-md-6">
						                                                <p>{{producto.SUBCATEGORIA}}</p>
						                                            </div>
						                                        </div>
						                                        <div class="row">
						                                            <div class="col-md-6">
						                                                <label>Color</label>
						                                            </div>
						                                            <div class="col-md-6">
						                                                <p>{{producto.COLOR}}</p>
						                                            </div>
						                                        </div>
						                                        <div class="row">
						                                            <div class="col-md-6">
						                                                <label>Temporada</label>
						                                            </div>
						                                            <div class="col-md-6">
						                                                <p>{{producto.TEMPORADA}}</p>
						                                            </div>
						                                        </div>
						                                        <div class="row">
						                                            <div class="col-md-6">
						                                                <label>Periodo</label>
						                                            </div>
						                                            <div class="col-md-6">
						                                                <p>{{producto.PERIODO}}</p>
						                                            </div>
						                                        </div>
						                                        <div class="row">
						                                            <div class="col-md-6">
						                                                <label>IVA</label>
						                                            </div>
						                                            <div class="col-md-6">
						                                                <p>{{producto.IMPUESTO}}</p>
						                                            </div>
						                                        </div>
						                                        <div class="row">
						                                            <div class="col-md-6">
						                                                <label>Cantidad Mayorista</label>
						                                            </div>
						                                            <div class="col-md-6">
						                                                <p>{{producto.CANT_MAYORISTA}}</p>
						                                            </div>
						                                        </div>
						                                        <div class="row">
						                                            <div class="col-md-6">
						                                                <label>Unidad de Medida</label>
						                                            </div>
						                                            <div class="col-md-6">
						                                                <p>{{producto.PRESENTACION}}</p>
						                                            </div>
						                                        </div>
						                                        <div class="row">
						                                            <div class="col-md-6">
						                                                <label>Moneda</label>
						                                            </div>
						                                            <div class="col-md-6">
						                                                <p>{{producto.MONEDA}}</p>
						                                            </div>
						                                        </div>
						                                        <div class="row">
						                                            <div class="col-md-6">
						                                                <label>Descuento</label>
						                                            </div>
						                                            <div class="col-md-6">
						                                                <p>{{producto.DESCUENTO}}</p>
						                                            </div>
						                                        </div>
						                                        <div class="row">
								                                    <div class="col-md-12">
								                                        <label>Observación</label><br/>
								                                        <p>{{producto.OBSERVACION}}</p>
								                                    </div>
								                                </div>
						                            </div>
						                            <div class="tab-pane fade" id="lotes" role="tabpanel" aria-labelledby="lotes-tab">

						                            			<div v-if="loading.lotes" class="d-flex justify-content-center">
														     		<div class="spinner-grow" role="status" aria-hidden="true"></div>
																</div>

						                                        <table class="table" v-if="lotes.length > 0 && loading.lotes === false">
																  <thead>
																    <tr>
																      <th scope="col">#</th>
																      <th scope="col">Lote</th>
																      <th scope="col">Inicial</th>
																      <th scope="col">Existente</th>
																      <th scope="col">Costo</th>
																      <th scope="col">Creación</th>
																      <th scope="col">Vencimiento</th>
																    </tr>
																  </thead>
																  <tbody>
																    <tr v-for="(lote, index) in lotes" class="cuerpoTabla">
																      <th scope="row">{{index + 1}}</th>	
																      <th>{{lote.LOTE}}</th>
																      <td>{{lote.CANTIDAD_INICIAL}}</td>
																      <td>{{lote.CANTIDAD}}</td>
																      <td>{{lote.COSTO}}</td>
																      <td>{{lote.FECALTAS}}</td>
																      <td>{{lote.VENCIMIENTO}}</td>
																    </tr>
																  </tbody>
																</table>

																<div v-if="lotes.length === 0 && loading.lotes === false">
																	<div class="alert alert-primary" role="alert">
																	  <font-awesome-icon icon="info-circle" /> No hay lotes
																	</div>
																</div>
						                            </div>
						                            <div class="tab-pane fade" id="gondola" role="tabpanel" aria-labelledby="gondola-tab">

						                            		  <div v-if="loading.gondolas" class="d-flex justify-content-center">
														     	<div class="spinner-grow" role="status" aria-hidden="true"></div>
															  </div>
														<div v-if="loading.gondolas === false">
															<!-- TABLA DE GONDOLAS POR COMPRA  -->
															<div class="col-md-6">
								                                <label>POR PRODUCTO</label>
								                            </div>
						                                        <table class="table" v-if="gondolas.length > 0">
																  <thead>
																    <tr>
																      <th scope="col">#</th>
																      <th scope="col">Código</th>
																      <th scope="col">Descripción</th>
																      <th scope="col">Asignacion</th>
																    </tr>
																  </thead>
																  <tbody>
																    <tr v-for="(gondola, index) in gondolas" class="cuerpoTabla">
																      <th scope="row">{{index + 1}}</th>	
																      <th>{{gondola.ID}}</th>
																      <td>{{gondola.DESCRIPCION}}</td>
																      <td>{{gondola.FECALTAS}}</td>
																    </tr>
																  </tbody>
																</table>

																<div v-if="gondolas.length === 0">
																	<div class="alert alert-primary" role="alert">
																	  <font-awesome-icon icon="info-circle" /> No se asignaron góndolas
																	</div>
																</div>
														<div v-if="rack === 'SI'">
																	
															<!-- TABLA DE GONDOLAS POR COMPRA  -->
															<div class="col-md-6 mt-3">
								                                <label>POR COMPRA</label>
								                            </div>
															<table class="table" v-if="comprasGondolas.length > 0">
																<thead>
																	<tr>
																	    <th scope="col">#</th>
																	    <th scope="col">Codigo Compra</th>
																	    <th scope="col">Gondola</th>
																	    <th scope="col">Fecha</th>
																	</tr>
																</thead>
																<tbody>
																	<tr v-for="(compra_gondola, index) in comprasGondolas" class="cuerpoTabla">
																	    <th scope="row">{{index + 1}}</th>	
																	    <th>{{compra_gondola.CODIGO}}</th>
																	    <td>{{compra_gondola.GONDOLA}}</td>
																	    <td>{{compra_gondola.FECHA}}</td>
																	</tr>
																</tbody>
															 </table>
															<div v-if="comprasGondolas.length === 0">
																<div class="alert alert-primary" role="alert">
																	<font-awesome-icon icon="info-circle" /> No hay compras con gondolas del producto.
																</div>
															</div>
															 
															 <!-- TABLA DE DEVOLUCIONES -->
															 
															<div class="col-md-6">
								                                <label>POR TRANSFERENCIA</label>
								                            </div>
															<table class="table" v-if="transferenciasGondolas.length > 0">
																<thead>
																	<tr>
																	    <th scope="col">#</th>
																	    <th scope="col">Codigo Transferencia</th>
																	    <th scope="col">Gondola</th>
																	    <th scope="col">Fecha</th>
																	</tr>
																</thead>
																<tbody>
																	<tr v-for="(transferencia_gondola, index) in transferenciasGondolas" class="cuerpoTabla">
																	    <th scope="row">{{index + 1}}</th>
																	    <td>{{transferencia_gondola.CODIGO}}</td>
																	    <td>{{transferencia_gondola.GONDOLA}}</td>
																	    <td>{{transferencia_gondola.FECHA}}</td>

																	</tr>
																</tbody>
															 </table>
															<div v-if="transferenciasGondolas.length === 0">
																<div class="alert alert-primary" role="alert">
																	<font-awesome-icon icon="info-circle" /> No hay transferencias con gondolas del producto.
																</div>
															</div>
														</div>
													  </div>
						                            </div>
						                            <div class="tab-pane fade" id="proveedores" role="tabpanel" aria-labelledby="proveedores-tab">
																<div v-if="loading.compras" class="d-flex justify-content-center">
														            <div class="spinner-grow" role="status" aria-hidden="true"></div>
														        </div>

						                                        <table class="table" v-if="proveedores.length > 0 && loading.compras === false">
																  <thead>
																    <tr>
																      <th scope="col">#</th>
																      <th scope="col">Código</th>
																      <th scope="col">Proveedor</th>
																      <th scope="col">Cantidad</th>
																      <th scope="col">Guaranies</th>
																      <th scope="col">Dolares</th>
																      <th scope="col">Reales</th>
																      <th scope="col">Pesos</th>
																      <th scope="col">Ult. Compra</th>
																    </tr>
																  </thead>
																  <tbody>
																    <tr v-for="proveedor in proveedores" class="cuerpoTabla">
																      <th scope="row">{{proveedor.C}}</th>	
																      <th>{{proveedor.CODIGO}}</th>
																      <th>{{proveedor.NOMBRE}}</th>
																      <td>{{proveedor.CANTIDAD}}</td>
																      <td>{{proveedor.GUARANIES}}</td>
																      <td>{{proveedor.DOLARES}}</td>
																      <td>{{proveedor.PESOS}}</td>
																      <td>{{proveedor.REALES}}</td>
																      <td>{{proveedor.FECALTAS}}</td>
																    </tr>
																  </tbody>
																</table>

																<div v-if="proveedores.length === 0 && loading.compras === false">
																	<div class="alert alert-primary" role="alert">
																	  <font-awesome-icon icon="info-circle" /> No hay compras
																	</div>
																</div>
						                            </div>

						                            <div class="tab-pane fade" id="transferencias" role="tabpanel" aria-labelledby="transferencias-tab">

						                            	<div v-if="loading.transferencias" class="d-flex justify-content-center">
														     <div class="spinner-grow" role="status" aria-hidden="true"></div>
														</div>

						                            	<div >
							                            	<div class="card" v-if="importados.length > 0 && loading.transferencias === false">
															  <div class="card-body">
															    <h5 class="card-title">Recibidos</h5>
															    <h6 class="card-subtitle mb-10 text-muted">Transferencias importadas de otras sucursales</h6>
															    <table class="table mt-4">
																	  <thead>
																	    <tr>
																	      <th scope="col">#</th>
																	      <th scope="col">Código</th>
																	      <th scope="col">Sucursal</th>
																	      <th scope="col">Cantidad</th>
																	      <th scope="col">Guaranies</th>
																	      <th scope="col">Dolares</th>
																	      <th scope="col">Reales</th>
																	      <th scope="col">Pesos</th>
																	      <th scope="col">Fecha</th>
																	    </tr>
																	  </thead>
																	  <tbody>
																	    <tr v-for="importado in importados" class="cuerpoTabla">
																	      <th scope="row">{{importado.C}}</th>	
																	      <th>{{importado.CODIGO}}</th>
																	      <th>{{importado.DESCRIPCION}}</th>
																	      <td>{{importado.CANTIDAD}}</td>
																	      <td>{{importado.GUARANIES}}</td>
																	      <td>{{importado.DOLARES}}</td>
																	      <td>{{importado.REALES}}</td>
																	      <td>{{importado.PESOS}}</td>
																	      <td>{{importado.FECHA}}</td>
																	    </tr>
																	  </tbody>
																</table>
															  </div>
															</div>

															<div v-if="importados.length === 0 && loading.transferencias === false">
																<div class="alert alert-primary" role="alert">
																  <font-awesome-icon icon="info-circle" /> No hay transferencias importadas
																</div>
															</div>

						                                </div>	

						                                <div class="mt-4 mb-4">
							                                <div class="card" v-if="enviadas.length > 0 && loading.transferencias === false">
															  <div class="card-body">
															    <h5 class="card-title">Enviados</h5>
															    <h6 class="card-subtitle mb-10 text-muted">Transferencias enviadas a otras sucursales</h6>
															    <table class="table mt-4">
																	  <thead>
																	    <tr>
																	      <th scope="col">#</th>
																	      <th scope="col">Código</th>
																	      <th scope="col">Sucursal</th>
																	      <th scope="col">Cantidad</th>
																	      <th scope="col">Guaranies</th>
																	      <th scope="col">Dolares</th>
																	      <th scope="col">Reales</th>
																	      <th scope="col">Pesos</th>
																	      <th scope="col">Fecha</th>
																	    </tr>
																	  </thead>
																	  <tbody>
																	    <tr v-for="(enviada, index) in enviadas" class="cuerpoTabla">
																	      <th scope="row">{{index + 1}}</th>	
																	      <th>{{enviada.CODIGO}}</th>
																	      <th>{{enviada.DESCRIPCION}}</th>
																	      <td>{{enviada.CANTIDAD}}</td>
																	      <td>{{enviada.GUARANIES}}</td>
																	      <td>{{enviada.DOLARES}}</td>
																	      <td>{{enviada.REALES}}</td>
																	      <td>{{enviada.PESOS}}</td>
																	      <td>{{enviada.FECHA}}</td>
																	    </tr>
																	  </tbody>
																</table>
															  </div>
															</div>

															<div v-if="enviadas.length === 0 && loading.transferencias === false">
																<div class="alert alert-primary" role="alert">
																  <font-awesome-icon icon="info-circle" /> No hay transferencias enviadas
																</div>
															</div>	
														</div>

						                            </div>

						                            
						                            <!-- <div class="tab-pane fade show " id="ubicacion" role="tabpanel" aria-labelledby="ubicacion-tab">

						                            	<div v-if="loading.ubicacion" class="d-flex justify-content-center">
														    <div class="spinner-grow" role="status" aria-hidden="true"></div>
														</div>

						                                        <div class="row" v-if="ubicacion !== false">
						                                            <div class="col-md-6">
						                                                <label>Shelf</label>
						                                            </div>
						                                            <div class="col-md-6">
						                                                <p>{{ubicacion.SHELF}}</p>
						                                            </div>

						                                            <div class="col-md-12">
						                                            	<hr>
						                                            </div>
						                                            	
						                                            <div class="col-md-6">
						                                                <label>Linea</label>
						                                            </div>
						                                            <div class="col-md-6">
						                                                <p>{{ubicacion.LINE}}</p>
						                                            </div>

						                                            <div class="col-md-12">
						                                            	<hr>
						                                            </div>

						                                            <div class="col-md-6">
						                                                <label>Posición</label>
						                                            </div>

						                                            <div class="col-md-6">
						                                                <p>{{ubicacion.POSITION}}</p>
						                                            </div>

						                                            <div class="col-md-12">
						                                            	<hr>
						                                            </div>

						                                            <div class="col-md-6">
						                                                <label>Ocupación</label>
						                                            </div>

						                                            <div class="col-md-6">
						                                                <p>{{ubicacion.OCCUPATION}}</p>
						                                            </div>

						                                            <div class="col-md-12">
						                                            	<hr>
						                                            </div>

						                                            <div class="col-md-6">
						                                                <label>Way of Display</label>
						                                            </div>

						                                            <div class="col-md-6">
						                                                <p>{{ubicacion.WAY}}</p>
						                                            </div>
						                                        </div>

						                                        <div v-if="ubicacion === false">
																	<div class="alert alert-primary" role="alert">
																	  <font-awesome-icon icon="info-circle" /> No hay ubicaciones designadas
																	</div>
																</div>
						                            </div> -->


							                        <div class="tab-pane fade" id="movimientos" role="tabpanel" aria-labelledby="movimientos-tab">

							                        	<div v-if="loading.movimientos" class="d-flex justify-content-center">
														    <div class="spinner-grow" role="status" aria-hidden="true"></div>
														</div>

														<div v-if="loading.movimientos === false">
														 <div class="col-md-6">
							                                    <label>VENTAS</label>
							                             </div>
														 <!-- TABLA DE VENTAS -->

														 <div class="mt-2" v-if="ventas.length > 0">
														 	<producto-detalle-venta ref="venta_producto" :codigo="codigo"></producto-detalle-venta>
														 </div>
														 <div v-if="ventas.length === 0">
															<div class="alert alert-primary" role="alert">
																<font-awesome-icon icon="info-circle" /> No hay ventas realizadas.
															</div>
														 </div>
														 <!-- TABLA DE NOTA DE CREDITO -->
														 <div class="col-md-6 mt-3">
							                                    <label>NOTAS DE CRÉDITO</label>
							                             </div>
														 <table class="table" v-if="creditos.length > 0">
															<thead>
																<tr>
																    <th scope="col">#</th>
																    <th scope="col">ID</th>
																    <th scope="col">Cliente</th>
																    <th scope="col">Cantidad</th>
																    <th scope="col">Precio</th>
																    <th scope="col">Total</th>
																    <th scope="col">Fecha</th>
																</tr>
															</thead>
															<tbody>
																<tr v-for="(credito, index) in creditos" class="cuerpoTabla">
																    <th scope="row">{{index + 1}}</th>	
																    <th>{{credito.CODIGO}}</th>
																    <td>{{credito.CLIENTE}}</td>
																    <td>{{credito.CANTIDAD}}</td>
																    <td>{{credito.PRECIO}}</td>
																    <td>{{credito.TOTAL}}</td>
																    <td>{{credito.FECHA}}</td>
																</tr>
															</tbody>
														 </table>
														 <div v-if="creditos.length === 0">
															<div class="alert alert-primary" role="alert">
																<font-awesome-icon icon="info-circle" /> No hay Notas de Crédito.
															</div>
														 </div>
														 
														 <!-- TABLA DE DEVOLUCIONES -->
														 
														 <div class="col-md-6">
							                                    <label>DEVOLUCIONES DEL PRODUCTO</label>
							                             </div>
														 <table class="table" v-if="devolucionProd.length > 0">
															<thead>
																<tr>
																    <th scope="col">#</th>
																    <th scope="col">ID</th>
																    <th scope="col">Cliente</th>
																    <th scope="col">Cantidad</th>
																    <th scope="col">Precio</th>
																    <th scope="col">Total</th>
																    <th scope="col">Fecha</th>
																</tr>
															</thead>
															<tbody>
																<tr v-for="(devolucion, index) in devolucionProd" class="cuerpoTabla">
																    <th scope="row">{{index + 1}}</th>
																    <td>{{devolucion.ID}}</td>
																    <td>{{devolucion.CLIENTE}}</td>
																    <td>{{devolucion.CANTIDAD}}</td>
																    <td>{{devolucion.PRECIO}}</td>
																    <td>{{devolucion.TOTAL}}</td>
																    <td>{{devolucion.FECHA}}</td>

																</tr>
															</tbody>
														  </table>
														  <div v-if="devolucionProd.length === 0">
															<div class="alert alert-primary" role="alert">
																<font-awesome-icon icon="info-circle" /> No hay devoluciones.
															</div>
														 </div>
														 <!-- TABLA DEVOLUCION A PORVEEDOR -->
														 <div class="col-md-6 mt-3">
							                                    <label>DEVOLUCIONES A PROVEEDOR</label>
							                             </div>
														 <table class="table" v-if="devolucionesProv.length > 0">
															<thead>
																<tr>
																    <th scope="col">#</th>
																    <th scope="col">Proveedor</th>
																    <th scope="col">ID</th>
																    <th scope="col">Cantidad</th>
																    <th scope="col">Costo</th>
																    <th scope="col">Total</th>
																    <th scope="col">Lote</th>
																    <th scope="col">Fecha</th>
																    <th scope="col">Motivo</th>
																</tr>
															</thead>
															<tbody>
																<tr v-for="(devolucionProv, index) in devolucionesProv" class="cuerpoTabla">
																    <th scope="row">{{index + 1}}</th>	
																    <th>{{devolucionProv.PROVEEDOR}}</th>
																    <td>{{devolucionProv.CODIGO}}</td>
																    <td>{{devolucionProv.CANTIDAD}}</td>
																    <td>{{devolucionProv.COSTO}}</td>
																    <td>{{devolucionProv.TOTAL}}</td>
																    <td>{{devolucionProv.LOTE}}</td>
																    <td>{{devolucionProv.FECHA}}</td>
																    <td>{{devolucionProv.MOTIVO}}</td>
																</tr>
															</tbody>
														 </table>
														 <div v-if="devolucionesProv.length === 0">
															<div class="alert alert-primary" role="alert">
																<font-awesome-icon icon="info-circle" /> No hay devoluciones a proveedor.
															</div>
														 </div>
														 <!-- TABLA DE VECIMIENTO -->

														 <div class="col-md-12" v-if="vencidos.length > 0">
							                                    <label>VENCIDOS</label>
														 <table class="table">
															<thead>
																<tr>
																    <th scope="col">#</th>
																    <th scope="col">Lote</th>
																    <th scope="col">Cantidad</th>
																    <th scope="col">Costo</th>
																    <th scope="col">Total</th>
																    <th scope="col">Entrada</th>
																    <th scope="col">Vencimiento</th>
																</tr>
															</thead>
															<tbody>
																<tr v-for="(vencido, index) in vencidos" class="cuerpoTabla">
																    <th scope="row">{{index + 1}}</th>	
																    <th>{{vencido.LOTE}}</th>
																    <td>{{vencido.CANTIDAD}}</td>
																    <td>{{vencido.COSTO}}</td>
																    <td>{{vencido.TOTAL}}</td>
																    <td>{{vencido.ENTRADA}}</td>
																    <td>{{vencido.VENCIMIENTO}}</td>
																</tr>
															</tbody>
														 </table>
							                             </div>

														 <!-- TABLA SALIDA DE PRODUCTO -->

														 <div class="col-md-6 mt-3">
							                                    <label>SALIDAS</label>
							                             </div>
														 <table class="table" v-if="salidas.length > 0">
															<thead>
																<tr>
																    <th scope="col">#</th>
																    <th scope="col">ID</th>
																    <th scope="col">Cantidad</th>
																    <th scope="col">Costo</th>
																    <th scope="col">Total</th>
																    <th scope="col">Lote</th>
																    <th scope="col">Fecha</th>
																    <th scope="col">Motivo</th>
																</tr>
															</thead>
															<tbody>
																<tr v-for="(salida, index) in salidas" class="cuerpoTabla">
																    <th scope="row">{{index + 1}}</th>	
																    <td>{{salida.ID}}</td>
																    <td>{{salida.CANTIDAD}}</td>
																    <td>{{salida.COSTO}}</td>
																    <td>{{salida.TOTAL}}</td>
																    <td>{{salida.LOTE}}</td>
																    <td>{{salida.FECHA}}</td>
																    <td>{{salida.MOTIVO}}</td>
																</tr>
															</tbody>
														 </table>
														 <div v-if="salidas.length === 0">
															<div class="alert alert-primary" role="alert">
																<font-awesome-icon icon="info-circle" /> No hay salidas.
															</div>
														 </div>
														</div>
							                        </div>

						                            <div class="tab-pane fade" id="inventarios" role="tabpanel" aria-labelledby="inventarios-tab">

							                        	<div v-if="loading.inventarios" class="d-flex justify-content-center">
														    <div class="spinner-grow" role="status" aria-hidden="true"></div>
														</div>

						                                <table class="table" v-if="loading.inventarios === false && inventarios.length > 0">
															<thead>
																<tr>
																    <th scope="col">#</th>
																    <th scope="col">ID</th>
																    <th scope="col">OBSERVACIÓN</th>
																    <th scope="col">MOTIVO</th>
																    <th scope="col">GONDOLA</th>
																    <th scope="col">CONTEO</th>
																    <th scope="col">STOCK</th>
																    <th scope="col">FECHA</th>
																</tr>
															</thead>
															<tbody>
																<tr v-for="(inventario, index) in inventarios" class="cuerpoTabla">
																    <th scope="row">{{index + 1}}</th>	
																    <td>{{inventario.ID}}</td>
																    <td>{{inventario.OBSERVACION}}</td>
																    <td>{{inventario.MOTIVO}}</td>
																    <td>{{inventario.GONDOLA}}</td>
																    <td>{{inventario.CONTEO}}</td>
																    <td>{{inventario.STOCK}}</td>
																    <td>{{inventario.FECHA}}</td>
																</tr>
															</tbody>
														</table>

														<div v-if="loading.inventarios === false && inventarios.length === 0">
															<div class="alert alert-primary" role="alert">
																<font-awesome-icon icon="info-circle" /> No hay inventarios
															</div>
														</div>
													</div>
						                        </div>
						                    </div>
			                            </div>	
			                        </div>
			                    </div>
			                </div>  
				      </div>
				      <div class="modal-footer">
				        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				      </div>
				    </div>
				  </div>
				</div>

				<!-- The Modal PARA LA IMAGEN -->

				<div id="myModal" class="modal modal-imagen">
					<span class="close-imagen">&times;</span>
					<img class="modal-content modal-content-imagen" id="img01">
					<div id="caption"></div>
				</div>

		</div>
		<!-- ******************************************************************* -->	
</template>
<script>
	export default {
	  props: ["codigo", "rack"],	
      data(){
        return {
          	producto: {
          		DESCRIPCION: '',
          		STOCK: '',
          		IMAGEN: '',
          		PREC_VENTA: '',
          		PREMAYORISTA: '',
          		PREVIP: '',
          		PRECOSTO: '',
          		FECALTAS: '',
                FECMODIF: '',
                FECHULT_C: '',
                FECHULT_V: '',
                CATEGORIA: '',
                SUBCATEGORIA: '',
                COLOR: '',
                IMPUESTO: '',
                PRESENTACION: '',
                MONEDA: '',
                DESCUENTO: '',
                OBSERVACION: '',
                STOCK_MIN: '',
                CANT_MAYORISTA: '',
                BAJA: '',
                DEPOSITO: '',
                DEPOSITO_DESC: ''
          	},
          	deposito: {
          		CANTIDAD: '',
          		DESCRIPCION: ''
          	},
          	lotes: {
          		LOTE: '', 
          		CANTIDAD_INICIAL: '', 
          		CANTIDAD: '', 
          		COSTO: '', 
          		FECALTAS: '',
          		VENCIMIENTO: ''
          	},
          	proveedores: {
          		CANTIDAD: '',
          		PROVEEDOR: '',
          		NOMBRE: '',
          		FECALTAS: '',
          		CODIGO: ''
          	},
          	importados: {
          		DESCRIPCION: '',
          		CANTIDAD_TRANSFERENCIA: '',
          		CANTIDAD: ''
          	},
          	gondolas: {
          		CODIGO: '',
          		DESCRIPCION: '',
          		FECALTAS: ''
          	},
          	comprasGondolas: {
          		CODIGO: '',
          		GONDOLA: '',
          		FECHA: ''
          	},
          	transferenciasGondolas: {
          		CODIGO: '',
          		GONDOLA: '',
          		FECHA: ''
          	},
          	enviadas: {

          	}, 
          	seleccion: {
          		informacion: true,
          		lotes: false,
          		transferencias: false
          	},
          	loading: {
          		compras: false,
          		transferencias: false,
          		lotes: false,
          		gondolas: false,
          		ubicacion: false,
          		movimientos: false,
          		inventarios: false
          	}, ubicacion: {
          		SHELF: '', 
          		LINE: '',
          		POSITION: '',
          		OCCUPATION: '',
          		WAY: '',
          		MAIN_CATEGORY: ''
          	}, inventarios: {
          		CONTEO: '',
          		FECHA: '',
          		GONDOLA: '',
          		ID: '', 
          		MOTIVO: '',
          		OBSERVACION: '',
          		STOCK: ''
          	},
          	ventas: {
          		CODIGO: '',
          		PRECIO: '',
          		CANTIDAD: '',
          		TOTAL: '',
          		FECHA: '',
          		CLIENTE: ''
          	},
          	vencidos: {
          		LOTE: '',
          		CANTIDAD: '',
          		COSTO: '',
          		ENTRADA: '',
          		VENCIMIENTO: ''
          	},
          	devolucionesProv: {
          		CODIGO: '',
				CANTIDAD: '',
				COSTO: '',
				TOTAL: '',
				LOTE: '',
				FECHA: '',
				PROVEEDOR: '',
				MOTIVO: ''
          	},
          	devolucionProd: {
          		ID: '',
          		PRECIO: '',
          		CANTIDAD: '',
          		TOTAL: '',
          		FECHA: '',
          		CLIENTE: ''
          	},
          	creditos: {
          		CODIGO: '',
          		PRECIO: '',
          		CANTIDAD: '',
          		TOTAL: '',
          		FECHA: '',
          		CLIENTE: ''
          	}, 
          	salidas: {
          		ID: '',
				CANTIDAD: '',
				COSTO: '',
				TOTAL: '',
				LOTE: '',
				FECHA: '',
				MOTIVO: ''
          	}   
         }
      },
      watch: { 
        codigo: function(newVal, oldVal) { 
            this.obtener_datos(newVal);
        }
      }, 
      methods: {
      	mostrar(){

      		// ------------------------------------------------------------------------

      		// MOSTRAR MODAL

      		$('.modal-detalle').modal('show');

      		// ------------------------------------------------------------------------

      		// FIJAR TAB LOTE

      		$('#informacion-tab').trigger('click');

      		// ------------------------------------------------------------------------

      	},
      	obtener_datos(valor){

      		// ------------------------------------------------------------------------

      		let me = this;

      		// ------------------------------------------------------------------------

      		// LLAMAR DATOS 

      		Common.obtenerProductoDetalleCommon(valor).then(data => {
           		me.producto = data.producto;
           		me.producto.IMAGEN = data.imagen; 
           		me.deposito = data.deposito;
           	}).catch((err) => {
           		
           	});

      		// ------------------------------------------------------------------------

      	},
      	obtenerGondolas(){

      		// ------------------------------------------------------------------------

      		let me = this;
      		me.loading.gondolas = true;

      		// ------------------------------------------------------------------------

      		// LLAMAR DATOS 

      		Common.obtenerGondolasProductoCommon(me.codigo).then(data => {
      			me.loading.gondolas = false;
           		me.gondolas = data.gondolas;

           		if(me.rack === 'SI'){
           			me.comprasGondolas = data.COMPRAS;
           			me.transferenciasGondolas = data.TRANSFERENCIAS;
           		}
           	}).catch((err) => {
           		
           	});

      		// ------------------------------------------------------------------------

      	},
      	obtenerLotes(){

      		// ------------------------------------------------------------------------

      		let me = this;
      		me.loading.lotes = true;

      		// ------------------------------------------------------------------------

      		// LLAMAR DATOS 

      		Common.obtenerLotesConCantidadCommon(me.codigo).then(data => {
      			me.loading.lotes = false;
           		me.lotes = data;
           	}).catch((err) => {
           		
           	});

      		// ------------------------------------------------------------------------

      	},
      	obtenerProveedores(){

      		// ------------------------------------------------------------------------

      		let me = this;
      		me.loading.compras = true;

      		// ------------------------------------------------------------------------

      		// LLAMAR DATOS 

      		Common.obtenerProveedoresProductoCommon(me.codigo).then(data => {
      			me.loading.compras = false;
           		me.proveedores = data.proveedor;
           	}).catch((err) => {

           	});
           	

      		// ------------------------------------------------------------------------

      	},
      	obtenerTransferenciaProductos(){

      		// ------------------------------------------------------------------------

      		let me = this;
      		me.loading.transferencias = true;

      		// ------------------------------------------------------------------------

      		// LLAMAR DATOS 

      		Common.obtenerTransferenciaProductoCommon(me.codigo).then(data => {
      			me.loading.transferencias = false;
           		me.importados = data.importados;
           		me.enviadas = data.enviadas;
           	}).catch((err) => {
           		
           	});

      		// ------------------------------------------------------------------------

      	}, obtenerUbicacion(){

      		// ------------------------------------------------------------------------

      		let me = this;
      		me.loading.ubicacion = true;

      		// ------------------------------------------------------------------------

      		// LLAMAR DATOS 

      		Common.obtenerUbicacionCommon(me.codigo).then(data => {
      			me.loading.ubicacion = false;
           		me.ubicacion = data; 
           	}).catch((err) => {
           		
           	});

      		// ------------------------------------------------------------------------

      	}, obtenerInventario(){

      		let me = this;
      		me.loading.inventarios = true;

      		// ------------------------------------------------------------------------

      		// LLAMAR DATOS 

      		Common.obtenerInventarioCommon(me.codigo).then(data => {
      			me.loading.inventarios = false;
           		me.inventarios = data.inventario;

           	}).catch((err) => {
           		
           	});

      	}, obtenerMovimientosProductos(){

      		// ------------------------------------------------------------------------

      		let me = this;
      		me.loading.movimientos = true;

      		// ------------------------------------------------------------------------

      		// LLAMAR DATOS 

      		Common.obtenerMovimientosProductosCommon(me.codigo).then(data => {
      			me.loading.movimientos = false;
           		me.ventas = data.ventas;
           		me.vencidos = data.vencidos;
           		me.devolucionesProv = data.devolucionesProv;
           		me.devolucionProd = data.devolucionProd;
           		me.creditos = data.notaCredito;
           		me.salidas = data.salida;
           	}).catch((err) => {
           		
           	});

      		// ------------------------------------------------------------------------

      	},
      	agrandarImagen(){
      		
      		// ------------------------------------------------------------------------

        	// ABRIR EL MODAL

            // Get the modal
			var modal = document.getElementById("myModal");

			// Get the image and insert it inside the modal - use its "alt" text as a caption
			var img = document.getElementById("myImg");
			var modalImg = document.getElementById("img01");
			var captionText = document.getElementById("caption");
			img.onclick = function(){
			  modal.style.display = "block";
			  modalImg.src = this.src;
			  captionText.innerHTML = this.alt;
			}

			// Get the <span> element that closes the modal
			var span = document.getElementsByClassName("close-imagen")[0];

			// When the user clicks on <span> (x), close the modal
			span.onclick = function() { 
			  modal.style.display = "none";
			}

            // ------------------------------------------------------------------------  
      	}
      },
        mounted() {
        	
        	// ------------------------------------------------------------------------

        	// ABRIR EL MODAL

            // Get the modal
			var modal = document.getElementById("myModal");

			// Get the image and insert it inside the modal - use its "alt" text as a caption
			var img = document.getElementById("myImg");
			var modalImg = document.getElementById("img01");
			var captionText = document.getElementById("caption");
			img.onclick = function(){
			  modal.style.display = "block";
			  modalImg.src = this.src;
			  captionText.innerHTML = this.alt;
			}

			// Get the <span> element that closes the modal
			var span = document.getElementsByClassName("close-imagen")[0];

			// When the user clicks on <span> (x), close the modal
			span.onclick = function() { 
			  modal.style.display = "none";
			}

            // ------------------------------------------------------------------------   
        }
    }
</script>
<style>

/* IMAGEN ABRE S*/
#myImg {
  border-radius: 5px;
  cursor: pointer;
  transition: 0.3s;
}

#myImg:hover {opacity: 0.7;}

/* The Modal (background) */
.modal-imagen {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
}

/* Modal Content (image) */
.modal-content-imagen {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
}

/* Caption of Modal Image */
#caption {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
  text-align: center;
  color: #ccc;
  padding: 10px 0;
  height: 150px;
}

/* Add Animation */
.modal-content-imagen, #caption {  
  -webkit-animation-name: zoom;
  -webkit-animation-duration: 0.6s;
  animation-name: zoom;
  animation-duration: 0.6s;
}

@-webkit-keyframes zoom {
  from {-webkit-transform:scale(0)} 
  to {-webkit-transform:scale(1)}
}

@keyframes zoom {
  from {transform:scale(0)} 
  to {transform:scale(1)}
}

/* The Close Button */
.close-imagen {
  position: absolute;
  top: 15px;
  right: 35px;
  color: #f1f1f1;
  font-size: 40px;
  font-weight: bold;
  transition: 0.3s;
}

.close-imagen:hover,
.close-imagen:focus {
  color: #bbb;
  text-decoration: none;
  cursor: pointer;
}

/* 100% Image Width on Smaller Screens */
@media only screen and (max-width: 700px){
  .modal-content-imagen {
    width: 100%;
  }
}

/* IMAGEN CIERRA */

.emp-profile{
    padding: 3%;
    margin-top: 3%;
    margin-bottom: 3%;
    border-radius: 0.5rem;
    background: #fff;
}
.profile-img{
    text-align: center;
}
.profile-img img{
    width: 70%;
    height: 100%;
}
.profile-img .file {
    position: relative;
    overflow: hidden;
    margin-top: -20%;
    width: 70%;
    border: none;
    border-radius: 0;
    font-size: 15px;
    background: #212529b8;
}
.profile-img .file input {
    position: absolute;
    opacity: 0;
    right: 0;
    top: 0;
}
.profile-head h5{
    color: #333;
}
.profile-head h6{
    color: #0062cc;
}
.producto-edit-btn{
    border: none;
    border-radius: 1.5rem;
    width: 70%;
    padding: 2%;
    font-weight: 600;
    color: #6c757d;
    cursor: pointer;
}
.proile-rating{
    font-size: 12px;
    color: #818182;
    margin-top: 5%;
}
.proile-rating span{
    color: #495057;
    font-size: 15px;
    font-weight: 600;
}
.profile-head .nav-tabs{
    margin-bottom:5%;
}
.profile-head .nav-tabs .nav-link{
    font-weight:600;
    border: none;
}
.profile-head .nav-tabs .nav-link.active{
    border: none;
    border-bottom:2px solid #0062cc;
}
.profile-work{
    padding: 14%;
    margin-top: -15%;
}
.profile-work p{
    font-size: 12px;
    color: #818182;
    font-weight: 600;
    margin-top: 10%;
}
.profile-work .detalle {
    text-decoration: none;
    color: #495057;
    font-weight: 600;
    font-size: 14px;
}
.profile-work ul{
    list-style: none;
}
.informacion-tab label{
    font-weight: 600;
}
.informacion-tab p{
    font-weight: 600;
    color: #0062cc;
}
.cuerpoTabla {
	font-size: 12px;
}
.card-subtitle {
	font-size: 12px;
}
</style>