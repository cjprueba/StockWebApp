<template>

	<div class="container mt-4">

		<div class="row" v-if="$can('inventario.crear')">

			<div class="col-md-12">

				<!-- ------------------------------------------------------------------------------------- -->

				<!-- TITULO  -->
					
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb">
							<li class="breadcrumb-item active" aria-current="page">Realizar Inventario</li>
					</ol>
				</nav>
				
			   <!-- ------------------------------------------------------------------------------------- -->

			</div>

			<div class="col-md-12">

				<!-- ------------------------------------------------------------------------------------- -->

				<!-- CARD INVENTARIO -->

				<div class="card" v-if="ventana === 1">
				  <div class="card-header">
				    Inventario
				  </div>
				  <div class="card-body">
				  	<div class="row">

					  	<div class="col-md-12">
					  		<selected-sucursal @sucursal_seleccionada="seleccionarSucursal"></selected-sucursal>
					  		<label class="mt-3">Observación</label>
					  		<textarea class="playSound form-control" id="exampleFormControlTextarea1" v-model="observacion" rows="3"></textarea>
					  	</div>
					  	<div class="col-md-6 mt-3">
					  		<label>Motivo</label>
						  	<select v-model="motivo" class="custom-select custom-select-sm">
						  		<option value="AUDITORIA">Auditoría</option>
						  		<option value="AJUSTE">Ajuste</option>
						  		<option value="CONTEO DE STOCK">Conteo de Stock</option>
						  		<option value="ROBO DE PRODUCTO">Robo de Producto</option>
						  	</select>
					  	</div>

					  	<div class="col-md-6 mt-3">
					  		<select-gondola v-model="seleccion_gondola" v-bind:selecciones="seleccion_gondola_modificar"></select-gondola>
					  	</div>

					  	<div class="col-md-12">
					  		<div class="text-right mt-3"> 
					  			<button class="btn btn-primary" v-on:click="guardarInventario()">Crear</button>
					  		</div>
					  	</div>	
				  	</div>
				  </div>
				</div>

				<!-- ------------------------------------------------------------------------------------- -->

				<!-- CONTAR PRODUCTOS -->

				<div v-else>

					<!-- ------------------------------------------------------------------ -->

			    	<!-- SALTO DE LINEA -->

			    	<hr>

					<!-- ------------------------------------------------------------------ -->

					<!-- COMPONENTE CODIGO PRODUCTO CANTIDAD -->

					<div class="mb-3">
						<div class="row">

							<div class="col-md-12 text-right">

						      <!-- ------------------------------------------------------------------------ -->

						      <!-- BOTON CAMARA -->

						      <camara-bardcode @codigo_camara="codigo_camara"></camara-bardcode>
						      
						      <!-- ------------------------------------------------------------------------ -->

						    </div>

							<div class="col-md-6">
								<codigo-producto  ref="compontente_codigo_producto_cantidad" v-model="codigoProductoCantidad"></codigo-producto >
							</div>	
							<div class="col-md-6">
								<label>Cantidad</label>
								<input class="form-control form-control-sm shadow-sm" type="text" v-model="cantidad" v-on:blur="cargarProductosInventarioPorCantidad()">
							</div>
						</div>	
						
						
					</div>

					<!-- ------------------------------------------------------------------------------------- -->
					
					<!-- SALTO DE LINEA -->

			    	<hr>

					<!-- ------------------------------------------------------------------ -->

					<!-- COMPONENTE CODIGO PRODUCTO -->

					<div class="mb-3">
						<codigo-producto @codigo_producto="cargarProductosInventario" ref="compontente_codigo_producto" v-model="codigoProducto"></codigo-producto >
					</div>

					<!-- ------------------------------------------------------------------------------------- -->
					
					<!-- MENSAJE GUARDADO  -->

					<transition name="slide-fade">

						<div class="alert alert-success mt-3" role="alert" v-if="mostrar">
						  <i class="fa fa-check"></i> {{response}} - <strong>{{codigoResponse}}</strong> - Cantidad: {{cantidadResponse}}
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
						    <span aria-hidden="true">&times;</span>
						  </button>  
						</div>

						<div class="alert alert-danger mt-3 mb-3" role="alert" v-if="mostrar === false">
						  <i class="fa fa-exclamation-triangle"></i> {{response}} - <strong>{{codigoResponse}}</strong>
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
						    <span aria-hidden="true">&times;</span>
						  </button>  
						</div>

					</transition>

					<!-- ------------------------------------------------------------------------------------- -->

					<!-- TABLA DE PRODUCTOS INGRESADOS -->

					<table id="tablaProductosConteo" class="table table-striped table-bordered table-sm" style="width:100%">
		                <thead>
		                    <tr>
		                        <th >Codigo Producto</th>
		                        <th>Descripción</th>
		                        <th class="conteoColumna">Conteo</th>
		                        <th class="stockColumna">Stock</th>
		                        <th>Comentario</th>
		                        <th>Acción</th>
		                    </tr>
		                </thead>
		                <tbody>
		                    
		                </tbody>
		                <tfoot>
		                	<tr>
		                		<th></th>
		                		<th>TOTALES</th>
			                	<th></th>
			                	<th></th>
			                	<th></th>
			                	<th></th>
		                	</tr>
		                </tfoot>	
		            </table>

		            <!-- ------------------------------------------------------------------------------------- -->

				</div>	
			   
			   	

			</div>

		</div>

		<!-- ------------------------------------------------------------------------ -->

		<div v-else>
			<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div>
		
		<!-- ------------------------------------------------------------------------ -->
	</div>
</template>

<script>
	require('howler');

	export default {
      props: [''],
      data(){
        return {
        	sucursal: 'null',
        	observacion: '',
        	ventana: 2,
        	response: '',
            codigoResponse: '',
            mostrar: '',
            cantidadResponse: '',
            id_Inventario: '',
            mostrarDatatable: true,
            codigoProducto: '',
            codigoProductoCantidad: '',
            cantidad: '',
            motivo: 'AUDITORIA',
          	seleccion_gondola: [{}],
          	seleccion_gondola_modificar: [{}]
        }
      }, 
      methods: {
      		codigo_camara(codigo){

	          // ------------------------------------------------------------------------

	          // CODIGO DE CAMARA RECIBIDO DEL COMPONENTE

	          this.codigoProductoCantidad = codigo;

	          // ------------------------------------------------------------------------

	        },
            seleccionarSucursal(valor){

            	// ------------------------------------------------------------------------

            	this.sucursal = valor;

				// ------------------------------------------------------------------------

           }, guardarInventario(){

           		// ------------------------------------------------------------------------

           		// INICIAR VARIABLES 

           		let me = this;

           		var datos = {
           			sucursal: me.sucursal,
           			observacion: me.observacion,
           			motivo: me.motivo,
           			gondola: me.seleccion_gondola
           		}

           		// ------------------------------------------------------------------------

           		Common.guardarInventarioCommon(datos).then(data => {
           			if (data.response === true) {

           				Swal.fire(
						  'Creado !',
						  'El conteo de inventario ha sido creado correctamente !',
						  'success'
						).then((result) => {
						  if (result.value) {

						  	// ------------------------------------------------------------------------ 

						  	// SI DA OK LO REDIRIJE A CONTAR PRODUCTOS

						    this.ventana = 2;
           					this.id_Inventario = data.id;

						    // ------------------------------------------------------------------------ 

						   }
						});
           				
           			}
           		});

           		// ------------------------------------------------------------------------

           }, cargarProductosInventario(codigo){

           		// ------------------------------------------------------------------------

           		// INICIAR VARIABLES 

	        	let me = this;
	        	me.mostrarDatatable = true;
	        	me.mostrar = '';
	        	var table = $('#tablaProductosConteo').DataTable();

        		
	        	// ------------------------------------------------------------------------

	        	if (codigo === '' || codigo === undefined) {
	        		return;
	        	}

	        	// ------------------------------------------------------------------------

	        	Common.agregarInventarioCommon(codigo, this.id_Inventario, 1).then(data => {
					   me.codigoResponse = data["codigo"];
					   me.response = data["status"];
					   
					   if (data["response"] === true) {

					   		me.mostrar = data["response"];
					   		me.cantidadResponse = data["cantidad"];
					   		
					   		// ------------------------------------------------------------------------

					   		// REPRODUCIR SONIDO OK 

					   		this.playSound(require("./../../../sonidos/ok.mp3"));

					   		// ------------------------------------------------------------------------

					   		// MOSTRAR TOAST 

					   		me.toast('Contado - '+me.codigoResponse+'', 'Cantidad: '+me.cantidadResponse+'','b-toaster-top-right', 'success');

					   		// ------------------------------------------------------------------------

	        				// RECARGAR DATATABLE 

	        				table.ajax.reload( null, false );

							// ------------------------------------------------------------------------

					   } else {
					   		me.mostrar = data["response"];

					   		// ------------------------------------------------------------------------

					   		// REPRODUCIR SONIDO ERROR
					   		
					   		this.playSound(require("./../../../sonidos/error.mp3"));

					   		// ------------------------------------------------------------------------

					   		// LLAMAR A TOAST ERROR 

					   		me.toast('Error - '+codigo+'', 'Este producto no existe !','b-toaster-top-right', 'danger');

					   		// ------------------------------------------------------------------------
					   }
				});

	        	// ------------------------------------------------------------------------

	        	// LLAMAR AL METODO HIJO 

	        	this.codigoProducto = '';
	        	//this.$refs.compontente_codigo_producto.input.focus();

	        	// ------------------------------------------------------------------------
					
        	}, cargarProductosInventarioPorCantidad(){

           		// ------------------------------------------------------------------------

           		// INICIAR VARIABLES 

	        	let me = this;
	        	me.mostrarDatatable = true;
	        	me.mostrar = '';
	        	var table = $('#tablaProductosConteo').DataTable();

	        	// ------------------------------------------------------------------------

	        	if (this.codigoProductoCantidad === '' || this.codigoProductoCantidad === undefined) {
	        		return;
	        	}


	        	if (this.cantidad === '' || this.cantidad === undefined) {
	        		return;
	        	}

	        	// ------------------------------------------------------------------------

	        	Common.agregarInventarioCommon(this.codigoProductoCantidad, this.id_Inventario, this.cantidad).then(data => {
					   me.codigoResponse = data["codigo"];
					   me.response = data["status"];
					   
					   if (data["response"] === true) {

					   		me.mostrar = data["response"];
					   		me.cantidadResponse = data["cantidad"];
					   		
					   		// ------------------------------------------------------------------------

					   		// REPRODUCIR SONIDO OK 

					   		this.playSound(require("./../../../sonidos/ok.mp3"));

					   		// ------------------------------------------------------------------------

					   		// MOSTRAR TOAST 

					   		me.toast('Contado - '+me.codigoResponse+'', 'Cantidad: '+me.cantidadResponse+'','b-toaster-top-right', 'success');

					   		// ------------------------------------------------------------------------

	        				// RECARGAR DATATABLE 

	        				table.ajax.reload( null, false );

							// ------------------------------------------------------------------------

					   } else {
					   		me.mostrar = data["response"];

					   		// ------------------------------------------------------------------------

					   		// REPRODUCIR SONIDO ERROR
					   		
					   		this.playSound(require("./../../../sonidos/error.mp3"));

					   		// ------------------------------------------------------------------------

					   		// LLAMAR A TOAST ERROR 

					   		me.toast('Error - '+this.codigoProductoCantidad+'', 'Este producto no existe !','b-toaster-top-right', 'danger');

					   		// ------------------------------------------------------------------------
					   }
				});

	        	// ------------------------------------------------------------------------

	        	// LLAMAR AL METODO HIJO 

	        	this.codigoProductoCantidad = '';
	        	this.cantidad = '';
	        	this.$refs.compontente_codigo_producto_cantidad.vaciarDevolver();

	        	// ------------------------------------------------------------------------
					
        	}, recargarDatatable(){

        		// ------------------------------------------------------------------------

        		let me = this;

        		// ------------------------------------------------------------------------

        		var table = $('#tablaProductosConteo').DataTable();

        		table.ajax.reload( null, false );

        		// ------------------------------------------------------------------------
        	},
        	toast(title, body, toaster, status, append = false) {
		        
		        // ------------------------------------------------------------------------

		        // REALIZAR PERSONALIZABLE  LOS TOAST 

		        this.$bvToast.toast(`${body}`, {
		          title: `${title}`,
		          toaster: toaster,
		          solid: true,
		          appendToast: append,
		          variant: `${status}`
		        })

		        // ------------------------------------------------------------------------
		    },playSound (sound) {
			      if(sound) {
			        var audio = new Audio(sound);
			        audio.play();
			      }
			 }, mostrarInventario(id){

			 	// ------------------------------------------------------------------------

	        	// INICIAR VARIABLES 

	        	let me = this;

	        	// ------------------------------------------------------------------------

	        	// SE ENCARGA DE COMPROBAR SI EXISTE CODIGO EN LA URL PARA PODER CARGAR UN INVENTARIO EXISTENTE
	        	
	        	if (id !== undefined) {

	        		// ------------------------------------------------------------------------

	        		// CARGAR VARIABLE EN ID 

	        		this.id_Inventario = id;

	        		// ------------------------------------------------------------------------

	        		// MOSTRAR LA SEGUNDA VENTANA 

	        		me.ventana = 2;

	        		// ------------------------------------------------------------------------
	        		

	        	} else {
	        		me.ventana = 1;
	        	}


	        		
	        	// ------------------------------------------------------------------------

			 }
      },
        mounted() {
        	
        	// ------------------------------------------------------------------------

            // INICIAR VARIABLES

            let me = this;
            

        	// ------------------------------------------------------------------------

        	// LLAMAR INVENTARIO HECHO

        	me.mostrarInventario(me.$route.params.id);
        	
        	// ------------------------------------------------------------------------
        	
        	var table = $('#tablaProductosConteo').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                        		"data": {
	                 				id: me.$route.params.id
	                 			},
                                 "url": "/inventarioProductos",
                                 "contentType": "application/json; charset=utf-8",
                                 "type": "GET"
                               },
                        "columns": [
                            { "data": "COD_PROD" },
                            { "data": "DESCRIPCION" },
                            { "data": "CONTEO" },
                            { "data": "STOCK" },
                            { "data": "COMENTARIO" },
                            { "data": "ACCION" }
                        ]      
                    });
        	
        	// ------------------------------------------------------------------------

        	$('#tablaProductosConteo').on('click', 'tbody tr #comentario', function() {

                	// *******************************************************************

                	// INICIAR VARIABLE 

                	var producto = '';

                	// *******************************************************************

                	// OBTENER COSTO DEL PRODUCTO DE LA TABLA 

                	producto = table.row($(this).parents('tr')).data().COD_PROD;

                    // *******************************************************************

                    // ABRIR EL SWEET ALERT
                     
                    Swal.mixin({
					  input: 'text',
					  confirmButtonText: 'Next &rarr;',
					  showCancelButton: true,
					  progressSteps: ['1']
					}).queue([
					  {
					    title: 'Comentario',
					    text: 'Ingrese el Comentario'
					  }
					]).then((result) => {
					  if (result.value) {
					    const comentario = result.value[0];
					    
					    Common.editarComentarioProductoInventarioCommon(me.id_Inventario, producto, comentario).then(data => {

							// ------------------------------------------------------------------------ 

							if (data.response === true) {
								Swal.fire({
							      title: 'Cambiado',
							      confirmButtonText: 'Aceptar !'
							    })

							    table.ajax.reload( null, false );
							}

							

							// ------------------------------------------------------------------------ 

						}).catch(error => {
							Swal.showValidationMessage(
							  `Request failed: ${error}`
							)
						});

					    
					  }
					})

                    // *******************************************************************

            });
        	
        	 $('#tablaProductosConteo').on('click', 'tbody tr #eliminar', function() {

                    // *******************************************************************

                    // OBTENER DATOS DEL PRODUCTO DATATABLE JS
                    
                    Swal.fire({
                      title: '¿ Eliminar ?',
                      text: 'Eliminar el producto',
                      type: 'warning',
                      showLoaderOnConfirm: true,
                      showCancelButton: true,
                      confirmButtonColor: 'btn btn-success',
                      cancelButtonColor: '#d33',
                      confirmButtonText: 'Si !',
                      cancelButtonText: 'Cancelar',
                      preConfirm: () => {

                        return Common.eliminarProductoInventarioCommon(me.id_Inventario, table.row($(this).parents('tr')).data().COD_PROD).then(data => {

                            // ------------------------------------------------------------------------

                            // REVISAR SI HAY DATOS 

                            if (!data.response === true) {
                              throw new Error(data.statusText);
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
                                  'Eliminado !',
                                  'Se ha eliminado el producto correctamente !',
                                  'success'
                        )

                        table.ajax.reload( null, false );

                        // ------------------------------------------------------------------------

                      }
                    })
                    //me.codigo = table.row($(this).parents('tr')).data().CODIGO;

                    // *******************************************************************

                });
        }
    }
</script>
<style>
	.bounce-enter-active {
	  animation: bounce-in .5s;
	}
	.bounce-leave-active {
	  animation: bounce-in .5s reverse;
	}
	@keyframes bounce-in {
	  0% {
	    transform: scale(0);
	  }
	  50% {
	    transform: scale(1.5);
	  }
	  100% {
	    transform: scale(1);
	  }
	}

	.fade-enter-active, .fade-leave-active {
	  transition: opacity .5s;
	}
	.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
	  opacity: 0;
	}

	.slide-fade-enter-active {
	  transition: all .3s ease;
	}
	.slide-fade-leave-active {
	  transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
	}
	.slide-fade-enter, .slide-fade-leave-to
	/* .slide-fade-leave-active below version 2.1.8 */ {
	  transform: translateX(10px);
	  opacity: 0;
	}
</style>