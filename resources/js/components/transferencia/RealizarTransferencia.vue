<template>
	<div class="container-fluid mt-4">

        <!-- ------------------------------------------------------------------------------------- -->

		<!-- FORMULARIO  -->

		<div class="card border-bottom-primary">
		  
		  <div class="card-header">
		  	<div class="row">
			  	<div class="col-md-11 text-primary font-weight-bold">
			  		Transferencia 
			  	</div>
			  	<div class="col-md-1">
			  		<div class="input-group">
						<div class="input-group-prepend">
							{{ $route.params.id }}
						</div>
					</div>
			  	</div>
		  	</div>	 
		  </div>

		  <div class="card-body">

			<div class="row">

                <!-- ******************************************************************* -->

				<!-- ORIGEN  -->

				<div class="col-md-1">
						<label for="validationTooltip01">Origen</label>
						<div class="input-group ">
							<div class="input-group-prepend">
								<button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".origen-modal" v-on:click="cargarSucursales(1,0),activarBuscar(1)"><font-awesome-icon icon="search"/></button>
							</div>
							<input class="form-control form-control-sm" type="text" v-model="codigoOrigen" v-on:blur="cargarSucursales(2, 1)">
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
						<input class="form-control form-control-sm" v-model="codigoDestino" v-on:blur="cargarSucursales(2, 2)" type="text" >
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
						<input class="form-control form-control-sm" v-model="codigoEnvia" type="text" v-on:blur="cargarEmpleados(1)">
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
						<input class="form-control form-control-sm" v-model="codigoTransporta" type="text" v-on:blur="cargarEmpleados(2)">
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
						<input class="form-control form-control-sm" v-model="codigoRecibe" type="text" v-on:blur="cargarEmpleados(3)">
					</div>
				</div>	

				<div class="col-md-5">
					<label class="mt-1" for="validationTooltip01">Descripción</label>
					<input class="form-control form-control-sm" type="text" v-model="descripcionRecibe"  disabled>
				</div>	

				<div class="col-md-6">
					<label class="mt-1" for="validationTooltip01">Nro. Caja</label>
					<input class="form-control form-control-sm" type="text" v-model="nro_caja">
				</div>

			</div>

				</div>
		</div>

		<!-- FINAL DE FORMULARIO -->

       <!-- ------------------------------------------------------------------------------------- -->

		<!-- AGREGAR PRODUCTO -->

		<div class="card mt-3 border-bottom-primary">
			<div class="card-header text-primary font-weight-bold">
				Agregar Producto
			</div>	
			<div class="card-body">	
				<div class="row">
					<div class="col-md-2">
						<label for="validationTooltip01">Código</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".producto-modal"><font-awesome-icon icon="search"/></button>
							</div>

							<datalist id="productos">
							  <option v-for="producto in productos" :value="producto.CODIGO"></option>
							</datalist>

							<input ref="codigo" class="custom-select custom-select-sm" type="text" list="productos" v-model="codigoProducto" v-bind:class="{ 'is-invalid': validarCodigoProducto }" v-on:keyup="filtrarProductos()" v-on:blur="cargarProductos()">
						</div>
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
						<input class="form-control form-control-sm" type="text" v-on:keyup="formatoCantidad" v-on:blur="agregarProductoTransferencia()" v-bind:class="{ 'is-invalid': validarCantidad }" v-model="cantidadProducto">
					</div>

					<div class="col-md-2">
						<label for="validationTooltip01">Stock</label>
						<input class="form-control form-control-sm" type="text" v-model="stockProducto" disabled>
					</div>
				</div>
			</div>	
		</div>		

		<!-- FINAL AGREGAR PRODUCTO -->
		
        <!-- ------------------------------------------------------------------------------------- -->

        <!-- TABLA TRANSFERENCIA -->

		<div class="col-md-12 mt-3">
			<table id="tablaTransferencia" class="table table-striped table-bordered table-sm mb-3" style="width:100%">
                <thead>
                    <tr>
                        <th></th>
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
				<div class="text-right">
					<button v-on:click="guardarTransferencia()" class="btn btn-primary" id="guardar">Guardar</button>
					<button v-on:click="modificarTransferencia()" class="btn btn-warning" id="modificar">Modificar</button>
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

        <!-- MODAL PRODUCTOS -->

                <div class="modal fade producto-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle">Productos: </small></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                            <table id="tablaModalProductos" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Codigo</th>
                                        <th>Descripcion</th>
                                        <th>Precio Venta</th>
                                        <th>Precio Costo</th>
                                        <th>Precio Mayorista</th>
                                        <th>Stock</th>
                                        <th>IVA</th>
                                        <th>Imagen</th>
                                        <th>Moneda</th>
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

	    <!-- TOAST PRODUCTO TRANSFERENCIA MODIFICADO -->

		<b-toast id="toast-producto-transferencia-eliminado" variant="danger" solid>
	      <template v-slot:toast-title>
	        <div class="d-flex flex-grow-1 align-items-baseline">
	          <strong class="mr-auto">Éxito !</strong>
	          <small class="text-muted mr-2">eliminado</small>
	        </div>
	      </template>
	      Este producto ha sido eliminado con éxito !
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
		</div>
</template>
<script>

	import { HotTable } from '@handsontable/vue';
	export default {
      props: ['candec', 'monedaCodigo', 'tab_unica'],  
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
            moneda_enviar: 0
        }
      },
      components: {
        HotTable
      }, 
      methods: {
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
      	} ,
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
        cargarProductos() {

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES

            let me = this;

            // ------------------------------------------------------------------------

            // CONTROLAR SI HAY DATOS EN EL TEXTBOX Y RETORNAR SI NO EXISTE DATO

            if (me.codigoProducto.length <= 0) {
                me.inivarAgregarProducto();
                return;
            }	

            // ------------------------------------------------------------------------

            // OBTENER CODIGO PRODUCTO POR CODIGO INTERNO

    		Common.codigoInternoCommon(me.codigoProducto).then(data => {

    			// ------------------------------------------------------------------------

    			// SI DATA ES IGUAL A 0 NO ENCONTRO CODIGO INTERNO 

	    		if (data === '0') {
	            	data = me.codigoProducto; 
	            } 

            	// ------------------------------------------------------------------------

            	// CONSULTAR PRODUCTO DETERMINADO

                axios.post('/producto', {'codigo': data, 'Opcion': 1}).then(function (response) {
                    
                    if(response.data.producto === 0) {

                        // *******************************************************************

                        // MARCAR EN ROJO TEXTBOX SI NO EXISTE PRODUCTO
                    
                        me.validarCodigoProducto = true;
                        me.codigoProducto = '';
                        me.descripcionProducto = '';
                        me.precioProducto = '';
                        me.stockProducto = '';
                        em.monedaProducto = '';
                        me.ivaProducto = '';

                        // *******************************************************************

                    } else {


                        // *******************************************************************

                        // LLENAR DESCRIPCION DE PRODUCTO

                        me.codigoProducto = data;
                        me.descripcionProducto = response.data.producto[0].DESCRIPCION;
                        me.stockProducto  = response.data.producto[0].STOCK;
                        me.ivaProducto = response.data.producto[0].IVA;
                        me.monedaProducto = response.data.producto[0].MONEDA;

                        // *******************************************************************

                        // REVISAR SI PRECIO PRODUCTO ES IGUAL A LA MONEDA DEL SISTEMA, REALIZAR COTIZACION SI NO LO ES

                        Common.calcularCotizaciónPrecioCommon(response.data.producto[0].PREC_VENTA, response.data.producto[0].MONEDA, me.monedaCodigo, me.candec, me.tab_unica).then(data => {
						  me.precioProducto = data
						});

                        // *******************************************************************

                        me.validarCodigoProducto = false;

                        // *******************************************************************
                    }
                });

            	// ------------------------------------------------------------------------

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
            const inputCodigoProducto = this.$refs.codigo;

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

            if (me.precioProducto.length === 0  || me.precioProducto === '0') {
                me.validarPrecioUnitario = true;
                return;
            } else {
                me.validarPrecioUnitario = false;
            }

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

            // REVISAR SI EXISTE VALORES REPETIDOS EN TABLA TRANSFERENCIAS - SALIR SI EXISTE

            if (Common.existeProductoDataTableCommon(tableTransferencia, me.codigoProducto) == true) {
            	me.validarCodigoProducto = true;
            	me.$bvToast.show('toast-codigo-repetido');
            	return;
            } else {
            	me.validarCodigoProducto = false;
            }

            // ------------------------------------------------------------------------

            // REVISAR SI CANTIDAD SUPERA STOCK

            if (parseFloat(me.cantidadProducto) > parseFloat(me.stockProducto)) {
            	me.validarCantidad = true;
            	me.$bvToast.show('toast-cantidad-superada');
            	return;
            } else {
            	me.validarCantidad = false;
            }

            // ------------------------------------------------------------------------

            // CARGAR DATO EN TABLA TRANSFERENCIAS

            me.agregarFilaTabla(me.codigoProducto, me.descripcionProducto, me.cantidadProducto, me.precioProducto, iva, precio, me.ivaProducto, me.stockProducto);

            // ------------------------------------------------------------------------

            // VACIAR TEXTBOX AGREGAR PRODUCTO

            me.inivarAgregarProducto();

            // ------------------------------------------------------------------------

            // DAR FOCO A CODIGO PRODUCTO 
            
            this.$refs.codigo.focus();

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

			this.moneda_enviar = valor;

			// ------------------------------------------------------------------------ 

			// REVISAR QUE BOTON ESTA DESACTIVADO PARA REDIGIRLO A MODIFICAR O GUARDAR 
			
			if (document.getElementById('guardar').disabled === false) {
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
				transporta: me.codigoTransporta,
				recibe: me.codigoRecibe,
				monedaSistema: me.monedaCodigo,
				monedaEnviar: me.moneda_enviar,
				nro_caja: me.nro_caja,
				tab_unica: me.tab_unica
			}

			// ------------------------------------------------------------------------ 

			Common.guardarTransferenciaCommon(tableTransferencia.rows().data(), data).then(data => {
			  
				if (data === true) {
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
				} else {
					Swal.fire(
					  'Error !',
					  'La Transferencia no se ha guardado correctamente !',
					  'Error'
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

        	// ------------------------------------------------------------------------

        	// SE ENCARGA DE COMPROBAR SI EXISTE CODIGO EN LA URL PARA PODER CARGAR UNA TRANSFERENCIA EXISTENTE
        	
        	if (codigo !== undefined) {

        		// ------------------------------------------------------------------------

        		// OCULTAR BOTON GUARDAR 

        		Common.ocultarBoton('guardar');

        		// ------------------------------------------------------------------------

        		// AGREGAR CABECERA 

        		Common.obtenerCabeceraTransferenciaCommon(codigo).then(data => {
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

        		// AGREGAR CUERPO

        		Common.obtenerCuerpoTransferenciaCommon(codigo).then(data => {
        			data.map(function(x) {
					   
        			   // ------------------------------------------------------------------------
        			   	
					   // EMPEZAR A CARGAR PRODUCTOS EN TRANSFERENCIA 

					   me.agregarFilaTabla(x.CODIGO_PROD, x.DESCRIPCION, x.CANTIDAD, x.PRECIO, x.IVA, x.TOTAL, '', '');
					  
					   // ------------------------------------------------------------------------

					});
        		});

        		// ------------------------------------------------------------------------

        	} else {

        		// ------------------------------------------------------------------------

        		// OCULTAR BOTON MODIFICAR 

        		Common.ocultarBoton('modificar');

        		// ------------------------------------------------------------------------

        	}

        	// ------------------------------------------------------------------------

        }, agregarFilaTabla(codigo, descripcion, cantidad, precio, iva, total, iva_porcentaje, stock){

        	// ------------------------------------------------------------------------

        	//	INICIAR VARIABLES 

        	 var tableTransferencia = $('#tablaTransferencia').DataTable();

        	// ------------------------------------------------------------------------

        	// AGREGAR FILAS 

        	 tableTransferencia.rows.add( [ {
		                    "ITEM": '',
		                    "CODIGO":   codigo,
		                    "DESCRIPCION":     descripcion,
		                    "CANTIDAD": cantidad,
		                    "PRECIO":    precio,
		                    "IVA":       iva,
		                    "TOTAL":       total,
		                    "ACCION":    "&emsp;<a href='#' id='mostrarProductoFila' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a> &emsp;<a href='#' id='editarModal' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>&emsp;<a href='#' id='eliminarModal' title='Eliminar'><i class='fa fa-trash text-danger' aria-hidden='true'></i></a>",
		                    "IVA_PORCENTAJE": iva_porcentaje,
		                    "STOCK": stock
		                } ] )
		     .draw();

		    // ------------------------------------------------------------------------ 

            // AGREGAR INDEX A LA TABLA TRANSFERNCIAS

            tableTransferencia.on( 'order.dt search.dt', function () {
                tableTransferencia.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();

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
				    return Common.modificarTransferenciaCommon(codigo, tableTransferencia.rows().data(), data).then(data => {
				    if (!data === true) {
				        throw new Error('error');
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
						      'Modificado !',
						      'Se ha modificado correctamente la transferencia !',
						      'success'
					)
				  }
			})
			
			// ------------------------------------------------------------------------ 

			// DEVOLVER VALOR A VARIABLE

			this.moneda_enviar = 0;

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

            $(document).ready( function () {

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
                // INICIAR EL DATATABLE PRODUCTOS MODAL
                // ------------------------------------------------------------------------
                
                var tableProductos = $('#tablaModalProductos').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "url": "/producto",
                                 "dataType": "json",
                                 "type": "GET",
                                 "contentType": "application/json; charset=utf-8"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "DESCRIPCION" },
                            { "data": "PREC_VENTA" },
                            { "data": "PRECOSTO" },
                            { "data": "PREMAYORISTA" },
                            { "data": "STOCK" },
                            { "data": "IVA" },
                            { "data": "IMAGEN" },
                            { "data": "MONEDA" }
                        ]      
                    });
                
                // ------------------------------------------------------------------------

                // RECARGAR SIEMPRE TABLA PRODUCTOS 

                setInterval( function () {
				    tableProductos.ajax.reload( null, false ); // user paging is not reset on reload
				}, 30000 );

                // ------------------------------------------------------------------------
                
                // SELECCIONAR UNA FILA - SE PUEDE BORRAR - SIN USO


                $('#tablaModalProductos').on('click', 'tbody tr', function() {

                    if ($(this).hasClass('selected')) {
                        $(this).removeClass('selected');
                    } else {
                        tableProductos.$('tr.selected').removeClass('selected');
                        $(this).addClass('selected');
                    }

                    // *******************************************************************

                    // CARGAR LOS VALORES A LAS VARIABLES DE PRODUCTO

                    me.codigoProducto = tableProductos.row(this).data().CODIGO;
                    me.descripcionProducto = tableProductos.row(this).data().DESCRIPCION;  
                    me.stockProducto = tableProductos.row(this).data().STOCK;

                    Common.calcularCotizaciónPrecioCommon(tableProductos.row(this).data().PREC_VENTA, tableProductos.row(this).data().MONEDA, me.monedaCodigo, me.candec, me.tab_unica).then(data => {
						  me.precioProducto = data
					});

                   	me.ivaProducto = tableProductos.row(this).data().IVA;

                    // *******************************************************************

                    // CERRAR EL MODAL
                     
                     $('.producto-modal').modal('hide');

                    // *******************************************************************

                });

                //FIN TABLA MODAL PRODUCTOS
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

                	// PROHIBIR EDITADO SI CANTIDAD O PRECIO ES CERO

                	if (me.editarCantidad === '0' || me.editarPrecio === '0') {
                		me.$bvToast.show('toast-editar-cero');
                		return;	
                	}

                	// *******************************************************************

                	// PROHIBIR EDITADO SI CANTIDAD SUPERA STOCK

                	if (parseFloat(Common.quitarComaCommon(me.editarCantidad)) > parseFloat(Common.quitarComaCommon(me.editarStock))) {
                		me.$bvToast.show('toast-cantidad-superada');
                		return;	
                	}

                    // *******************************************************************

                    // CARGAR LO EDITADO

                    tableTransferencia.cell(me.editarRow, 3).data(me.editarCantidad).draw();
                    tableTransferencia.cell(me.editarRow, 4).data(me.editarPrecio).draw();

                    // *******************************************************************
                    
                    // CALCULAR PRECIO TOTAL

                    precio = Common.multiplicarCommon(me.editarCantidad, me.editarPrecio, me.candec);

                    // *******************************************************************

                    // CALCULO IVA 
		            
		            iva = Common.calcularIVACommon(precio, me.editarIvaProducto, me.candec);

		            // *******************************************************************

		            // CARGAR PRECIO CALCULADO 

                    tableTransferencia.cell(me.editarRow, 6).data(precio).draw();

                    // *******************************************************************

                    // CARGAR IVA CALCULADO 

                    tableTransferencia.cell(me.editarRow, 5).data(iva).draw();

                    // *******************************************************************

                    // LLAMAR TOAST MODIFICADO

                    me.$bvToast.show('toast-producto-transferencia-modificado');

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

                // MOSTRAR MODAL ELIMINAR

                $('#tablaTransferencia').on('click', 'tbody tr #eliminarModal', function() {

                    // *******************************************************************

                    // ABRIR EL MODAL

                    $('.eliminar-producto-transferencia-modal').modal('show');

                    // *******************************************************************

                });


                // ------------------------------------------------------------------------

                // ELIMINAR FILA

                $('#eliminarFila').on('click', function() {

                    // *******************************************************************

                    // ELIMINAR 
                    
	                tableTransferencia.row($(me.editarRow)).remove().draw(); 

                    // *******************************************************************

                    // LLAMAR TOAST ELIMINAR

                    me.$bvToast.show('toast-producto-transferencia-eliminado');

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
        }
    }
</script>
<style>

</style>