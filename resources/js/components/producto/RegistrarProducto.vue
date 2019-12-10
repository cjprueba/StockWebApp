<template>
	<div class="container-fluid">
		
		<div class="mt-3 mb-3" v-if="$can('products.create')">
			
			<!-- ------------------------------------------------------------------ -->

			<!-- MENSAJE DE ERROR SI NO HAY CONECCION  -->
			
			<mensaje v-bind:mostrar_error="mostrar_error, mensaje"></mensaje>

			<!-- ------------------------------------------------------------------------------------- -->
			
			<!-- DIVISORIA VUESAX -->

			<!-- <vs-divider>
			   Registrar Producto
			</vs-divider> -->
			
			<!-- ------------------------------------------------------------------------------------- -->

			<!-- CARD REGISTRAR PRODUCTO -->

			<!-- <div class="card shadow">
			  <div class="card-header">
			    <ul class="nav nav-tabs card-header-tabs">
			      <li class="nav-item">
			        <a class="nav-link active text-primary" href="#" value="1"><b>Producto</b></a>
			      </li>
			      <li class="nav-item">
			        <a class="nav-link" href="#" value="2">Producto</a>
			      </li>
			      <li class="nav-item">
			        <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
			      </li>
			    </ul>
			  </div>
			  <div class="card-body"> -->

			  	<!-- ------------------------------------------------------------------------------------- -->

			  	<!-- PRODUCTO - CUERPO 1 CARD -->

			  	<!-- <div v-if="nav === '1'"> -->

			<vs-tabs>
      			<vs-tab label="Registrar">	
				    <div class="row mt-3">

				    		

				    	<!-- ------------------------------------------------------------------ -->

				    	<div class="col-md-12">

				    		<!-- ------------------------------------------------------------------ -->

				    		<!-- HABILITAR CODIGO REAL -->

					   		<div class="my-1">
								  <div class="custom-control custom-switch mr-sm-2">
								   <input type="checkbox" class="custom-control-input" id="customControlAutosizing" v-model="checked_codigo_real">
								   <label class="custom-control-label" for="customControlAutosizing">Código Real</label>
								 </div>
							</div>

							<!-- ------------------------------------------------------------------ -->

				    	</div>	

				    	<!-- ------------------------------------------------------------------ -->

				    	<!-- PRIMERA COLUMNA -->

				    	<div class="col-md-6">
				    		<div clas="row">

					    		<!-- ------------------------------------------------------------------ -->

				    			<!-- SALTO DE LINEA -->

				    			<hr>

							    <!-- ------------------------------------------------------------------ -->

				    			<!-- GRUPO DE FORMA CODIGO -->

					    		<div class="form-row">

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- CODIGO PRODUCTO -->

					    			<div class="col-6">
					    				<codigo-producto  v-model="codigo_producto" v-bind:candec="candec, monedaCodigo, tab_unica, shadow, checked_codigo_real, validar_codigo_producto" @codigo_producto="codigoProducto"></codigo-producto>
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- CODIGO INTERNO -->

					    			<div class="col-6">
					    				<label>Código Interno</label>
					    				<input type="text" class="form-control form-control-sm" v-model="codigo_interno" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_codigo_interno }" v-on:change="() => generado = false">
					    			</div>	

					    			<!-- ------------------------------------------------------------------ -->


				    			</div>

							    <!-- ------------------------------------------------------------------ -->

							    <!-- GRUPO DE FORMA CODIGO REAL -->

					    		<div class="form-row mt-3" v-if="checked_codigo_real">

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- CODIGO REAL -->

					    			<div class="col-12">
					    				<label>Código Real</label>
					    				<input type="text" class="form-control form-control-sm" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_codigo_real }" v-model="codigo_real" v-on:keyup="actualizarCodigo" v-on:blur="asignar">
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

				    			</div>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- SALTO DE LINEA -->
				    			
				    			<hr>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- GRUPO DESCRIPCION -->

					    		<div class="form-row">

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- DESCRIPCION -->

					    			<div class="col-12">
					    				<label>Descripción</label>
					    				<input type="text" class="form-control form-control-sm" v-model="descripcion" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_descripcion }" v-on:blur="descripcionUpper">
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    		</div>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- SALTO DE LINEA -->
				    			
				    			<hr>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- GRUPO DE FORMA CATEGORIA Y SUB -->

					    		<div class="form-row">

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- CATEGORIA -->

					    			<div class="col-md-6">
					    				<selected-categoria v-model="seleccion_categoria" v-bind:shadow="shadow, validar_categoria" @opciones="opcionesCategoria"></selected-categoria>
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- SUB CATEGORIA -->

					    			<div class="col-md-6">
					    				<selected-sub-categoria v-model="seleccion_sub_categoria" v-bind:shadow="shadow, validar_sub_categoria" @descripcion_sub_categoria="descripcionSubCategoria"></selected-sub-categoria>
					    			</div>	

					    			<!-- ------------------------------------------------------------------ -->

					    		</div>

					    		<!-- ------------------------------------------------------------------ -->

					    		<!-- SALTO DE LINEA -->

				    			<hr>

				    			<!-- ------------------------------------------------------------------ -->

					    		<!-- FORM ROW COLOR -->

					    		<div class="form-row mt-3" v-if="mostrar_color">

						    		<!-- ------------------------------------------------------------------ -->

					    			<!-- COLOR -->

					    			<div class="col-md-12">
					    				<select-color v-model="seleccion_color" v-bind:shadow="shadow, validar_color" @cambiar_codigo="asignar" @descripcion_color="descripcionColor"></select-color>
					    			</div>	

					    			<!-- ------------------------------------------------------------------ -->

				    			</div>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- FORM ROW TELA -->

					    		<div class="form-row mt-3" v-if="mostrar_tela">

						    		<!-- ------------------------------------------------------------------ -->

					    			<!-- TELA -->

					    			<div class="col-md-12">
					    				<select-tela v-model="seleccion_tela" v-bind:shadow="shadow, validar_tela" @descripcion_tela="descripcionTela"></select-tela>
					    			</div>	

					    			<!-- ------------------------------------------------------------------ -->

				    			</div>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- FORM ROW TALLE -->

					    		<div class="form-row mt-3" v-if="mostrar_talle">

						    		<!-- ------------------------------------------------------------------ -->

					    			<!-- TALLE -->

					    			<div class="col-md-12">
					    				<select-talle v-model="seleccion_talle" v-bind:shadow="shadow, validar_talle" @cambiar_codigo="asignar" @descripcion_talle="descripcionTalle"></select-talle>
					    			</div>	

					    			<!-- ------------------------------------------------------------------ -->

				    			</div>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- FORM ROW GENERO -->

					    		<div class="form-row mt-3" v-if="mostrar_genero">

						    		<!-- ------------------------------------------------------------------ -->

					    			<!-- GENERO -->

					    			<div class="col-md-12">
					    				<select-genero v-model="seleccion_genero" v-bind:shadow="shadow, validar_genero" @descripcion_genero="descripcionGenero"></select-genero>
					    			</div>	

					    			<!-- ------------------------------------------------------------------ -->

				    			</div>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- FORM ROW MARCA -->

					    		<div class="form-row mt-3" v-if="mostrar_marca">

						    		<!-- ------------------------------------------------------------------ -->

					    			<!-- MARCA -->

					    			<div class="col-md-12">
					    				<select-marca v-model="seleccion_marca" v-bind:shadow="shadow, validar_marca" @descripcion_marca="descripcionMarca"></select-marca>
					    			</div>	

					    			<!-- ------------------------------------------------------------------ -->

				    			</div>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- FORM ROW PROVEEDOR -->

					    		<div class="form-row mt-3">

						    		<!-- ------------------------------------------------------------------ -->

					    			<!-- PROVEEDOR -->

					    			<div class="col-md-12">
					    				<select-proveedor v-model="seleccion_proveedor" v-bind:shadow="shadow, validar_proveedor"></select-proveedor>
					    			</div>	

					    			<!-- ------------------------------------------------------------------ -->

				    			</div>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- SALTO DE LINEA -->

				    			<hr>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- FORM ROW PRESENTACION Y UBCACION -->
					    		
					    		<div class="form-row mt-3">

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- PRESENTACIÓN -->

					    			<div class="col-md-12">
					    				<div class="text-left">
					    					<label>Presentación</label>
					    				</div>
					    				<select class="custom-select custom-select-sm" v-model="seleccion_presentacion" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_presentacion }">
						                    <option value="UNIDADES">1 - UNIDADES</option>
						                    <option value="2">2 - PESABLE</option>
						                    <option value="3">3 - METROS</option>
						                    <option value="4">4 - CAJAS</option>
						                    <option value="5">5 - CIEN</option>
						                    <option value="6">6 - DOCENA</option>
						                    <option value="7">7 - LITROS</option>
						                    <option value="8">8 - PAQUETES</option>
						                    <option value="9">9 - SOBRES</option>
						            	</select>
					    			</div>	

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- UBICACIÓN -->

					    			<!-- <div class="col-md-6">
					    				<div class="text-left">
					    					<label>Ubicación</label>
					    				</div>
					    				<input type="text" class="form-control form-control-sm" v-bind:class="{ 'shadow-sm': shadow }">
					    			</div> -->

					    			<!-- ------------------------------------------------------------------ -->

				    			</div>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- SALTO DE LINEA -->

				    			<hr>

				    			<!-- ------------------------------------------------------------------ -->

				    		</div>		
				    	</div>

				    	<!-- ------------------------------------------------------------------ -->

				    	<!-- SEGUNDA COLUMNA -->

				    	<div class="col-md-6">
				    		<div clas="row">

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- SALTO DE LINEA -->

				    			<hr>

							    <!-- ------------------------------------------------------------------ -->

				    			<!-- FORM ROW IVA Y DESCUENTO -->
					    		
					    		<div class="form-row mt-3">

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- IVA -->

					    			<div class="col-md-4">
					    				<label for="inlineFormInputGroupUsername">I.V.A.</label>
					    				<div class="input-group input-group-sm"  v-bind:class="{ 'shadow-sm': shadow }">
						    				<input type="text" class="form-control form-control-sm" id="inlineFormInputGroupUsername" v-model="iva" v-on:blur="formatoIVA">
						    				<div class="input-group-append">
									          <div class="input-group-text">%</div>
									        </div>
									    </div>    
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- DESCUENTO MAXIMO -->

					    			<div class="col-md-4">
					    				<label>Descuento Máximo</label>
					    				<div class="input-group input-group-sm"  v-bind:class="{ 'shadow-sm': shadow }">
					    					<input type="text" class="form-control form-control-sm" v-model="descuento_maximo" v-on:blur="formatoDescuento">
					    					<div class="input-group-append">
									          <div class="input-group-text">%</div>
									        </div>
									    </div> 
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- MONEDA -->

					    			<div class="col-md-4">
					    				<select-moneda v-model="seleccion_moneda" v-bind:shadow="shadow, validar_moneda" @descripcion_moneda="llamarPrecios" @cantidad_decimales="cantidad_decimal"></select-moneda>
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    			</div>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- SALTO DE LINEA -->

				    			<hr v-if="mostrar_precios">

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- FORM ROW PRECIO VENTA Y PRECIO MAYORISTA -->
					    		
					    		<div class="form-row mt-3" v-if="mostrar_precios">

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- PRECIO VENTA -->

					    			<div class="col-md-6">
					    				<label>Precio Venta</label>
					    				<div class="input-group input-group-sm mb-3" v-bind:class="{ 'shadow-sm': shadow }">
					    					<div class="input-group-prepend">
											    <span class="input-group-text" id="inputGroup-sizing-sm">{{descripcion_precio}}</span>
											</div>
					    					<input type="text" v-bind:class="{ 'is-invalid': validar_precio_venta }" class="form-control form-control-sm" v-model="precio_venta" v-on:blur="formatoPrecioVenta">
					    				</div>
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- PRECIO MAYORISTA -->

					    			<div class="col-md-6">
					    				<label>Precio Mayorista</label>
					    				<div class="input-group input-group-sm mb-3" v-bind:class="{ 'shadow-sm': shadow }">
					    					<div class="input-group-prepend">
											    <span class="input-group-text" id="inputGroup-sizing-sm">{{descripcion_precio}}</span>
											</div>
					    					<input type="text" v-bind:class="{ 'is-invalid': validar_precio_mayorista }" class="form-control form-control-sm" v-model="precio_mayorista" v-on:blur="formatoPrecioMayorista">
					    				</div>	
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    		</div>

					    		<!-- ------------------------------------------------------------------ -->

				    			<!-- FORMA ROW PRECIO VIP Y COSTO -->
					    		
					    		<div class="form-row mt-3" v-if="mostrar_precios">

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- PRECIO VIP -->

					    			<div class="col-md-6">
					    				<label>Precio VIP</label>
					    				<div class="input-group input-group-sm mb-3" v-bind:class="{ 'shadow-sm': shadow }">
					    					<div class="input-group-prepend">
											    <span class="input-group-text" id="inputGroup-sizing-sm">{{descripcion_precio}}</span>
											</div>
					    					<input type="text" class="form-control form-control-sm" v-model="precio_vip" v-on:blur="formatoPrecioVIP">
					    				</div>	
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- PRECIO COSTO -->

					    			<div class="col-md-6">
					    				<label>Precio Costo</label>
					    				<div class="input-group input-group-sm mb-3" v-bind:class="{ 'shadow-sm': shadow }">
					    					<div class="input-group-prepend">
											    <span class="input-group-text" id="inputGroup-sizing-sm">{{descripcion_precio}}</span>
											</div>
					    					<input type="text" v-bind:class="{ 'is-invalid': validar_precio_costo }" class="form-control form-control-sm" v-model="precio_costo" v-on:blur="formatoPrecioCosto">
					    				</div>	
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    		</div>

					    		<!-- ------------------------------------------------------------------ -->

					    		<!-- SALTO DE LINEA -->

				    			<hr>

				    			<!-- ------------------------------------------------------------------ -->

					    		<!--  STOCK MINIMO -->
					    		
					    		<div class="form-row mt-3">

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- STOCK MINIMO -->

					    			<div class="col-md-12">
					    				<div class="text-left">
					    					<label>Stock Mínimo</label>
					    				</div>
					    				<input type="text" class="form-control form-control-sm" v-bind:class="{ 'shadow-sm': shadow }" v-model="stock_minimo" v-on:blur="formatoStockMinimo">
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    		</div>

					    		<!-- ------------------------------------------------------------------ -->

					    		<!-- GONDOLA -->
					    		
					    		<div class="form-row mt-3">

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- GONDOLA -->

					    			<div class="col-md-12">
					    				<select-gondola v-model="seleccion_gondola"  v-bind:shadow="shadow"></select-gondola>
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    		</div>

					    		<!-- ------------------------------------------------------------------ -->

					    		<!-- FORMA OBSERVACION -->
					    		
					    		<div class="form-row mt-3">

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- MONEDA -->

					    			<div class="col-md-12">
					    				<label for="exampleFormControlTextarea1">Observación</label>
						  				<textarea class="form-control" id="exampleFormControlTextarea1" v-bind:class="{ 'shadow-sm': shadow }" v-model="observacion" rows="3"></textarea>
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    		</div>

					    		<!-- ------------------------------------------------------------------ -->

					    		<!-- FORMA GONDOLA -->
					    		
					    		<div class="form-row mt-3">

					    			

					    		</div>

					    		<!-- ------------------------------------------------------------------ -->

					    		<!-- IMAGEN -->
					    		
					    		<div class="form-row mt-3">

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- IMAGEN -->

					    			<div class="col-md-12">

					    				<div class="card mb-3" v-bind:class="{ 'shadow-sm': shadow }">
										  <div class="row no-gutters">
										    <div class="col-md-4">
										      <img  :src="rutaImagen" class="card-img" alt="...">
										    </div>
										    <div class="col-md-8">
										      <div class="card-body">
										        <h5 class="card-title">{{fileName}}</h5>
										    	<p class="card-text">Selecione por favor la imagen.</p>
										    	<div class="custom-file">
												  <input type="file" class="custom-file-input" id="customFile" v-on:change="cambiarImagen($event.target.files[0])" lang="es">
												  <label class="custom-file-label" for="customFile">Elegir Archivo</label>
												</div>
										      </div>
										    </div>
										  </div>
										</div>

					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    		</div>

					    		<!-- ------------------------------------------------------------------ -->

				    		</div>	
				    	</div>

				    	<!-- ------------------------------------------------------------------ -->	

				    	<!-- SEGUNDA FILA -->

				    	<div class="col-md-12">

				    		<!-- ------------------------------------------------------------------ -->	

				    		<!-- BOTON NUEVO, MODIFICAR O GUARDAR -->

				    		
				    			<div class="form-inline float-right">

				    				<!-- ------------------------------------------------------------------ -->	

				    				<!-- NUEVO -->

				    				<div class="form-group mx-sm-3">
				    					<button class="btn btn-secondary" v-on:click="()=> location.reload()">Nuevo</button>
				    				</div>

				    				<!-- ------------------------------------------------------------------ -->	

				    				<!-- GUARDAR, EDITAR -->

				    				<div class="form-group">
				    					<button v-on:click="guardar" v-bind:class="{ 'btn btn-primary': estado_boton.boton_primary, 'btn btn-warning': estado_boton.boton_warning }" class="btn btn-primary"><font-awesome-icon icon="save" /> {{estado_boton.boton}} </button>
				    				</div>
				    				
				    				<!-- ------------------------------------------------------------------ -->	

				    			</div>	
				    			
				  

				    		<!-- ------------------------------------------------------------------ -->	

				    	</div>
				    		
				    	<!-- ------------------------------------------------------------------ -->

				    </div>	
			    <!-- </div> -->

			    <!-- ------------------------------------------------------------------------------------- -->
				</vs-tab>
				<vs-tab label="Información">
				</vs-tab>	
			    <!-- CUERPO 2 CARD -->

			   <!--  <div v-if="nav === '2'">
				    <h5 class="card-title">PROBANDO</h5>
				    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
				    <a href="#" class="btn btn-primary">Go somewhere</a>
			    </div>
 -->
			    <!-- ------------------------------------------------------------------------------------- -->
			</vs-tabs>	
			  <!-- </div>
			</div> -->

			<!-- ------------------------------------------------------------------------------------- -->
		</div>

		<div v-else>
			<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div>	
	</div>
</template>
<script>
	

  export default {
  	  props: ['candec', 'monedaCodigo', 'tab_unica'],
      data(){
        return {
          nav: "1",
          codigo_producto: '',
          codigo_interno: '',
          codigo_real: '',
          descripcion: '',
          seleccion_categoria: 'null',
          seleccion_sub_categoria: 'null',
          seleccion_color: 'null',
          seleccion_tela: 'null',
          seleccion_talle: 'null',
          seleccion_genero: 'null',
          seleccion_marca: 'null',
          seleccion_proveedor: 'null',
          seleccion_moneda: 'null',
          seleccion_proveedor: 'null',
          seleccion_gondola: [{}],
          seleccion_presentacion: 'UNIDADES',
          observacion: '',
          iva: '10',
          descuento_maximo: 0,
          generado: true,
          rutaImagen: require('./../../../imagenes/SinImagen.png'),
          fileName: 'Imagen',
          shadow: true,
          mostrar_precios: false,
          descripcion_precio: '',
          precio_venta: '',
          precio_mayorista: '',
          precio_vip: '',
          precio_costo: '',
          candec_moneda_textbox: 0,
          stock_minimo: 0,
          mostrar_color: false,
          mostrar_tela: false,
          mostrar_talle: false,
          mostrar_genero: false,
          mostrar_marca: false,
          checked_codigo_real: false,
          descri_color: '',
          descri_tela: '',
          descri_talle: '',
          descri_genero: '',
          descri_marca: '',
          descri_categoria: '',
          descri_sub_categoria: '',
          validar_codigo_producto: false,
          validar_codigo_interno: false,
          validar_descripcion: false,
          validar_categoria: false,
          validar_sub_categoria: false,
          validar_color: false,
          validar_tela: false,
          validar_talle: false,
          validar_genero: false,
          validar_marca: false,
          validar_proveedor: false,
          validar_presentacion: false,
          validar_moneda: false,
          validar_precio_venta: false,
          validar_precio_mayorista: false,
          validar_precio_costo: false,
          validar_codigo_real: false,
          mostrar_error: false,
          mensaje: '',
          estado_boton: {
          		boton: 'Guardar',
          		boton_primary: true,
          		boton_warning: false,
          		mostrar_nuevo: false,
          },
          datos: []
        }
      }, 
      methods: {
            codigoProducto(valor){

            	// ------------------------------------------------------------------------

            	// CARGAR CODIGO PRODUCTO 

            	this.codigo_producto = valor;

            	// ------------------------------------------------------------------------

            	// OBTENER CODIGO GENARADO 

            	Common.obtenerProductoCommon(this.codigo_producto, 1).then(data => {

            		// ------------------------------------------------------------------------

            		// RETONAR SI ES FALSE LA RESPUESTA YA QUE NO ENCONTRO PRODUCTO 

            		if (data.response === false) {
            			return;
            		}

            		// ------------------------------------------------------------------------

            		// CARGAR DATOS DEL PRODUCTO 

            		this.codigo_producto = data.producto.CODIGO;
                    this.codigo_interno = data.producto.CODIGO_INTERNO; 
                    this.descripcion = data.producto.DESCRIPCION; 
                    this.iva = data.producto.IVA; 
                    this.seleccion_categoria = data.producto.LINEA.toString();
                    this.seleccion_sub_categoria = data.producto.SUBLINEA.toString();
                    this.proveedor = data.producto.PROVEEDOR;
                    this.seleccion_color = data.producto.COLOR.toString();
                    this.seleccion_tela = data.producto.TELA.toString();
                    this.seleccion_talle = data.producto.TALLE.toString();
                    this.seleccion_genero = data.producto.GENERO.toString();
                    this.seleccion_marca = data.producto.MARCA.toString();
                    this.seleccion_presentacion = data.producto.PRESENTACION;
                    this.descuento_maximo = data.producto.DESCUENTO; 
                    this.precio_venta = data.producto.PREC_VENTA;
                    this.precio_mayorista = data.producto.PREMAYORISTA;
                    this.precio_vip = data.producto.PREVIP;
                    this.precio_costo = data.producto.PRECOSTO;
                    this.stock_minimo = data.producto.STOCK_MIN;
                    this.seleccion_gondola = data.producto.FK_GONDOLA;
                    this.observacion = data.producto.OBSERVACION;
                    this.seleccion_moneda = data.producto.MONEDA.toString();

                    // ------------------------------------------------------------------------

                    // CARGAR CODIGO REAL 

                    if (data.producto.CODIGO_REAL !== '' && data.producto.CODIGO_REAL !== null && data.producto.CODIGO_REAL !== undefined) {
                    	this.checked_codigo_real = true;
                    	this.codigo_real = data.producto.CODIGO_REAL;
                    }

            		// ------------------------------------------------------------------------

            		// REVISAR IMAGEN 

            		if (data.imagen === false) {
            			this.rutaImagen = './../../../imagenes/SinImagen.png';
            		} else {
            			this.rutaImagen = data.imagen;
            		}
            		// ------------------------------------------------------------------------

            		// CAMBIAR BOTON 

            		this.estado_boton.boton = 'Modificar';
            		this.estado_boton.boton_primary = false;
            		this.estado_boton.boton_warning = true;
            		this.estado_boton.mostrar_nuevo = true;

           			// ------------------------------------------------------------------------

           		}).catch((err) => {
           			this.mostrar_error = true;
           			this.mensaje = err+' - ¡ Revise la conección y recargue la página !';
           		});

           		// ------------------------------------------------------------------------
            }, 
            cambiarImagen(f){

            	let me = this;

            	if (f) {
					if ( /(jpe?g|png|gif)$/i.test(f.type) ) {
						var r = new FileReader();
						r.onload = function(e) { 
							var base64Img = e.target.result;
							// var binaryImg = convertDataURIToBinary(base64Img);
							// var blob = new Blob([binaryImg], {type: f.type});
							// blobURL = window.URL.createObjectURL(blob);
							me.fileName = f.name;
							me.rutaImagen = base64Img;
						}
						r.readAsDataURL(f);

						
					} else { 
						alert("Failed file type");
					}
			    } else { 
					alert("Failed to load file");
			    }
            },
            llamarPrecios(descripcion){

            	// -------------------------------------------------------------------------------------

            	// MOSTRAR PRECIOS

            	this.mostrar_precios = true;

            	// -------------------------------------------------------------------------------------

            	// CARGAR DESCRIPCION A ADDON

            	this.descripcion_precio = descripcion;

            	// -------------------------------------------------------------------------------------

            },cantidad_decimal(valor){

            	// -------------------------------------------------------------------------------------

            	// CARGAR LA CANTIDAD DE DECIMALES DE LA MONEDA SELECCIONADA DEL TEXTBOX 

            	this.candec_moneda_textbox = valor;

            	// -------------------------------------------------------------------------------------

            	// LAMAR FORMATO PRECIOS 

            	this.formatoPrecioVenta();
            	this.formatoPrecioMayorista();
            	this.formatoPrecioVIP();
            	this.formatoPrecioCosto();
            	
            	// -------------------------------------------------------------------------------------

            },formatoPrecioVenta(){

                // ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;

	            // ------------------------------------------------------------------------

	            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A PRECIO

	            me.precio_venta = Common.darFormatoCommon(me.precio_venta, me.candec_moneda_textbox);

	            // ------------------------------------------------------------------------

            }, formatoPrecioMayorista(){

                // ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;

	            // ------------------------------------------------------------------------

	            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A PRECIO

	            me.precio_mayorista = Common.darFormatoCommon(me.precio_mayorista, me.candec_moneda_textbox);

	            // ------------------------------------------------------------------------

            }, formatoPrecioVIP(){

                // ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;

	            // ------------------------------------------------------------------------

	            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A PRECIO

	            me.precio_vip = Common.darFormatoCommon(me.precio_vip, me.candec_moneda_textbox);

	            // ------------------------------------------------------------------------

            }, formatoPrecioCosto(){

                // ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;

	            // ------------------------------------------------------------------------

	            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A PRECIO

	            me.precio_costo = Common.darFormatoCommon(me.precio_costo, me.candec_moneda_textbox);

	            // ------------------------------------------------------------------------

            }, formatoIVA(){

            	// ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;

	            // ------------------------------------------------------------------------

	            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A PRECIO

	            me.iva = Common.darFormatoCommon(me.iva, 0);

	            // ------------------------------------------------------------------------

            }, formatoDescuento(){

            	// ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;

	            // ------------------------------------------------------------------------

	            // REVISAR QUE NO SUPERE VALOR 100 DE DESCUENTO 

	            if (parseInt(me.descuento_maximo) > 100) {
	            	me.descuento_maximo = 100;
	            }

	            // ------------------------------------------------------------------------

	            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A PRECIO

	            me.descuento_maximo = Common.darFormatoCommon(me.descuento_maximo, 0);

	            // ------------------------------------------------------------------------

            }, formatoStockMinimo(){

            	// ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;

	            // ------------------------------------------------------------------------

	            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A PRECIO

	            me.stock_minimo = Common.darFormatoCommon(me.stock_minimo, 0);

	            // ------------------------------------------------------------------------

            }, opcionesCategoria(valor){

            	// ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;

	            // ------------------------------------------------------------------------

	            // CONVERTIR VALOR 1 Y O EN BOOLEANO

	            me.mostrar_color = !!+valor.ATRIBCOLOR;
	            me.mostrar_tela = !!+valor.ATRIBTELA;
	            me.mostrar_talle = !!+valor.ATRIBTALLE;
	            me.mostrar_genero = !!+valor.ATRIBGENERO;
	            me.mostrar_marca = !!+valor.ATRIBMARCA;

	            // ------------------------------------------------------------------------

	            // CARGAR DESCRIPCION

	            me.descri_categoria = valor.DESCRIPCION;

	            // ------------------------------------------------------------------------

	            // GENERAR DESCRIPCION 

	            this.generarDescripcion();

	            // ------------------------------------------------------------------------

            }, actualizarCodigo(){

            	// ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;

	            // ------------------------------------------------------------------------

	            me.codigo_producto = me.codigo_real;

	            // ------------------------------------------------------------------------

            }, asignar(valor){

            	// ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;
	            var codigo = '';
	            codigo = me.codigo_real;

	            // ------------------------------------------------------------------------

	            // GENERAR CODIGO PRODUCTO 

	            if (me.seleccion_talle !== 'null') {
	            	codigo = codigo+'-'+me.seleccion_talle;
	            }

	            if (me.seleccion_color !== 'null') {
	            	codigo = codigo+'-'+me.seleccion_color;
	            }

	            // ------------------------------------------------------------------------

	            // TRANSFORMAR CODIGO 

	            if (me.checked_codigo_real) {
	            	me.codigo_producto = codigo;
	            }

	            // ------------------------------------------------------------------------

            }, obtenerCodigo(){

            	// ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;

            	// ------------------------------------------------------------------------

            	// OBTENER CODIGO GENARADO 

            	Common.generarCodigoCommon().then(data => {
           			me.codigo_producto = data;
           		}).catch((err) => {
           			this.mostrar_error = true;
           			this.mensaje = err+' - ¡ Revise la conección y recargue la página !';
           		});

           		// ------------------------------------------------------------------------

            	// OBTENER CODIGO INTERNO GENARADO 

            	Common.generarCodigoInternoCommon().then(data => {
           			me.codigo_interno = data;
           		}).catch((err) => {
           			this.mostrar_error = true;
           			this.mensaje = err+' - ¡ Revise la conección y recargue la página !';
           		});

           		// ------------------------------------------------------------------------

            }, generarDescripcion() {

            	// ------------------------------------------------------------------------

            	// INICIAR VARIABLES 

            	var descripcion = '';

            	// ------------------------------------------------------------------------

            	// MARCA DESCRIPCION 

            	descripcion = descripcion.concat(this.mostrar_marca ? this.descri_marca : '');

            	// ------------------------------------------------------------------------

            	// CATEGORIA DESCRIPCION 

       			descripcion = descripcion.concat(' '+this.descri_categoria);

       			// ------------------------------------------------------------------------

            	// SUB CATEGORIA DESCRIPCION 

       			descripcion = descripcion.concat(' '+this.descri_sub_categoria);

       			// ------------------------------------------------------------------------

            	// GENERO DESCRIPCION 

       			descripcion = descripcion.concat(this.mostrar_genero ? ' '+this.descri_genero : '');

       			// ------------------------------------------------------------------------

            	// TALLE DESCRIPCION 

       			descripcion = descripcion.concat(this.mostrar_talle ? ' '+this.descri_talle : '');

       			// ------------------------------------------------------------------------

            	// COLOR DESCRIPCION 

       			descripcion = descripcion.concat(this.mostrar_color ? ' '+this.descri_color : '');

       			// ------------------------------------------------------------------------

            	// TELA DESCRIPCION 

       			descripcion = descripcion.concat(this.mostrar_tela ? ' '+this.descri_tela : '');

            	// ------------------------------------------------------------------------

            	// CARGAR DESCRIPCION TEXTBOX

            	this.descripcion = descripcion.toUpperCase();

            	// ------------------------------------------------------------------------

            }, controlar() {
            	
            	// ------------------------------------------------------------------------

            	// INICIAR VARIABLES 

            	var retornar = false;

            	// ------------------------------------------------------------------------

            	// VERIFICAR TEXTBOXS
            	
            	if (this.codigo_producto === '') {
            		retornar = true;
            		this.validar_codigo_producto = true;
            	} else {
            		this.validar_codigo_producto = false;
            	}

            	if (this.codigo_interno === '') {
            		retornar = true;
            		this.validar_codigo_interno = true;
            	} else {
            		this.validar_codigo_interno = false;
            	}

            	if (this.codigo_real === '' && this.checked_codigo_real  === true) {
            		retornar = true;
            		this.validar_codigo_real = true;
            	} else {
            		this.validar_codigo_real = false;
            	}

            	if (this.descripcion === '') {
            		this.validar_descripcion = true;
            		retornar = true;
            	} else {
            		this.validar_descripcion = false;
            	}

            	if (this.seleccion_categoria === 'null' || this.seleccion_categoria === '') {
            		retornar = true;
            		this.validar_categoria = true;
            	} else {
            		this.validar_categoria = false;
            	}

            	if (this.seleccion_sub_categoria === 'null' || this.seleccion_sub_categoria === '') {
            		retornar = true;
            		this.validar_sub_categoria = true;
            	} else {
            		this.validar_sub_categoria = false;
            	}

            	if (this.mostrar_color === true && (this.seleccion_color === 'null' || this.seleccion_color === '')) {
            		retornar = true;
            		this.validar_color = true;
            	} else {
            		this.validar_color = false;
            	}

            	if (this.mostrar_tela === true && (this.seleccion_tela === 'null' || this.seleccion_tela === '')) {
            		retornar = true;
            		this.validar_tela = true;
            	} else {
            		this.validar_tela = false;
            	}

            	if (this.mostrar_talle === true && (this.seleccion_talle === 'null' || this.seleccion_talle === '')) {
            		retornar = true;
            		this.validar_talle = true;
            	} else {
            		this.validar_talle = false;
            	}

            	if (this.mostrar_genero === true && (this.seleccion_genero === 'null' || this.seleccion_genero === '')) {
            		retornar = true;
            		this.validar_genero = true;
            	} else {
            		this.validar_genero = false;
            	}

            	if (this.mostrar_marca === true && (this.seleccion_marca === 'null' || this.seleccion_marca === '')) {
            		retornar = true;
            		this.validar_marca = true;
            	} else {
            		this.validar_marca = false;
            	}

            	if (this.seleccion_proveedor === 'null' || this.seleccion_proveedor === '') {
            		retornar = true;
            		this.validar_proveedor = true;
            	} else {
            		this.validar_proveedor = false;
            	}

            	if (this.seleccion_presentacion === '') {
            		retornar = true;
            		this.validar_presentacion = true;
            	} else {
            		this.validar_presentacion = false;
            	}

            	if (this.seleccion_moneda === '') {
            		retornar = true;
            		this.validar_moneda = true;
            	} else {
            		this.validar_moneda = false;
            	}

            	if (this.precio_venta === "0" || this.precio_venta === "0.00") {
            		retornar = true;
            		this.validar_precio_venta = true;
            	} else {
            		this.validar_precio_venta = false;
            	}
            	
            	if (this.precio_mayorista === "0" || this.precio_mayorista === "0.00") {
            		retornar = true;
            		this.validar_precio_mayorista = true;
            	} else {
            		this.validar_precio_mayorista = false;
            	}

            	if (this.precio_costo === "0" || this.precio_costo === "0.00") {
            		retornar = true;
            		this.validar_precio_costo = true;
            	} else {
            		this.validar_precio_costo = false;
            	}

            	// ------------------------------------------------------------------------

            	// RETORNAR SI ALGUNOS DE LOS TEXTBOX NO CUMPLE CON LOS REQUISITOS

            	if (retornar) {
            		return false;
            	}

            	// ------------------------------------------------------------------------

            }, descripcionColor(valor){
            	
            	// ------------------------------------------------------------------------

            	// CARGAR DESCRIPCION COLOR 

            	this.descri_color = valor;

            	// ------------------------------------------------------------------------

            	// GENERAR DESCRIPCION 

            	this.generarDescripcion();

            	// ------------------------------------------------------------------------

            }, descripcionTela(valor){
            	
            	// ------------------------------------------------------------------------

            	// CARGAR DESCRIPCION COLOR 

            	this.descri_tela = valor;

            	// ------------------------------------------------------------------------

            	// GENERAR DESCRIPCION 

            	this.generarDescripcion();

            	// ------------------------------------------------------------------------

            }, descripcionTalle(valor){
            	
            	// ------------------------------------------------------------------------

            	// CARGAR DESCRIPCION COLOR 

            	this.descri_talle = valor;

            	// ------------------------------------------------------------------------

            	// GENERAR DESCRIPCION 

            	this.generarDescripcion();

            	// ------------------------------------------------------------------------

            }, descripcionGenero(valor){
            	
            	// ------------------------------------------------------------------------

            	// CARGAR DESCRIPCION COLOR 

            	this.descri_genero = valor;

            	// ------------------------------------------------------------------------

            	// GENERAR DESCRIPCION 

            	this.generarDescripcion();

            	// ------------------------------------------------------------------------

            }, descripcionMarca(valor){
            	
            	// ------------------------------------------------------------------------

            	// CARGAR DESCRIPCION COLOR 

            	this.descri_marca = valor;

            	// ------------------------------------------------------------------------

            	// GENERAR DESCRIPCION 

            	this.generarDescripcion();

            	// ------------------------------------------------------------------------

            }, descripcionSubCategoria(valor){
            	
            	// ------------------------------------------------------------------------

            	// CARGAR DESCRIPCION COLOR 

            	this.descri_sub_categoria = valor;

            	// ------------------------------------------------------------------------

            	// GENERAR DESCRIPCION 

            	this.generarDescripcion();

            	// ------------------------------------------------------------------------

            }, descripcionUpper(){
            
            	// ------------------------------------------------------------------------

            	// CONVERTIR EN UPPER CASE

            	this.descripcion = this.descripcion.toUpperCase();

            	// ------------------------------------------------------------------------

            }, guardar(){
            	
            	// ------------------------------------------------------------------------

            	// INICIAR VARIABLES 

            	let me = this;

            	// ------------------------------------------------------------------------

            	// CONTROLAR SI CUMPLES LOS TEXTBOX

            	if (this.controlar() === false){
            		return;
            	}

            	// ------------------------------------------------------------------------

            	// REVISAR RUTA IMAGEN 

            	if (this.rutaImagen === '/images/SinImagen.png?343637be705e4033f95789ab8ec70808') {
            		this.rutaImagen === '';
            	}

            	// ------------------------------------------------------------------------

            	// PREPARAR DATOS
            	
            	this.datos = {
            		codigo_producto: this.codigo_producto,
            		codigo_interno: this.codigo_interno,
            		codigo_real: this.codigo_real,
            		descripcion: this.descripcion,
            		categoria: this.seleccion_categoria,
            		subCategoria: this.seleccion_sub_categoria,
            		color: this.seleccion_color,
            		tela: this.seleccion_tela,
            		talle: this.seleccion_talle,
            		genero: this.seleccion_genero,
            		marca: this.seleccion_marca,
            		proveedor: this.seleccion_proveedor,
            		presentacion: this.seleccion_presentacion,
            		iva: this.iva,
            		descuentoMaximo: this.descuento_maximo,
            		moneda: this.seleccion_moneda,
            		precioVenta: this.precio_venta,
            		precioMayorista: this.precio_mayorista,
            		precioVip: this.precio_vip,
            		precioCosto: this.precio_costo,
            		stockMinimo: this.stock_minimo,
            		gondola: this.seleccion_gondola,
            		observacion: this.observacion,
            		imagen: this.rutaImagen,
            		generado: this.generado
            	}

            	// ------------------------------------------------------------------------

            	// GUARDAR PRODUCTO

            	Swal.fire({
				  title: '¿ Guardar ?',
				  text: "Guardar el producto " + me.codigo_producto + " !",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  confirmButtonColor: 'btn btn-success',
				  cancelButtonColor: '#d33',
				  confirmButtonText: 'Si, guardalo!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {

				    return Common.guardarProductoCommon(this.datos).then(data => {

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
						      'Guardado !',
						      'Se ha guardado correctamente el producto !',
						      'success'
					)

				  	// ------------------------------------------------------------------------

				  	// LIMPIAR TEXTBOX 

					me.limpiar();

					// ------------------------------------------------------------------------

				  }
				})

            	// ------------------------------------------------------------------------

            }, limpiar(){

            	// ------------------------------------------------------------------------

            	// GENERAR NUEVO CODIGO 

            	this.obtenerCodigo();

            	// ------------------------------------------------------------------------

            	// INICIAR 

	          	this.codigo_real = '';
	          	this.descripcion = '';
	          	this.seleccion_categoria = 'null';
	          	this.seleccion_sub_categoria = 'null';
	          	this.seleccion_color = 'null';
	          	this.seleccion_tela = 'null';
	          	this.seleccion_talle = 'null';
	          	this.seleccion_genero = 'null';
	          	this.seleccion_marca = 'null';
	          	this.seleccion_proveedor = 'null';
	          	this.seleccion_moneda = 'null';
	          	this.seleccion_proveedor = 'null';
	          	this.seleccion_gondola = 'null';
	          	this.seleccion_presentacion = 1;
	          	this.observacion = '';
	          	this.iva = '10';
	          	this.descuento_maximo = 0;
	          	this.generado = true;
	          	this.rutaImagen = require('./../../../imagenes/SinImagen.png');
	          	this.fileName = 'Imagen';
	          	this.mostrar_precios = false;
	          	this.descripcion_precio = '';
	          	this.precio_venta = '';
	          	this.precio_mayorista = '';
	          	this.precio_vip = '';
	          	this.precio_costo = '';
	          	this.candec_moneda_textbox = 0;
	          	this.stock_minimo = 0;
	          	this.mostrar_color = false;
	          	this.mostrar_tela = false;
	          	this.mostrar_talle = false;
	          	this.mostrar_genero = false;
	          	this.mostrar_marca = false;
	          	this.checked_codigo_real = false;
	          	this.descri_color = '';
	          	this.descri_tela = '';
	          	this.descri_talle = '';
	          	this.descri_genero = '';
	          	this.descri_marca = '';
	          	this.descri_categoria = '';
	          	this.descri_sub_categoria = '';
	          	this.validar_codigo_producto = false;
	          	this.validar_codigo_interno = false;
	          	this.validar_descripcion = false;
	          	this.validar_categoria = false;
	          	this.validar_sub_categoria = false;
	          	this.validar_color = false;
	          	this.validar_tela = false;
	         	this.validar_talle = false;
	          	this.validar_genero = false;
	          	this.validar_marca = false;
	          	this.validar_proveedor = false;
	          	this.validar_presentacion = false;
	          	this.validar_moneda = false;
	          	this.validar_precio_venta = false;
	          	this.validar_precio_mayorista = false;
	          	this.validar_precio_costo = false;
	          	this.validar_codigo_real = false;

	          	// ------------------------------------------------------------------------

            }
      },
        mounted() {

        	// -------------------------------------------------------------------------------------

        	// INICIAR VARIABLES

        	let me = this;
        	me.seleccion_moneda = String(me.monedaCodigo);

        	// -------------------------------------------------------------------------------------

        	// GENERAR CODIGOS

        	me.obtenerCodigo();

        	// -------------------------------------------------------------------------------------

        	// NAV LINK 

           	$(".nav .nav-link").on("click", function(){
			   $(".nav").find(".active").removeClass("active");
			   $(this).addClass("active");
			   me.nav = $(this).attr('value');
			});

			// -------------------------------------------------------------------------------------
        }
    }
</script>
