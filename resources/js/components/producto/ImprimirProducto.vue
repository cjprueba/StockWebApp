<template>

	<div class="container mt-3">
		<div class="col-14">
			<div class="card shadow border-bottom-primary mb-3">
				<div v-if="$can('producto.etiquetas') && $can('producto')">
					<!-- -------------------------------------------------------- TITULO ----------------------------------------------------------- -->
					<div class="text-center card-header">Imprimir Codigo QR o Codigo de Barra</div>
					<div class="card-body">

						
							
					    <div v-if="ok" class=" ml-2 row my-2">
								<div class="custom-control custom-switch">
									<input  v-on:change="eliminar_valores" type="checkbox" class="mr-4 custom-control-input" id="switchQr" v-model="checkedQr">
									<label class="custom-control-label" for="switchQr">Imprimir Qr</label>
								</div>
						</div>



					    <!-- --------------------------------------------------- TARJETAS ---------------------------------------------------------- -->
						
						<div class="row">

						<!-- ------------------------------------------------- PRIMER CUADRO ---------------------------------------------------------- -->
						
					    <div class="col-12">
				    		<div class="card">
				      			<div class="card-body">

					  				<!-- ----------------------------------- CODIGO, DESCRIPCION Y LINEA DEL PRODUCTO ------------------------------------- -->
									<div class="row mt--2">

										<div class="col-3">
											<codigo-producto v-bind:class="{ 'is-invalid': validar.codigoProd }" @codigo_producto="cargarProductos" ref="compontente_codigo_producto" v-model="producto.codigoProd" disabled></codigo-producto>
										</div>

										<div class="col-6">
								    			<label>Descripción Del Producto</label>
									      		<input v-bind:class="{ 'is-invalid': validar.descripion }" v-model="producto.descripcion" type="text" class="form-control form-control-sm" disabled>
								    	</div>
										    	

										<!-- ------------------------------------------------------------------ -->
										<!-- GONDOLA -->

										<div class="col-3">
										    <label v-if="checkedQr" for="validationTooltip01">Lotes</label>
					            			<select  class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validar.lote }" v-model="loteSeleccion" v-on:change="cambiar_stock" v-if="checkedQr" @input="$emit('input', $event.target.value)" >
							                    <option :value="{id : 0, nlote: 0,cantidad : 0 }">0 - Seleccionar</option>
							                    <option  data-item-type="lote.LOTE" v-for="lote in lotes"  :id="lote.LOTE" :value="{id : lote.ID, nlote: lote.LOTE,cantidad : lote.CANTIDAD }">{{ lote.LOTE }} - {{ lote.CANTIDAD }}</option>
					            			</select>
										</div>

									<!-- ------------------------------------------------------------------ -->
									</div>

									<!-- ---------------------------------- PRECIO UNITARIO, CANT EXISTENTE Y CANTIDAD ------------------------------------ -->

									<div class="row mt-3">

					      				<!-- -------------------------------------- PRECIO MAYORISTA------------------------------------------------------- -->

										<div class="col-3" >
							    				<label>Precio Mayorista</label>
								      			<input v-bind:class="{ 'is-invalid': validar.precioUnitario }" v-model="producto.precioMayorista" type="text" class="form-control form-control-sm" disabled>
										</div>

										<!-- -------------------------------------- PRECIO UNITARIO ------------------------------------------------------- -->

										<div class="col-3" >
							    				<label>Precio Unitario</label>
								      			<input v-bind:class="{ 'is-invalid': validar.precioUnitario }" v-model="producto.precioUnitario" type="text" class="form-control form-control-sm" disabled>
										</div>

										<!-- -------------------------------------- CANTIDAD EXIST. ------------------------------------------------------- -->

										<div class="col">
												<label>Cantidad Existente</label>
												<input v-bind:class="{ 'is-invalid': validar.cantidadExistente }" v-model="producto.cantidadExistente" type="text" class="form-control form-control-sm" disabled>
										</div>	

										<!-- ------------------------------------------- DESCUENTO -------------------------------------------------------- -->

										<!-- --------------------------------------------- CANTIDAD ------------------------------------------------------- -->

										<div class="col">
							    				<label>Cantidad</label>
								      			<input ref="cantidad_ticket" v-bind:class="{ 'is-invalid': validar.cantidad }" v-on:blur="agregarProductoRemision()" v-on:keyup.prevent.13="agregarProductoRemision()" v-model="producto.cantidad" type="text" class="form-control form-control-sm">
										</div>
										


									</div>
									<hr>
									<div class="row mt-3">
										<div class="col-4" align="center">
											<label class="form-check-label font-weight-bold ">Precio de Productos</label>
											<br>
											<div class="form-check ml-5" align="left">
											  <input class="form-check-input" type="radio" name="precioProducto" id="precioProducto4" v-model="seleccionPrecio" value="4">
											  <label class="form-check-label" for="precioProducto4">Precio de Venta sin descripción</label>
											</div>
											<div class="form-check ml-5" align="left">
											  <input class="form-check-input" type="radio" name="precioProducto" id="precioProducto1" v-model="seleccionPrecio" value="1">
											  <label class="form-check-label" for="precioProducto1">Precio de Venta</label>
											</div>
											<div class="form-check ml-5" align="left">
											  <input class="form-check-input" type="radio" name="precioProducto" id="precioProducto2" v-model="seleccionPrecio" value="2">
											  <label class="form-check-label" for="precioProducto2">Precio Mayorista</label>
											</div>
											<div class="form-check ml-5" align="left">
											  <input class="form-check-input" type="radio" name="precioProducto" id="precioProducto3" v-model="seleccionPrecio" value="3">
											  <label class="form-check-label" for="precioProducto3">Precio Venta y Mayorista</label>
											</div>
										</div>

										<div class="col-4" align="center">
											<label class="form-check-label font-weight-bold ">Tamaño de Etiquetas</label>
											<br>
											
											<div class="form-check ml-5" align="left">
											  <input class="form-check-input" type="radio" name="tamañoTiquet1" id="tamañoTiquet1" v-model="seleccionTamaño" value="1">
											  <label class="form-check-label" for="tamañoTiquet1">Gondola (9cm x 3,5cm)</label>
											</div>

											<div class="form-check ml-5" align="left">
											  <input class="form-check-input" type="radio" name="tamañoTiquet1" id="tamañoTiquet2" v-model="seleccionTamaño" value="2">
											  <label class="form-check-label" for="tamañoTiquet2">Proveedor (3,3cm x 2,2cm)</label>
											</div>
											
											<div class="form-check ml-5" align="left">
											  <input class="form-check-input" type="radio" name="tamañoTiquet1" id="tamañoTiquet3" v-model="seleccionTamaño" value="3">
											  <label class="form-check-label" for="tamañoTiquet3">Producto (3,3cm x 2,2cm)</label>
											</div>
											
											<div class="form-check ml-5" align="left">
											  <input class="form-check-input" type="radio" name="tamañoTiquet1" id="tamañoTiquet4" v-model="seleccionTamaño" value="4">
											  <label class="form-check-label" for="tamañoTiquet4">Producto (5,5cm x 2,9cm)</label>
											</div>

											<div class="form-check ml-5" align="left">
											  <input class="form-check-input" type="radio" name="tamañoTiquet1" id="tamañoTiquet5" v-model="seleccionTamaño" value="5">
											  <label class="form-check-label" for="tamañoTiquet5">QR Link (3,3cm x 2,2cm)</label>
											</div>

										</div>

										<!-- --------------------------------------------- TIPO DE CODIGO ------------------------------------------------------- -->
										<div class="col-4">
											<label class="form-check-label font-weight-bold ml-5">Tipo de Código</label>
											<br>
											<div class="form-check ml-5" align="left">
											  <input class="form-check-input" type="radio" name="TipoCodigo" id="TipoDeCodigo1"  v-model="seleccionCodigo" value="1">
											  <label class="form-check-label" for="TipoDeCodigo1" >Código Interno</label>
											</div>
											<div class="form-check ml-5" align="left">
											  <input class="form-check-input" type="radio" name="TipoCodigo" id="TipoDeCodigo2" v-model="seleccionCodigo" value="2">
											  <label class="form-check-label" for="TipoDeCodigo2">Código Producto </label>
											</div>
											<label class="form-check-label font-weight-bold ml-5">Tipo de moneda</label>
											<br>
											<div class="form-check ml-5" align="left">
											  <input class="form-check-input" type="radio" name="TipoMoneda" id="TipoDeMoneda1"  v-model="seleccionMoneda" value="1">
											  <label class="form-check-label" for="TipoDeMoneda1" >Guaranies (G$)</label>
											</div>
											<div class="form-check ml-5" align="left">
											  <input class="form-check-input" type="radio" name="TipoMoneda" id="TipoDeMoneda2" v-model="seleccionMoneda" value="2">
											  <label class="form-check-label" for="TipoDeMoneda2">Dolares (U$)</label>
											</div>
											<div v-if="seleccionMoneda>0" align="left">
								                  <hr>

												 
								                  <label for="cotizacion" class="col-7 ml-5 font-weight-bold">Cotización</label>

								                   <input  id="cotizacion" v-model="ingresarCotizacion" type="text" class="form-control col-7 ml-5" >
											</div>
										</div>
									</div>

									<!-- --------------------------------------------- PROVEEDOR ------------------------------------------------------- -->
									<div v-if="seleccionTamaño=='2'" class="col-12" >
										<div class="row">
											<div class="col-1"></div>
											<div class="col-10"><hr></div>										
										</div>
										<div class="row">
											<div class="col-6">
												<div class="mb-3">
												  	<label for="formGroupExampleInput" class="form-label font-weight-bold ml-2 ">Nombre</label>
												  	<input type="text" class="form-control form-control-sm" id="formGroupExampleInput" v-model="proveedor.nombre" v-bind:class="{ 'is-invalid': validarProveedor.nombre }">
												</div>

												<div class="mb-3">
												  	<label for="formGroupExampleInput2" class="form-label font-weight-bold ml-2">Razón</label>
												  	<input type="text" class="form-control form-control-sm" id="formGroupExampleInput2" v-model="proveedor.razon" v-bind:class="{ 'is-invalid': validarProveedor.razon }">
												</div>
												
												<div class="mb-3">
												  	<label for="formGroupExampleInput3" class="form-label font-weight-bold mt-2 ml-2">Dirección</label>
												  	<input type="text" class="form-control form-control-sm" id="formGroupExampleInput3" v-model="proveedor.direccion" v-bind:class="{ 'is-invalid': validarProveedor.direccion }">
												</div>


												<div class="mb-3">
												  	<label for="formGroupExampleInput4" class="form-label font-weight-bold  mt-2 ml-2">Teléfono</label>
												  	<input type="text" class="form-control form-control-sm" id="formGroupExampleInput4" v-model="proveedor.telefono" v-bind:class="{ 'is-invalid': validarProveedor.telefono }">
												</div>
											</div>

											<div class="col-6">
												
												
												<div class="mb-3">
												  	<label for="formGroupExampleInput5" class="form-label font-weight-bold  mt-2 ml-2">FAX</label>
												  	<input type="text" class="form-control form-control-sm" id="formGroupExampleInput5" v-model="proveedor.fax" v-bind:class="{ 'is-invalid': validarProveedor.fax }">
												</div>
												<div class="mb-3">
												  	<label for="formGroupExampleInput6" class="form-label  mt-2 font-weight-bold ml-2">RUC</label>
												  	<input type="text" class="form-control form-control-sm" id="formGroupExampleInput6" v-model="proveedor.ruc" v-bind:class="{ 'is-invalid': validarProveedor.ruc }">
												</div>

												<div class="mb-3">
												  	<label for="formGroupExampleInput7" class="form-label  mt-2 font-weight-bold ml-2">Ciudad</label>
												  	<input type="text" class="form-control form-control-sm"" id="formGroupExampleInput7" v-model="proveedor.ciudad" v-bind:class="{ 'is-invalid': validarProveedor.ciudad }">
												</div>
												<hr>
												<div class="row">
													
													<div class="col-7"></div>
													<div class="mb-3 col " align="center">
													  	<label for="formGroupExampleInput6" class="form-label  mt-2 font-weight-bold" >Cantidad de ticket</label>
													  	<input type="text" class="form-control form-control-sm" id="formGroupExampleInput6" v-model="proveedor.cantidad" v-bind:class="{ 'is-invalid': validarProveedor.cantidad }">
													</div>
														
												</div>
												
													
												</div>
											</div>
											
										</div>
										
									</div>
								</div>
							</div>
						</div>

				    	
						<!-- --------------------------------------------------- SEGUNDO CUADRO ------------------------------------------------------- -->
						
				    	<div class="col-12">

				    			<!-- -------------------------------- BOTONES LIMPIAR, GUARDAR, MODIFICAR Y ELIMINAR --------------------------------- -->	
							<div class="row mt-4"> 
								<!-- <div class="col text-center">
									<button v-on:click="Imprimir_QR" type="button" class="btn btn-outline-info btn-block" :disabled="!checkedQr" >Imprimir Qr</button>
								</div> -->
								<div class="col text-center">
									<button v-on:click="Imprimir_barcode" type="button" class="btn btn-outline-info btn-block" :disabled="checkedQr">Imprimir</button>
								</div>
								<!-- <div class="col text-center">
									<button v-on:click="Imprimir_barinterno" type="button" class="btn btn-outline-info btn-block" :disabled="checkedQr">Imprimir Codigo Interno</button>
								</div> -->
								<!-- <div class="col text-center">
									<button v-on:click="Imprimir_QR" type="button" class="btn btn-outline-info btn-block" :disabled="checkedQr">Imprimir Qr Link</button>
								</div> -->
							</div>
				  		</div>
				  		</div>

							<!-- -------------------------------------- TARJETA DE TABLA DE PRODUCTOS ------------------------------------------------- -->
						
						<div class="card mt-3 my-2">
							<div class="col-12">

								<!-- --------------------------------------------MOSTRAR LOADING------------------------------------------------------- -->

							    <div class="col-md-12">
									<div v-if="procesar" class="d-flex justify-content-center mt-3">
										Guardando...
							            <div class="spinner-grow text-success" role="status" aria-hidden="true"></div>
							        </div>
						        </div>
								
								<!-- ------------------------------------------ TABLA DE PRODUCTOS ---------------------------------------------------- -->
								
								<div class="col-md-12 mt-4">
									<table id="tablaProductos" class="display nowrap table table-striped table-bordered table-sm mb-3" style="width:100%">
						                <thead>
						                    <tr>
						                        <th></th>
						                        <th class="codigoDeclarados">Codigo Producto</th>
						                        <th >Codigo Interno</th>
						                        <th>Descripción</th>
						                        <th>Lote</th>
				                        		<th class="cantidadColumna">Cantidad</th>
						                        <th>Precio</th>
						                        <th>Precio Mayorista</th>
						                        <th>Acción</th>
				                        		<th>Stock</th>
				                        		<th>Id Lote</th>
				                        		<th>Moneda</th>
				                        		
						                    </tr>
						                </thead>
						                <tbody>
						                	
						                </tbody>
						                <tfoot>
						                	<tr>
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
							                	
						                	</tr>
						                </tfoot>	
				            		</table>
				            	</div>

							</div>
						</div>

						<!-- ------------------------------------------------------- MODALES------------------------------------------------------- -->

						<!--------------------------------------------------- MODAL EDITAR PRODUCTO --------------------------------------------------->

				                <div class="modal fade editar-producto-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
				                  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
				                    <div class="modal-content">
				                      <div class="modal-header">
				                        <h5 class="modal-title text-primary text-center" ><font-awesome-icon icon="barcode"/> {{editarCodigo}}</h5>
				                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				                          <span aria-hidden="true">&times;</span>
				                        </button>
				                      </div>
				                      <div class="modal-body">
				                            <div class="row">
				                                <div class="col-md-12">
				                                    <label>Precio Unitario</label>
				                                    <input type="text" name="" v-model="editarPrecio" class="form-control form-control-sm" disabled>
				                                </div>
				                                <div class="col-md-12 mt-3">
				                                    <label>Cantidad</label>
				                                    <input type="text" name="" v-model="editarCantidad" v-on:blur="formatoEditarPrecio()" class="form-control form-control-sm">
				                                </div>
				                            </div>      
				                      </div>
				                      <div class="modal-footer">
				                        <button type="button" class="btn btn-warning" id="editarFila">Editar</button>
				                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				                      </div>
				                    </div>
				                  </div>
				                </div>  

				        <!-- -------------------------------------------------MODAL ELIMINAR PRODUCTO --------------------------------------------------->

				                <div class="modal fade eliminar-producto-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
				                  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
				                    <div class="modal-content">
				                      <div class="modal-header">
				                        <h5 class="modal-title text-primary text-center" >¿Eliminar <font-awesome-icon icon="barcode"/> {{editarCodigo}}?</h5>
				                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				                          <span aria-hidden="true">&times;</span>
				                        </button>
				                      </div>
				                      <div class="modal-body text-center">
				                            <button type="button" class="btn btn-danger" id="eliminarFila" data-dismiss="modal">Eliminar</button>
				                        	<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				                      </div>
				                    </div>
				                  </div>
				                </div>

				        <!---------------------------------------------------- MODAL MOSTRAR PRODUCTO ---------------------------------------------------->

				                <div class="modal fade mostrar-producto-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
				                  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
				                    <div class="modal-content">
				                      <div class="modal-header">
				                        <h5 class="modal-title text-primary text-center" ><font-awesome-icon icon="barcode"/> {{editarCodigo}}</h5>
				                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				                          <span aria-hidden="true">&times;</span>
				                        </button>
				                      </div>
				                      <div class="modal-body">
				                          <div class="card" >
											  <img  :src="productoImagen[0].IMAGEN" class="card-img-top" alt="">
											  <div class="card-body">
											    <p class="card-text text-center">{{productoImagen[0].DESCRIPCION}}</p>
											  </div>
											  <ul class="list-group list-group-flush">
											    <li class="list-group-item"><span class="text-primary text-left">Stock: </span> <span class="float-right">{{productoImagen[0].STOCK}}</span></li>
											    <li class="list-group-item"><span class="text-primary text-left">Precio Costo: </span> <span class="float-right">{{productoImagen[0].PRECOSTO}}</span></li>
											    <li class="list-group-item"><span class="text-primary text-left">Precio Venta: </span> <span class="float-right">{{productoImagen[0].PREC_VENTA}}</span></li>
											    <li class="list-group-item"><span class="text-primary text-left">Precio Mayorista: </span> <span class="float-right">{{productoImagen[0].PREMAYORISTA}}</span></li>
											    <li class="list-group-item"><span class="text-primary text-left">Precio VIP: </span> <span class="float-right">{{productoImagen[0].PREVIP}}</span></li>
											    <li class="list-group-item"><span class="text-primary text-left">Última Compra: </span> <span class="float-right">{{productoImagen[0].FECHULT_C}}</span></li>
											  </ul>
											  
											</div>  
				                      </div>
				                      <div class="modal-footer">
				                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				                      </div>
				                    </div>
				                  </div>
				                </div>        

				        <!------------------------------------------------ MODAL PRODUCTOS SIN REGISTRAR ------------------------------------------------>

				        <div class="modal fade" id="modal_no_registrados" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
						  <div class="modal-dialog modal-dialog-scrollable" role="document">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h5 class="modal-title" id="exampleModalScrollableTitle">Productos no registrados</h5>
						        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						          <span aria-hidden="true">&times;</span>
						        </button>
						      </div>
						      <div class="modal-body">
						        <table class="table table-striped table-light table-sm" v-if="no_registrados.length > 0">
								  <thead class="thead-light">
								    <tr>
								      <th scope="col">#</th>
								      <th scope="col">Código</th>
								      <th scope="col">Cantidad</th>
								      <th scope="col">Guardado</th>
								      <th scope="col">No guardado</th>
								    </tr>
								  </thead>
								  <tbody>
								    <tr v-bind:class="{ 'table-danger': no_registrado.guardado === 0, 'table-warning': no_registrado.guardado > 0 }" v-for="(no_registrado, index) in no_registrados">
								      <th scope="row">{{index+1}}</th>
								      <td>{{no_registrado.cod_prod}}</td>
								      <td>{{no_registrado.cantidad}}</td>
								      <td>{{no_registrado.guardado}}</td>
								      <td>{{no_registrado.restante}}</td>
								    </tr>
								  </tbody>
								</table>
						      </div>
						      <div class="modal-footer">
						        <button type="button" class="btn btn-secondary" v-on:click="mostrarRemision" data-dismiss="modal">Cerrar</button>
						      </div>
						    </div>
						  </div>
						</div>
						
						 
						<!-- ---------------------------------------------------------- TOASTS -------------------------------------------------------- -->

						<!-- ------------------------------------------------ TOAST CODIGO PRODUCTO REPETIDO ------------------------------------------ -->

						<b-toast id="toast-codigo-repetido" variant="warning" solid>
					      <template v-slot:toast-title>
					        <div class="d-flex flex-grow-1 align-items-baseline">
					          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
					          <strong class="mr-auto">¡Error!</strong>
					          <small class="text-muted mr-2">repetido</small>
					        </div>
					      </template>
					      ¡Este producto ya existe en la tabla, favor de elegir otro o modificarlo!
					    </b-toast>

					    <!-- ---------------------------------------------TOAST PRODUCTO MODIFICADO---------------------------------------------------- -->

						<b-toast id="toast-producto-modificado" variant="success" solid>
					      <template v-slot:toast-title>
					        <div class="d-flex flex-grow-1 align-items-baseline">
					          <strong class="mr-auto">¡Éxito!</strong>
					          <small class="text-muted mr-2">modificado</small>
					        </div>
					      </template>
					      ¡Este producto ha sido modificado con éxito!
					    </b-toast>

					    <!-- ------------------------------------------------TOAST CODIGO PRODUCTO REPETIDO-------------------------------------------- -->

						<b-toast id="toast-editar-cero" variant="warning" solid>
					      <template v-slot:toast-title>
					        <div class="d-flex flex-grow-1 align-items-baseline">
					          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
					          <strong class="mr-auto">¡Error!</strong>
					          <small class="text-muted mr-2">cero</small>
					        </div>
					      </template>
					      ¡La cantidad y el precio no deben ser cero!
					    </b-toast>

					    <!-- ----------------------------------------------TOAST CODIGO PRODUCTO REPETIDO---------------------------------------------- -->

						<b-toast id="toast-cantidad-superada" variant="warning" solid>
					      <template v-slot:toast-title>
					        <div class="d-flex flex-grow-1 align-items-baseline">
					          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
					          <strong class="mr-auto">¡Error!</strong>
					          <small class="text-muted mr-2">superada</small>
					        </div>
					      </template>
					      ¡La cantidad ha superado el stock!
					    </b-toast>

					    <!-- -------------------------------------------------TOAST COMPLETAR CABECERA------------------------------------------------- -->

						<b-toast id="toast-completar-cabecera" variant="warning" solid>
					      <template v-slot:toast-title>
					        <div class="d-flex flex-grow-1 align-items-baseline">
					          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
					          <strong class="mr-auto">¡Error!</strong>
					          <small class="text-muted mr-2">incompleto</small>
					        </div>
					      </template>
					      ¡Complete todos los datos correctamente!
					    </b-toast>
				  	</div>
				</div>
			</div>
		</div>
	</div>
  	<!-- ---------------------------------------------------- FIN-------------- ------------------------------------------------------- -->
</template>

<script>
	
	export default{
      props: ['candec', 'monedaCodigo', 'tab_unica', 'id_sucursal'],
		data(){

			return{
				ok:false,
  				selected: '',
  				checkedQr:false,
				moneda: '1',
				decimal: 0,
				existe: false, 
				ivaProducto: '',
				btnguardar: true,
				codigo_remision: '',
				seleccionTamaño: 0,
				seleccionCodigo: 0,
				seleccionMoneda: 0,
				ingresarCotizacion: 0,
				seleccionPrecio: 0,

				productoImagen: [{
	         		PREVIP: '',
	         		IMAGEN: '',
	         		PRECOSTO: '',
	         		FECHULT_C: '',
	         		PREC_VENTA: '',
	         		DESCRIPCION: '',
	         		PREMAYORISTA: ''
         		}],
               lotes:[],
               loteSeleccion:{
               	    nlote:0,
                    id:0,
                    cantidad:0, 
                           },
                 
				producto: {
					linea: '',
					cantidad: '',
					codigoProd: '',
					interno:'',
					descripcion: '',
					precioUnitario: '',
					precioMayorista:'',
					cantidadExistente: '',
					descuento: '',
					moneda:''

				},

				validarProveedor:{
					nombre: false,
					direccion: false,
					ciudad: false,
					telefono: false,
					razon: false,
					fax: false,
					ruc: false,
					cantidad: false
				},
				proveedor:{
					nombre: '',
					direccion: '',
					ciudad: '',
					telefono: '',
					razon: '',
					fax: '',
					ruc: '',
					cantidad: '',
					controlar:true
				},


				validar:{
				
					linea: false,
					nombreUsuario: false,
					cantidad: false,
					codigoProd: false,
					lote:false,
					descripcion: false,
					precioUnitario: false,
					codigo_remision: false,
					cantidadExistente: false,
					descuento: false
				},
				choiceText:'',

	            editarRow: '',
	            editarStock: '',
	            editarPrecio: '',
				editarCodigo: '',
	            editarCantidad: '',
	            editarIvaProducto: '',
	            editarDescuento: '',
	            procesar: false,
           		no_registrados: [],
           		usuarioId: '',
           		nombreUsuario: '',
           		
           		checkedMayorista: false

			}
		},

		methods:{
			cambiar_stock () {
           let me=this;
       me.producto.cantidadExistente=me.loteSeleccion.cantidad;
                 
  },
   eliminar_valores () {
   		var tableProductos = $('#tablaProductos').DataTable();
   	if(tableProductos.data().count()){
                    Swal.fire({
						  title: 'Se tendra que eliminar los datos de la tabla.',
						  text: "¿Estas seguro de cambiar el proceso?.",
						  icon: 'warning',
						  showCancelButton: true,
						  confirmButtonText: '¡Aceptar!',
						  cancelButtonText: '¡Cancelar!',
						  reverseButtons: true,
						  allowOutsideClick: false
						}).then((result) => {


								if (

						  	// ------------------------------------------------------------------------

						    /* CANCELAR EL DISMISS */

						    result.dismiss === Swal.DismissReason.cancel
                            
						    // ------------------------------------------------------------------------

						  ) {

						    // ------------------------------------------------------------------------ 

							

							   this.checkedQr=true;

							// ------------------------------------------------------------------------ 

						  }else{
						  	// ELIMINA LOS DATOS DE LA TABLA
						  	 this.loteSeleccion.nlote=0;
						  	 this.loteSeleccion.id=0;
						  	 this.loteSeleccion.cantidad=0;
       					     tableProductos.clear().draw();
						  	
						  }

               })
   	}
   
  
  },


			// GUARDA O MODIFICA LOS DATOS

			guardar(){

				// ------------------------------------------------------------------------ 

				// INICIAR VARIABLES 

				let me = this;
				var tableProductos = $('#tablaProductos').DataTable();
				var data = [];

				// ------------------------------------------------------------------------ 

				// Llamar Controlador 
				
				if (this.inivarCabecera(tableProductos) === true) {
					me.$bvToast.show('toast-completar-cabecera');
					return;
				}

				// --------------------------------- PREPARAR ARRAY ------------------------

				data = {

					existe: me.existe,
					codigo: me.codigo_remision,
					tab_unica: me.tab_unica,
					monedaSistema: me.monedaCodigo,
					usuario: me.usuarioId,
					mayorista: me.checkedMayorista
				}

				// ------------------------------------------------------------------------ 

				// ACTIVAR LOADING 

				me.procesar = true;

				// ------------------------------------------------------------------------ 

				Common.guardarRemisionCommon(tableProductos.rows().data().toArray(), data).then(data => {
				    
					if (data.response === true) {

						// ------------------------------------------------------------------------

						// DESACTIVAR LOADING

						me.procesar = false;

						// ------------------------------------------------------------------------

						Swal.fire(
						  	'¡Guardado!',
	                     	'¡Se ha guardado correctamente la Nota de Remisión!',
	                     	'success'
						)

						me.procesar = false;

						this.remisionPDF();

						this.limpiar();


						// .then((result) => {

						//   if (result.value) {

						//   	// ------------------------------------------------------------------------ 

						//   	// SI DA OK LO REDIRIJE A MOSTRAR  

						//     this.$router.push('/move1');

						//     // ------------------------------------------------------------------------ 

						//    }

						// });

					} else if (data.response === false) {

						// ------------------------------------------------------------------------ 

						// DESACTIVAR LOADING 

						me.procesar = false;

						// ------------------------------------------------------------------------

						// MOSTRAR SWAL DE PRODUCTOS NO GUARDADOS 

						Swal.fire({
						  title: '¡No se ha guardado correctamente!',
						  text: "¡Algunos productos no se registraron correctamente!",
						  icon: 'warning',
						  showCancelButton: true,
						  confirmButtonText: '¡Aceptar!',
						  cancelButtonText: '¡Ver productos!',
						  reverseButtons: true,
						  allowOutsideClick: false
						}).then((result) => {

						  // if (result.value) {

						  // 	// ------------------------------------------------------------------------

						  // 	// REDIRIGIR A LA PAGINA MOSTRAR

						  //    this.$router.push('/tr1');

						  //    // ------------------------------------------------------------------------

						  // } else 
						  if (

						  	// ------------------------------------------------------------------------

						    /* CANCELAR EL DISMISS */

						    result.dismiss === Swal.DismissReason.cancel

						    // ------------------------------------------------------------------------

						  ) {

						    // ------------------------------------------------------------------------ 

							// SI EL TAMAÑO DEL ARRAY SUPERA CERO SIGNIFICA QUE HAY PRODUCTOS SIN GUARDAR

							me.no_registrados = data.productos;
						
							// ------------------------------------------------------------------------ 

							// MOSTRAR MODAL CON LOS PRODUCTOS 

							$('#modal_no_registrados').modal('show');

							// ------------------------------------------------------------------------ 

						  }
						})

						// ------------------------------------------------------------------------ 

					}else{

	                   	Swal.fire(
	                     	'¡Error!',
	                     	data.statusText,
	                     	'warning'
	                    )
	                    me.procesar = false;
	                }
           		
           			// MOSTRAR ERROR
           			
           		}).catch((err) => {
	                console.log(err);
	                this.mostrar_error = true;
	                this.mensaje = err;
	                me.procesar = false;
             	});

				// ------------------------------------------------------------------------ 
			},

			modificarRemision(){

	        	// ------------------------------------------------------------------------

	        	// INICIAR VARIABLES 

				let me = this;
				var tableProductos = $('#tablaProductos').DataTable();
				var data = [];
				var codigo = this.codigo_remision;

				// ------------------------------------------------------------------------ 

				// Llamar Controlador 
				
				if (this.inivarCabecera(tableProductos) === true) {
					me.$bvToast.show('toast-completar-cabecera');
					return;
				}

				// ------------------------------------------------------------------------ 

				// ------------------------------------------------------------------------ 

				// PREPARAR ARRAY 

				data = {

						existe: false,
						codigo: me.codigo_remision,
						tab_unica: me.tab_unica,
						monedaSistema: me.monedaCodigo,
						usuario: me.usuarioId,
				}

				// ------------------------------------------------------------------------ 

				// MOSTRAR MENSAJE PARA MODIFICAR 
				
				Swal.fire({
					title: '¿Estás seguro?',
					text: "¡Modificar la Nota de Remision " + codigo + "!",
					type: 'warning',
					showLoaderOnConfirm: true,
					showCancelButton: true,
					confirmButtonColor: '#d33',
					cancelButtonColor: '#3085d6',
					confirmButtonText: '¡Sí, modificalo!',
					cancelButtonText: 'Cancelar',
					preConfirm: () => {
						this.procesar = true;
					    return Common.modificarRemisionCommon(tableProductos.rows().data().toArray(), data).then(data => {
					    if (data.response !== true) {
					        throw new Error(data.statusText);
					        this.procesar = false;
					    }
					  	return data.response;
						}).catch(error => {
					        Swal.showValidationMessage(
					          `Request failed: ${error}`
					        )
					        this.procesar = false;
					    });
					}

					}).then((result) => {
					  if (result.value) {
					  	Swal.fire(
							      '¡Modificado!',
							      '¡Se ha modificado correctamente la Nota de Remision!',
							      'success'
						)
						this.procesar = false;
						this.remisionPDF();
						this.limpiar();
					  }
				});

				// ------------------------------------------------------------------------

        	},

        	controlador(){
        		let me=this;
        		if (me.seleccionTamaño==='2') {
        			if(me.proveedor.nombre==='' || me.proveedor.nombre.length===0){
						me.validarProveedor.nombre= true;
						me.proveedor.controlar=false;
					}else{
						me.validarProveedor.nombre=false;
					}

					if(me.proveedor.direccion==='' || me.proveedor.direccion.length===0){
						me.validarProveedor.direccion= true;
						me.proveedor.controlar=false;
					}else{
						me.validarProveedor.direccion=false;
					}

					if(me.proveedor.ciudad==='' || me.proveedor.ciudad.length===0){
						me.validarProveedor.ciudad= true;
						me.proveedor.controlar=false;
					}else{
						me.validarProveedor.ciudad=false;
					}

					if(me.proveedor.telefono==='' || me.proveedor.telefono.length===0){
						me.validarProveedor.telefono= true;
						me.proveedor.controlar=false;
					}else{
						me.validarProveedor.telefono=false;
					}

					if(me.proveedor.razon==='' || me.proveedor.razon.length===0){
						me.validarProveedor.razon= true;
						me.proveedor.controlar=false;
					}else{
						me.validarProveedor.razon=false;
					}

					if(me.proveedor.fax==='' || me.proveedor.fax.length===0){
						me.validarProveedor.fax= true;
						me.proveedor.controlar=false;
					}else{
						me.validarProveedor.fax=false;
					}

					if(me.proveedor.ruc==='' || me.proveedor.ruc.length===0){
						me.validarProveedor.ruc= true;
						me.proveedor.controlar=false;
					}else{
						me.validarProveedor.ruc=false;
					}

					if(me.proveedor.cantidad==='' || me.proveedor.cantidad.length===0){
						me.validarProveedor.cantidad= true;
						me.proveedor.controlar=false;
					}else{
						me.validarProveedor.cantidad=false;
					}
					return me.proveedor.controlar;
        		}
	        		
        	},

			inivarCabecera(tablaEnviada){

				// ------------------------------------------------------------------------

				// INICIAR VARIABLES 

				let me = this;
				var tabla = tablaEnviada.rows().data();


	            // ------------------------------------------------------------------------

	            // SI LA TABLA ESTA VACIA DEVUELVE TAMBIEN TRUE 

	            if(me.nombreUsuario.length === 0){

	            	me.validar.nombreUsuario = true;
	            	return true;
	            }else{
	            	me.validar.nombreUsuario = false;
	            }
	   
	            if (tabla.length === 0){
	            	return true;
	            }

	            // ------------------------------------------------------------------------

	            // RETORNAR VALOR 
	           
	            return false;

	            // ------------------------------------------------------------------------

			},

			conseguir_id(lote_id){

				// ------------------------------------------------------------------------

				// INICIAR VARIABLES 
		           console.log(this.loteSeleccion);
	            // ------------------------------------------------------------------------

			},
			
			eliminar(){

				// GUARDAR LOS DATOS EN UNA VARIABLE

				var eliminar = {

    				codigo: this.codigo_remision,
    				existe: this.existe
    			}


    			// LLAMAR A UNA FUNCION PARA ELIMINAR LOS DATOS

    			Common.eliminarNotaRemCommon(eliminar).then(data => {

    				// MENSAJE DE CONFIRMACION O ERROR

    				if(data.response===true){
	                  	Swal.fire(
		                    '¡Eliminado!',
		                    '¡Se ha eliminado correctamente la Nota de Remisión!',
		                    'success'
	                  	)
    					this.limpiar();
	               	}else{
		                Swal.fire(
		                    '¡Error!',
		                    data.statusText,
		                    'warning'
		                )
	               	}

	               	// MENSAJE DE ERROR EN LA FUNCION COMUN

    			}).catch((err) => {
	                console.log(err);
	                this.mostrar_error = true;
	                this.mensaje = err;
              	});

			},

			// LIMPIA TODO EL FORMULARIO

			limpiar(){

				this.moneda = '1';
				this.nombreUsuario= '';
				this.producto.codigoProd = '';
				this.producto.descripcion = '';
				this.producto.precioUnitario = '';
				this.producto.cantidadExistente = '';
				this.producto.linea = '';
				this.codigo_remision = '';
				this.producto.cantidad = '';
				this.validar.codigoProd = false;
				this.validar.cantidad = false;
				this.validar.codigo = false;
				this.validar.cantidadExistente = false;
				this.validar.precioUnitario = false;
				this.validar.descripcion = false;
				this.validar.linea = false;
				this.ivaProducto = '';
				this.btnguardar = true;
				this.existe = false;
				this.checkedMayorista = false;
				this.usuarioId = '';
	        	var tableProductos = $('#tablaProductos').DataTable();
	        	tableProductos.clear().draw();
	        	this.nuevaNotaDeRemision();
	        	this.procesar = false;
	        	this.$refs.componente_textbox_remision.recargar();
			},

			nuevaNotaDeRemision(){

				Common.nuevaNotaCommon().then(data=> {

					if(data !== 1){

		        		this.codigo_remision = data.remision[0].CODIGO;

					}else{
						this.codigo_remision = 1;
					}

		        });	

			},

			mostrarRemision(codigo){

				this.codigo_remision = codigo;
				this.existe = true;
				this.btnguardar = false;

				var tableProductos = $('#tablaProductos').DataTable();
	        	tableProductos.clear().draw();

	        	// ------------------------------------------------------------------------

	        	// INICIAR VARIABLES 

	        	let me = this;
	        	var stock = 0;

	        	// ------------------------------------------------------------------------

	        	// SE ENCARGA DE COMPROBAR SI EXISTE CODIGO EN LA URL PARA PODER CARGAR UNA REMISION EXISTENTE
	        	
	        	if (codigo !== undefined) {

	        		// ------------------------------------------------------------------------

	        		// OCULTAR BOTON GUARDAR 

	        		me.btnguardar = false;

	        		// ------------------------------------------------------------------------

	        		// AGREGAR CABECERA, ENVIO CERO PARA CODIGO ORIGEN PARA ESPECIFICAR QUE
	        		// NECESITO DATOS DE LA PROPIA SUCURSAL DEL USUARIO

	        		Common.obtenerCuerpoRemisionCommon(codigo, 0).then(data => {
	        			data.map(function(x) {
						   
						   // ------------------------------------------------------------------------

						   // CALCULAR STOCK ACTUAL CON POSIBLE ELIMINACION

						   stock = parseFloat(x.STOCK) + parseFloat(x.CANTIDAD);

	        			   // ------------------------------------------------------------------------
	        			   	
						   // EMPEZAR A CARGAR PRODUCTOS EN REMISION 

						   me.agregarFilaTabla(x.CODIGO_PROD, x.DESCRIPCION, x.CANTIDAD, x.PRECIO, x.IVA, x.TOTAL, x.IVA_PORCENTAJE, stock, x.DESCUENTO);
						  
						   // ------------------------------------------------------------------------

						});
	        		});

	        		// ------------------------------------------------------------------------

	        		// AGREGAR CUERPO, ENVIO CERO PARA CODIGO ORIGEN PARA ESPECIFICAR QUE
	        		// NECESITO DATOS DE LA PROPIA SUCURSAL DEL USUARIO

	        		// ------------------------------------------------------------------------

	        	} else {

	        		// ------------------------------------------------------------------------

	        		// OCULTAR BOTON MODIFICAR 

	        		me.btnguardar = true;

	        		// ------------------------------------------------------------------------

	        	}

	        	// ------------------------------------------------------------------------

	        },

			//CARGA LOS DATOS DEL PRODUCTO

			cargarProductos(codigo) {
        	
	            // ------------------------------------------------------------------------

	            // INICIAR VARIABLES

	            let me = this;

	            // ------------------------------------------------------------------------

	            // CONTROLAR SI HAY DATOS EN EL TEXTBOX Y RETORNAR SI NO EXISTE DATO

	            if (codigo.length <= 0) {
	                me.inivarAgregarProducto();
	                return;
	            }	
	            
	            // ------------------------------------------------------------------------
	    			
	    		// SI DATA ES IGUAL A 0 NO ENCONTRO CODIGO INTERNO 
	    		
		        var data = {
		            'codigo': codigo,
		            'monedaSistema': me.monedaCodigo, 
		            'candec': me.candec, 
		            'tab_unica': me.tab_unica,
		            'mayorista': me.checkedMayorista
		        }; 
		        
	            // ------------------------------------------------------------------------

	            // CONSULTAR PRODUCTO DETERMINADO

	            axios.post('/producto', {'data': data, 'Opcion': 1}).then(function (response) {
	                   
	                    if(response.data.producto === 0) {

	                        // *******************************************************************

	                        // MARCAR EN ROJO TEXTBOX SI NO EXISTE PRODUCTO
	                    
	                        me.validar.codigoProd = true;
	                        me.producto.codigoProd = '';
	                        me.producto.descripcion = '';
	                        me.producto.precioUnitario = '';
	                        me.producto.cantidadExistente = '';
	                        me.ivaProducto = '';
	                        me.producto.linea = '';
	                        me.producto.descuento = '';
	                        me.producto.interno='';
	                        me.$refs.compontente_codigo_producto.vaciarDevolver();

	                        // *******************************************************************

	                    } else {

	                        // *******************************************************************

	                        // LLENAR DESCRIPCION DE PRODUCTO
	                     
	                        me.producto.codigoProd = response.data.producto.CODIGO;
	                        me.producto.descripcion = response.data.producto.DESCRIPCION;
	                        me.producto.cantidadExistente  = response.data.producto.STOCK;
	                        me.ivaProducto = response.data.producto.IVA;
	                        me.producto.precioUnitario = response.data.valor;
                            me.producto.interno=response.data.producto.CODIGO_INTERNO;
	                         me.producto.precioMayorista = Common.darFormatoCommon(response.data.producto.PREMAYORISTA,me.candec);
	                        me.producto.linea = response.data.producto.LINEA;
	                        me.producto.moneda= response.data.producto.MONEDA;
                            me.lotes=response.data.lote;
                            me.$refs.cantidad_ticket.focus();
	                        if(me.checkedMayorista === true){

	                        	me.producto.descuento = 0;
	                        }else{
	                        	me.producto.descuento = response.data.producto.DESCUENTO;
	                        }


							// *******************************************************************

		                    // AGREGAR PRODUCTO AUTOMATICAMENTE 

					    	// if (me.switch_un_producto === true) {
					    	// 	me.producto.cantidad = 1;
					    	// 	me.agregarProductoRemision();
					    	// 	me.producto.codigoProd = '';
					    	// }

				            // *******************************************************************

	                        me.validar.codigoProd = false;

	                        // *******************************************************************
	                    }
	            });

	    		// ------------------------------------------------------------------------

	        },

			agregarProductoRemision(){

	            // ------------------------------------------------------------------------

	            // INICIAR VARIABLES


	            let me = this;
	            var tableProductos = $('#tablaProductos').DataTable();
	            var precio = 0;
	            var iva = 0;
	            var precioConDescuento=0;
	            var descuento = 0;
	              me.$refs.compontente_codigo_producto.vaciarDevolver();
	            // ------------------------------CONTROLADOR------------------------------------------

	            // CONTROLADOR

	            if (me.producto.codigoProd === null  || me.producto.codigoProd.length === 0) {
	                me.validar.codigoProd = true;
	                return;
	            } else {
	                me.validar.codigoProd = false;
	            }

	            // ------------------------------DESCRIPCION------------------------------------------

	            if (me.producto.descripcion === null || me.producto.descripcion.length === 0) {
	                me.validar.descripcion = true;
	                return;
	            } else {
	                me.validar.descripcion = false;
	            }

	            // ------------------------------PRECIO------------------------------------------

	            if (me.producto.precioUnitario === null || me.producto.precioUnitario.length === 0  || me.producto.precioUnitario === '0') {
	                me.validar.precioUnitario = true;
	                return;
	            } else {
	                me.validar.precioUnitario = false;
	            }

	            // ------------------------------CANTIDAD------------------------------------------

	            if ( me.producto.cantidad === null ||  me.producto.cantidad.length === 0  || me.producto.cantidad === '0') {
	                me.validar.cantidad = true;
	                return;
	            } else {
	                me.validar.cantidad = false;
	            }
	            // ------------------------------DESCUENTO------------------------------------------

	                  if((me.loteSeleccion.id==='0' || me.loteSeleccion.id===0) && me.checkedQr===true){
                 	me.validar.lote=true;
                     return;
                 }else{
                 	me.validar.lote=false
                 }


	            // ------------------------------------------------------------------------

	            //  TOTAL

	            /*precio = Common.multiplicarCommon(me.producto.cantidad, me.producto.precioUnitario, me.candec);

	            if(me.producto.descuento > 0 && me.checkedMayorista === false){

	            	descuento = Common.descuentoCommon(me.producto.precioUnitario, me.producto.descuento, me.candec);
	            	precioConDescuento = Common.restarCommon(me.producto.precioUnitario, descuento, me.candec);
	            	precio = Common.multiplicarCommon(me.producto.cantidad, precioConDescuento, me.candec);

	            }else{

	            	precioConDescuento = me.producto.precioUnitario;
	            }*/
	         
	            // ------------------------------------------------------------------------

	            //  DAR FORMATO AL RESULTADO FINAL PARA MOSTRAR EN DATATABLE 

	           /* precio = Common.darFormatoCommon(precio, me.candec);*/

	            // ------------------------------------------------------------------------

	            // CALCULAR IVA
	            
	          /*  iva = Common.calcularIVACommon(precio, me.ivaProducto, me.candec);*/

	            // ------------------------------------------------------------------------

	            // CARGAR DATO EN TABLA

	            me.agregarFilaTabla(me.producto.codigoProd, me.producto.descripcion, me.producto.cantidad,iva, me.producto.precioUnitario,me.producto.precioMayorista,me.producto.cantidadExistente,me.producto.interno,me.producto.moneda );
	          



	            // ------------------------------------------------------------------------

	            // VACIAR TEXTBOX AGREGAR PRODUCTO

	            me.inivarAgregarProducto();

	            // ------------------------------------------------------------------------

		        // LLAMAR AL METODO HIJO 

		        this.producto.codigoProd = '';
		        // this.$refs.compontente_codigo_producto.vaciarDevolver();

		        // ------------------------------------------------------------------------

	        },

	        agregarFilaTabla(codigo, descripcion, cantidad,lote, precio,mayorista, stock,interno, moneda){

	        	// ------------------------------------------------------------------------

	        	//	INICIAR VARIABLES 

	        	let me = this;
	        	var tableProductos = $('#tablaProductos').DataTable();
	        	var productoExistente = [];
	        	var cantidadNueva = 0;

	        	// ------------------------------------------------------------------------

	            // REVISAR SI EXISTE VALORES REPETIDOS EN TABLA  
	            // LA OPCION 1 ES PARA DEVOLVER SOLO TRUE O FALSE SI EXISTE O NO
	            // LA OPCION 2 ES PARA DEVOLVER MAS DATOS DEL PRODUCTO 
	            if(me.checkedQr===true){
 				productoExistente = Common.existeProductoImprimirEtiquetaQrDataTableCommon(tableProductos, codigo, 2,me.loteSeleccion.nlote);
	            }else{
	            productoExistente = Common.existeProductoImprimirEtiquetaDataTableCommon(tableProductos, codigo, 2);
	            }

	           
	           
	            if (productoExistente.respuesta == true) {

	            	// ------------------------------------------------------------------------

	            	// SUMAR LA CANTIDAD E IVA

	            	cantidadNueva = parseFloat(productoExistente.cantidad) + parseFloat(cantidad);

	            	// ------------------------------------------------------------------------

	            	// REVISAR SI CANTIDAD SUPERA STOCK

/*	            	if (Common.cantidadSuperadaCommon(cantidadNueva, productoExistente.stock)) {
	            		me.validar.cantidad = true;
		            	me.$bvToast.show('toast-cantidad-superada');
		            	return;
	            	} else {
	            		me.validar.cantidad = false;
	            	}*/

	            	// ------------------------------------------------------------------------

	            	// EDITAR CANTIDAD PRODUCTO 

	            	me.editarCantidadProducto(tableProductos, cantidadNueva, productoExistente.precio, productoExistente.stock, productoExistente.row);
	            	return;

	            	// ------------------------------------------------------------------------

	            } else {
	            	me.validar.codigoProd= false;
	            }

	            // ------------------------------------------------------------------------

	            // REVISAR SI CANTIDAD SUPERA STOCK

/*	            if (Common.cantidadSuperadaCommon(cantidad, stock)) {
		            me.validar.cantidad = true;
		            me.$bvToast.show('toast-cantidad-superada');
		            return;
		        } else {
		            me.validar.cantidad = false;
		        }*/

	        	// AGREGAR FILAS 

	        	 tableProductos.rows.add( [ {
			                    "ITEM": '',
			                    "CODIGO":   codigo,
			                    "CODIGO_INTERNO":interno,
			                    "DESCRIPCION":     descripcion,
			                  
			                    "CANTIDAD": cantidad,
			                      "LOTE":      me.loteSeleccion.nlote,
			                    "PRECIO":    precio,
			                   "PRECIO_MAYORISTA":    mayorista,
			                    "ACCION":    "&emsp;<a role='button' id='mostrarProductoFila' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a> &emsp;<a role='button' id='editarModal' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>&emsp;<a role='button'  title='Eliminar'><i id='eliminarModal' class='fa fa-trash text-danger' aria-hidden='true'></i></a>",
			                  
			                    "STOCK": stock,
			                    "ID_LOTE": me.loteSeleccion.id,
			                    "MONEDA": moneda

			                   
			                } ] )
			     .draw();

			    // ------------------------------------------------------------------------ 

	            // AGREGAR INDEX A LA TABLA

	            tableProductos.on( 'order.dt search.dt', function () {
	                tableProductos.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	                    cell.innerHTML = i+1;
	                } );
	            } ).draw();

	            // ------------------------------------------------------------------------

	            // AJUSTAR COLUMNAS DE ACUERDO AL DATO QUE CONTIENEN

	            tableProductos.columns.adjust().draw();

	            // ------------------------------------------------------------------------

	        },

	        formatoEditarPrecio(){

				// ------------------------------------------------------------------------

				// DAR FORMATO A LOS VALORES DE MODAL EDITAR 

				this.editarCantidad = Common.darFormatoCommon(this.editarCantidad, 0);

				// ------------------------------------------------------------------------

			},

	        inivarAgregarProducto(){

				// ------------------------------------------------------------------------

				// VACIAR TEXTBOX 

	            this.producto.linea = '';
	            this.producto.cantidad = '';
				this.producto.codigoProd = '';
				this.producto.descripcion = '';
				this.loteSeleccion.nlote=0;
				this.loteSeleccion.id=0;
				this.loteSeleccion.cantidad=0;
				this.producto.descuento = '';
	            this.producto.precioUnitario = '';
	            this.producto.precioMayorista = '';
	            this.producto.cantidadExistente = '';
	            this.$refs.compontente_codigo_producto.vaciarDevolver();

	            // ------------------------------------------------------------------------   

			},

			editarCantidadProducto(tabla, cantidad, precio_producto, stock, row){

	        	// ------------------------------------------------------------------------

	        	// INICIAR VARIABLES

	        	let me = this;
	        	var precio = 0;
	        	

	        	// ------------------------------------------------------------------------

	            // PROHIBIR EDITADO SI CANTIDAD O PRECIO ES CERO

	            if (cantidad === '0' || precio_producto === '0') {
	                me.$bvToast.show('toast-editar-cero');
	                return;	
	            }

	            // ------------------------------------------------------------------------

	            // PROHIBIR EDITADO SI CANTIDAD SUPERA STOCK
	                	
/*	            if (parseFloat(Common.quitarComaCommon(cantidad)) > parseFloat(Common.quitarComaCommon(stock))) {
	                me.$bvToast.show('toast-cantidad-superada');
	                return;	
	            }*/

	            // ------------------------------------------------------------------------

	            // CARGAR LO EDITADO

	            tabla.cell(row, 5).data(cantidad).draw();
	            /*tabla.cell(row, 5).data(precio_producto).draw();*/

	            // ------------------------------------------------------------------------
	                    
	            // CALCULAR PRECIO TOTAL

	      
	            // ------------------------------------------------------------------------

	            // CALCULO IVA 
			            
			  

			    // ------------------------------------------------------------------------

			    // CARGAR PRECIO CALCULADO 

	   /*         tabla.cell(row, 6).data(precio_producto).draw();*/

	            // ------------------------------------------------------------------------

	            // CARGAR IVA CALCULADO 

	          

	           	// ------------------------------------------------------------------------

	            // LLAMAR TOAST MODIFICADO

	            me.$bvToast.show('toast-producto-modificado');

	            // ------------------------------------------------------------------------

	        },

	        cargarCodigo(valor){

				this.usuarioId = valor;
			},

			cargarMoneda(valor){

				this.moneda = valor.toString();
			},

			enviar_id(id){

          		this.usuarioId = id;
        	},

        	enviar_nombre(data){

	           this.nombreUsuario = data;

	        },

	        cantidadDecimal(valor){

	        	this.decimal = valor;
	        },



	        Imprimir_QR(){
				let me = this;
           		var tableProductos = $('#tablaProductos').DataTable();
				// ------------------------------------------------------------------------ 
                    
				Common.generarPdfQrProductoCommon(tableProductos.rows().data().toArray()).then(response => {
					var reader = new FileReader();
					reader.readAsDataURL(new Blob([response])); 
					reader.onloadend = function() {
					     var base64data = reader.result;
					     base64data = base64data.replace("data:application/octet-stream;base64,", "");
					    return me.imprimir(base64data);
					}
				});
			},





			Imprimir_barcode(){
				let me = this;
				if(me.controlador()===false){
					me.proveedor.controlar=true;
					return;
				}

			    var precio=me.seleccionPrecio;
				var tamañoEtiqueta = me.seleccionTamaño;
				var tipoCodigo = me.seleccionCodigo;
				var tipoMoneda = me.seleccionMoneda;
				var cotizacion = me.ingresarCotizacion;
				var proveedorA = me.proveedor;
				var tableProductos = $('#tablaProductos').DataTable();
				var delayInMilliseconds = 3000;
				console.log( cotizacion);
	        	
				
				Common.generarPdfBarcodeProductoCommon(tableProductos.rows().data().toArray(), tamañoEtiqueta, tipoCodigo, precio, proveedorA, tipoMoneda, me.ingresarCotizacion).then(response => {	
					var reader = new FileReader();
					reader.readAsDataURL(new Blob([response])); 
					reader.onloadend = function() {
					    var base64data = reader.result;
					    base64data = base64data.replace("data:application/octet-stream;base64,", "");

					    return me.imprimir(base64data);
					}
				});

				

			
			},




			// Imprimir_barinterno(){

			// 	let me = this;
   //         		var tableProductos = $('#tablaProductos').DataTable();
			// 	// ------------------------------------------------------------------------ 
                    
			// 	Common.generarPdfBarinternoProductoCommon(tableProductos.rows().data().toArray()).then(response => {

			// 			var reader = new FileReader();
			// 			reader.readAsDataURL(new Blob([response])); 
			// 			reader.onloadend = function() {
			// 			     var base64data = reader.result;
			// 			     base64data = base64data.replace("data:application/octet-stream;base64,", "");
			// 			    return me.imprimir(base64data);
			// 			 }

			// 	});
			// },





		imprimir(base64) {

				let me = this;
				var tableProductos = $('#tablaProductos').DataTable();

				qz.websocket.connect().then(function() {
					if(me.seleccionTamaño==='1'){
						return qz.printers.find("GONDOLA"); // Pass the printer name into the next Promise
					}else{
						return qz.printers.find("ETIQUETA"); // Pass the printer name into the next Promise
					} 
				}).then(function(printer) {

					var config = qz.configs.create(printer);
					var data = [{ 
						type: 'pixel',
           				format: 'pdf',
           				flavor: 'base64',
						data: base64
					}];

					return qz.print(config, data).then(function() {
					   	qz.websocket.disconnect();
					   	Swal.close();
					   	tableProductos.clear().draw();

					});
						 
					   
				}).catch(function(e) { console.error(e); });

			},

	        remisionPDF(){

	        	var data = {

    				codigo: this.codigo_remision
    			}

				Common.generarPdfRemisionCommon(data);
	        }

		},

		mounted(){

			// INICIAR VARIABLES

            let me = this;
            var precio = 0;
            var iva = 0;
            me.$refs.compontente_codigo_producto.vaciarDevolver();
		    me.codigo_remision = me.nuevaNotaDeRemision();
		 	

			 // INICIAR EL DATATABLE PRODUCTOS 

                // ------------------------------------------------------------------------

                var tableProductos = $('#tablaProductos').DataTable({
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "dom": "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
						"<'row'<'col-sm-12'tr>>" +
						"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
				        "buttons": [
				            { extend: 'copy', text: '<i class="fa fa-copy"></i>', titleAttr: 'Copiar', className: 'btn btn-secondary' },
				        	{ extend: 'excelHtml5', text: '<i class="fa fa-file"></i>', titleAttr: 'Excel', className: 'btn btn-success' },
				            { extend: 'pdfHtml5', text: '<i class="fa fa-file"></i>', titleAttr: 'Pdf', className: 'btn btn-danger' }, 
				            { extend: 'print', text: '<i class="fa fa-print"></i>', titleAttr: 'Imprimir', className: 'btn btn-secondary', title: 'NotaDeRemision', messageTop: 'Productos registrados para la impresion' }
				        ], 
				         "columnDefs": [
				           
				            {
				                "targets": [ 9 ],
				                "visible": false,
				                "searchable": false
				            },
				            {
				                "targets": [ 10 ],
				                "visible": false,
				                "searchable": false
				            },
							{
				                "targets": [ 11 ],
				                "visible": false,
				                "searchable": false
				            }

				           
				        ], 
                        "columns": [
                            { "data": "ITEM" },
                            { "data": "CODIGO" },
                            { "data": "CODIGO_INTERNO" },
                            { "data": "DESCRIPCION" },
                            { "data": "LOTE" },
                            { "data": "CANTIDAD" },
                            { "data": "PRECIO" },
                            { "data": "PRECIO_MAYORISTA" },
                            { "data": "ACCION" },
                            { "data": "STOCK" },
                            { "data": "ID_LOTE" },
                            { "data": "MONEDA" },
                        ],

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
					                Common.darFormatoCommon(precio, me.candec)
					           );

						      // *******************************************************************

						  });

						  // *******************************************************************

						  // TOTAL 

						  api.columns('.totalColumna', {
						    
						  }).every(function() {
						    var sum = this
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

						    $( api.columns('.totalColumna').footer() ).html(
					                Common.darFormatoCommon(sum, me.candec)
					        ); 

					        // CALCULO TOTAL

						  	// me.total = Common.darFormatoCommon(sum, me.candec); 

						    // *******************************************************************

						  });

						  // *******************************************************************

						  // TOTAL IVA 
/*
						  api.columns('.ivaColumna', {
						    
						  }).every(function() {
						    var ivaSum = this
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

						      $( api.columns('.ivaColumna').footer() ).html(
					                Common.darFormatoCommon(ivaSum, me.candec)
					           ); 

					           // CALCULO IVA
						  		// me.iva = Common.darFormatoCommon(ivaSum, me.candec);

						      // *******************************************************************

						  });*/

						  // *******************************************************************

						  // CALCULO SUBTOTAL

						  // me.subtotal = Common.restarCommon(me.total, me.iva, me.candec);

						  // *******************************************************************
						}      
                });
				// ------------------------------------------------------------------------

                // ASIGNAR INLINE BUTTONS 

                tableProductos.buttons().container()
    			.appendTo( $('div.eight.column:eq(0)', tableProductos.table().container()) );

				// ------------------------------------------------------------------------

            	// DESPUES DE INICIAR LA TABLA LLAMAR A LA CONSULTA PARA CARGAR CABECERA Y CUERPO 

            	// me.mostrar(me.$route.params.id);

            	// ------------------------------------------------------------------------

                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------
                //                  EDITAR FILA DATATABLE
                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------

                //  CUANDO SE HACE CLICK EN LA FILA CARGAR LOS DATOS EN LAS VARIABLES PARA EDITAR

                $('#tablaProductos').on('click', 'tbody tr', function() {

                    // *******************************************************************
                    
                    // CARGAR LOS VALORES A LAS VARIABLES DE EDITAR PRODUCTO

                    me.editarPrecio = tableProductos.row(this).data().PRECIO;
                    me.editarCantidad = tableProductos.row(this).data().CANTIDAD;  
                    me.editarCodigo = tableProductos.row(this).data().CODIGO;
                  
                    me.editarStock = tableProductos.row(this).data().STOCK;
                 
                    me.editarRow = tableProductos.row( this );

                    // *******************************************************************

                });

                // ------------------------------------------------------------------------

                // MOSTRAR MODAL EDITAR

                $('#tablaProductos').on('click', 'tbody tr #editarModal', function() {

                    // *******************************************************************

                    // ABRIR EL MODAL
                     
                    $('.editar-producto-modal').modal('show');

                    // *******************************************************************

                });

                // ------------------------------------------------------------------------

                // EDITAR FILA

                $('#editarFila').on('click', function() {

                	// *******************************************************************

                	// EDITAR 

                	me.editarCantidadProducto(tableProductos, me.editarCantidad, me.editarPrecio, me.editarStock, me.editarRow);

                	// *******************************************************************

                    // OCULTAR MODAL EDITAR 

                    $('.editar-producto-modal').modal('hide');

                    // *******************************************************************

                });

                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------
                //              FIN EDITAR FILA DATATABLE
                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------

                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------
                //                  ELIMINAR FILA DATATABLE
                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------

                // MOSTRAR SWEET ELIMINAR

                $('#tablaProductos').on('click', 'tbody tr #eliminarModal', function() {

                    // *******************************************************************

                    // ABRIR EL SWEET ALERT

                    Swal.fire({
					  title: '¿Estas seguro?',
					  text: "¡Eliminar el producto " + me.editarCodigo + "!",
					  type: 'warning',
					  showLoaderOnConfirm: true,
					  showCancelButton: true,
					  confirmButtonColor: '#d33',
					  cancelButtonColor: '#3085d6',
					  confirmButtonText: 'Sí, ¡eliminalo!',
					  cancelButtonText: 'Cancelar'
					}).then((result) => {
					  if (result.value) {

					  	// *******************************************************************

	                    // ELIMINAR 
	                    
		                tableProductos.row($(me.editarRow)).remove().draw(); 

	                    // *******************************************************************

					  	Swal.fire(
							      '¡Eliminado!',
							      '¡Se ha eliminado el producto de la tabla!',
							      'success'
						)

						// *******************************************************************

					  }
					})

                    // *******************************************************************

                });

                // ------------------------------------------------------------------------

                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------
                //              FIN ELIMINAR FILA DATATABLE
                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------

                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------
                //              MOSTRAR PRODUCTO FILA DATATABLE
                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------

                // MOSTRAR MODAL ELIMINAR

                $('#tablaProductos').on('click', 'tbody tr #mostrarProductoFila', function() {

                    // *******************************************************************

		        	// OBTENER DATOS DEL PRODUCTO
		        	
		        	axios.post('/producto', {'codigo': (tableProductos.row($(this).parents('tr')).data().CODIGO), 'Opcion': 2}).then(function (response) {
						me.productoImagen = response.data.productoImagen;
					});

                    // *******************************************************************

                    // ABRIR EL MODAL

                    $('.mostrar-producto-modal').modal('show');

                    // *******************************************************************

                });

                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------
                //              FIN MOSTRAR FILA DATATABLE
                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------

            // -----------------------------------------------------------------------
        }
    }

</script>