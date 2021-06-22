<template>
	<div v-if="$can('producto.descuentolote') && $can('producto')" class="container-fluid mt-3">
		<div class="row">

            <!-- --------------------------------------------- TITULO  ---------------------------------------- -->

            <div class="col-md-12 mt-3">
                <div class="section-title">
                    <h4>DESCUENTO POR LOTE</h4>
                </div>
            </div>
            
            <!-- ---------------------------------------------- CARD --------------------------------------- -->

			<div class="col-md-12">
				<div class="card border-bottom-primary mb-3">
					<div class="card-body">
					    
            			<!-- ----------------------------- CODIGO PRODUCTO, LOTES Y DETALLES ---------------------------- -->

						<div class="row">

						    <div class="col-md-3">
						    	<codigo-producto ref="compontente_codigo_producto" v-model="producto.CODIGO" @codigo_producto="buscarLotes"></codigo-producto >
						    </div>

						    <div class="col-md-3">
						    	<lote-general v-model="producto.LOTE" :value="producto.LOTE" @lote_producto="filtrar_lote_producto" ref="componente_lote"></lote-general>
						    </div>

						    <div class="col-md-2">
						    	<label>Stock</label>
						    	<input class="form-control form-control-sm" type="text" v-model="producto.STOCK" disabled>
						    </div>

						    <div class="col-md-2">
						    	<label>Vencimiento</label>
						    	<div class="input-group input-group-sm">
						    		<div class="input-group-append">
										<div class="input-group-text"><font-awesome-icon icon="calendar-day"/></div>
									</div>
							    	<input class="form-control form-control-sm" type="text" v-model="producto.VENCIMIENTO" disabled>
							    </div>
						    </div>

						</div>	

            			<!-- ------------------------------- MOTIVO, DESCUENTO E INTERVALO ------------------------------ -->

						<div class="row mt-3">
	                        <div class="col-md-3">
	                            <label>Motivo</label>
	                            <select v-model="motivo" class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validar.motivo }">
	                                <option :value="null">SELECCIONAR</option>
	                                <option :value="1">1. LIQUIDACIÓN</option>
	                                <option :value="2">2. POR VENCIMIENTO</option>
	                                <option :value="3">3. PROMOCIÓN</option>
	                            </select>       
	                        </div>

						   	<div class="col-md-3">
						    	<label>Porcentaje descuento</label>
						    	<div class="input-group input-group-sm">
						    		<div class="input-group-append">
										<div class="input-group-text">%</div>
									</div>
						    		<input type="number" class="form-control form-control-sm" v-bind:class="{ 'is-invalid': validar.descuento }" v-model="descuento" :min="1" :max="100" >
								</div> 
						    </div>

						    <div class="col-md-2">
						    	<label>Desde:</label>
						    	<div id="sandbox-container">
						    		<div class="input-daterange input-group">
							    		<div class="input-group-append">
											<div class="input-group-text"><font-awesome-icon icon="calendar-alt"/></div>
									</div>
									<input id='selectedInicialFecha' class="input-sm form-control form-control-sm" v-model="selectedInicialFecha" v-bind:class="{ 'is-invalid': validarInicialFecha }"/>
						    		</div>
						    	</div>
						    </div>

						    <div class="col-md-2">
						    	<label>Hasta:</label>
						    	<div id="sandbox-container">
						    		<div class="input-daterange input-group">
							    		<div class="input-group-append">
											<div class="input-group-text"><font-awesome-icon icon="calendar-alt"/></div>
										</div>
										<input name="end" id='selectedFinalFecha' class="input-sm form-control form-control-sm" v-model="selectedFinalFecha" v-bind:class="{ 'is-invalid': validarFinalFecha }"/>
						    		</div>
						    	</div>
						    </div>

						    <div class="col-md-2">
						    	<label class="mt-3"></label><br>
						    	<button class="btn btn-primary btn-sm" v-on:click="agregarProducto">Agregar</button>
						    </div>

						</div>	

            			<!-- ----------------------------------- TABLA DE PRODUCTOS  ------------------------------------ -->
            <div class="row">
							<div class="col-md-12 mt-3">
							   	<table id="tablaDescuentoLote" class="table table-hover table-bordered table-sm mb-3 mt-3" style="width:100%">
							        <thead>
							            <tr>
							                <th>#</th>
							                <th>Código</th>
							                <th>Descripción</th>
							                <th>Lote</th>
							                <th>Vencimiento</th>
							                <th>Descuento</th>
							                <th>Motivo</th>
							                <th>Inicio</th>
							                <th>Final</th>
							                <th>Acción</th>
							            </tr>
							       	</thead>
							    </table>
							</div>
						</div>

						<!-- -------------------------------------- BOTON GUARDAR ---------------------------------- -->

						<div class="col-md-12">
						    <div class="float-right mt-3">
						    	<button class="btn btn-primary btn-sm" v-on:click="guardar">Guardar</button>
						    </div>	
						</div>

					</div>
				</div>
			</div>
		</div>

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
            				
            				<!-- ----------------------------------- SELECCION DE TIPO ------------------------------------ -->
		                    
		                    <div class="col-md-12">
	                            <label>Motivo</label>
	                            <select v-model="editar.motivo" class="custom-select custom-select-sm">
	                                <option :value="null">SELECCIONAR</option>
	                                <option :value="1">1. LIQUIDACIÓN</option>
	                                <option :value="2">2. POR VENCIMIENTO</option>
	                                <option :value="3">3. PROMOCIÓN</option>
	                            </select>       
	                        </div>

            				<!-- ----------------------------------- INPUT DESCUENTO ------------------------------------ -->
						   	
						   	<div class="col-md-12">
						    	<label>Porcentaje descuento</label>
						    	<div class="input-group input-group-sm">
						    		<div class="input-group-append">
										<div class="input-group-text">%</div>
									</div>
						    		<input type="number" class="form-control form-control-sm" v-model="editar.descuento" :min="1" :max="100" >
								</div> 
						    </div>

            				<!-- ----------------------------------- FECHA INICIO ------------------------------------ -->
						    
						    <div class="col-md-12">
						    	<label>Desde:</label>
						    	<div id="sandbox-container">
						    		<div class="input-daterange input-group">
							    		<div class="input-group-append">
											<div class="input-group-text"><font-awesome-icon icon="calendar-alt"/></div>
										</div>
										<input id='selectedInicialFechaEdicion' class="input-sm form-control form-control-sm" v-model="editar.selectedInicialFecha"/>
						    		</div>
						    	</div>
						    </div>

            				<!-- ----------------------------------- FECHA FIN ------------------------------------ -->
						    
						    <div class="col-md-12">
						    	<label>Hasta:</label>
						    	<div id="sandbox-container">
						    		<div class="input-daterange input-group">
							    		<div class="input-group-append">
											<div class="input-group-text"><font-awesome-icon icon="calendar-alt"/></div>
										</div>
										<input name="end" id='selectedFinalFechaEdicion' class="input-sm form-control form-control-sm" v-model="editar.selectedFinalFecha"/>
						    		</div>
						    	</div>
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

	    <!-- ------------------------------------- TOAST PRODUCTO PRODUCTO MODIFICADO ----------------------------------- -->

		<b-toast id="toast-producto-modificado" variant="success" solid>
			<template v-slot:toast-title>
			 	<div class="d-flex flex-grow-1 align-items-baseline">
			        <strong class="mr-auto">¡Éxito!</strong>
			        <small class="text-muted mr-2">modificado</small>
			    </div>
			</template>
			¡Este registro se ha sido modificado con éxito!
		</b-toast>

		<!-- ---------------------------------------- TOAST CODIGO PRODUCTO REPETIDO -------------------------------- -->

		<b-toast id="toast-producto-existente" variant="warning" solid>
		    <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">¡Error!</strong>
		          <small class="text-muted mr-2">Ya existe</small>
		        </div>
		    </template>
		    ¡Los datos del producto con ese lote ya ha sido añadido a la tabla!
		</b-toast>

		<!-- ---------------------------------- TOAST CODIGO PRODUCTO REPETIDO -------------------------------------- -->

		<b-toast id="toast-seleccionar-producto-lote" variant="warning" solid>
		    <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">¡Error!</strong>
		          <small class="text-muted mr-2">Seleccione un producto</small>
		        </div>
		    </template>
		    ¡Seleccione un producto y un lote!
		</b-toast>

		<!-- ---------------------------------- TOAST AGREGAR DATOS -------------------------------------- -->

		<b-toast id="toast-cargar-datos" variant="warning" solid>
		    <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">¡Error!</strong>
		          <small class="text-muted mr-2">Agregue los datos</small>
		        </div>
		    </template>
		    ¡Primero debe agregar los datos para guardar el descuento!
		</b-toast>

		<!-- ----------------------------------- MODAL DETALLE PRODUCTO ------------------------------------- -->

		<producto-detalle ref="detalle_producto" :codigo="codigo_detalle"></producto-detalle>

		<!-- ------------------------------------------------------------------------ -->

	</div>

	<!-- ------------------------------------------------------------------------ -->

    <div v-else>
        <cuatrocientos-cuatro></cuatrocientos-cuatro>
    </div>  	

	<!-- ------------------------------------------------------------------------ -->

</template>

<script>

    export default {

      props: ['monedaCodigo'],

	    data(){

	        return {
	            selectedInicialFecha: '',
	            selectedFinalFecha: '',
	            validarInicialFecha: false,
	            validarFinalFecha: false,
	            descuento: 30,
	            motivo: "null",
	        	producto: {
	        		CODIGO: '',
	        		DESCRIPCION: '',
	        		LOTE: '',
	        		STOCK: '',
	        		INICIAL: '',
	        		VENCIMIENTO: '',
	        		LOTE_ID: ''
	        	},
	        	validar: {
	        		CODIGO: false,
	        		descuento: false,
	        		LOTE: false,
	        		TABLA: false,
	                motivo: false
	        	}, 
	        	editar: {
	        		codigo: '',
	        		selectedInicialFecha: '',
	              	selectedFinalFecha: '',
	              	descuento: 0,
	              	motivo: "null",
	              	row: ''
	        	},
	        	codigo_detalle: ''
	        }
	    }, 
	    methods: {

	      	buscarLotes(codigo){

	      		// ------------------------------------------------------------------------

	      		let me = this;

	      		me.producto.CODIGO = codigo;
	      		me.$refs.componente_lote.obtenerDatosLote(codigo, me.monedaCodigo);

	      		// ------------------------------------------------------------------------

	      	},

	      	filtrar_lote_producto(valor){

	      		this.producto.DESCRIPCION = valor.descripcion;
	      		this.producto.LOTE = valor.lote;
	      		this.producto.STOCK = valor.cantidad;
	      		this.producto.INICIAL = valor.inicial;
	      		this.producto.VENCIMIENTO = valor.vencimiento;
	      		this.producto.LOTE_ID = valor.lote_id;

	      	},
	      	inivarAgregarProducto(){

	      		this.producto = {
	        		CODIGO: '',
	        		LOTE: '',
	        		STOCK: '',
	        		INICIAL: '',
	        		VENCIMIENTO: '',
	        		LOTE_ID: ''
	        	}

	        	this.descuento = 30;
	        	this.selectedFinalFecha = '';
	        	this.selectedInicialFecha = '';
	        	this.motivo = "null";

	      	},
	        agregarProducto(){

	            // ------------------------------------------------------------------------

	            // INICIAR VARIABLES

	            let me = this;
	            var tableDescuentoLote = $('#tablaDescuentoLote').DataTable();
	            var productoExistente = [];
	            var falta = false;

	            // ------------------------------------------------------------------------

	            // CONTROLADOR

	            if (me.producto.CODIGO.length === 0) {
	                me.validar.CODIGO = true;
	                falta = true;
	            } else {
	                me.validar.CODIGO = false;
	            }

	            if (me.descuento.length === 0  || me.descuento === 0 || me.descuento > 100) {
	                me.validar.descuento = true;
	                falta = true;
	            } else {
	                me.validar.descuento = false;
	            }
	            
	            if (me.producto.LOTE.length === 0) {
	                me.validar.LOTE = true;
	                falta = true;

	            	me.$bvToast.show('toast-seleccionar-producto-lote');
	            } else {
	                me.validar.LOTE = false;
	            }

	            
	            if (me.selectedInicialFecha.length === 0 || me.selectedInicialFecha.length === '') {
	                me.validarInicialFecha = true;
	                falta = true;
	            } else {
	                me.validarInicialFecha = false;
	            }

	            
	            if (me.selectedFinalFecha.length === 0 || me.selectedFinalFecha.length === '') {
	                me.validarFinalFecha = true;
	                falta = true;
	            } else {
	                me.validarFinalFecha = false;
	            }

	            if (me.motivo === "null") {
	                me.validar.motivo = true;
	                falta = true;
	            } else {
	                me.validar.motivo = false;
	            }

	            if(falta){
	            	return;
	            }
	            // ------------------------------------------------------------------------

	            productoExistente = Common.existeProductoLoteDataTableCommon(tableDescuentoLote, me.producto.CODIGO, me.producto.LOTE, 3);

	            // ------------------------------------------------------------------------

	           	

	            if (productoExistente.respuesta == true) {

	            	// ------------------------------------------------------------------------

	            	me.$bvToast.show('toast-producto-existente');

	            	return true;
	            }

	            // ------------------------------------------------------------------------

	            // CARGAR DATO EN TABLA 

	            me.agregarFilaTabla(me.producto.CODIGO, me.producto.DESCRIPCION, me.producto.LOTE, me.producto.VENCIMIENTO, me.descuento, me.motivo, me.selectedInicialFecha, me.selectedFinalFecha, me.producto.LOTE_ID);

	            // ------------------------------------------------------------------------

	            // VACIAR TEXTBOX AGREGAR PRODUCTO

	            me.inivarAgregarProducto();

	    		// ------------------------------------------------------------------------

		        // LLAMAR AL METODO HIJO 

		        this.producto.CODIGO = '';
		        this.$refs.compontente_codigo_producto.vaciarDevolver();

		        // ------------------------------------------------------------------------

	        }, editarCantidadProducto(tabla, descuento, motivo, fecha_inicio, fecha_final, row){

	        	// ------------------------------------------------------------------------

	        	// INICIAR VARIABLES

	        	let me = this;

	        	if (descuento.length !== 0  && descuento !== 0 && descuento < 100) {
	                
		            // ------------------------------------------------------------------------

		            // CARGAR LO EDITADO

		            tabla.cell(row, 5).data(descuento).draw();
	            }
	            
	            if (fecha_inicio.length !== 0 && fecha_inicio.length !== '') {
	               

				    // ------------------------------------------------------------------------

				    // CARGAR FECHA INICIAL 

		            tabla.cell(row, 7).data(fecha_inicio).draw();	
	            }

	            
	            if (fecha_final.length !== '') {

				    // ------------------------------------------------------------------------

				    // CARGAR FECHA FINAL 

		            tabla.cell(row, 8).data(fecha_final).draw();
	                
	            }

	            if(motivo !== null) {

		            // ------------------------------------------------------------------------
		            
		            // CARGAR MOTIVO 

	            	tabla.cell(row, 6).data(motivo).draw();
	            }

	            // ------------------------------------------------------------------------

	            // LLAMAR TOAST MODIFICADO

	            me.$bvToast.show('toast-producto-modificado');

	            // ------------------------------------------------------------------------

	            // VACIAR TEXTBOX AGREGAR PRODUCTO

	            me.inivarAgregarProducto();

	            // ------------------------------------------------------------------------

	        }, agregarFilaTabla(codigo, descripcion, lote, vencimiento, descuento, motivo, inicio, final, lote_id){

	        	// ------------------------------------------------------------------------

	        	//	INICIAR VARIABLES 

	        	let me = this;
	        	var tableDescuentoLote = $('#tablaDescuentoLote').DataTable();

	            // ------------------------------------------------------------------------

	            // VENCIMIENTO 

	            // if (me.mostrar.VENCIMIENTO === 0) {
	            // 	vencimiento = 'N/A';
	            // }

	            // ------------------------------------------------------------------------

	        	// AGREGAR FILAS 

	        	 tableDescuentoLote.rows.add( [ {
			                    "ITEM": '',
			                    "CODIGO":   codigo,
			                    "DESCRIPCION":     descripcion,
			                    "LOTE": lote,
			                    "VENCIMIENTO": vencimiento,
			                    "DESCUENTO": descuento,
			                    "MOTIVO":    motivo,
			                    "FECHA_INICIO": inicio,
			                    "FECHA_FINAL": final,
				                "ACCION":    "&emsp;<a role='button' id='mostrarProductoFila' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a> &emsp;<a role='button' id='editarModal' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>&emsp;<a role='button'  title='Eliminar'><i id='eliminarProducto' class='fa fa-trash text-danger' aria-hidden='true'></i></a>",
			                    "LOTE_ID": lote_id
			                } ] )
			     .draw();

			    // ------------------------------------------------------------------------ 

	            // AGREGAR INDEX A LA TABLA TRANSFERNCIAS

	            tableDescuentoLote.on( 'order.dt search.dt', function () {
	                tableDescuentoLote.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	                    cell.innerHTML = i+1;
	                } );
	            } ).draw();

	            // ------------------------------------------------------------------------

	            // AJUSTAR COLUMNAS DE ACUERDO AL DATO QUE CONTIENEN

	            tableDescuentoLote.columns.adjust().draw();

	            // ------------------------------------------------------------------------

	        }, 
	        controlador() {

	        	// ------------------------------------------------------------------------

	        	let me = this;
	        	var falta = false;
	        	var tableDescuentoLote = $('#tablaDescuentoLote').DataTable();

	        	// ------------------------------------------------------------------------

	        	// CONTROLADOR

	            if (tableDescuentoLote.rows().data().length === 0) {
	            	me.validar.TABLA = true;

	            	me.$bvToast.show('toast-cargar-datos');
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
	        guardar() {

	        	// ------------------------------------------------------------------------

	        	// INICIAR VARIABLES 

	        	let me = this;
	        	var tableDescuentoLote = $('#tablaDescuentoLote').DataTable();

	        	// ------------------------------------------------------------------------

	        	// CONTROLAR QUE EXISTAN DATOS A GUARDAR

	        	if (me.controlador() === true){
	        		return;
	        	}

	        	// ------------------------------------------------------------------------

	        	// PREPARAR ARRAY 

	        	var data = {
	        		descuento: tableDescuentoLote.rows().data().toArray()
	        	};

	        	// ------------------------------------------------------------------------

	      		// SWEET ALERT

	      		Swal.fire({
					title: '¿Estás seguro?',
					text: "¡¿Dar descuento a estos productos?!",
					type: 'warning',
					showLoaderOnConfirm: true,
					showCancelButton: true,
					confirmButtonColor: '#d33',
					cancelButtonColor: '#3085d6',
					confirmButtonText: '¡Sí, guardalo!',
					cancelButtonText: 'Cancelar',
					preConfirm: () => {
					    return Common.guardarLoteDescuentoCommon(data).then(data => {
					    	if (!data.response === true) {
					          throw new Error(data.statusText);
					        }
					        var tableDescuentoLote = $('#tablaDescuentoLote').DataTable();
					        tableDescuentoLote.clear().draw();
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
							'¡Guardado!',
							result.value.statusText,
							'success'
						)

						// ------------------------------------------------------------------------

						// RECARGAR
						  	
						me.$router.push('/descLote');

						// ------------------------------------------------------------------------

					}
				})

				// ------------------------------------------------------------------------
	        }
	    },  
        mounted() {

          // ------------------------------------------------------------------------

          let me = this;


        	$(function(){
		   		$('#sandbox-container .input-daterange').datepicker({
		   		    keyboardNavigation: false,
    				forceParse: false
    			});
    			$("#selectedInicialFecha").datepicker().on(
			     	"changeDate", () => {
			     		me.selectedInicialFecha = $('#selectedInicialFecha').val();
			     	}
				);
				$("#selectedFinalFecha").datepicker().on(
					"changeDate", () => {
			     		me.selectedFinalFecha = $('#selectedFinalFecha').val();
			     	}
				);
    			$("#selectedInicialFechaEdicion").datepicker().on(
			     	"changeDate", () => {
			     		me.editar.selectedInicialFecha = $('#selectedInicialFechaEdicion').val();
			     	}
				);
				$("#selectedFinalFechaEdicion").datepicker().on(
					"changeDate", () => {
			     		me.editar.selectedFinalFecha = $('#selectedFinalFechaEdicion').val();
			     	}
				);
			});

          // ------------------------------------------------------------------------

          // PREPARAR DATATABLE 

          var tableDescuentoLote = $('#tablaDescuentoLote').DataTable({
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "searching": false,
                        "paging": false,
                        "columns": [
                            { "data": "ITEM" },
                            { "data": "CODIGO" },
                            { "data": "DESCRIPCION" },
                            { "data": "LOTE" },
                            { "data": "VENCIMIENTO" },
                            { "data": "DESCUENTO" },
                            { "data": "MOTIVO" },
                            { "data": "FECHA_INICIO" },
                            { "data": "FECHA_FINAL" },
                            { "data": "ACCION" },
                            { "data": "LOTE_ID" },
                        ],
	                    "columnDefs": [
	                        	{
					                "targets": [ 10 ],
					                "visible": false,
					                "searchable": false
					            }
					    ],
                    });
                    


          		// ------------------------------------------------------------------------
                // ------------------------------------------------------------------------
                //                  EDITAR FILA DATATABLE
                // ------------------------------------------------------------------------
                // ------------------------------------------------------------------------

                //  CUANDO SE HACE CLICK EN LA FILA CARGAR LOS DATOS EN LAS VARIABLES PARA EDITAR

                $('#tablaDescuentoLote').on('click', 'tbody tr', function() {

                    // *******************************************************************
                    
                    // CARGAR LOS VALORES A LAS VARIABLES DE EDITAR PRODUCTO

                    me.editar.codigo = tableDescuentoLote.row(this).data().CODIGO;
                    me.editar.descuento = tableDescuentoLote.row(this).data().DESCUENTO;
                    me.editar.row = tableDescuentoLote.row( this );
                    me.editar.selectedInicialFecha = tableDescuentoLote.row(this).data().FECHA_INICIO;
                    me.editar.selectedFinalFecha = tableDescuentoLote.row(this).data().FECHA_FINAL;  
                    me.editar.motivo = tableDescuentoLote.row(this).data().MOTIVO; 

                    // *******************************************************************

                });

                // ------------------------------------------------------------------------

                // MOSTRAR MODAL EDITAR

                $('#tablaDescuentoLote').on('click', 'tbody tr #editarModal', function() {

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

                	me.editarCantidadProducto(tableDescuentoLote, me.editar.descuento,  me.editar.motivo, me.editar.selectedInicialFecha, me.editar.selectedFinalFecha, me.editar.row);


                	// *******************************************************************

                    // OCULTAR MODAL EDITAR 

                    $('.editar-producto-modal').modal('hide');

                	// *******************************************************************

                });
			
			// ------------------------------------------------------------------------

           	// MOSTRAR MODAL PRODUCTO

            $('#tablaDescuentoLote').on('click', 'tbody tr #mostrarProductoFila', function() {

	            // *******************************************************************

	            // OBTENER DATOS DEL PRODUCTO DATATABLE JS

	            me.codigo_detalle = tableDescuentoLote.row($(this).parents('tr')).data().CODIGO;
	            me.$refs.detalle_producto.mostrar();

				// *******************************************************************

            });

          // ------------------------------------------------------------------------

          // MOSTRAR SWEET ELIMINAR

          tableDescuentoLote.on('click', 'tbody tr #eliminarProducto', function() {

           	// *******************************************************************

            // ABRIR EL SWEET ALERT

            Swal.fire({
				title: '¿Estás seguro?',
				text: "¡Eliminar el producto " + tableDescuentoLote.row($(this).parents('tr')).data().CODIGO + " de la tabla!",
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
	                    
	                tableDescuentoLote.row($(this).parents('tr')).remove().draw(); 

	                // *******************************************************************

					Swal.fire(
						'¡Eliminado!',
						'¡Se ha eliminado el producto!',
						'success'
					)

    				// *******************************************************************

					  }
					})

                    // *******************************************************************

            });

            // ------------------------------------------------------------------------
        }
    }
</script>