<template>
	<div class="container">
		<div v-if="$can('inventario.crear')">

			<div class="col-md-12 mt-3">

				<!-- ---------------------------------- TITULO  --------------------------------------------------- -->

		        <div class="section-title">
		            <h4>CREAR COMBO</h4>
		        </div>
				
			   <!-- ------------------------------------------------------------------------------------- -->

			</div>
			<div class="row">
				<div class="col-5">
					<div class="card">
						<div class="card-body">

							<label>Código Combo</label>
							<combo-filtrar v-bind:class="{ 'is-invalid': validar.codigo }" v-model="combo.codigo" ref="componente_textbox_combo" :codigo="codigo" @codigo="cargarCodigo" @descripcion="cargarDescripcion" @cantidad="cargarCantidad" @precio="cargarPrecio"></combo-filtrar>

							<label class="mt-3">Descripción</label>
							<input v-bind:class="{ 'is-invalid': validar.descripcion }" v-model="combo.descripcion" type="text" class="form-control form-control-sm">

							<div class="row">
								<div class="col-6">
									<label class="mt-3">Cantidad</label>
									<input v-bind:class="{ 'is-invalid': validar.cantidad }" v-model="combo.cantidad" type="text" class="form-control form-control-sm">
								</div>	
								<div class="col-6">
									<label class="mt-3">Precio Combo</label>
									<input v-bind:class="{ 'is-invalid': validar.precio }" v-model="combo.precio" type="text" class="form-control form-control-sm">
								</div>	
							</div>
						</div>
					</div>
				</div>

				<div class="col-5">
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-9">
									
									<!-- ----------------------------------- TEXTBOX PRODUCTOS  ----------------------------------- -->

									<codigo-producto v-bind:class="{ 'is-invalid': validar.cod_producto }" ref="compontente_codigo_producto" v-model="producto.CODIGO"  @codigo_producto="cargarProductos"></codigo-producto>
								</div>
								<div class="col-3">
									
									<!-- ---------------------------------- CANTIDAD ---------------------------------------------- -->

									<label>Desc. %</label>
									<input v-bind:class="{ 'is-invalid': validar.DESCUENTO }" v-model="producto.DESCUENTO" type="number" class="form-control form-control-sm">
								</div>
							</div>

							<div class="row mt-3">
								<!-- ------------------------------- PRECIO MAYORISTA------------------------------------------------ -->

								<div class="col-md-6">
									<label>Precio Mayorista</label>
									<input v-bind:class="{ 'is-invalid': validar.precioMayorista }" v-model="producto.PRECIO_MAYORISTA" type="text" class="form-control form-control-sm" disabled>
								</div>

								<!-- ---------------------------------- PRECIO UNITARIO ---------------------------------------------- -->

								<div class="col-md-6">
									<label>Precio Unitario</label>
									<input v-bind:class="{ 'is-invalid': validar.precioUnitario }" v-model="producto.PRECIO_UNITARIO" type="text" class="form-control form-control-sm" disabled>
								</div>
							</div>
							 
							<div class="row mt-3">
								<!-- ----------------------------------- DESCRIPCION DEL P. ------------------------------------------ -->

								<div class="col-md-6">
									<label>Stock Del Producto</label>
									<input v-bind:class="{ 'is-invalid': validar.stock }" v-model="producto.STOCK" type="text" class="form-control form-control-sm" disabled>
								</div>

								<!-- ---------------------------------- CANTIDAD ---------------------------------------------- -->

								<div class="col-md-6">
									<label>Cantidad</label>
									<input v-bind:class="{ 'is-invalid': validar.pro_cantidad }" v-model="producto.CANTIDAD" type="number" class="form-control form-control-sm">
							    </div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-2">
					<div class="card">
						<span v-html="imagen.RUTA"></span>
					</div>		
					<div class="col-md-12">
						<hr>
					</div>

					<div class="row mt-3 mb-3">
						<div class="col-md-6">
							<button class="btn btn-outline-dark btn-sm btn-block" v-on:click="nuevoC"><small>Nuevo</small></button>
						</div>
						<div class="col-md-6">
							<button class="btn btn-outline-dark btn-sm btn-block"  v-if='btnguardar' v-on:click="guardar"><small>Guardar</small></button>
					    	
							<button class="btn btn-outline-dark btn-sm btn-block" v-else v-on:click="guardar"><small>Modificar</small></button>
						</div>	
					</div>
				</div>
			</div>
			<!-- ------------------------------------------ TABLA DE PRODUCTOS ----------------------------------------------- -->
						
			<div class="row mt-4">
				<div class="col-12">
					
					<table id="tablaProductos" class="table table-hover table-bordered table-sm mb-3 mt-3" style="width:100%">
					    <thead>
					        <tr>
					            <th></th>
					            <th>Código Producto</th>
					            <th>Descripción</th>
					            <th>Desc.</th>
					            <th class="cantidadColumna">Cantidad</th>
					            <th class="precioMayColumna">Precio Mayorista</th>
					            <th class="precioUnitColumna">Precio Unitario</th>
					            <th>Acción</th>
					        </tr>
					    </thead>	
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
				            </tr>
				        </tfoot>	
			        </table>
				</div>
		    </div>

		<!-- ------------------------------------------------------------------------ -->

		<!-- MODAL DETALLE PRODUCTO -->

		<producto-detalle ref="detalle_producto" :codigo="codigo_detalle"></producto-detalle>


		<!--------------------------------------------------- MODAL EDITAR PRODUCTO ------------------------------------------------>

	    <div class="modal fade editar-producto-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
	            <div class="modal-content">
	                <div class="modal-header">
	                    <h5 class="modal-title text-primary text-center" ><font-awesome-icon icon="barcode"/> {{editar.codigo}}</h5>
	                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                     <span aria-hidden="true">&times;</span>
	                    </button>
	                </div>
	                <div class="modal-body">  
	                    <div class="row">
	                        <div class="col-md-12 mt-3">
	                            <label>Precio Unitario</label>
	                            <input type="text" name="" v-model="editar.precioUnitario" class="form-control form-control-sm" disabled>
	                        </div>
	                        <div class="col-md-12 mt-3">
	                            <label>Descuento %</label>
	                            <input type="text" name="" v-model="editar.descuento" class="form-control form-control-sm">
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

	    <!-- ------------------------------------ TOAST FALTA COMPLETAR ------------------------------------ -->

		<b-toast id="toast-falta-productos" variant="warning" solid>
		    <template v-slot:toast-title>
		      	<div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">¡Error!</strong>
		          <small class="text-muted mr-2">Incompleto</small>
		        </div>
		    </template>
		      ¡Para guardar se necesitan añadir productos en la tabla!
		</b-toast>

		<!-- ----------------------------------- TOAST DESCUENTO SUPERADO ------------------------------------- -->

			<b-toast id="toast-descuento-error" variant="warning" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">¡Error!</strong>
		          <small class="text-muted mr-2">Equivocado</small>
		        </div>
		      </template>
		      ¡Error en la aplicación del descuento, favor verifque su valor!
		    </b-toast>

		<!-- ----------------------------------- TOAST DESCUENTO SUPERADO ------------------------------------- -->

			<b-toast id="toast-stock-cero" variant="warning" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">¡Error!</strong>
		          <small class="text-muted mr-2">Stock cero</small>
		        </div>
		      </template>
		      ¡Sin stock!
		    </b-toast>

		<!-- ----------------------------------- TOAST DESCUENTO SUPERADO ------------------------------------- -->

			<b-toast id="toast-cantidad-superada" variant="warning" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">¡Error!</strong>
		          <small class="text-muted mr-2">superado</small>
		        </div>
		      </template>
		      ¡La cantidad ingresada ya supera el stock!
		    </b-toast>
		
		<!-- --------------------------------- TOAST FALTA COMPLETAR --------------------------------------- -->

			<b-toast id="toast-no-existe" variant="warning" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">¡Error!</strong>
		          <small class="text-muted mr-2">No existe</small>
		        </div>
		      </template>
		      ¡Este código no existe!
		    </b-toast>

		<!-- ------------------------------------- TOAST PRODUCTO TRANSFERENCIA MODIFICADO ----------------------------------- -->

			<b-toast id="toast-producto-modificado" variant="success" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <strong class="mr-auto">¡Éxito!</strong>
		          <small class="text-muted mr-2">modificado</small>
		        </div>
		      </template>
		      ¡Este producto ha sido modificado con éxito!
		    </b-toast>
		    
		<!-- ------------------------------------------------TOAST CODIGO PRODUCTO REPETIDO-------------------------------------- -->

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

		<!-- ------------------------------------------------------------------------ -->
		</div>
		<div v-else>
			<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div>
		
		<!-- ------------------------------------------------------------------------ -->
	</div>	
</template>
<script>
	export default{
      props: ['candec', 'monedaCodigo', 'id_sucursal'],
		data(){
			return {
				combo: {
					codigo: '',
					descripcion: '',
					cantidad: '',
					precio: '',
				},

				producto: {
					CODIGO: '',
					PRECIO_UNITARIO: '',
					PRECIO_MAYORISTA: '',
					STOCK: '',
					PRECIO_TOTAL_MAY: '',
					PRECIO_TOTAL_UNIT: '',
					CANTIDAD: 1,
					DESCUENTO: 0,
					DESCUENTO_UNITARIO: ''
				},

				validar:{
					codigo: false,
					descripcion: false,
					cantidad: false,
					precio: false,
					precioUnitario: false,
					precioMayorista: false,
					cod_producto: false,
					pro_descripion: false,
					pro_cantidad: false,
					stock: false,
				},
				imagen: {
	         		RUTA: "<img src='http://172.16.249.20:8080/storage/imagenes/productos/product.png'  class='card-img-top'>",
	         	},
	         	codigo_detalle: '',
	         	editar: {
	         		codigo: '',
	         		precioUnitario: '',
	         		descuento: '',
	         		row: '',
	         	},

	         	datos: {
	         		cabecera: '',
	         		moneda: '',
	         		productos: []
	         	},
	         	btnguardar: true,
	         	existe: false
			}
		},
		methods:{

			inivarAgregarProducto(){

				// ------------------------------------------------------------------------

				// VACIAR TEXTBOX 

				this.producto = {
					CODIGO: '',
					PRECIO_UNITARIO: '',
					PRECIO_MAYORISTA: '',
					STOCK: '',
					PRECIO_TOTAL_MAY: '',
					PRECIO_TOTAL_UNIT: '',
					CANTIDAD: 1,
					DESCUENTO: 0
				}

	            // ------------------------------------------------------------------------   

			}, 

			guardar(){
				let me = this;
	        	var tableProductos = $('#tablaProductos').DataTable();

				if(me.controlar() === true){

					me.datos = {
		        		cabecera: me.combo,
		        		moneda: me.monedaCodigo,
		        		productos: tableProductos.rows().data().toArray()
		        	}

		        	Swal.fire({
					  title: 'Guardando Combo',
					  html: 'Cerrare en cuanto guarde.',
					  onBeforeOpen: () => {

					  	// ------------------------------------------------------------------------

					  	// MOSTRAR CARGAR 

					    Swal.showLoading()

					    // ------------------------------------------------------------------------

					    Common.guardarComboCommon(me.datos).then(data => {

					    		// ------------------------------------------------------------------------

						    	if(!data.response === true){
						          throw new Error(data.statusText);
						        }

						        if(data.response){

						        	Swal.close();

									// ------------------------------------------------------------------------

									Swal.fire(
										'¡Guardado!',
										 data.statusText,
										'success'
									)

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
				}
			},

			controlar(){

				let me = this;

				var guardar = true;
	            var tableProductos = $("#tablaProductos").DataTable();

				if(me.combo.descripcion.length  === 0 || me.combo.descripcion === ' '){
					me.validar.descripcion = true;
					guardar = false;
				}else{
					me.validar.descripcion = false;
				}

				if(me.combo.codigo.length  === 0 || me.combo.codigo === ' '){
					me.validar.codigo = true;
					guardar = false;
				}else{
					me.validar.codigo = false;
				}

				if(me.combo.cantidad.length  === 0 || me.combo.cantidad === '0'){
					me.validar.cantidad = true;
					guardar = false;
				}else{
					me.validar.cantidad = false;
				}

				if(me.combo.precio.length  === 0 || me.combo.precio === '0' || me.combo.precio === '0.00'){
					me.validar.precio = true;
					guardar = false;
				}else{
					me.validar.precio = false;
				}

				if(tableProductos.rows().data().length === 0){

		           	// ------------------------------------------------------------------------

		            // LLAMAR TOAST TABLA INCOMPLETA

		            me.$bvToast.show('toast-falta-productos');
		            return;
				}

				return guardar;
			},

			nuevoC(){

      			// ------------------------------------------------------------------------ 

      			// RECARGAR LA PAGINA 

      			Swal.fire({
					title: '¿Crear nuevo combo?',
					type: 'warning',
					showLoaderOnConfirm: true,
					showCancelButton: true,
					confirmButtonColor: '#d33',
					cancelButtonColor: '#3085d6',
					confirmButtonText: 'Aceptar',
					cancelButtonText: 'Cancelar'
				}).then((result) => {
					
					if (result.value === true) {
						window.location.href = '/comb1';
					}
					
				})

	        	// ------------------------------------------------------------------------

      		},

      		cargarCodigo(valor){

				this.combo.codigo = valor;
				this.btnguardar = false;
				this.existe = true;

				var tableProductos = $('#tablaProductos').DataTable();
	        	tableProductos.clear().draw();

	        	// ------------------------------------------------------------------------

	        	// INICIAR VARIABLES 

	        	let me = this;

	        	// ------------------------------------------------------------------------

	        	// SE ENCARGA DE COMPROBAR SI EXISTE CODIGO EN LA URL PARA PODER CARGAR UN COMBO EXISTENTE
	        	
	        	if (codigo !== undefined) {

	        		// ------------------------------------------------------------------------

	        		// OCULTAR BOTON GUARDAR 

	        		me.btnguardar = false;

	        		// ------------------------------------------------------------------------

	        		// AGREGAR CABECERA, ENVIO CERO PARA CODIGO ORIGEN PARA ESPECIFICAR QUE
	        		// NECESITO DATOS DE LA PROPIA SUCURSAL DEL USUARIO

	        		Common.obtenerCuerpoComboCommon(codigo, 0).then(data => {
	        			data.map(function(x) {
						   
						   // ------------------------------------------------------------------------

						   // CALCULAR STOCK ACTUAL CON POSIBLE ELIMINACION

						   stock = parseFloat(x.STOCK) + parseFloat(x.CANTIDAD);

	        			   // ------------------------------------------------------------------------
	        			   	
						   // EMPEZAR A CARGAR PRODUCTOS EN REMISION 

						   me.agregarFilaTabla(x.CODIGO_PROD, x.DESCRIPCION, x.CANTIDAD, x.PRECIO);
						  
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

	        cargarCantidad(valor){

				this.combo.cantidad = valor;
			},
			
			cargarDescripcion(valor){

				this.combo.descripcion = valor;
			},
			
			cargarPrecio(valor){

				this.combo.precio = valor;
			},

      		recargar(){

      			// ------------------------------------------------------------------------ 

      			// RECARGAR LA PAGINA 

      			window.location.href = '/comb1';

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

		        // REVISAR SI ES PARA DESCUENTO O CANTIDAD 

		        if (codigo.substring(0, 1) === "+") {

		        	me.producto.CANTIDAD = codigo.substring(1, 20);
		        	me.producto.CODIGO = '';
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

		        	me.producto.CODIGO = '';
		        	me.producto.TIPO_DESCUENTO = 3;
		        	return;
		        }

	            // ------------------------------------------------------------------------

	            // CONSULTAR PRODUCTO CON LOTE 

	            Common.obtenerProductoPOSCommon(codigo, me.monedaCodigo).then(data => {

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

		            	me.producto.CODIGO = data.producto.CODIGO;
		            	me.producto.DESCRIPCION = data.producto.DESCRIPCION;
		            	me.producto.PRECIO_UNITARIO = data.producto.PREC_VENTA;
		            	me.producto.PRECIO_MAYORISTA = data.producto.PREMAYORISTA;
		            	me.producto.STOCK = data.producto.STOCK;

						// ------------------------------------------------------------------------

						// IMAGEN DEL PRODUCTO 

						// me.imagen.RUTA = data.imagen;

						// ------------------------------------------------------------------------

						// AGREGAR PRODUCTO 

						me.agregarProducto();

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
	            var tableProductos = $("#tablaProductos").DataTable();
	            var rowClass = "";

	            // ------------------------------------------------------------------------

	            // CONTROLADOR

	            if (me.producto.CODIGO.length === 0) {
	                me.validar.CODIGO = true;
	                return;
	            } else {
	                me.validar.CODIGO = false;
	            }

	            if (me.producto.PRECIO_UNITARIO.length === 0) {
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

	            if (me.producto.DESCUENTO > 0 && me.producto.DESCUENTO < 100) {
	            	rowClass = "table-warning";
	            } else if (me.producto.DESCUENTO == 100) {
	            	rowClass = "table-danger";
	            }
	            
	            // ------------------------------------------------------------------------

	            // DESCUENTO UNITARIO 

	            me.producto.DESCUENTO_UNITARIO = Common.descuentoCommon(me.producto.DESCUENTO, me.producto.PRECIO_UNITARIO, me.candec);

	            // ------------------------------------------------------------------------

	            // RESTAR DESCUENTO DE PRECIO UNITARIO 

	            me.producto.PRECIO_UNITARIO = Common.restarCommon(me.producto.PRECIO_UNITARIO, me.producto.DESCUENTO_UNITARIO, me.candec);

	            // ------------------------------------------------------------------------

	            // CARGAR DATO EN TABLA VENTAS

	            me.agregarFilaTabla(me.producto.CODIGO, me.producto.DESCRIPCION, me.producto.DESCUENTO, me.producto.CANTIDAD, me.producto.PRECIO_UNITARIO, me.producto.PRECIO_MAYORISTA, tipo, rowClass);

	            // ------------------------------------------------------------------------

	            // VACIAR TEXTBOX AGREGAR PRODUCTO

	            me.inivarAgregarProducto();

	    		// ------------------------------------------------------------------------

		        // LLAMAR AL METODO HIJO 

		        this.producto.CODIGO = '';
		        this.$refs.compontente_codigo_producto.vaciarDevolver();

		        // ------------------------------------------------------------------------

	        },

	        agregarFilaTabla(codigo, descripcion, descuento, cantidad, precio, premayorista, tipo, rowClass){

	        	// ------------------------------------------------------------------------
	        	
	        	//	INICIAR VARIABLES 

	        	let me = this;
	        	var tableProductos = $('#tablaProductos').DataTable();
	        	var productoExistente = [];
	        	var cantidadNueva = 0;

	        	// ------------------------------------------------------------------------

	            // REVISAR SI EXISTE VALORES REPETIDOS EN TABLA TRANSFERENCIAS 
	            // LA OPCION 1 ES PARA DEVOLVER SOLO TRUE O FALSE SI EXISTE O NO
	            // LA OPCION 2 ES PARA DEVOLVER MAS DATOS DEL PRODUCTO 

	            productoExistente = Common.existeProductoDataTableCommon(tableProductos, codigo, 2);
	           	
	            if (productoExistente.respuesta == true && tipo !== 2) {

	            	// ------------------------------------------------------------------------

	            	// SUMAR LA CANTIDAD E IVA

	            	cantidadNueva = parseFloat(productoExistente.cantidad) + parseFloat(cantidad);

		            // ------------------------------------------------------------------------

	            	// REVISAR SI LA NUEVA CANTIDAD SUPERA AL STOCK

	            	if (cantidadNueva > me.producto.STOCK) {
	            		me.$bvToast.show('toast-cantidad-superada');
		            	return;
	            	}

	            	// ------------------------------------------------------------------------

	            	// EDITAR CANTIDAD PRODUCTO 
	            	
	            	me.editarCantidadProducto(tableProductos, cantidadNueva, precio, productoExistente.row, descuento, rowClass);
	            	return;

	            	// ------------------------------------------------------------------------

	            } else if (productoExistente.respuesta == true){
	            	me.validar.cod_producto = false;
	            	me.$bvToast.show('toast-servicio-repetido');
	            	return;
	            }

	            // ------------------------------------------------------------------------

	        	// AGREGAR FILAS 

	        	tableProductos.rows.add( [ {
			                    "ITEM": '',
			                    "CODIGO":   codigo,
			                    "DESCRIPCION":     descripcion,
			                    "DESCUENTO": descuento,
			                    "CANTIDAD": cantidad,
			                    "PRECIO_MAYORISTA": premayorista,
			                    "PRECIO_UNITARIO":    precio,
			                    "ACCION":    "&emsp;<a role='button' id='mostrarProductoFila' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a> &emsp;<a role='button'  title='Eliminar'><i id='eliminarProducto' class='fa fa-trash text-danger' aria-hidden='true'></i></a>",
			                } ] )
			     .draw()
			     .nodes()
    			 .to$()
			     .addClass(rowClass);
				
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
	        editarCantidadProducto(tabla, cantidad, precio, row, descuento, rowClass){
	        	
	        	// ------------------------------------------------------------------------

	        	// INICIAR VARIABLES

	        	let me = this;

	        	// ------------------------------------------------------------------------

	            // PROHIBIR EDITADO SI CANTIDAD O PRECIO ES CERO

	            if (cantidad === '0') {
	                me.$bvToast.show('toast-editar-cero');
	                return;	
	            }

	            // ------------------------------------------------------------------------

	            // CARGAR DESCUENTO

	            tabla.cell(row, 3).data(descuento).draw();

	            // ------------------------------------------------------------------------

	            // CARGAR CANTIDAD

	            tabla.cell(row, 4).data(cantidad).draw();

	            // ------------------------------------------------------------------------

	            // CARGAR PRECIO 
	            
	            tabla.cell(row, 6).data(precio).draw();

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

	        },
		},

		mounted(){

			let me = this;

			// INICIAR EL DATATABLE PRODUCTOS 

            // ------------------------------------------------------------------------

            var tableProductos = $('#tablaProductos').DataTable({
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "searching": false,
                        "paging": false,
                        "columns": [
                            { "data": "ITEM" },
                            { "data": "CODIGO" },
                            { "data": "DESCRIPCION" },
                            { "data": "DESCUENTO" },
                            { "data": "CANTIDAD" },
                            { "data": "PRECIO_MAYORISTA" },
                            { "data": "PRECIO_UNITARIO" },
                            { "data": "ACCION" },
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

						  // PRECIO UNITARIO 

						  api.columns('.precioUnitColumna', {
						    
						  }).every(function() {
						    var precioUnit = this
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

						      $( api.columns('.precioUnitColumna').footer() ).html(
					                Common.darFormatoCommon(precioUnit, me.candec)
					           );

						      // *******************************************************************

							});

						  // *******************************************************************

						  // PRECIO

						  api.columns('.precioMayColumna', {
						    
						  }).every(function() {
						    var precioMay = this
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

						      $( api.columns('.precioMayColumna').footer() ).html(
					                Common.darFormatoCommon(precioMay, me.candec)
					           );

						      // *******************************************************************

							});
						}
					});

			// ------------------------------------------------------------------------

           	// MOSTRAR MODAL PRODUCTO

            $('#tablaProductos').on('click', 'tbody tr #mostrarProductoFila', function() {

	            // *******************************************************************

	            // OBTENER DATOS DEL PRODUCTO DATATABLE JS

	            me.codigo_detalle = tableProductos.row($(this).parents('tr')).data().CODIGO;
	            me.$refs.detalle_producto.mostrar();

				// *******************************************************************

            });

            // ------------------------------------------------------------------------

			// MOSTRAR SWEET ELIMINAR

            $('#tablaProductos').on('click', 'tbody tr #eliminarProducto', function() {

                // *******************************************************************

                // ABRIR EL SWEET ALERT

                Swal.fire({
					  title: '¿Estás seguro?',
					  text: "¡Eliminar el producto " + tableProductos.row($(this).parents('tr')).data().CODIGO + "!",
					  type: 'warning',
					  showLoaderOnConfirm: true,
					  showCancelButton: true,
					  confirmButtonColor: '#d33',
					  cancelButtonColor: '#3085d6',
					  confirmButtonText: '¡Sí, eliminalo!',
					  cancelButtonText: 'Cancelar'

				}).then((result) => {

					if (result.value) {

					  	// *******************************************************************

	                    // ELIMINAR 
	                    
		                tableProductos.row($(this).parents('tr')).remove().draw(); 

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
		}
	}
</script>