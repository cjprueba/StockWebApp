<template>
	
	<div class="container-fluid mt-4">
		
		<!-- ---------------------------------- TITULO  --------------------------------------------------- -->

        <div class="col-md-12 mt-3">
            <div class="section-title">
                <h4>CAMBIO DE PRECIO</h4>
            </div>
        </div>

	    <div class="card">
	      	<div class="card-body">
	      		<div class="row">
					<div class="col-md-4">
						<label>Para: </label>
						<input v-bind:class="{ 'is-invalid': validar.receptor }" v-model="producto.receptor" type="text" class="form-control form-control-sm">
					</div>
	      		</div>

				<div class="row">
					<div class="col-md-12">
						<label class="mt-3">Observación</label>
						<textarea class="playSound form-control" id="exampleFormControlTextarea1" v-model="motivo" rows="3"></textarea>
					</div>
				</div>
				<div class="row mt-3">

					<!-- ----------------------------------- TEXTBOX PRODUCTOS  ----------------------------------------- -->

					<div class="col-md-3">
						<codigo-producto v-bind:class="{ 'is-invalid': validar.cod_producto }" @codigo_producto="cargarProductos" ref="compontente_codigo_producto" v-model="producto.cod_producto" disabled></codigo-producto>
					</div>

					<!-- ----------------------------------- DESCRIPCION DEL P. ------------------------------------------ -->

					<div class="col-md-6">
						<label>Descripción Del Producto</label>
						<input v-bind:class="{ 'is-invalid': validar.descripion }" v-model="producto.descripcion" type="text" class="form-control form-control-sm" disabled>
					</div>

					<!-- ----------------------------------- BTN AGREGAR A DATATABLE ------------------------------------- -->
					<div class="col-md">
					  	<div class="text-center mt-3"> 
					  		<button class="btn btn-primary" v-on:click="agregarProducto()">Agregar Cambio</button>
					  	</div>
					</div>

				</div>

				<!-- ---------------------------- PRECIO UNITARIO ---------------------------- -->

				<div class="row mt-3">

					<!-- ------------------------------- PRECIO MAYORISTA------------------------------------------------ -->

					<div class="col-md-3">
						<label>Precio Mayorista</label>
						<input v-bind:class="{ 'is-invalid': validar.precioMayorista }" v-model="producto.precioMayorista" type="text" class="form-control form-control-sm" disabled>
					</div>

					<!-- ------------------------------- NUEVO PRECIO MAYORISTA------------------------------------------------ -->

					<div class="col-md-3">
						<label>Nuevo Precio Mayorista</label>
						<input v-bind:class="{ 'is-invalid': validar.precioMayoristaNuevo }" v-model="producto.precioMayoristaNuevo" type="text" class="form-control form-control-sm">
					</div>


					<!-- ---------------------------------- PRECIO UNITARIO ---------------------------------------------- -->

					<div class="col-md-3">
						<label>Precio Unitario</label>
						<input v-bind:class="{ 'is-invalid': validar.precioUnitario }" v-model="producto.precioUnitario" type="text" class="form-control form-control-sm" disabled>
					</div>
					<!-- ---------------------------------- NUEVO PRECIO UNITARIO ---------------------------------------------- -->

					<div class="col-md-3">
						<label>Nuevo Precio Unitario</label>
						<input v-bind:class="{ 'is-invalid': validar.precioUnitarioNuevo }" v-model="producto.precioUnitarioNuevo" type="text" class="form-control form-control-sm">
					</div> 
				</div>

				<div class="row">
					<!-- --------------------------------------------MOSTRAR LOADING------------------------------------------------- -->

					<div class="col-md-12">
						<div v-if="procesar" class="d-flex justify-content-center mt-3">
								Guardando...
					        <div class="spinner-grow text-success" role="status" aria-hidden="true"></div>
					    </div>
				    </div>
						
					<!-- ------------------------------------------ TABLA DE PRODUCTOS ----------------------------------------------- -->
						
					<div class="col-md-12 mt-4">

						<table id="tablaProductos" class="table table-hover table-bordered table-sm mb-3 mt-3" style="width:100%">
				            <thead>
				                <tr>
				                    <th></th>
				                    <th>Código Producto</th>
				                    <th>Descripción</th>
				                    <th>Precio Mayorista</th>
				                    <th>Nuevo Precio Mayorista</th>
				                    <th>Precio Unitario</th>
				                    <th>Nuevo Precio Unitario</th>
				                    <th>Acción</th>
				                </tr>
				            </thead>	
		            	</table>
		            </div>
		        </div>
			</div>
		</div>

		<!-- ------------------------------------------------------ MODALES------------------------------------------------------ -->

		<!--------------------------------------------------- MODAL EDITAR PRODUCTO ------------------------------------------------>

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
	                            <label>Precio Mayorista</label>
	                            <input type="text" name="" v-model="editarPrecioMayorista" class="form-control form-control-sm" disabled>
	                        </div>
	                        <div class="col-md-12 mt-3">
	                            <label>Nuevo Precio Mayorista</label>
	                            <input type="text" name="" v-model="editarPrecioMayoristaNew" v-on:blur="formatoEditarPrecioMay()" class="form-control form-control-sm">
	                        </div>
	                    </div>
	                    <div class="row">
	                        <div class="col-md-12 mt-3">
	                            <label>Precio Unitario</label>
	                            <input type="text" name="" v-model="editarPrecioUnitario" class="form-control form-control-sm" disabled>
	                        </div>
	                        <div class="col-md-12 mt-3">
	                            <label>Nuevo Precio Unitario</label>
	                            <input type="text" name="" v-model="editarPrecioUnitarioNew" v-on:blur="formatoEditarPrecioUnit()" class="form-control form-control-sm">
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

	    <!-- ---------------------------------------------TOAST PRODUCTO MODIFICADO----------------------------------------------- -->

		<b-toast id="toast-producto-modificado" variant="success" solid>
		    <template v-slot:toast-title>
		      	<div class="d-flex flex-grow-1 align-items-baseline">
		          <strong class="mr-auto">¡Éxito!</strong>
		          <small class="text-muted mr-2">modificado</small>
		        </div>
		    </template>
		    ¡Este producto ha sido modificado con éxito!
		</b-toast>

		<!-- ---------------------------------------------TOAST PRODUCTO MODIFICADO----------------------------------------------- -->

		<b-toast id="toast-producto-existente" variant="warning" solid>
		    <template v-slot:toast-title>
		      	<div class="d-flex flex-grow-1 align-items-baseline">
		          <strong class="mr-auto">¡Ya existen datos!</strong>
		          <small class="text-muted mr-2">advertencia</small>
		        </div>
		    </template>
		    ¡Este producto ya existe en la Tabla!
		</b-toast>

		<!-- ---------------------------------------------TOAST PRODUCTO SIN VARIACION ----------------------------------------------- -->

		<b-toast id="toast-producto-sin-cambios" variant="warning" solid>
		    <template v-slot:toast-title>
		      	<div class="d-flex flex-grow-1 align-items-baseline">
		          <strong class="mr-auto">¡No hay modificaciones!</strong>
		          <small class="text-muted mr-2">advertencia</small>
		        </div>
		    </template>
		    ¡No has registrado ningun cambio en algun precio!
		</b-toast>


		<!-- ------------------------------------ TOAST FALTA COMPLETAR ------------------------------------ -->

		<b-toast id="toast-falta-completar" variant="warning" solid>
		    <template v-slot:toast-title>
		      	<div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">¡Error!</strong>
		          <small class="text-muted mr-2">Incompleto</small>
		        </div>
		    </template>
		      ¡Para agregar se necesita añadir más datos!
		</b-toast>

		<!-- -------------------------------------- TOAST PRECIO CERO ---------------------------------- -->

		<b-toast id="toast-precio-cero" variant="warning" solid>
		    <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">¡Error!</strong>
		          <small class="text-muted mr-2">cero</small>
		        </div>
		    </template>
		      ¡El precio no puede ser cero!
		</b-toast>
	</div>
</template>
<script>
	
	export default{
      props: ['candec', 'monedaCodigo', 'tab_unica', 'id_sucursal'],
		data(){
			return{

            	motivo: '',
				existe: false, 
				btnguardar: true,
				receptor: '',
				procesar: false,
				producto: {
					cod_producto: '',
					descripcion: '',
					precioUnitario: '',
					precioMayorista: '',
					precioUnitarioNuevo: '',
					precioMayoristaNuevo: '',
				},
				validar:{
					cod_producto: false,
					descripcion: false,
					precioUnitario: false,
					precioMayorista: false,
					precioUnitarioNuevo: false,
					precioMayoristaNuevo: false,
				},
				editarPrecioUnitario: '',
				editarPrecioMayorista: '',
				editarPrecioUnitarioNew: '',
				editarPrecioMayoristaNew: '',
				editarCodigo: '',
	            editarRow: '',
			}
		},

		methods:{

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

	            // CONSULTAR PRODUCTO DETERMINADO

	            axios.post('/productoBuscar', {'codigo': codigo}).then(function (response) {
	                   
	                    if(response.data.producto === 0) {

	                        // *******************************************************************

	                        // MARCAR EN ROJO TEXTBOX SI NO EXISTE PRODUCTO
	                    
	                        me.validar.cod_producto = true;
	                        me.producto.cod_producto = '';
	                        me.producto.descripcion = '';
	                        me.producto.precioUnitario = '';

	                    } else {

	                        // LLENAR DESCRIPCION DE PRODUCTO
	                     
	                        me.producto.cod_producto = response.data.producto.CODIGO;
	                        me.producto.descripcion = response.data.producto.DESCRIPCION;
	                        me.producto.precioUnitario = Common.darFormatoCommon(response.data.producto.PREC_VENTA, me.candec);
	                        me.producto.precioMayorista = Common.darFormatoCommon(response.data.producto.PREMAYORISTA, me.candec);
	                        me.producto.precioUnitarioNuevo = Common.darFormatoCommon(response.data.producto.PREC_VENTA, me.candec);
	                        me.producto.precioMayoristaNuevo = Common.darFormatoCommon(response.data.producto.PREMAYORISTA, me.candec);


	                        me.validar.cod_producto = false;

	                        // *******************************************************************
	                    }
	            });

	    		// ------------------------------------------------------------------------

	        },

	        inivarAgregarProducto(){

				// ------------------------------------------------------------------------

				// VACIAR TEXTBOX 

				this.producto.cod_producto = '';
				this.producto.descripcion = '';
	            this.producto.precioUnitario = '';
	            this.producto.precioMayorista = '';
	            this.producto.precioUnitarioNuevo = '';
	            this.producto.precioMayoristaNuevo = '';

	            // ------------------------------------------------------------------------   

			},

			agregarProducto(){

	            // ------------------------------------------------------------------------

	            // INICIAR VARIABLES

	            let me = this;
	            var tableProductos = $('#tablaProductos').DataTable();
	            var agregar = true;

	            // ------------------------------CONTROLADOR------------------------------------------

	            // CONTROLADOR

	            if (me.producto.cod_producto.length === 0) {
	                me.validar.cod_producto = true;
	                agregar = false;
	            } else {
	                me.validar.cod_producto = false;
	            }

	            // ------------------------------DESCRIPCION------------------------------------------

	            if (me.producto.descripcion.length === 0) {
	                me.validar.descripcion = true;
	                agregar = false;
	            } else {
	                me.validar.descripcion = false;
	            }

	            // ------------------------------PRECIO------------------------------------------

	            if (me.producto.precioUnitario.length === 0  || me.producto.precioUnitario === '0' || me.producto.precioUnitario === '0.00') {
	                me.validar.precioUnitario = true;
	                agregar = false;
	            } else {
	                me.validar.precioUnitario = false;
	            }

	            // ------------------------------PRECIO------------------------------------------

	            if (me.producto.precioMayorista.length === 0  || me.producto.precioMayorista === '0' || me.producto.precioMayorista === '0.00') {
	                me.validar.precioMayorista = true;
	                agregar = false;
	            } else {
	                me.validar.precioMayorista = false;
	            }

	            // ------------------------------PRECIO NUEVO------------------------------------------

	            if (me.producto.precioUnitarioNuevo.length === 0  || me.producto.precioUnitarioNuevo === '0' || me.producto.precioUnitarioNuevo === '0.00') {
	                me.validar.precioUnitarioNuevo = true;
	                agregar = false;

	            } else {
	                me.validar.precioUnitarioNuevo = false;
	            }

	            // ------------------------------PRECIO NUEVO------------------------------------------

	            if (me.producto.precioMayoristaNuevo.length === 0  || me.producto.precioMayoristaNuevo === '0' || me.producto.precioMayoristaNuevo === '0.00') {
	                me.validar.precioMayoristaNuevo = true;
	                agregar = false;

	            } else {
	                me.validar.precioMayoristaNuevo = false;
	            }

	            // ------------------------------------------------------------------------

	            // CARGAR DATO EN TABLA

	            if(agregar === true){

	           		me.agregarFilaTabla(me.producto.cod_producto, me.producto.descripcion, me.producto.precioUnitario, me.producto.precioMayorista, me.producto.precioUnitarioNuevo, me.producto.precioMayoristaNuevo);
	           	}else{

		           	// ------------------------------------------------------------------------

		            // LLAMAR TOAST MODIFICADO

		            me.$bvToast.show('toast-falta-completar');

		            // ------------------------------------------------------------------------
	            	return;
	           	}

		        // this.$refs.compontente_codigo_producto.vaciarDevolver();

		        // ------------------------------------------------------------------------

	        },

	        agregarFilaTabla(codigo, descripcion, precioUnit, preMayorista, nuevoPrecioUnit, nuevoPrecioMay){

	        	// ------------------------------------------------------------------------

	        	//	INICIAR VARIABLES 

	        	let me = this;
	        	var tableProductos = $('#tablaProductos').DataTable();
	        	var productoExistente = [];

	        	// ------------------------------------------------------------------------

	            // REVISAR SI EXISTE VALORES REPETIDOS EN TABLA  
	            // LA OPCION 1 ES PARA DEVOLVER SOLO TRUE O FALSE SI EXISTE O NO
	            // LA OPCION 2 ES PARA DEVOLVER MAS DATOS DEL PRODUCTO 

	            productoExistente = Common.productoExisteDataTableCommon(tableProductos, codigo, 2);
	           
	            if (productoExistente.respuesta === true) {

		           	// ------------------------------------------------------------------------

		            // LLAMAR TOAST MODIFICADO

		            me.$bvToast.show('toast-producto-existente');

		            // ------------------------------------------------------------------------
	            	return;

	            	// ------------------------------------------------------------------------

	            } else {
	            	me.validar.cod_producto = false;
	            }

	            if(precioUnit === nuevoPrecioUnit && preMayorista === nuevoPrecioMay){

		           	// ------------------------------------------------------------------------

		            // LLAMAR TOAST MODIFICADO

		            me.$bvToast.show('toast-producto-sin-cambios');

		            // ------------------------------------------------------------------------

	            	return;

	            	// ------------------------------------------------------------------------

	            }

	            // ------------------------------------------------------------------------

	        	// AGREGAR FILAS 

	        	 tableProductos.rows.add( [ {
			                    "ITEM": '',
			                    "CODIGO":   codigo,
			                    "DESCRIPCION":     descripcion,
			                    "PREMAYORISTA": Common.darFormatoCommon(preMayorista, me.candec),
			                    "NUEVOPREMAYORISTA": Common.darFormatoCommon(nuevoPrecioMay, me.candec),
			                    "PRECIO":    Common.darFormatoCommon(precioUnit, me.candec),
			                    "NUEVOPRECIO":    Common.darFormatoCommon(nuevoPrecioUnit, me.candec),
			                    "ACCION":    "&emsp;<a role='button' id='editarModal' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>&emsp;<a role='button'  title='Eliminar'><i id='eliminarModal' class='fa fa-trash text-danger' aria-hidden='true'></i></a>",
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

	            // VACIAR TEXTBOX AGREGAR PRODUCTO

	            me.inivarAgregarProducto();

	            // ------------------------------------------------------------------------


		        // LLAMAR AL METODO HIJO 

		        this.producto.cod_producto = '';

	            // ------------------------------------------------------------------------
	        },

	        formatoEditarPrecioUnit(){

				// ------------------------------------------------------------------------

				// DAR FORMATO A LOS VALORES DE MODAL EDITAR 

				this.editarPrecioUnitario = Common.darFormatoCommon(this.editarPrecioUnitario, this.candec);

				// ------------------------------------------------------------------------

			},

	        formatoEditarPrecioMay(){

				// ------------------------------------------------------------------------

				// DAR FORMATO A LOS VALORES DE MODAL EDITAR 

				this.editarPrecioMayorista = Common.darFormatoCommon(this.editarPrecioMayorista, this.candec);

				// ------------------------------------------------------------------------

			},

			editarPrecioProducto(tabla, precioMayorista, precioUnitario, editarPrecioMayorista, editarPrecioUnitario, row){

	        	// ------------------------------------------------------------------------

	        	// INICIAR VARIABLES

	        	let me = this;

	        	// ------------------------------PRECIO UNITARIO NUEVO------------------------------------------

	            if((editarPrecioUnitario.length === 0  || editarPrecioUnitario === '0' || editarPrecioUnitario === '0.00') || (editarPrecioMayorista.length === 0  || editarPrecioMayorista === '0' || editarPrecioMayorista === '0.00')){
	               
	               // ------------------------------------------------------------------------

		            // LLAMAR TOAST MODIFICADO

		            me.$bvToast.show('toast-precio-cero');

		            // ------------------------------------------------------------------------
	            	
	            	return;

	            	// ------------------------------------------------------------------------
	            }

	            if(precioUnitario === editarPrecioUnitario && precioMayorista === editarPrecioMayorista){

		           	// ------------------------------------------------------------------------

		            // LLAMAR TOAST MODIFICADO

		            me.$bvToast.show('toast-producto-sin-cambios');

		            // ------------------------------------------------------------------------
		            
	            	return;

	            	// ------------------------------------------------------------------------

	            }

	            // ------------------------------------------------------------------------

	            // CARGAR LO EDITADO

	            tabla.cell(row, 4).data(editarPrecioMayorista).draw();
	            tabla.cell(row, 6).data(editarPrecioUnitario).draw();

	            // LIMPIAR VARIABLES 

	            me.producto.precioUnitario = '';
	            me.producto.precioMayorista = '';

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
                            { "data": "PREMAYORISTA" },
                            { "data": "NUEVOPREMAYORISTA" },
                            { "data": "PRECIO" },
                            { "data": "NUEVOPRECIO" },
                            { "data": "ACCION" },
                        ],
                    });

            // ------------------------------------------------------------------------
            // ------------------------------------------------------------------------
            //                  EDITAR FILA DATATABLE
            // ------------------------------------------------------------------------
            // ------------------------------------------------------------------------

            //  CUANDO SE HACE CLICK EN LA FILA CARGAR LOS DATOS EN LAS VARIABLES PARA EDITAR

            $('#tablaProductos').on('click', 'tbody tr', function() {

                // *******************************************************************
                    
                // CARGAR LOS VALORES A LAS VARIABLES DE EDITAR PRODUCTO

                me.editarPrecioUnitario = tableProductos.row(this).data().PRECIO;
                me.editarPrecioUnitarioNew = tableProductos.row(this).data().NUEVOPRECIO;
                me.editarPrecioMayorista = tableProductos.row(this).data().PREMAYORISTA;
                me.editarPrecioMayoristaNew = tableProductos.row(this).data().NUEVOPREMAYORISTA; 
                me.editarCodigo = tableProductos.row(this).data().CODIGO;
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

               	me.editarPrecioProducto(tableProductos, me.editarPrecioMayorista, me.editarPrecioUnitario, me.editarPrecioMayoristaNew, me.editarPrecioUnitarioNew, me.editarRow);

                // *******************************************************************

                // OCULTAR MODAL EDITAR 

                $('.editar-producto-modal').modal('hide');

                // *******************************************************************

            });

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
		}
    }

</script>