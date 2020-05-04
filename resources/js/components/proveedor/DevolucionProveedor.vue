<template>
	<div class="container-fluid mt-3">
		<div class="row" v-if="$can('proveedor.devolucion')">
				<div class="col-md-12">
					<div class="card border-bottom-info mb-3">
					  <div class="card-header">
					  	 <h6>Devolución a proveeedor</h6>
					  </div>
					  <div class="card-body">
					    
					    <div class="row">
					    	<div class="col-md-3">
					    		<select-proveedor v-model="producto.PROVEEDOR" :deshabilitar="deshabilitar.PROVEEDOR" :validar_proveedor="validar.PROVEEDOR"></select-proveedor>
					    	</div>

					    	<div class="col-md-6">
					    		<label>Observación</label>
					    		<input class="form-control custom-select-sm" type="text" v-model="devolucion.OBSERVACION" v-bind:class="{ 'is-invalid': validar.OBSERVACION }">
					    	</div>

					    	<div class="col-md-3">
								<select-moneda v-model="devolucion.MONEDA" :deshabilitar="deshabilitar.MONEDA" @descripcion_moneda="descripcionMoneda" @cantidad_decimales="cantidadDecimal"></select-moneda>
							</div>

					    	<div class="col-md-12">
					    		<hr>
					    	</div>
					    	
					    	<div class="col-md-3">
					    		<codigo-producto ref="compontente_codigo_producto" v-model="producto.CODIGO" @codigo_producto="cargarLotes"></codigo-producto >
					    	</div>

					    	<div class="col-md-3">
					    		<lote-proveedor v-model="producto.LOTE" :value="producto.LOTE" @lote_producto="lote_producto" ref="componente_lote"></lote-proveedor>
					    	</div>

					    	<div class="col-md-3">
					    		<label>Stock</label>
					    		<input class="form-control form-control-sm" type="text" v-model="producto.STOCK" disabled>
					    	</div>

					    	<div class="col-md-3">
					    		<label>Cantidad</label>
					    		<input v-bind:class="{ 'is-invalid': validar.CANTIDAD }" class="form-control form-control-sm" type="number" v-model="producto.CANTIDAD" :min="1" :max="producto.STOCK" v-on:blur="agregarProducto">
					    	</div>

					    	<div class="col-md-12">
					    		<hr>
					    	</div>

					    	<div class="col-md-12 mt-3">
					    		<table id="tablaDevolucion" class="display nowrap table table-striped table-bordered table-sm mb-3" style="width:100%" >
					                <thead>
					                    <tr>
					                        <th>#</th>
					                        <th>Codigo</th>
					                        <th>Descripción</th>
					                        <th>Lote</th>
					                        <th>Inicial</th>
					                        <th>Stock</th>
					                        <th class="cantidadColumna">Cant.</th>
					                        <th class="costoColumna">Costo</th>
					                        <th class="costoTotalColumna">C. Total</th>
					                        <th>Venc.</th>
					                        <th>Acción</th>
					                        <th>ID LOTE</th>
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
						                	<th>TOTALES</th>
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

					    	<!-- ------------------------------------------------------------------------ -->

					    	<!-- BOTON PROCESAR -->

					    	<div class="col-md-12">
					    		<div class="float-right mt-3">
					    		 	<button class="btn btn-success btn-sm" v-on:click="guardar">Procesar</button>
					    		</div>	
					    	</div>

					    	<!-- ------------------------------------------------------------------------ -->

					    </div>	
					  </div>
					</div>
				</div>
		</div>

		<!-- ------------------------------------------------------------------------ -->

        <div v-else>
            <cuatrocientos-cuatro></cuatrocientos-cuatro>
        </div>
        
        <!-- ------------------------------------------------------------------------ -->
        
		<!-- TOAST PRODUCTO TRANSFERENCIA MODIFICADO -->

			<b-toast id="toast-producto-devolucion-modificado" variant="success" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <strong class="mr-auto">Éxito !</strong>
		          <small class="text-muted mr-2">modificado</small>
		        </div>
		      </template>
		      Este producto ha sido modificado con éxito !
		    </b-toast>

		<!-- ------------------------------------------------------------------------ -->

		 <!-- TOAST CODIGO PRODUCTO REPETIDO -->

		<b-toast id="toast-producto-stock-superado" variant="warning" solid>
	      <template v-slot:toast-title>
	        <div class="d-flex flex-grow-1 align-items-baseline">
	          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
	          <strong class="mr-auto">Error !</strong>
	          <small class="text-muted mr-2">superada</small>
	        </div>
	      </template>
	      La cantidad ha superado el stock !
	    </b-toast>

	    <!-- ------------------------------------------------------------------------ -->

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
        		LOTE: '',
        		PROVEEDOR: '',
        		STOCK: '',
        		COSTO: '',
        		COSTO_TOTAL: '',
        		INICIAL: '',
        		VENCIMIENTO: '',
        		MONEDA: '',
        		DECIMAL: '',
        		LOTE_ID: ''
        	},
        	validar: {
        		CODIGO: false,
        		CANTIDAD: false,
        		LOTE: false,
        		PROVEEDOR: false,
        		OBSERVACION: false,
        		TABLA: false
        	}, 
        	deshabilitar: {
        		PROVEEDOR: false,
        		MONEDA: false
        	},
        	devolucion: {
        		OBSERVACION: '',
        		MONEDA: '',
        		DESCRIPCION_MONEDA: '',
        		TOTAL: '',
        		DECIMAL: ''
        	},
        	seleccion: {
        		CODIGO: ''
        	}
        }
      }, 
      methods: {
      	cargarLotes(codigo){

      		// ------------------------------------------------------------------------

      		let me = this;

      		// ------------------------------------------------------------------------

      		me.producto.CODIGO = codigo;
      		me.$refs.componente_lote.obtenerDatosLote(codigo, me.producto.PROVEEDOR, me.devolucion.MONEDA);

      		// ------------------------------------------------------------------------

      	},
      	lote_producto(valor){

      		this.producto.DESCRIPCION = valor.descripcion;
      		this.producto.LOTE = valor.lote;
      		this.producto.STOCK = valor.cantidad;
      		this.producto.COSTO = valor.costo;
      		this.producto.INICIAL = valor.inicial;
      		this.producto.VENCIMIENTO = valor.vencimiento;
      		this.producto.MONEDA = valor.moneda;
      		this.producto.DECIMAL = valor.decimal;
      		this.producto.LOTE_ID = valor.lote_id;

      	},
      	inivarAgregarProducto(){

      		this.producto = {
        		CODIGO: '',
        		CANTIDAD: '',
        		PROVEEDOR: this.producto.PROVEEDOR,
        		LOTE: '',
        		STOCK: '',
        		COSTO: '',
        		COSTO_TOTAL: '',
        		INICIAL: '',
        		VENCIMIENTO: '',
        		MONEDA: '',
        		DECIMAL: '',
        		LOTE_ID: ''
        	}

      	}, descripcionMoneda(valor){

        	// ------------------------------------------------------------------------

        	// DESCRIPCION MONEDA

        	this.devolucion.DESCRIPCION_MONEDA = valor;

        	// ------------------------------------------------------------------------

        }, cantidadDecimal(valor){

        	// ------------------------------------------------------------------------
        	
        	// DESCRIPCION MONEDA

        	this.devolucion.DECIMAL = valor;
        	
        	// ------------------------------------------------------------------------

        }, 
        agregarProducto(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES

            let me = this;
            var tableDevolucion = $('#tablaDevolucion').DataTable();
            var productoExistente = [];
            var cantidadNueva = 0;

            // ------------------------------------------------------------------------

            // CONTROLADOR

            if (me.producto.PROVEEDOR.length === 0) {
                me.validar.PROVEEDOR = true;
                falta = true;
            } else {
                me.validar.PROVEEDOR = false;
            }

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

            if (me.producto.PROVEEDOR.length === 0) {
                me.validar.PROVEEDOR = true;
                return;
            } else {
                me.validar.PROVEEDOR = false;
            }
            
            if (me.producto.LOTE.length === 0) {
                me.validar.LOTE = true;
                return;
            } else {
                me.validar.LOTE = false;
            }

            // ------------------------------------------------------------------------

            productoExistente = Common.existeProductoLoteDataTableCommon(tableDevolucion, me.producto.CODIGO, me.producto.LOTE, 3);

            // ------------------------------------------------------------------------

           	

            if (productoExistente.respuesta == true) {

            	// ------------------------------------------------------------------------

            	// SUMAR LA CANTIDAD EXISTENTE CON LA CANTIDAD NUEVA AGREGADA

            	cantidadNueva = parseFloat(productoExistente.cantidad) + parseFloat(me.producto.CANTIDAD);

            	// ------------------------------------------------------------------------

            	if (cantidadNueva <= me.producto.STOCK) {
            		me.editarCantidadProducto(tableDevolucion, cantidadNueva, productoExistente.costo, productoExistente.row);
            	} else {
            		me.$bvToast.show('toast-producto-stock-superado');
            		return true;
            	}

            	return true;
            }

            // ------------------------------------------------------------------------

            //  QUITAR COMA DE PRECIO

            me.producto.COSTO_TOTAL = Common.multiplicarCommon(me.producto.CANTIDAD, me.producto.COSTO, me.producto.DECIMAL);
         
            // ------------------------------------------------------------------------

            //  DAR FORMATO AL RESULTADO FINAL PARA MOSTRAR EN DATATABLE 

            me.producto.COSTO_TOTAL = Common.darFormatoCommon(me.producto.COSTO_TOTAL, me.producto.DECIMAL);

            // ------------------------------------------------------------------------

            // CARGAR DATO EN TABLA COMPRAS

            me.agregarFilaTabla(me.producto.CODIGO, me.producto.DESCRIPCION, me.producto.LOTE, me.producto.INICIAL, me.producto.CANTIDAD, me.producto.COSTO, me.producto.COSTO_TOTAL, me.producto.VENCIMIENTO, me.producto.STOCK, me.producto.LOTE_ID);

            // ------------------------------------------------------------------------

            // VACIAR TEXTBOX AGREGAR PRODUCTO

            me.inivarAgregarProducto();

            // ------------------------------------------------------------------------

            // VERIFICAR SI LA TABLA YA TIENE PRODUCTOS PARA DESHABILITAR MONEDA 

            if (tableDevolucion.rows().data().length > 0) {
    			me.deshabilitar.PROVEEDOR = true;
    			me.deshabilitar.MONEDA = true;
    		}

    		// ------------------------------------------------------------------------

	        // LLAMAR AL METODO HIJO 

	        this.producto.CODIGO = '';
	        this.$refs.compontente_codigo_producto.vaciarDevolver();

	        // ------------------------------------------------------------------------

        }, editarCantidadProducto(tabla, cantidad, costo, row){

        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES

        	let me = this;
        	var costo_total = 0;
        	var iva = 0;

            // ------------------------------------------------------------------------

            // CARGAR LO EDITADO

            tabla.cell(row, 6).data(cantidad).draw();

            // ------------------------------------------------------------------------
            
            // CARGAR COSTO 

            tabla.cell(row, 7).data(costo).draw();

            // ------------------------------------------------------------------------

            // CALCULAR PRECIO TOTAL

            costo_total = Common.multiplicarCommon(cantidad, costo, me.devolucion.DECIMAL);

		    // ------------------------------------------------------------------------

		    // CARGAR PRECIO CALCULADO 

            tabla.cell(row, 8).data(costo_total).draw();

            // ------------------------------------------------------------------------

            // LLAMAR TOAST MODIFICADO

            me.$bvToast.show('toast-producto-devolucion-modificado');

            // ------------------------------------------------------------------------

            // VACIAR TEXTBOX AGREGAR PRODUCTO

            me.inivarAgregarProducto();

            // ------------------------------------------------------------------------

        }, agregarFilaTabla(codigo, descripcion, lote, inicial, cantidad, costo, costo_total, vencimiento, stock, lote_id){

        	// ------------------------------------------------------------------------

        	//	INICIAR VARIABLES 

        	let me = this;
        	var tableDevolucion = $('#tablaDevolucion').DataTable();

            // ------------------------------------------------------------------------

            // VENCIMIENTO 

            // if (me.mostrar.VENCIMIENTO === 0) {
            // 	vencimiento = 'N/A';
            // }

            // ------------------------------------------------------------------------

        	// AGREGAR FILAS 
        	//"<a style='text-decoration: underline; cursor: pointer' id='cod_prod_tabla'>"+codigo+"</a>"
        	 tableDevolucion.rows.add( [ {
		                    "C": '',
		                    "CODIGO":   codigo,
		                    "DESCRIPCION":     descripcion,
		                    "LOTE": lote,
		                    "INICIAL": inicial,
		                    "STOCK": stock,
		                    "CANTIDAD": cantidad,
		                    "COSTO":    costo,
		                    "COSTO_TOTAL":    costo_total,
		                    "VENCIMIENTO": vencimiento,
		                    "ACCION":    "&emsp;<a role='button'  title='Eliminar'><i id='eliminarProducto' class='fa fa-trash text-danger' aria-hidden='true'></i></a>",
		                    "LOTE_ID": lote_id,
		                } ] )
		     .draw();

		    // ------------------------------------------------------------------------ 

            // AGREGAR INDEX A LA TABLA TRANSFERNCIAS

            tableDevolucion.on( 'order.dt search.dt', function () {
                tableDevolucion.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();

            // ------------------------------------------------------------------------

            // AJUSTAR COLUMNAS DE ACUERDO AL DATO QUE CONTIENEN

            tableDevolucion.columns.adjust().draw();

            // ------------------------------------------------------------------------

        }, 
        controlador() {

        	// ------------------------------------------------------------------------

        	let me = this;
        	var falta = false;
        	var tableDevolucion = $('#tablaDevolucion').DataTable();

        	// ------------------------------------------------------------------------

        	// CONTROLADOR

        	if (me.producto.PROVEEDOR.length === 0) {
                me.validar.PROVEEDOR = true;
                falta = true;
            } else {
                me.validar.PROVEEDOR = false;
            }

            if (me.devolucion.OBSERVACION.length === 0) {
                me.validar.OBSERVACION = true;
                falta = true;
            } else {
                me.validar.OBSERVACION = false;
            }

            if (tableDevolucion.rows().data().length === 0) {
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
        guardar() {

        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES 

        	let me = this;
        	var tableDevolucion = $('#tablaDevolucion').DataTable();

        	// ------------------------------------------------------------------------

        	// CONTROLAR TEXTBOX / SI ES TRUE SE DETIENE LA OPERACION DE GUARDADO

        	if (me.controlador() === true){
        		return;
        	}

        	// ------------------------------------------------------------------------

        	// PREPARAR ARRAY 

        	var data = {
        		proveedor: me.producto.PROVEEDOR,
        		observacion: me.devolucion.OBSERVACION,
        		moneda: me.devolucion.MONEDA,
        		total: me.devolucion.TOTAL,
        		productos: tableDevolucion.rows().data().toArray()
        	};

        	// ------------------------------------------------------------------------

      		// SWEET ALERT

      		Swal.fire({
				title: 'Estas seguro ?',
				text: "Devolver estos productos !",
				type: 'warning',
				showLoaderOnConfirm: true,
				showCancelButton: true,
				confirmButtonColor: '#d33',
				cancelButtonColor: '#3085d6',
				confirmButtonText: 'Si, guardalo!',
				cancelButtonText: 'Cancelar',
				preConfirm: () => {
				    return Common.guardarDevolucionProveedorCommon(data).then(data => {
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

					// REDIRIGIR A MOSTRAR COMPRAS
					  	
					me.$router.push('/pro4');

					// ------------------------------------------------------------------------

				}
			})

			// ------------------------------------------------------------------------
        }
      },  
        mounted() {

          // ------------------------------------------------------------------------

          let me = this;
          me.devolucion.MONEDA = String(me.monedaCodigo);

          // ------------------------------------------------------------------------

          // PREPARAR DATATABLE 

          var tableDevolucion = $('#tablaDevolucion').DataTable({
                        "processing": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "columns": [
                            { "data": "C" },
                            { "data": "CODIGO" },
                            { "data": "DESCRIPCION" },
                            { "data": "LOTE" },
                            { "data": "INICIAL" },
                            { "data": "STOCK" },
                            { "data": "CANTIDAD" },
                            { "data": "COSTO" },
                            { "data": "COSTO_TOTAL" },
                            { "data": "VENCIMIENTO" },
                            { "data": "ACCION" },
                            { "data": "LOTE_ID" }
                        ],
	                    "columnDefs": [
	                        	{
					                "targets": [ 11 ],
					                "visible": false,
					                "searchable": false
					            }
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

						  // COSTO

						  api.columns('.costoColumna', {
						    
						  }).every(function() {
						    var costo = this
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

						      $( api.columns('.costoColumna').footer() ).html(
					                Common.darFormatoCommon(costo, me.devolucion.DECIMAL)
					           );

						      // *******************************************************************

						  });

						  // *******************************************************************

						  // COSTO TOTAL

						  api.columns('.costoTotalColumna', {
						    
						  }).every(function() {
						    var costoTotal = this
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

						      $( api.columns('.costoTotalColumna').footer() ).html(
					                me.devolucion.TOTAL = Common.darFormatoCommon(costoTotal, me.devolucion.DECIMAL)
					           );

						      // *******************************************************************

						  });

						  // *******************************************************************
						  }      
          });
                    
          // ------------------------------------------------------------------------

          // CAMBIAR TAMAÑO FUENTE 

          $("#tablaDevolucion").css("font-size", 12);
          tableDevolucion.columns.adjust().draw();

          // ------------------------------------------------------------------------

          // MOSTRAR SWEET ELIMINAR

          tableDevolucion.on('click', 'tbody tr #eliminarProducto', function() {

           	// *******************************************************************

            // ABRIR EL SWEET ALERT

            Swal.fire({
				title: 'Estas seguro ?',
				text: "Eliminar el producto " + tableDevolucion.row($(this).parents('tr')).data().CODIGO + " !",
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
	                    
	                tableDevolucion.row($(this).parents('tr')).remove().draw(); 

	                // *******************************************************************

					Swal.fire(
						'Eliminado!',
						'Se ha eliminado el producto de la tabla !',
						'success'
					)

					// *******************************************************************

					// VERIFICAR SI LA TABLA YA NO TIENE PRODUCTOS PARA HABILITAR MONEDA 
            
	            	if (tableDevolucion.rows().data().length === 0) {
	    				me.deshabilitar.PROVEEDOR = false;
	    				me.deshabilitar.MONEDA = false;
	    			} 

    				// *******************************************************************

					  }
					})

                    // *******************************************************************

            });

            // ------------------------------------------------------------------------

            tableDevolucion.on('click', 'tbody tr a#cod_prod_tabla', function() {
            	console.log($(this).text());
            });

            // ------------------------------------------------------------------------
        }
    }
</script>
