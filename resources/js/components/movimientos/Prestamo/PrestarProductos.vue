<template>
	<div class="container-fluid mt-3">
		<div class="row">

            <!-- ------------------------------------------------------------------------------------- -->

            <!-- TITULO  -->
            
            <div class="col-md-12 mt-3">
                <div class="section-title">
                    <h4>PRÉSTAMO DE PRODUCTOS</h4>
                </div>
            </div>
            
            <!-- ------------------------------------------------------------------------------------- -->

			<div class="col-md-12">
				<div class="card border-bottom-primary mb-3">
					<div class="card-body">
					    <div class="row">

                            <!-- -------------------------- NOMBRE ------------------------------------- -->
                            
                            <div class="col-md-5 mt-3">
                                <label>Cliente</label><br>
                                <h4 class="text-primary text-center">{{cliente.NOMBRE}}</h4>
                            </div>

                            <!-- -------------------------- GARANTIA ------------------------------------- -->

                            <div class="col-md-3 mt-3">
                                <label>Garantía</label>
                                <select v-model="garantia.TIPO" class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validar.GARANTIA }">
                                        <option :value="null">Seleccionar</option>
                                        <option :value="1">Objeto</option>
                                        <option :value="2">Dinero</option>
                                </select>       
                            </div>

                            <!-- -------------------------- CLIENTE FILTRAR O REGISTRAR ------------------------------------- -->

                            <div class="col-md-3 ml-5">
                                
                                <vs-divider> Cliente </vs-divider>
                            
                                <!-- ------------------------------------------------------------------------------------- -->

                                <busqueda-cliente-modal ref="componente_cliente_modal" @codigo="codigoCliente" @nombre="nombreCliente"></busqueda-cliente-modal>
                            </div>

                            <div class="col-md-12 mt-3">
                                <hr>
                            </div>

                            <!-- -------------------------- OBSERVACION ------------------------------------- -->

					    	<div class="col-md-12">
					    		<label>Observación</label>
					    		<input class="form-control custom-select-sm" type="text" v-model="OBSERVACION" v-bind:class="{ 'is-invalid': validar.OBSERVACION }">
					    	</div>

					    	<div class="col-md-12 mt-3">
					    		<hr>
					    	</div>
					    	
					    	<div class="col-md-3">
					    		<codigo-producto ref="compontente_codigo_producto" v-model="producto.CODIGO" @codigo_producto="cargarProducto" :validar_codigo_producto="validar.CODIGO"></codigo-producto >
					    	</div>

					    	<div class="col-md-6">
					    		<label>Descripción</label>
					    		<input class="form-control form-control-sm" type="text" v-model="producto.DESCRIPCION" disabled>
					    	</div>

					    	<div class="col-md-3">
					    		<label>Cantidad</label>
					    		<input v-bind:class="{ 'is-invalid': validar.CANTIDAD }" class="form-control form-control-sm" type="number" v-model="producto.CANTIDAD" :min="1" :max="producto.STOCK" v-on:blur="agregarProducto">
					    	</div>

					    	<div class="col-md-12">
					    		<hr>
					    	</div>

					    	<div class="col-md-12 mt-3">
					    		<table id="tablaProductoPrestar" class="display nowrap table table-striped table-bordered table-sm mb-3" style="width:100%" >
					                <thead>
					                    <tr>
					                        <th>#</th>
					                        <th>Código</th>
					                        <th>Descripción</th>
					                        <th>Stock</th>
					                        <th class="cantidadColumna">Cantidad</th>
					                        <th>Acción</th>
					                    </tr>
					                </thead>
					            </table>
					    	</div>

					    	<!-- ------------------------------------------------------------------------ -->

					    	<!-- BOTON PROCESAR -->

					    	<div class="col-md-12">
					    		<div class="float-right mt-3">
					    		 	<button class="btn btn-primary btn-sm" v-on:click="guardar">Prestar</button>
					    		</div>	
					    	</div>

					    	<!-- ------------------------------------------------------------------------ -->

					    </div>	
					</div>
                </div>
			</div>
		</div>

		<!-- ------------------------------------------------------------------------ -->

        <!-- <div v-else>
            <cuatrocientos-cuatro></cuatrocientos-cuatro>
        </div> -->
        
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
                                <label>Stock Disponible</label>
                                <input type="text" name="" v-model="editarStock" class="form-control form-control-sm" disabled>
                            </div>
                            <div class="col-md-12 mt-3">
                                <label>Cantidad</label>
                                <input type="text" name="" v-model="editarCantidad" class="form-control form-control-sm">
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

        <!-- ------------------------------------------------TOAST CODIGO PRODUCTO REPETIDO-------------------------------------- -->

        <b-toast id="toast-editar-cero" variant="warning" solid>
            <template v-slot:toast-title>
                <div class="d-flex flex-grow-1 align-items-baseline">
                  <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
                  <strong class="mr-auto">¡Error!</strong>
                  <small class="text-muted mr-2">cero</small>
                </div>
            </template>
            ¡La cantidad no debe ser cero!
        </b-toast>

        <!-- -------------------------------------------TOAST CODIGO PRODUCTO REPETIDO------------------------------------------- -->

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

        <!-- ---------------------------------------------TOAST COMPLETAR CABECERA---------------------------------------------- -->

        <b-toast id="toast-completar-cabecera" variant="warning" solid>
            <template v-slot:toast-title>
                <div class="d-flex flex-grow-1 align-items-baseline">
                  <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
                  <strong class="mr-auto">¡Error!</strong>
                  <small class="text-muted mr-2">incompleto</small>
                </div>
            </template>
              ¡Para realizar el préstamo se necesita añadir más datos!
        </b-toast>

	</div>	
</template>
<script>
    export default {
      props: ['monedaCodigo'],
      data(){
        return {
        	producto: {
        		CODIGO: '',
        		DESCRIPCION: '',
        		CANTIDAD: '',
        		STOCK: ''
        	},
        	
            cliente: {
                CODIGO: '1',
                NOMBRE: 'CLIENTE OCASIONAL',
            },

            garantia: {
                TIPO: null,
            },

            validar: {
        		CODIGO: false,
        		CANTIDAD: false,
        		OBSERVACION: false,
        		TABLA: false,
                NOMBRE: false,
                COD_CLIENTE: false,
                GARANTIA: false
        	}, 
        	
            OBSERVACION: '',
            editarRow: '',
            editarStock: '',
            editarCodigo: '',
            editarCantidad: ''
        }
      }, 
      methods: {

            //CARGA LOS DATOS DEL PRODUCTO

            cargarProducto(codigo) {
            
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
                        
                        me.validar.CODIGO = true;
                        me.producto.CODIGO = '';
                        me.producto.DESCRIPCION = '';
                        me.producto.STOCK = '';

                    }else{

                        // LLENAR DESCRIPCION DE PRODUCTO
                         
                        me.producto.CODIGO = response.data.producto.CODIGO;
                        me.producto.DESCRIPCION = response.data.producto.DESCRIPCION;
                        me.producto.STOCK = response.data.producto.STOCK;

                        me.validar.CODIGO = false;

                        // *******************************************************************
                    }
                });

                // ------------------------------------------------------------------------
            },

            inivarAgregarProducto(){

                // ------------------------------------------------------------------------

                // VACIAR TEXTBOX 

                this.producto.CODIGO = '';
                this.producto.DESCRIPCION = '';
                this.producto.STOCK = '';
                this.producto.CANTIDAD = '';


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

            nombreCliente(cliente){

                // ------------------------------------------------------------------------

                // NOMBRE CLIENTE 

                this.cliente.NOMBRE = cliente;

                // ------------------------------------------------------------------------

            },
            agregarProducto(){

                // ------------------------------------------------------------------------

                // INICIAR VARIABLES

                let me = this;
                var tableProductoPrestar = $('#tablaProductoPrestar').DataTable();
                var productoExistente = [];
                var cantidadNueva = 0;

                // ------------------------------------------------------------------------

                // CONTROLADOR

                if (me.producto.CODIGO.length === 0) {
                    me.validar.CODIGO = true;
                    return;
                } else {
                    me.validar.CODIGO = false;
                }

                if (me.producto.CANTIDAD.length === 0  || me.producto.CANTIDAD === '0') {
                    me.validar.CANTIDAD = true;
                    return;
                } else {
                    me.validar.CANTIDAD = false;
                }

                if (me.producto.CANTIDAD > me.producto.STOCK) {
                    me.validar.CANTIDAD = true;
                    return;
                } else {
                    me.validar.CANTIDAD = false;
                }

                // CARGAR DATO EN TABLA

                me.agregarFilaTabla(me.producto.CODIGO, me.producto.DESCRIPCION,  me.producto.STOCK, me.producto.CANTIDAD);

                // ------------------------------------------------------------------------

                // VACIAR TEXTBOX AGREGAR PRODUCTO

                me.inivarAgregarProducto();

        		// ------------------------------------------------------------------------

    	        // LLAMAR AL METODO HIJO 

    	        this.producto.CODIGO = '';
    	        this.$refs.compontente_codigo_producto.vaciarDevolver();

    	        // ------------------------------------------------------------------------

            }, 
            agregarFilaTabla(codigo, descripcion, stock, cantidad){

                // ------------------------------------------------------------------------
                
                //  INICIAR VARIABLES 

                let me = this;
                var tableProductoPrestar = $('#tablaProductoPrestar').DataTable();
                var productoExistente = [];
                var cantidadNueva = 0;

                // ------------------------------------------------------------------------

                // REVISAR SI EXISTE VALORES REPETIDOS EN TABLA TRANSFERENCIAS 
                // LA OPCION 1 ES PARA DEVOLVER SOLO TRUE O FALSE SI EXISTE O NO
                // LA OPCION 2 ES PARA DEVOLVER MAS DATOS DEL PRODUCTO 

                productoExistente = Common.existeProductoDataTableCommon(tableProductoPrestar, codigo, 2);
                
                if (productoExistente.respuesta == true) {

                    // ------------------------------------------------------------------------

                    // SUMAR LA CANTIDAD

                    cantidadNueva = parseFloat(productoExistente.cantidad) + parseFloat(cantidad);

                    // ------------------------------------------------------------------------

                    // EDITAR CANTIDAD PRODUCTO 
                    
                    me.editarCantidadProducto(tableProductoPrestar, cantidadNueva, stock, productoExistente.row);
                    return;

                    // ------------------------------------------------------------------------

                }else{

                    me.validar.CODIGO = false;
                }

                
                // ------------------------------------------------------------------------

                // REVISAR SI CANTIDAD SUPERA STOCK

                if (Common.cantidadSuperadaCommon(cantidad, stock)) {
                    me.validar.CANTIDAD = true;
                    me.$bvToast.show('toast-cantidad-superada');
                    return;
                } else {
                    me.validar.CANTIDAD = false;
                }

                // AGREGAR FILAS 

                 tableProductoPrestar.rows.add( [ {
                                "ITEM": '',
                                "CODIGO":   codigo,
                                "DESCRIPCION":     descripcion,
                                "STOCK": stock,
                                "CANTIDAD": cantidad,
                                "ACCION":    "&emsp;<a role='button' id='editarModal' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>&emsp;<a role='button'  title='Eliminar'><i id='eliminarProducto' class='fa fa-trash text-danger' aria-hidden='true'></i></a>",
                            } ] )
                 .draw();

                // ------------------------------------------------------------------------ 

                // AGREGAR INDEX A LA TABLA

                tableProductoPrestar.on( 'order.dt search.dt', function () {
                    tableProductoPrestar.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                        cell.innerHTML = i+1;
                    } );
                } ).draw();

                // ------------------------------------------------------------------------

                // AJUSTAR COLUMNAS DE ACUERDO AL DATO QUE CONTIENEN

                tableProductoPrestar.columns.adjust().draw();

                // ------------------------------------------------------------------------

            },
            editarCantidadProducto(tabla, cantidad, stock, row){

                // ------------------------------------------------------------------------

                // INICIAR VARIABLES

                let me = this;

                // ------------------------------------------------------------------------

                // PROHIBIR EDITADO SI CANTIDAD O PRECIO ES CERO

                if (cantidad <= 0) {
                    me.$bvToast.show('toast-editar-cero');
                    return; 
                }

                // ------------------------------------------------------------------------

                // REVISAR SI LA NUEVA CANTIDAD SUPERA AL STOCK

                if (cantidad > stock) {
                    me.$bvToast.show('toast-cantidad-superada');
                    return;
                }

                // ------------------------------------------------------------------------

                // CARGAR LO EDITADO

                tabla.cell(row, 4).data(cantidad).draw();

                // LLAMAR TOAST MODIFICADO

                me.$bvToast.show('toast-producto-modificado');

                // ------------------------------------------------------------------------

            },

            controlador(){

            	// ------------------------------------------------------------------------

            	let me = this;
            	var falta = false;
            	var tableProductoPrestar = $('#tablaProductoPrestar').DataTable();

            	// ------------------------------------------------------------------------

            	// CONTROLADOR

                if (me.garantia.TIPO === null) {
                    me.validar.GARANTIA = true;
                    falta = true;
                } else {
                    me.validar.GARANTIA = false;
                }

                if (me.OBSERVACION.length === 0) {
                    me.validar.OBSERVACION = true;
                    falta = true;
                } else {
                    me.validar.OBSERVACION = false;
                }

                if (tableProductoPrestar.rows().data().length === 0) {
                	me.validar.TABLA = true;
                	falta = true;
                } else {
                	me.validar.TABLA = false;
                }

            	// ------------------------------------------------------------------------

            	// RETORNAR FALTA - SI ES TRUE SE DETIENE EL GUARDADO 
                // SI ES FALSE CONTINUA LA OPERACION 

                return falta;

            	// ------------------------------------------------------------------------

            },

            guardar(){

            	// ------------------------------------------------------------------------

            	// INICIAR VARIABLES 

            	let me = this;
            	var tableProductoPrestar = $('#tablaProductoPrestar').DataTable();

            	// ------------------------------------------------------------------------

            	// CONTROLAR TEXTBOX / SI ES TRUE SE DETIENE LA OPERACION DE GUARDADO

            	if (me.controlador() === true){
                    me.$bvToast.show('toast-completar-cabecera');
            		return;
            	}

            	// ------------------------------------------------------------------------

            	// PREPARAR ARRAY 

            	var data = {
            		tipo: me.garantia.TIPO,
            		observacion: me.OBSERVACION,
                    codigoCliente: me.cliente.CODIGO,
            		productos: tableProductoPrestar.rows().data().toArray()
            	};

            	// ------------------------------------------------------------------------

          		// SWEET ALERT

          		Swal.fire({
    				title: '¿Estas seguro?',
    				text: "¡Prestar estos productos!",
    				type: 'warning',
    				showLoaderOnConfirm: true,
    				showCancelButton: true,
    				confirmButtonColor: '#d33',
    				cancelButtonColor: '#3085d6',
    				confirmButtonText: '¡Sí, guardalo!',
    				cancelButtonText: 'Cancelar',
    				preConfirm: () => {
    				    return Common.guardarPrestamoProductoCommon(data).then(data => {
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

    					// ------------------------------------------------------------------------

                        // RECARGAR
                        window.location.href = '/mov6';

    				}else{

                            Swal.fire(
                                '¡Error!',
                                data.statusText,
                                'warning'
                            )
                        }
    			})

    			// ------------------------------------------------------------------------
            }
        }, 

        mounted() {

          // ------------------------------------------------------------------------

          let me = this;

          // ------------------------------------------------------------------------

          // PREPARAR DATATABLE 

          var tableProductoPrestar = $('#tablaProductoPrestar').DataTable({
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "searching": false,
                        "paging": false,
                        "columns": [
                            { "data": "ITEM" },
                            { "data": "CODIGO" },
                            { "data": "DESCRIPCION" },
                            { "data": "STOCK" },
                            { "data": "CANTIDAD" },
                            { "data": "ACCION" },
                        ],
            });
                    
          // ------------------------------------------------------------------------

          // CAMBIAR TAMAÑO FUENTE 

          $("#tablaProductoPrestar").css("font-size", 12);
          tableProductoPrestar.columns.adjust().draw();

          // ------------------------------------------------------------------------
          // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------
                //                  EDITAR FILA DATATABLE
                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------

                //  CUANDO SE HACE CLICK EN LA FILA CARGAR LOS DATOS EN LAS VARIABLES PARA EDITAR

                $('#tablaProductoPrestar').on('click', 'tbody tr', function() {

                    // *******************************************************************
                    
                    // CARGAR LOS VALORES A LAS VARIABLES DE EDITAR PRODUCTO

                    me.editarCantidad = tableProductoPrestar.row(this).data().CANTIDAD;  
                    me.editarCodigo = tableProductoPrestar.row(this).data().CODIGO;
                    me.editarStock = tableProductoPrestar.row(this).data().STOCK;
                    me.editarRow = tableProductoPrestar.row( this );

                    // *******************************************************************

                });

                // ------------------------------------------------------------------------

                // MOSTRAR MODAL EDITAR

                $('#tablaProductoPrestar').on('click', 'tbody tr #editarModal', function() {

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

                    me.editarCantidadProducto(tableProductoPrestar, me.editarCantidad, me.editarStock, me.editarRow);

                    // *******************************************************************

                    // OCULTAR MODAL EDITAR 

                    $('.editar-producto-modal').modal('hide');

                    // *******************************************************************

                });

                // ------------------------------------------------------------------------
          // MOSTRAR SWEET ELIMINAR

          tableProductoPrestar.on('click', 'tbody tr #eliminarProducto', function() {

           	// *******************************************************************

            // ABRIR EL SWEET ALERT

            Swal.fire({
				title: '¿Estas seguro?',
				text: "¡Eliminar el producto " + tableProductoPrestar.row($(this).parents('tr')).data().CODIGO + "!",
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
	                    
	                tableProductoPrestar.row($(this).parents('tr')).remove().draw(); 

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
