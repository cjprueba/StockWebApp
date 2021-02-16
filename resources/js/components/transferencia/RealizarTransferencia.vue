<template>
	<div class="container-fluid  bg-light">

	<!-- 	<div v-if="$can('transferencia.crear')" class="mt-4"> -->

			<!-- ------------------------------------------------------------------------------------- -->

			<!-- TITULO  -->
			
			<vs-divider>
				Realizar Transferencia
			</vs-divider>
		
	        <!-- ------------------------------------------------------------------------------------- -->

	        <!-- SWITCHS -->

	       <div class="col-12">

	        	<!-- ------------------------------------------------------------------------------------- -->

	        	<!-- UN PRODUCTO -->
             <form class="form-inline">
			   	<div class="my-1">
					<div class="custom-control custom-switch">
						<input type="checkbox" class="custom-control-input" id="customControlAutosizing" v-model="switch_un_producto">
						<label class="custom-control-label" for="customControlAutosizing">Un Producto</label>
						
					</div>
					
				</div>


				<!-- ------------------------------------------------------------------------------------- -->

	        	<!-- CONSIGNACION -->
	        	 <div class="ml-2 my-1">
					<div class="custom-control custom-switch">
						<input type="checkbox" class="custom-control-input" v-on:click="consignacion" id="consignacionControlAutosizing" v-model="switch_consignacion">
						<label class="custom-control-label" for="consignacionControlAutosizing">Consignación</label>
					</div>
		        </div>
				<!-- ------------------------------------------------------------------------------------- -->
		     </form>

		   </div>
			
			<!-- ------------------------------------------------------------------------------------- -->

		   	<vs-divider position="left">
			 	Transferencia
		   	</vs-divider>

	       	<!-- ------------------------------------------------------------------------------------- -->

			<!-- FORMULARIO  -->

			<div class="col-12 bg-white rounded shadow-sm py-2 p-4">

			  <div class="mt-3">

				<div class="row">

	                <!-- ******************************************************************* -->

					<!-- ORIGEN  -->

					<div class="col-md-1">
							<label for="validationTooltip01">Origen</label>
							<div class="input-group ">
								<div class="input-group-prepend">
									<button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".origen-modal" v-on:click="cargarSucursales(1,0),activarBuscar(1)"><font-awesome-icon icon="search"/></button>
								</div>
								<input class="form-control form-control-sm" type="text" v-model="codigoOrigen" v-on:blur="cargarSucursales(2, 1)" disabled>
							</div>
					</div>	

					<div class="col-md-5">
						<label for="validationTooltip01" >Descripción</label>
						<input class="form-control form-control-sm" v-bind:class="{ 'is-invalid': validarOrigen }" type="text"  v-model="descripcionOrigen" disabled>
					</div>

					<!-- FIN ORIGEN -->

	                <!-- ******************************************************************* -->

					<!-- DESTINO -->

					<div class="col-md-1">
						<label for="validationTooltip01">Destino</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".origen-modal" v-on:click="cargarSucursales(1,0),activarBuscar(2)"><font-awesome-icon icon="search"/></button>
							</div>
							<input tabindex="2" class="form-control form-control-sm" v-model="codigoDestino" v-on:blur="cargarSucursales(2, 2)" type="text" >
						</div>
					</div>	

					<div class="col-md-5">
						<label  for="validationTooltip01">Descripción</label>
						<input class="form-control form-control-sm" v-bind:class="{ 'is-invalid': validarDestino }" type="text" v-model="descripcionDestino" disabled>
					</div>

					<!-- FIN DESTINO -->

	                <!-- ******************************************************************* -->

				</div>

				<div class="row">

	                <!-- ******************************************************************* -->

					<!-- ENVIA -->

					<div class="col-md-1">
						<label class="mt-1" for="validationTooltip01">Envia</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".empleado-modal" v-on:click="activarBuscarEmpleado(1)"><font-awesome-icon icon="search"/></button>
							</div>
							<input tabindex="3" class="form-control form-control-sm" v-model="codigoEnvia" type="text" v-on:blur="cargarEmpleados(1)">
						</div>
					</div>	

					<div class="col-md-5">
						<label class="mt-1" for="validationTooltip01">Descripción</label>
						<input class="form-control form-control-sm" type="text"  v-model="descripcionEnvia" disabled>
					</div>

					<!-- FIN ENVIA -->

	                <!-- ******************************************************************* -->

					<!-- TRANSPORTA -->

					<div class="col-md-1">
						<label class="mt-1" for="validationTooltip01">Transporta</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<button type="button" class="btn btn-secondary btn-sm"  v-on:click="activarBuscarEmpleado(2)" data-toggle="modal" data-target=".empleado-modal"><font-awesome-icon icon="search"/></button>
							</div>
							<input tabindex="4" class="form-control form-control-sm" v-model="codigoTransporta" type="text" v-on:blur="cargarEmpleados(2)">
						</div>
					</div>	

					<div class="col-md-5">
						<label class="mt-1" for="validationTooltip01">Descripción</label>
						<input class="form-control form-control-sm" type="text" v-model="descripcionTransporta"  disabled>
					</div>

					<!-- FIN TRANSPORTA -->

	                <!-- ******************************************************************* -->

				</div>

				<div class="row">

					<div class="col-md-1">
						<label class="mt-1" for="validationTooltip01">Recibe</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<button type="button" class="btn btn-secondary btn-sm"  v-on:click="activarBuscarEmpleado(3)" data-toggle="modal" data-target=".empleado-modal"><font-awesome-icon icon="search"/></button>
							</div>
							<input tabindex="5" class="form-control form-control-sm" v-model="codigoRecibe" type="text" v-on:blur="cargarEmpleados(3)">
						</div>
					</div>	

					<div class="col-md-5">
						<label class="mt-1" for="validationTooltip01">Descripción</label>
						<input class="form-control form-control-sm" type="text" v-model="descripcionRecibe"  disabled>
					</div>	

					<div class="col-md-6">
						<label class="mt-1" for="validationTooltip01">Nro. Caja</label>
						<input tabindex="6" class="form-control form-control-sm" type="text" v-model="nro_caja" v-on:blur="productosCompra">
					</div>

				</div>

					</div>
			</div>

			<!-- FINAL DE FORMULARIO -->

		   <!-- ------------------------------------------------------------------------------------- -->

		   <vs-divider position="left">
			 	Agregar Producto
		   </vs-divider>

		   <!-- ------------------------------------------------------------------------------------- -->
		   
		   <!-- AGREGAR PRODUCTO -->

			<div class="col-12 bg-white rounded shadow-sm py-2 p-4">
		
				<div class="mt-3">	
					<div class="row">
						<div class="col-md-2">
							
								
								<codigo-producto tabIndexPadre=7 @codigo_producto="cargarProductos" ref="compontente_codigo_producto" v-model="codigoProducto"></codigo-producto >

							<!-- </div> -->
						</div>	

						<div class="col-md-4">
							<label for="validationTooltip01">Descripción</label>
							<input class="form-control form-control-sm" type="text" v-model="descripcionProducto" v-bind:class="{ 'is-invalid': validarDescripcionProducto }" disabled>
						</div>

						<div class="col-md-2">
							<label for="validationTooltip01">Precio Unitario</label>
							<input class="form-control form-control-sm" type="text" v-model="precioProducto" v-bind:class="{ 'is-invalid': validarPrecioUnitario }" disabled>
						</div>

						<div class="col-md-2">
							<label for="validationTooltip01">Cantidad</label>
							<input tabindex="8" class="form-control form-control-sm" type="text" v-on:keyup="formatoCantidad" v-on:blur="agregarProductoTransferencia()" v-bind:class="{ 'is-invalid': validarCantidad }" v-on:keyup.prevent.13="agregarProductoTransferencia()" v-model="cantidadProducto">
						</div>


	 					<!-- <div class="col-md-2">
							<label for="validationTooltip01">Cantidad</label>
							<input class="form-control form-control-sm" type="text" v-on:keyup="formatoCantidad" v-on:blur="" v-bind:class="{ 'is-invalid': validarCantidad }" v-model="cantidadProducto">
						</div> -->


						<div class="col-md-2">
							<label for="validationTooltip01">Stock</label>
							<input class="form-control form-control-sm" type="text" v-model="stockProducto" disabled>
						</div>
					</div>
				</div>	
			</div>		
			
			<!-- ------------------------------------------------------------------------------------- -->

		    <!-- MOSTRAR LOADING -->

		    <div class="col-md-12">
				<div v-if="procesar" class="d-flex justify-content-center mt-3">
					Guardando...
		            <div class="spinner-grow text-success" role="status" aria-hidden="true"></div>
		        </div>
	        </div>

			<!-- ------------------------------------------------------------------------ -->

	        <!-- TABLA TRANSFERENCIA -->

			<div class="col-md-12 mt-4">
				<table id="tablaTransferencia" class="display nowrap table table-striped table-bordered table-sm mb-3" style="width:100%">
	                <thead>
	                    <tr>
	                        <th>ITEM</th>
	                        <th class="codigoDeclarados">Codigo Producto</th>
	                        <th>Descripción</th>
	                        <th class="cantidadColumna">Cantidad</th>
	                        <th class="precioColumna">Precio</th>
	                        <th class="ivaColumna">IVA</th>
	                        <th class="totalColumna">Total</th>
	                        <th>Acción</th>
	                        <th>IVA-PORCENTAJE</th>
	                        <th>STOCK</th>
	                    </tr>
	                </thead>
	                <tbody>
	                    
	                </tbody>
	                <tfoot>
	                	<tr>
	                		<th></th>
	                		<th></th>
	                		<th>TOTALES</th>
		                	<th></th>
		                	<th></th>
		                	<th></th>
		                	<th></th>
		                	<th></th>
	                	</tr>
	                </tfoot>	
	            </table>
			</div>

			<!-- ******************************************************************* -->

			<!-- SEPARADOR -->

			<hr>
			
			<!-- ******************************************************************* -->

			<!-- BOTONES ABM -->

			<div class="col-md-12 mt-3 mb-3">
					<div class="text-right" v-if="btnguardar">
						<button v-on:click="guardarTransferencia()" class="btn btn-dark px-4 rounded-pill" id="guardar">Guardar</button>
					</div>
					<div class="text-right" v-else>
						<button v-on:click="modificarTransferencia()" class="btn btn-warning px-4 rounded-pill" id="modificar">Modificar</button>
					</div>	
			</div>

			<!-- ******************************************************************* -->

			<!-- ------------------------------------------------------------------------ -->

			<!-- MODALES -->

			<!-- ******************************************************************* -->

			<!-- MODAL ORIGEN -->

					<div class="modal fade origen-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
					  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
					    <div class="modal-content">
					      <div class="modal-header">
					        <h5 class="modal-title" id="exampleModalCenterTitle">Sucursales: </small></h5>
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					          <span aria-hidden="true">&times;</span>
					        </button>
					      </div>
					      <div class="modal-body">
					        	<table id="tablaModal" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
							        <thead>
							            <tr>
							                <th>Codigo</th>
							                <th>Nombre Sucursal</th>
							                <th>Razon Social</th>
							                <th>Dirección</th>
							                <th>RUC</th>
							            </tr>
							        </thead>
							        <tbody>
							            <tr v-for="(sucursal, index) in sucursales" v-on:click="seleccionOrigen(sucursal)" data-dismiss="modal">
							                <td href="#">{{sucursal.CODIGO}}</td>
							                <td href="#">{{sucursal.DESCRIPCION}}</td>
							                <td href="#">{{sucursal.RAZON_SOCIAL}}</td>
							                <td href="#">{{sucursal.DIRECCION}}</td>
							                <td href="#">{{sucursal.RUC}}</td>
							            </tr>
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

			<!-- MODAL EMPLEADOS -->

					<div class="modal fade empleado-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
					  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
					    <div class="modal-content">
					      <div class="modal-header">
					        <h5 class="modal-title" id="exampleModalCenterTitle">Empleados: </small></h5>
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					          <span aria-hidden="true">&times;</span>
					        </button>
					      </div>
					      <div class="modal-body">
					        	<table id="tablaModalEmpleados" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
							        <thead>
							            <tr>
							                <th>Codigo</th>
							                <th>Nombre</th>
							                <th>CI</th>
							                <th>Dirección</th>
							                <th>SUCURSAL</th>
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

	        <!-- MODAL EDITAR PRODUCTO TRANSFERENCIA -->

	                <div class="modal fade editar-producto-transferencia-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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

	        <!-- ******************************************************************* -->

	        <!-- MODAL ELIMINAR PRODUCTO TRANSFERENCIA -->

	                <div class="modal fade eliminar-producto-transferencia-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	                  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
	                    <div class="modal-content">
	                      <div class="modal-header">
	                        <h5 class="modal-title text-primary text-center" >¿ Eliminar <font-awesome-icon icon="barcode"/> {{editarCodigo}} ?</h5>
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

	        <!-- ******************************************************************* -->

	        <!-- MODAL MOSTRAR PRODUCTO TRANSFERENCIA -->

	                <div class="modal fade mostrar-producto-transferencia-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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

	        <!-- ******************************************************************* -->

	        <!-- MODAL PRODUCTOS SIN REGISTRAR -->

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
			        <button type="button" class="btn btn-secondary" v-on:click="mostrarTransferencias" data-dismiss="modal">Cerrar</button>
			      </div>
			    </div>
			  </div>
			</div>
			
			<!-- ******************************************************************* -->

			<!-- FINAL MODALES --> 

			<!-- ------------------------------------------------------------------------ -->

			<!-- TOASTS -->

			<!-- ******************************************************************* -->

			<!-- TOAST CODIGO PRODUCTO REPETIDO -->

			<b-toast id="toast-codigo-repetido" variant="warning" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">Error !</strong>
		          <small class="text-muted mr-2">repetido</small>
		        </div>
		      </template>
		      Este producto ya existe en la tabla, favor de elegir otro o modificarlo !
		    </b-toast>

		    <!-- ******************************************************************* -->

		    <!-- TOAST PRODUCTO TRANSFERENCIA MODIFICADO -->

			<b-toast id="toast-producto-transferencia-modificado" variant="success" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <strong class="mr-auto">Éxito !</strong>
		          <small class="text-muted mr-2">modificado</small>
		        </div>
		      </template>
		      Este producto ha sido modificado con éxito !
		    </b-toast>

		    <!-- ******************************************************************* -->

		    <!-- TOAST CODIGO PRODUCTO REPETIDO -->

			<b-toast id="toast-editar-cero" variant="warning" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">Error !</strong>
		          <small class="text-muted mr-2">cero</small>
		        </div>
		      </template>
		      La cantidad y el precio no deben ser cero !
		    </b-toast>

		    <!-- ******************************************************************* -->

		    <!-- TOAST CODIGO PRODUCTO NO EXISTE -->

			<b-toast id="toast-no-existe" variant="warning" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">Error !</strong>
		          <small class="text-muted mr-2">Sin existencia</small>
		        </div>
		      </template>
		      No existe ese producto !
		    </b-toast>

		    <!-- ******************************************************************* -->

		    <!-- TOAST CODIGO PRODUCTO REPETIDO -->

			<b-toast id="toast-cantidad-superada" variant="warning" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">Error !</strong>
		          <small class="text-muted mr-2">superada</small>
		        </div>
		      </template>
		      La cantidad ha superado el stock !
		    </b-toast>

		    <!-- ******************************************************************* -->

		    <!-- TOAST COMPLETAR CABECERA -->

			<b-toast id="toast-completar-cabecera" variant="warning" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">Error !</strong>
		          <small class="text-muted mr-2">incompleto</small>
		        </div>
		      </template>
		      Complete todos los datos correctamente !
		    </b-toast>

		    <!-- ******************************************************************* -->

			<!-- ------------------------------------------------------------------------ -->

			<!-- MENU COTIZACION TRANSFERENCIA -->

			<cotizacionEnviarTransferencia 
			@mostrar-cotizacion-enviar="funcionMostrarCerrarCotizacion" 
			ref="cotizacionEnviarTransferencia" 
			@moneda_enviar="seleccionarMonedaEnviar"
			v-bind:moneda_codigo="monedaCodigo"
			></cotizacionEnviarTransferencia>

			<!-- ------------------------------------------------------------------------ -->

<!-- 		</div> -->

		<!-- <div v-else>
			<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div> -->

	</div>

</template>
<script>
	export default {
      props: ['candec', 'monedaCodigo', 'tab_unica', 'id_sucursal'],  
      data(){
        return {
         	sucursales: [],
         	empleados: [],
         	productos: [],
         	productoImagen: [{
         		DESCRIPCION: '',
         		IMAGEN: '',
         		PRECOSTO: '',
         		PREC_VENTA: '',
         		PREMAYORISTA: '',
         		PREVIP: '',
         		FECHULT_C: ''
         	}],
         	codigoOrigen: '',
         	codigoDestino: '',
            codigoTransporta: '',
            codigoEnvia: '',
            codigoRecibe: '',
         	descripcionOrigen: '',
         	descripcionDestino: '',
            descripcionEnvia: '',
            descripcionTransporta: '',
            descripcionRecibe: '',
            descripcionProducto: '',
            nro_caja: '',
         	validarOrigen: false,
         	validarDestino: false,
            validarCodigoProducto: false,
            validarDescripcionProducto: false,
            validarPrecioUnitario: false,
            validarCantidad: false,
         	buscarOrigen: false,
         	buscarDestino: false,
            buscarEnvia: false,
            buscarTransporta: false,
            buscarRecibe: false,
            codigoProducto: '',
            stockProducto: '',
            ivaProducto: '',
            cantidadProducto: '',
            monedaProducto: '',
            precioProducto: '',
            editarCodigo: '',
            editarPrecio: '',
            editarCantidad: '',
            editarStock: '',
            editarRow: '',
            moneda_enviar: 0,
            no_registrados: [],
            btnguardar: true,
            switch_un_producto: false,
            switch_consignacion: false,
            procesar: false,
            cantidad_row: 0,
            cotizacion: ''
        }
      }, 
      methods: {
      	consignacion(){
      		let me=this;
      		if(me.switch_consignacion===true){
      			return;
      		}

      		            			  Swal.fire({
						  title: '¿Desea realizar la transferencia a consignacion?',
						  text: 'los productos de esta transferencia podran ser devueltos en caso de que la sucursal a recibir lo desee!',
						  type: 'warning',
						  showLoaderOnConfirm: true,
						  showCancelButton: true,
						  confirmButtonColor: 'btn btn-success',
						  cancelButtonColor: '#d33',
						  confirmButtonText: 'Confirmar!',
						  cancelButtonText: 'Cancelar',
						  preConfirm: () => {


                               me.switch_consignacion=true;
						    	// ------------------------------------------------------------------------
 
						    	// REVISAR SI HAY DATOS 


				        		// ------------------------------------------------------------------------

								return true;

								// ------------------------------------------------------------------------

						  }

						}).then((result) => {
						  if (!result.value) {

						  	// ------------------------------------------------------------------------
                              	me.switch_consignacion=false;
						  	// LIMPIAR TEXTBOX 

						

							// ------------------------------------------------------------------------

						  }

						})

      	},
      	activarBuscar(opcion){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES

      		let me = this;

            // ------------------------------------------------------------------------

            // REVISAR CUAL FUE EL BOTON DE BUSCAR SELECCIONADO SUCURSALES

      		if (opcion === 1) {
      			me.buscarOrigen = true;
      			me.buscarDestino = false;
      		} else if (opcion === 2) {
      			me.buscarOrigen = false;
      			me.buscarDestino = true;
      		}

            // ------------------------------------------------------------------------
      	},
        activarBuscarEmpleado(opcion){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // REVISAR CUAL FUE EL BOTON DE BUSCAR SELECCIONADO EMPLEADOS

            if (opcion === 1) {
                me.buscarEnvia = true;
                me.buscarTransporta = false;
                me.buscarRecibe = false;
            } else if (opcion === 2) {
                me.buscarEnvia = false;
                me.buscarTransporta = true;
                me.buscarRecibe = false;
            } else if (opcion === 3) {
                me.buscarEnvia = false;
                me.buscarTransporta = false;
                me.buscarRecibe = true;
            }

            // ------------------------------------------------------------------------
        } ,
      	seleccionOrigen(row) {
      		let me = this;
      		if (this.buscarOrigen === true) {
      			me.codigoOrigen = row.CODIGO; 	
		      	me.descripcionOrigen = row.DESCRIPCION;
      		} else if (this.buscarDestino === true) {
      			me.codigoDestino = row.CODIGO; 	
		      	me.descripcionDestino = row.DESCRIPCION;
      		}
		},
      	llamarDatatable(){
      		$(document).ready( function () {
				    $('#tablaModal').DataTable({
				    	"bAutoWidth": false,
						"destroy": true  
				    });
			} );
      	},
       	cargarSucursales(opcion, origen){

       		// ------------------------------------------------------------------------

       		// INICIAR VARIABLES

       		let me = this;
       		var origenV = 0;

       		// ------------------------------------------------------------------------

       		// BUSCAR ORIGEN DE LA CONSULTA 

       		if (origen === 1) {
       			origenV = me.codigoOrigen;
       		} else if (origen === 2) {
       			origenV = me.codigoDestino;
       		}

       		// ------------------------------------------------------------------------

       		// REVISAR SI ES CONSULTA GENERAL O UNICA 

       		if (opcion === 1) {

       			// *******************************************************************

       			// CONSULTAR TODAS LAS SUCURSALES 

       			axios.get('/sucursal').then(function (response) {
					me.sucursales = response.data.sucursales;
					me.llamarDatatable();
				});

				// *******************************************************************

       		} else if (opcion === 2) {

       			// *******************************************************************

       			// CONSULTAR SUCURSAL DETERMINADA

       			axios.post('/sucursal', {'codigoOrigen': origenV}).then(function (response) {
       				
					if(response.data.sucursal === 0) {

						// *******************************************************************

						// MARCAR EN ROJO TEXTBOX 

						if (origen === 1) {
							me.validarOrigen = true;
							me.codigoOrigen = '';
							me.descripcionOrigen = '';
						} else if (origen === 2) {
							me.validarDestino = true;
							me.codigoDestino = '';
							me.descripcionDestino = '';
						}

						// *******************************************************************

					} else {

						// *******************************************************************

						// LLENAR DESCRIPCION DE ACUERDO A OPCION

						if (origen === 1) {
							me.descripcionOrigen = response.data.sucursal[0].DESCRIPCION;
							me.validarOrigen = false;
						} else if (origen === 2) {
							me.descripcionDestino = response.data.sucursal[0].DESCRIPCION;
							me.validarDestino = false;
						}

						// *******************************************************************
					}
				});

				// *******************************************************************
       		}

       		// ------------------------------------------------------------------------
       		
       	},
       	cargarEmpleados(origen)	{

       		// ------------------------------------------------------------------------

       		// INICIAR VARIABLES

       		let me = this;
       		var origenV = 0;

       		// ------------------------------------------------------------------------

       		// BUSCAR ORIGEN DE LA CONSULTA 

       		if (origen === 1) {
       			origenV = me.codigoEnvia;
       		} else if (origen === 2) {
       			origenV = me.codigoTransporta;
       		} else if (origen === 3) {
       			origenV = me.codigoRecibe;
       		}

       		// ------------------------------------------------------------------------

       		// CONSULTAR EMPLEADO DETERMINADO

       			axios.post('/empleado', {'codigo': origenV}).then(function (response) {
       				
					if(response.data.empleado === 0) {

						// *******************************************************************

						// MARCAR EN ROJO TEXTBOX 

						if (origen === 1) {
							me.validarEnvia = true;
							me.codigoEnvia = '';
							me.descripcionEnvia = '';
						} else if (origen === 2) {
							me.validarTransporta = true;
							me.codigoTransporta = '';
							me.descripcionTransporta = '';
						} else if (origen === 3) {
							me.validarRecibe = true;
							me.codigoRecibe = '';
							me.descripcionRecibe = '';
						}

						// *******************************************************************

					} else {

						// *******************************************************************

						// LLENAR DESCRIPCION DE ACUERDO A OPCION

						if (origen === 1) {
							me.descripcionEnvia = response.data.empleado[0].NOMBRE;
							me.validarEnvia = false;
						} else if (origen === 2) {
							me.descripcionTransporta = response.data.empleado[0].NOMBRE;
							me.validarTransporta = false;
						} else if (origen === 3) {
							me.descripcionRecibe = response.data.empleado[0].NOMBRE;
							me.validarRecibe = false;
						}

						// *******************************************************************
					}
				});

       		// ------------------------------------------------------------------------

       	},
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
	            'tab_unica': me.tab_unica
	        }; 
	        
            // ------------------------------------------------------------------------

            // CONSULTAR PRODUCTO DETERMINADO

            axios.post('/producto', {'data': data, 'Opcion': 1}).then(function (response) {
                   
                    if(response.data.producto === 0) {

                        // *******************************************************************

                        // MARCAR EN ROJO TEXTBOX SI NO EXISTE PRODUCTO
                    
                        me.validarCodigoProducto = true;
                        me.codigoProducto = '';
                        me.descripcionProducto = '';
                        me.precioProducto = '';
                        me.stockProducto = '';
                        me.monedaProducto = '';
                        me.ivaProducto = '';

                        // *******************************************************************

                        // NO EXISTE PRODUCTO 

                        me.$bvToast.show('toast-no-existe');

                        // *******************************************************************

                    } else {

                        // *******************************************************************

                        // LLENAR DESCRIPCION DE PRODUCTO

                        me.codigoProducto = response.data.producto.CODIGO;
                        me.descripcionProducto = response.data.producto.DESCRIPCION;
                        me.stockProducto  = response.data.producto.STOCK;
                        me.ivaProducto = response.data.producto.IVA;
                        me.monedaProducto = response.data.producto.MONEDA;
                        me.precioProducto = response.data.valor;

						// *******************************************************************

	                    // AGREGAR PRODUCTO TRANSFERENCIA AUTOMATICAMENTE 

				    	if (me.switch_un_producto === true) {
				    		me.cantidadProducto = 1;
				    		me.agregarProductoTransferencia();
				    		me.codigoProducto = '';
				    	}

			            // *******************************************************************

                        me.validarCodigoProducto = false;

                        // *******************************************************************
                    }
            });

    		// ------------------------------------------------------------------------

        }, 
        agregarProductoTransferencia(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES

            let me = this;
            var tableTransferencia = $('#tablaTransferencia').DataTable();
            var precio = 0;
            var iva = 0;

            // ------------------------------------------------------------------------

            // CONTROLADOR

            if (me.codigoProducto.length === 0) {
                me.validarCodigoProducto = true;
                return;
            } else {
                me.validarCodigoProducto = false;
            }

            if (me.descripcionProducto.length === 0) {
                me.validarDescripcionProducto = true;
                return;
            } else {
                me.validarDescripcionProducto = false;
            }

            // if (me.precioProducto.length === 0  || me.precioProducto === '0') {
            //     me.validarPrecioUnitario = true;
            //     return;
            // } else {
            //     me.validarPrecioUnitario = false;
            // }

            if (me.cantidadProducto.length === 0  || me.cantidadProducto === '0') {
                me.validarCantidad = true;
                return;
            } else {
                me.validarCantidad = false;
            }

            // ------------------------------------------------------------------------

            //  QUITAR COMA DE PRECIO

            precio = Common.multiplicarCommon(me.cantidadProducto, me.precioProducto, me.candec);
         
            // ------------------------------------------------------------------------

            //  DAR FORMATO AL RESULTADO FINAL PARA MOSTRAR EN DATATABLE 

            precio = Common.darFormatoCommon(precio, me.candec);

            // ------------------------------------------------------------------------

            // CALCULAR IVA

            iva = Common.calcularIVACommon(precio, me.ivaProducto, me.candec);

            // ------------------------------------------------------------------------

            // AGREGAR ITEM A FILA 

            me.cantidad_row = me.cantidad_row + 1;

            // ------------------------------------------------------------------------

            // CARGAR DATO EN TABLA TRANSFERENCIAS

            me.agregarFilaTabla(me.cantidad_row, me.codigoProducto, me.descripcionProducto, me.cantidadProducto, me.precioProducto, iva, precio, me.ivaProducto, me.stockProducto);

            // ------------------------------------------------------------------------

            // VACIAR TEXTBOX AGREGAR PRODUCTO

            me.inivarAgregarProducto();

            // ------------------------------------------------------------------------

	        // LLAMAR AL METODO HIJO 

	        this.codigoProducto = '';
	        this.$refs.compontente_codigo_producto.vaciarDevolver();

	        // ------------------------------------------------------------------------

        }, formatoPrecio(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A PRECIO

            me.precioProducto = Common.darFormatoCommon(me.precioProducto, me.candec);

            // ------------------------------------------------------------------------
            
        }, formatoCantidad(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // DAR FORMATO A CANTIDAD
            
            me.cantidadProducto = Common.darFormatoCommon(me.cantidadProducto, 0);

            // ------------------------------------------------------------------------
            
        }, formatoEditarPrecio(){

			// ------------------------------------------------------------------------

			// DAR FORMATO A LOS VALORES DE MODAL EDITAR 

			this.editarCantidad = Common.darFormatoCommon(this.editarCantidad, this.candec);

			// ------------------------------------------------------------------------

		}, filtrarProductos(){

			// ------------------------------------------------------------------------

			// LLAMAR FUNCION PARA FILTRAR PRODUCTOS

			Common.filtrarProductosCommon(this.codigoProducto).then(data => {
			  this.productos = data
			});

			// ------------------------------------------------------------------------

		}, inivarAgregarProducto(){

			// ------------------------------------------------------------------------

			// VACIAR TEXTBOX 

			this.codigoProducto = '';
			this.descripcionProducto = '';
            this.stockProducto = '';
            this.precioProducto = '';
            this.cantidadProducto = '';

            // ------------------------------------------------------------------------   

		}, seleccionarMonedaEnviar(valor) {

			// ------------------------------------------------------------------------ 

			// CARGAR MONEDA A ENVIAR 

			this.moneda_enviar = valor.selected;
			this.cotizacion = valor.cotizacion;

			// ------------------------------------------------------------------------ 

			// REVISAR QUE BOTON ESTA DESACTIVADO PARA REDIGIRLO A MODIFICAR O GUARDAR 
			
			if (this.btnguardar === true) {
				this.guardarTransferencia();
			} else {
				this.modificarTransferencia();
			}

			// ------------------------------------------------------------------------ 

		}, guardarTransferencia(){

			// ------------------------------------------------------------------------ 

			// INICIAR VARIABLES 

			let me = this;
			var tableTransferencia = $('#tablaTransferencia').DataTable();
			var data = [];

			// ------------------------------------------------------------------------ 

			// Llamar Controlador 
			
			if (this.inivarCabecera(tableTransferencia) === true) {
				me.$bvToast.show('toast-completar-cabecera');
				return;
			}

			// ------------------------------------------------------------------------ 

			// LLAMAR TABLA COTIZACION 

			if(this.moneda_enviar === 0) {
				this.funcionMostrarCerrarCotizacion();
				return;
			}

			// ------------------------------------------------------------------------ 

			// PREPARAR ARRAY 

			data = {
				origen: me.codigoOrigen,
				destino: me.codigoDestino,
				envia: me.codigoEnvia,
				consignacion:me.switch_consignacion,
				transporta: me.codigoTransporta,
				recibe: me.codigoRecibe,
				monedaSistema: me.monedaCodigo,
				monedaEnviar: me.moneda_enviar,
				nro_caja: me.nro_caja,
				tab_unica: me.tab_unica,
				cotizacion: me.cotizacion
			}

			// ------------------------------------------------------------------------ 

			// ACTIVAR LOADING 

			me.procesar = true;

			// ------------------------------------------------------------------------ 

			Common.guardarTransferenciaCommon(tableTransferencia.rows().data().toArray(), data).then(data => {
			    
				if (data.response === true) {

					// ------------------------------------------------------------------------

					// DESACTIVAR LOADING

					me.procesar = false;

					// ------------------------------------------------------------------------

					Swal.fire(
					  'Guardado !',
					  'La Transferencia ha sido guardada correctamente !',
					  'success'
					).then((result) => {

					  if (result.value) {

					  	// ------------------------------------------------------------------------ 

					  	// SI DA OK LO REDIRIJE A MOSTRAR TRANSFERENCIAS 

					    this.$router.push('/tr1');

					    // ------------------------------------------------------------------------ 

					   }

					});

				} else if (data.response === false) {

					// ------------------------------------------------------------------------ 

					// DESACTIVAR LOADING 

					me.procesar = false;

					// ------------------------------------------------------------------------

					// MOSTRAR SWAL DE PRODUCTOS NO GUARDADOS 

					Swal.fire({
					  title: 'No se ha guardado correctamente !',
					  text: "Algunos productos no se registraron correctamente !",
					  icon: 'warning',
					  showCancelButton: true,
					  confirmButtonText: 'Aceptar !',
					  cancelButtonText: 'Ver productos !',
					  reverseButtons: true,
					  allowOutsideClick: false
					}).then((result) => {

					  if (result.value) {

					  	// ------------------------------------------------------------------------

					  	// REDIRIGIR A LA PAGINA MOSTRAR

					     this.$router.push('/tr1');

					     // ------------------------------------------------------------------------

					  } else if (

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

				} else {
					Swal.fire(
					  'Error !',
					  'La Transferencia no se ha guardado correctamente !',
					  'error'
					);
				}
				
			});
			
			
			// ------------------------------------------------------------------------ 

			// DEVOLVER VALOR A VARIABLE

			this.moneda_enviar = 0;

			// ------------------------------------------------------------------------ 

		}, inivarCabecera(tablaEnviada){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES 

			let me = this;
			var tabla = tablaEnviada.rows().data();

			// ------------------------------------------------------------------------

            // CONTROLADOR CABECERA

            if (me.codigoOrigen.length === 0) {
                me.validarOrigen = true;
                return true;
            } else {
                me.validarOrigen = false;
            }

            if (me.codigoDestino.length === 0) {
                me.validarDestino = true;
                return true;
            } else {
                me.validarDestino = false;
            }

            if (me.codigoEnvia.length === 0) {
                me.validarEnvia = true;
                return true;
            } else {
                me.validarEnvia = false;
            }

            if (me.codigoTransporta.length === 0) {
                me.validarTransporta = true;
                return true;
            } else {
                me.validarTransporta = false;
            }

            if (me.codigoRecibe.length === 0) {
                me.validarRecibe = true;
                return true;
            } else {
                me.validarRecibe = false;
            }

            // ------------------------------------------------------------------------

            // RETORNAR SI ORIGEN Y DESTINO SON IGUALES

            if (me.codigoOrigen === me.codigoDestino) {
            	me.validarOrigen = true;
            	me.validarDestino = true;
            	return true;
            } else {
            	me.validarOrigen = false;
            	me.validarDestino = false;
            }

            // ------------------------------------------------------------------------

            // SI LA TABLA TRANSFERENCIA ESTA VACIA DEVUELVE TAMBIEN TRUE 

            if (tabla.length === 0){
            	return true;
            }

            // ------------------------------------------------------------------------

            // RETORNAR VALOR 
           
            return false;

            // ------------------------------------------------------------------------

		}, cotizacionEnviar(valor){

            // ------------------------------------------------------------------------ 

            // CAMBIAR EL MENU DE ACUERDO AL SIDEBAR

            console.log(valor);

            // ------------------------------------------------------------------------ 

        },
        funcionMostrarCerrarCotizacion() {
        	
        	this.$refs.cotizacionEnviarTransferencia.mostrarModal();

        }, mostrarTransferencia(codigo){

        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES 

        	let me = this;
        	var stock = 0;

        	// ------------------------------------------------------------------------

        	// SE ENCARGA DE COMPROBAR SI EXISTE CODIGO EN LA URL PARA PODER CARGAR UNA TRANSFERENCIA EXISTENTE
        	
        	if (codigo !== undefined) {

        		// ------------------------------------------------------------------------

        		// OCULTAR BOTON GUARDAR 

        		me.btnguardar = false;

        		// ------------------------------------------------------------------------

        		// AGREGAR CABECERA, ENVIO CERO PARA CODIGO ORIGEN PARA ESPECIFICAR QUE
        		// NECESITO DATOS DE LA PROPIA SUCURSAL DEL USUARIO

        		Common.obtenerCabeceraTransferenciaCommon(codigo, 0).then(data => {
        			me.codigoOrigen = data.CODIGO_ORIGEN;
        			me.codigoDestino = data.CODIGO_DESTINO;
        			me.codigoEnvia = data.CODIGO_ENVIA;
        			me.codigoTransporta = data.CODIGO_TRANSPORTA;
        			me.codigoRecibe = data.CODIGO_RECIBE;
        			me.descripcionOrigen = data.ORIGEN;
        			me.descripcionDestino = data.DESTINO;
        			me.descripcionEnvia = data.ENVIA;
        			me.descripcionTransporta = data.TRANSPORTA;
        			me.descripcionRecibe = data.RECIBE;
        			me.nro_caja = data.NRO_CAJA;
        		});

        		// ------------------------------------------------------------------------

        		// AGREGAR CUERPO, ENVIO CERO PARA CODIGO ORIGEN PARA ESPECIFICAR QUE
        		// NECESITO DATOS DE LA PROPIA SUCURSAL DEL USUARIO

        		Common.obtenerCuerpoTransferenciaCommon(codigo, 0).then(data => {
        			data.map(function(x) {
					   
					   // ------------------------------------------------------------------------

					   // CALCULAR STOCK ACTUAL CON POSIBLE ELIMINACION

					   stock = parseFloat(x.STOCK) + parseFloat(x.CANTIDAD);

        			   // ------------------------------------------------------------------------
        			   
        			   // SUMAR LA CANTIDAD DE NUMERACION 
        			   
        			   me.cantidad_row = x.ITEM;

        			   // ------------------------------------------------------------------------

					   // EMPEZAR A CARGAR PRODUCTOS EN TRANSFERENCIA 

					   me.agregarFilaTabla(x.ITEM, x.CODIGO_PROD, x.DESCRIPCION, x.CANTIDAD, x.PRECIO, x.IVA, x.TOTAL, x.IVA_PORCENTAJE, stock);
					  
					   // ------------------------------------------------------------------------

					});
        		});

        		// ------------------------------------------------------------------------

        	} else {

        		// ------------------------------------------------------------------------

        		// OCULTAR BOTON MODIFICAR 

        		me.btnguardar = true;

        		// ------------------------------------------------------------------------

        	}

        	// ------------------------------------------------------------------------

        }, agregarFilaTabla(item, codigo, descripcion, cantidad, precio, iva, total, iva_porcentaje, stock){

        	// ------------------------------------------------------------------------

        	//	INICIAR VARIABLES 

        	let me = this;
        	var tableTransferencia = $('#tablaTransferencia').DataTable();
        	var productoExistente = [];
        	var cantidadNueva = 0;

        	// ------------------------------------------------------------------------

            // REVISAR SI EXISTE VALORES REPETIDOS EN TABLA TRANSFERENCIAS 
            // LA OPCION 1 ES PARA DEVOLVER SOLO TRUE O FALSE SI EXISTE O NO
            // LA OPCION 2 ES PARA DEVOLVER MAS DATOS DEL PRODUCTO 

            productoExistente = Common.existeProductoDataTableCommon(tableTransferencia, codigo, 2);
           
            if (productoExistente.respuesta == true) {

            	// ------------------------------------------------------------------------

            	// SUMAR LA CANTIDAD E IVA

            	cantidadNueva = parseFloat(productoExistente.cantidad) + parseFloat(cantidad);
            	
            	if(productoExistente.stock<stock){
            		productoExistente.stock=stock;
            	}

            	// ------------------------------------------------------------------------

            	// REVISAR SI CANTIDAD SUPERA STOCK

            	if (Common.cantidadSuperadaCommon(cantidadNueva, productoExistente.stock)) {
            		me.validarCantidad = true;
	            	me.$bvToast.show('toast-cantidad-superada');
	            	return;
            	} else {
            		me.validarCantidad = false;
            	}

            	// ------------------------------------------------------------------------

            	// EDITAR CANTIDAD PRODUCTO 

            	me.editarCantidadProducto(tableTransferencia, cantidadNueva, productoExistente.precio, iva_porcentaje, productoExistente.stock, productoExistente.row);
            	return;

            	// ------------------------------------------------------------------------

            } else {
            	me.validarCodigoProducto = false;
            }

            // ------------------------------------------------------------------------

            // REVISAR SI CANTIDAD SUPERA STOCK

            if (Common.cantidadSuperadaCommon(cantidad, stock)) {
	            me.validarCantidad = true;
	            me.$bvToast.show('toast-cantidad-superada');
	            return;
	        } else {
	            me.validarCantidad = false;
	        }

            // ------------------------------------------------------------------------

            // SUMAR LA CANTIDAD DE FILAS 

            

            // ------------------------------------------------------------------------

        	// AGREGAR FILAS 

        	 tableTransferencia.rows.add( [ {
		                    "ITEM": item,
		                    "CODIGO":   codigo,
		                    "DESCRIPCION":     descripcion,
		                    "CANTIDAD": cantidad,
		                    "PRECIO":    precio,
		                    "IVA":       iva,
		                    "TOTAL":       total,
		                    "ACCION":    "&emsp;<a role='button' id='mostrarProductoFila' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a> &emsp;<a role='button' id='editarModal' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>&emsp;<a role='button'  title='Eliminar'><i id='eliminarModal' class='fa fa-trash text-danger' aria-hidden='true'></i></a>",
		                    "IVA_PORCENTAJE": iva_porcentaje,
		                    "STOCK": stock
		                } ] )
		     .draw();

		    // ------------------------------------------------------------------------ 

            // AGREGAR INDEX A LA TABLA TRANSFERNCIAS

            // tableTransferencia.on( 'search.dt', function () {
            //     tableTransferencia.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            //         cell.innerHTML = i+1;
            //     } );
            // } ).draw();

            // ------------------------------------------------------------------------

            // AJUSTAR COLUMNAS DE ACUERDO AL DATO QUE CONTIENEN

            tableTransferencia.columns.adjust().draw();

            // ------------------------------------------------------------------------

        },
        modificarTransferencia(){

        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES 

			let me = this;
			var tableTransferencia = $('#tablaTransferencia').DataTable();
			var data = [];
			var codigo = this.$route.params.id;

			// ------------------------------------------------------------------------ 

			// Llamar Controlador 
			
			if (this.inivarCabecera(tableTransferencia) === true) {
				me.$bvToast.show('toast-completar-cabecera');
				return;
			}

			// ------------------------------------------------------------------------ 

			// LLAMAR TABLA COTIZACION 

			if(this.moneda_enviar === 0) {
				this.funcionMostrarCerrarCotizacion();
				return;
			}

			// ------------------------------------------------------------------------ 

			// PREPARAR ARRAY 

			data = {
				origen: me.codigoOrigen,
				destino: me.codigoDestino,
				envia: me.codigoEnvia,
				transporta: me.codigoTransporta,
				recibe: me.codigoRecibe,
				monedaSistema: me.monedaCodigo,
				monedaEnviar: me.moneda_enviar,
				nro_caja: me.nro_caja,
				tab_unica: me.tab_unica
			}

			// ------------------------------------------------------------------------ 

			// MOSTRAR MENSAJE PARA MODIFICAR 
			
			Swal.fire({
				title: 'Estas seguro ?',
				text: "Modificar la Transferencia " + codigo + " !",
				type: 'warning',
				showLoaderOnConfirm: true,
				showCancelButton: true,
				confirmButtonColor: '#d33',
				cancelButtonColor: '#3085d6',
				confirmButtonText: 'Si, modificalo!',
				cancelButtonText: 'Cancelar',
				preConfirm: () => {
				    return Common.modificarTransferenciaCommon(codigo, tableTransferencia.rows().data().toArray(), data).then(data => {
				    if (data.response !== true) {
				        throw new Error(data.statusText);
				    }
				  	return data.response;
					}).catch(error => {
				        Swal.showValidationMessage(
				          `Request failed: ${error}`
				        )
				    });
				}
				}).then((result) => {
				  if (result.value) {
				  	Swal.fire(
						      'Modificado !',
						      'Se ha modificado correctamente la transferencia !',
						      'success'
					)
				  }
			});
			
			// ------------------------------------------------------------------------ 

			// DEVOLVER VALOR A VARIABLE

			this.moneda_enviar = 0;

			// ------------------------------------------------------------------------

        }, productosCompra(){

        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES

        	let me = this;

        	// ------------------------------------------------------------------------

        	// DEVOLVER SI ESTA VACIO 

        	if (me.nro_caja === '' || me.nro_caja === null || me.nro_caja === undefined) {
        		return;
        	}

        	// ------------------------------------------------------------------------


        		Swal.fire({
				  title: '¿ Importar ?',
				  text: "Importar productos de Nro. Caja " + me.nro_caja + " !",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  confirmButtonColor: '#d33',
				  cancelButtonColor: '#3085d6',
				  confirmButtonText: 'Si, importalo!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
				    return Common.obtenerProductosComprasDetCommon(this.nro_caja).then(data => {

				    	// ------------------------------------------------------------------------

				    	// REVISAR SI HAY DATOS 

				    	if (!data.response === true) {
				          throw new Error('error');
				        } else {
			        		data.datos.map(function(x) {

			        			  // ------------------------------------------------------------------------
			        			  
			        			  // SUMAR LAS CANTIDADES DE FILAS 

			        			  me.cantidad_row = me.cantidad_row + 1;

			        			  // ------------------------------------------------------------------------

								  // EMPEZAR A CARGAR PRODUCTOS EN TRANSFERENCIA 

								  me.agregarFilaTabla(me.cantidad_row, x.CODIGO_PROD, x.DESCRIPCION, x.CANTIDAD, x.PRECIO, x.IVA, x.TOTAL, x.IVA_PORCENTAJE, x.STOCK);
								  
								  // ------------------------------------------------------------------------

							});	
				        }

		        		// ------------------------------------------------------------------------

						return true;

						// ------------------------------------------------------------------------

        			}).catch(error => {
				        Swal.showValidationMessage(
				          `Request failed: ${error}`
				        )
				    });
				  }
				}).then((result) => {
				  if (result.value) {
				  	Swal.fire(
						      'Importado !',
						      'Se ha importado los productos de la compra !',
						      'success'
					)

				  }
				})

        	// ------------------------------------------------------------------------

        }, editarCantidadProducto(tabla, cantidad, precio_producto, iva_producto, stock, row){

        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES

        	let me = this;
        	var precio = 0;
        	var iva = 0;

        	// ------------------------------------------------------------------------

            // PROHIBIR EDITADO SI CANTIDAD O PRECIO ES CERO

            if (cantidad === '0' || precio_producto === '0') {
                me.$bvToast.show('toast-editar-cero');
                return;	
            }

            // ------------------------------------------------------------------------

            // PROHIBIR EDITADO SI CANTIDAD SUPERA STOCK
                	
            if (parseFloat(Common.quitarComaCommon(cantidad)) > parseFloat(Common.quitarComaCommon(stock))) {
                me.$bvToast.show('toast-cantidad-superada');
                return;	
            }

            // ------------------------------------------------------------------------

            // CARGAR LO EDITADO

            tabla.cell(row, 3).data(cantidad).draw();
            tabla.cell(row, 4).data(precio_producto).draw();

            // ------------------------------------------------------------------------
                    
            // CALCULAR PRECIO TOTAL

            precio = Common.multiplicarCommon(cantidad, precio_producto, me.candec);

            // ------------------------------------------------------------------------

            // CALCULO IVA 
		            
		    iva = Common.calcularIVACommon(precio, iva_producto, me.candec);

		    // ------------------------------------------------------------------------

		    // CARGAR PRECIO CALCULADO 

            tabla.cell(row, 6).data(precio).draw();

            // ------------------------------------------------------------------------

            // CARGAR IVA CALCULADO 

            tabla.cell(row, 5).data(iva).draw();

           	// ------------------------------------------------------------------------

            // LLAMAR TOAST MODIFICADO

            me.$bvToast.show('toast-producto-transferencia-modificado');

            // ------------------------------------------------------------------------

        }, mostrarTransferencias(){

        	// ------------------------------------------------------------------------ 

			// SI DA OK LO REDIRIJE A MOSTRAR TRANSFERENCIAS 

			this.$router.push('/tr1');

			// ------------------------------------------------------------------------ 

        }
      },  
        mounted() {

            // ------------------------------------------------------------------------
            
            // INICIAR VARIABLES

            let me = this;
            var precio = 0;
            var iva = 0;
            	
            // ------------------------------------------------------------------------

            // CARGAR SUCURSAL 

            this.codigoOrigen = this.id_sucursal;
            this.cargarSucursales(2, 1);

            // ------------------------------------------------------------------------

            $(document).ready( function () {

            	// ------------------------------------------------------------------------
                // >>
                // PROHIBIR A MODAL QUE SE CIERRE CLICANDO AFUERA 
                // ------------------------------------------------------------------------

            	$('#modal_no_registrados').modal({
				    backdrop: 'static',
				    keyboard: false,
				    show: false
				})

            	// ------------------------------------------------------------------------

			    $('#example22').DataTable({
			    	"bAutoWidth": false
			    });

                // ------------------------------------------------------------------------
                // >>
                // INICIAR EL DATATABLE EMPLEADOS MODAL
                // ------------------------------------------------------------------------

                var table = $('#tablaModalEmpleados').DataTable({
                        "processing": true,
                        "serverSide": true,
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
				            { extend: 'print', text: '<i class="fa fa-print"></i>', titleAttr: 'Imprimir', className: 'btn btn-secondary' }
				        ],
                        "ajax":{
                                 "url": "/empleado",
                                 "dataType": "json",
                                 "type": "GET"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "NOMBRE" },
                            { "data": "CI" },
                            { "data": "DIRECCION" },
                            { "data": "ID_SUCURSAL" }
                        ]      
                    });
                 
                // ------------------------------------------------------------------------
                
                // SELECCIONAR UNA FILA - SE PUEDE BORRAR - SIN USO


                $('#tablaModalEmpleados').on('click', 'tbody tr', function() {

                    if ($(this).hasClass('selected')) {
                        $(this).removeClass('selected');
                    } else {
                        table.$('tr.selected').removeClass('selected');
                        $(this).addClass('selected');
                    }

                    // *******************************************************************

                    // CARGAR LOS VALORES A LAS VARIABLES DE ACUERDO A - ENVIA, TRANPORTA, RECIBE

                    if (me.buscarEnvia === true) {
                       me.codigoEnvia = table.row(this).data().CODIGO;
                       me.descripcionEnvia = table.row(this).data().NOMBRE; 
                    } else if (me.buscarTransporta === true) {
                       me.codigoTransporta = table.row(this).data().CODIGO;
                       me.descripcionTransporta = table.row(this).data().NOMBRE;  
                    } else if (me.buscarRecibe === true) {
                       me.codigoRecibe = table.row(this).data().CODIGO;
                       me.descripcionRecibe = table.row(this).data().NOMBRE; 
                    } 
                     
                    // *******************************************************************

                    // CERRAR EL MODAL
                     
                    $('.empleado-modal').modal('hide');

                    // *******************************************************************

                });

                //FIN TABLA MODAL EMPLEADOS
                // <<   
                // ------------------------------------------------------------------------
                
                // ------------------------------------------------------------------------
                // >>
                // INICIAR EL DATATABLE TRANSFERENCIA 
                // ------------------------------------------------------------------------

                var tableTransferencia = $('#tablaTransferencia').DataTable({
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
				            { extend: 'print', text: '<i class="fa fa-print"></i>', titleAttr: 'Imprimir', className: 'btn btn-secondary', title: 'Transferencia', messageTop: 'Productos registrados para Transferencia' }
				        ],
				        "columnDefs": [
				            {
				                "targets": [ 8 ],
				                "visible": false,
				                "searchable": false
				            },
				            {
				                "targets": [ 9 ],
				                "visible": false,
				                "searchable": false
				            }
				        ], 
                        "columns": [
                            { "data": "ITEM" },
                            { "data": "CODIGO" },
                            { "data": "DESCRIPCION" },
                            { "data": "CANTIDAD" },
                            { "data": "PRECIO" },
                            { "data": "IVA" },
                            { "data": "TOTAL" },
                            { "data": "ACCION" },
                            { "data": "IVA_PORCENTAJE" },
                            { "data": "STOCK" }
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

						    // *******************************************************************

						  });

						  // *******************************************************************

						  // TOTAL IVA 

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

						      // *******************************************************************

						  });

						  // *******************************************************************

						  // CALCULO SUBTOTAL

						  //me.subtotal = Common.restarCommon(me.total, me.iva, me.candec);

						  // *******************************************************************
						}      
                });
                
                // ------------------------------------------------------------------------

                // ASIGNAR INLINE BUTTONS 

                tableTransferencia.buttons().container()
    			.appendTo( $('div.eight.column:eq(0)', tableTransferencia.table().container()) );

				// ------------------------------------------------------------------------

            	// DESPUES DE INICIAR LA TABLA TRANSFERENCIAS LLAMAR A LA CONSULTA PARA CARGAR CABECERA Y CUERPO 

            	me.mostrarTransferencia(me.$route.params.id);

            	// ------------------------------------------------------------------------

                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------
                //                  EDITAR FILA DATATABLE TRANSFERENICAS
                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------

                //  CUANDO SE HACE CLICK EN LA FILA CARGAR LOS DATOS EN LAS VARIABLES PARA EDITAR

                $('#tablaTransferencia').on('click', 'tbody tr', function() {

                    // *******************************************************************
                    
                    // CARGAR LOS VALORES A LAS VARIABLES DE EDITAR PRODUCTO TRANSFERENCIA

                    me.editarPrecio = tableTransferencia.row(this).data().PRECIO;
                    me.editarCantidad = tableTransferencia.row(this).data().CANTIDAD;  
                    me.editarCodigo = tableTransferencia.row(this).data().CODIGO;
                    me.editarIvaProducto = tableTransferencia.row(this).data().IVA_PORCENTAJE;
                    me.editarStock = tableTransferencia.row(this).data().STOCK;
                    me.editarRow = tableTransferencia.row( this );

                    // *******************************************************************

                });

                // ------------------------------------------------------------------------

                // MOSTRAR MODAL EDITAR

                $('#tablaTransferencia').on('click', 'tbody tr #editarModal', function() {

                    // *******************************************************************

                    // ABRIR EL MODAL
                     
                    $('.editar-producto-transferencia-modal').modal('show');

                    // *******************************************************************

                });

                // ------------------------------------------------------------------------

                // EDITAR FILA

                $('#editarFila').on('click', function() {

                	// *******************************************************************

                	// EDITAR TRANSFERENCIA

                	me.editarCantidadProducto(tableTransferencia, me.editarCantidad, me.editarPrecio, me.editarIvaProducto, me.editarStock, me.editarRow);

                	// *******************************************************************

                    // OCULTAR MODAL EDITAR 

                    $('.editar-producto-transferencia-modal').modal('hide');

                    // *******************************************************************

                });

                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------
                //              FIN EDITAR FILA DATATABLE TRANSFERENICAS
                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------

                 // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------
                //                  ELIMINAR FILA DATATABLE TRANSFERENICAS
                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------

                // MOSTRAR SWEET ELIMINAR

                $('#tablaTransferencia').on('click', 'tbody tr #eliminarModal', function() {

                    // *******************************************************************

                    // ABRIR EL SWEET ALERT

                    Swal.fire({
					  title: 'Estas seguro ?',
					  text: "Eliminar el producto " + me.editarCodigo + " !",
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
	                    
		                tableTransferencia.row($(me.editarRow)).remove().draw(); 

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

                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------
                //              FIN ELIMINAR FILA DATATABLE TRANSFERENICAS
                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------

                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------
                //              MOSTRAR PRODUCTO FILA DATATABLE TRANSFERENICAS
                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------

                // MOSTRAR MODAL ELIMINAR

                $('#tablaTransferencia').on('click', 'tbody tr #mostrarProductoFila', function() {

                    // *******************************************************************

		        	// OBTENER DATOS DEL PRODUCTO
		        	
		        	axios.post('/producto', {'codigo': (tableTransferencia.row($(this).parents('tr')).data().CODIGO), 'Opcion': 2}).then(function (response) {
						me.productoImagen = response.data.productoImagen;
					});

                    // *******************************************************************

                    // ABRIR EL MODAL

                    $('.mostrar-producto-transferencia-modal').modal('show');

                    // *******************************************************************

                });

                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------
                //              FIN MOSTRAR FILA DATATABLE TRANSFERENICAS
                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------

                // ------------------------------------------------------------------------
			});  

            // ------------------------------------------------------------------------

            // ENFOCAR 
            // CODIGO PARA CAMBIAR EL FOCUS CON ENTER EN INPUT
        	
        	$(document).on('keypress', 'input', function(e) {
        		
			  if(e.keyCode == 13 ) {
			           
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

        }
    }
</script>
<style>

</style>