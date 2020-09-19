<template>
	<div class="container-fluid">
		
		<div class="mt-3 mb-3" v-if="$can('producto.crear')">
			
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

				    	<div class="col-md-10">
							
							<form class="form-inline">

					    		<!-- ------------------------------------------------------------------ -->

					    		<!-- HABILITAR CODIGO REAL -->
								
						   		<div class="my-1">
									  <div class="custom-control custom-switch mr-sm-2">
									   <input type="checkbox" class="custom-control-input" id="customControlAutosizing" v-model="checked_codigo_real">
									   <label class="custom-control-label" for="customControlAutosizing">Código Real</label>
									 </div>
								</div>

								<!-- ------------------------------------------------------------------ -->
								
								<!-- VENCIMIENTO -->

								<div class="my-4">
									  <div class="custom-control custom-switch mr-sm-2">
									   <input type="checkbox" class="custom-control-input" id="switchVencimiento" v-model="checked_vencimiento">
									   <label class="custom-control-label" for="switchVencimiento">Vencimiento</label>
									 </div>
								</div>
								
								<!-- ------------------------------------------------------------------ -->

								<div class="my-4">
									  <div class="custom-control custom-switch mr-sm-2">
									   <input type="checkbox" class="custom-control-input" id="switchAutoDescripcion" v-model="checked_auto_descripcion" v-on:change="onChangeAutoDescripcion">
									   <label class="custom-control-label" for="switchAutoDescripcion">Auto Descripción</label>
									 </div>
								</div>
								
								<!-- ------------------------------------------------------------------ -->

								<div class="my-4">
									  <div class="custom-control custom-switch mr-sm-2">
									   <input type="checkbox" class="custom-control-input" id="switchOnline" v-model="checked_online" v-on:change="onDetalleWeb">
									   <label class="custom-control-label" for="switchOnline">Online</label>
									 </div>
								</div>

							</form>

							<!-- ------------------------------------------------------------------ -->

				    	</div>	
				    	
				    	<!-- ------------------------------------------------------------------ -->

				    	<!-- MOSTRAR DETALLE -->

				    	<div class="col-md-2">
				    		<div class="my-4 float-right" v-if="mostrar.detalle">
								<button type="button" class="btn btn-sm btn-outline-primary" v-on:click="detalle()">Ver Más</button>
							</div>
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
					    				<codigo-producto :tabIndexPadre=0 v-model="codigo_producto" v-bind:candec="candec, monedaCodigo, tab_unica, shadow, checked_codigo_real, validar_codigo_producto" @codigo_producto="codigoProducto"></codigo-producto>
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- CODIGO INTERNO -->

					    			<div class="col-6">
					    				<label>Código Interno</label>
					    				<input tabindexPadre=1 type="text" id="codigo_interno" class="form-control form-control-sm" v-model="codigo_interno" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_codigo_interno }"  v-on:change="() => generado = false">
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
					    				<input type="text" id="descripcion_producto" class="form-control form-control-sm" v-model="descripcion" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_descripcion }" v-on:blur="descripcionUpper">
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
					    				<selected-categoria :tabIndexPadre=2 ref="componente_categoria" v-model="seleccion_categoria" v-bind:shadow="shadow, validar_categoria" @opciones="opcionesCategoria"></selected-categoria>
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- SUB CATEGORIA -->

					    			<div class="col-md-6">

						    			<div class="text-left"> 
							                <label for="validationTooltip01">Sub Categoria</label>
							            </div>

							            <select :tabindex=3 class="custom-select custom-select-sm" v-on:change="llamarSubCategoria($event.target.value, subCategorias)" v-bind:shadow="shadow, validar_sub_categoria" :disabled="deshabilitar.subCategoria">
							                    <option :value="null">0 - Seleccionar</option>
							                    <option v-for="subCategoria in subCategorias" :selected="subCategoria.CODIGO === parseInt(seleccion_sub_categoria)" :value="subCategoria.CODIGO">{{ subCategoria.CODIGO }} - {{ subCategoria.DESCRIPCION }}</option>
							            </select>

						            </div>

					    			<!-- <div class="col-md-6">
					    				<selected-sub-categoria :tabIndexPadre=3 v-model="seleccion_sub_categoria" v-bind:shadow="shadow, validar_sub_categoria" @descripcion_sub_categoria="descripcionSubCategoria" :deshabilitar="deshabilitar.subCategoria" :categoria="seleccion_categoria"></selected-sub-categoria>
					    			</div>	 -->

					    			<!-- ------------------------------------------------------------------ -->

					    			

					    		</div>

					    		<!-- ------------------------------------------------------------------ -->

					    		<div class="form-row mt-3">

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- SUB CATEGORIA DETALE -->

					    			<div class="col-md-12">
					    				<selected-sub-categoria-detalle :tabIndexPadre=4 v-model="seleccion_sub_categoria_detalle" v-bind:shadow="shadow, validar_sub_categoria" @descripcion_sub_categoria_detalle="descripcionSubCategoriaDetalle" :deshabilitar="false"></selected-sub-categoria-detalle>
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
					    				<select-color :tabIndexPadre=5 v-model="seleccion_color" v-bind:shadow="shadow, validar_color" @cambiar_codigo="asignar" @descripcion_color="descripcionColor"></select-color>
					    			</div>	

					    			<!-- ------------------------------------------------------------------ -->

				    			</div>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- FORM ROW TELA -->

					    		<div class="form-row mt-3" v-if="mostrar_tela">

						    		<!-- ------------------------------------------------------------------ -->

					    			<!-- TELA -->

					    			<div class="col-md-12">
					    				<select-tela :tabIndexPadre=6 v-model="seleccion_tela" v-bind:shadow="shadow, validar_tela" @descripcion_tela="descripcionTela"></select-tela>
					    			</div>	

					    			<!-- ------------------------------------------------------------------ -->

				    			</div>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- FORM ROW TALLE -->

					    		<div class="form-row mt-3" v-if="mostrar_talle">

						    		<!-- ------------------------------------------------------------------ -->

					    			<!-- TALLE -->

					    			<div class="col-md-12">
					    				<select-talle :tabIndexPadre=7 v-model="seleccion_talle" v-bind:shadow="shadow, validar_talle" @cambiar_codigo="asignar" @descripcion_talle="descripcionTalle"></select-talle>
					    			</div>	

					    			<!-- ------------------------------------------------------------------ -->

				    			</div>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- FORM ROW GENERO -->

					    		<div class="form-row mt-3" v-if="mostrar_genero">

						    		<!-- ------------------------------------------------------------------ -->

					    			<!-- GENERO -->

					    			<div class="col-md-12">
					    				<select-genero :tabIndexPadre=8 v-model="seleccion_genero" v-bind:shadow="shadow, validar_genero" @descripcion_genero="descripcionGenero"></select-genero>
					    			</div>	

					    			<!-- ------------------------------------------------------------------ -->

				    			</div>


				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- FORM ROW MARCA -->

					    		<div class="form-row mt-3">

						    		<!-- ------------------------------------------------------------------ -->

					    			<!-- MARCA -->

					    			<label for="validationTooltip01">Marca</label>
						            <select :tabindex=9 class="custom-select custom-select-sm" v-on:change="seleccionarMarca($event.target.value)" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_marca }"  :disabled="!mostrar_marca">
						                    <option :value="null">0 - Seleccionar</option>
						                    <option v-for="marca in marcas" :selected="marca.CODIGO === parseInt(seleccion_marca)" :value="marca.CODIGO">{{ marca.CODIGO }} - {{ marca.DESCRIPCION }}</option>
						            </select>
					    			<!-- <div class="col-md-12">
					    				<search-marca :tabIndexPadre=9 v-model="seleccion_marca" ref="componente_select_marca" v-bind:shadow="shadow, validar_marca" @marcaDescripcion="descripcionMarca" :categoria="seleccion_categoria" :deshabilitar="!mostrar_marca"></search-marca>
					    			</div> -->	

					    			<!-- ------------------------------------------------------------------ -->

				    			</div>


				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- SALTO DE LINEA -->
				    			
				    			<hr>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- FORM ROW TEMPORADA -->

					    		<div class="form-row mt-3" v-if="mostrar.temporada">

						    		<!-- ------------------------------------------------------------------ -->

					    			<!-- TEMPORADA -->

					    			<label>Temporada</label>
					    			<div class="col-md-12">
					    				<select :tabindex=10 class="custom-select custom-select-sm" v-model="seleccion.temporada" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar.temporada }">
					    					<option value="0">SELECCIONAR</option>
						                    <option value="1">1 - PRIMAVERA</option>
						                    <option value="2">2 - VERANO</option>
						                    <option value="3">3 - OTOÑO</option>
						                    <option value="4">4 - INVIERNO</option>
						            	</select>
					    			</div>	

					    			<!-- ------------------------------------------------------------------ -->

				    			</div>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- FORM ROW PROVEEDOR -->

					    		<div class="form-row mt-3">

						    		<!-- ------------------------------------------------------------------ -->

					    			<!-- PROVEEDOR -->

					    			<div class="col-md-12">
					    				<select-proveedor :tabIndexPadre=11 v-model="seleccion_proveedor" v-bind:shadow="shadow, validar_proveedor"></select-proveedor>
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
					    				<select :tabindex=12 class="custom-select custom-select-sm" v-model="seleccion_presentacion" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_presentacion }">
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

				    			</div>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- SALTO DE LINEA -->

				    			<hr>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- FORM CHECKED DETALLES WEB -->

				    			<div class="form-row mt-3">

						    		<div class="my-4">
										  <div class="custom-control custom-switch mr-sm-2">
										   <input type="checkbox" class="custom-control-input" id="switchDetalleWeb" v-model="checked_detalleWeb" :disabled="checked_online">
										   <label class="custom-control-label" for="switchDetalleWeb">Detalle Web</label>
										 </div>
									</div>

									<!-- ------------------------------------------------------------------ -->

					    		</div>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- FORM ROW NOMBRE WEB -->

				    			<div class="form-row mt-3" v-if="checked_detalleWeb">

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- NOMBRE -->

					    			<div class="col-md-12">
					    				<div class="text-left">
					    					<label>Nombre Específico Web</label>
					    				</div>
					    				<input :tabindex=13 type="text" class="form-control form-control-sm" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_nombreWeb }" v-model="web.nombre">
					    			</div>
					    			<hr>
					    			<!-- ------------------------------------------------------------------ -->

					    		</div>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- FORM ROW DESCRIPCION WEB -->

				    			<div class="form-row mt-3" v-if="checked_detalleWeb">

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- DESCRIPCION -->

					    			<div class="col-md-12">
					    				<div class="text-left">
					    					<label>Descripcion Breve Web</label>
					    				</div>
					    				<textarea :tabindex=14 id="descripcionweb" class="form-control" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_descripcionWeb }" v-model="web.descripcion" rows="3"></textarea>
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    		</div>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- FORM ROW PESO WEB -->

				    			<div class="form-row mt-3" v-if="checked_detalleWeb">

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- PESO -->

					    			<div class="col-md-12">
					    				<div class="text-left">
					    					<label>Peso Web</label>
					    				</div>
					    				<input :tabindex=15 type="text" class="form-control form-control-sm" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_pesoWeb }" v-model="web.peso" v-on:blur="formatoPeso">
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    		</div>

					    		<!-- ------------------------------------------------------------------ -->

				    			<!-- FORM CHECKED DIMENSIONES -->

				    			<div class="form-row mt-3" v-if="checked_detalleWeb">

						    		<div class="my-4">
										  <div class="custom-control custom-switch mr-sm-2">
										   <input type="checkbox" class="custom-control-input" id="switchDimension" v-model="checked_dimension">
										   <label class="custom-control-label" for="switchDimension">Agregar Dimensiones</label>
										 </div>
									</div>

									<!-- ------------------------------------------------------------------ -->

					    		</div>

				    			<!-- ------------------------------------------------------------------ -->

				    			<!-- FORM ROW LONGITUD WEB -->
				    			
				    			<div class="form-row mt-3" v-if="checked_dimension && checked_detalleWeb">

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- LONGITUD -->

					    			<div class="col-md-12">
					    				<div class="text-left">
					    					<label>Longitud Web</label>
					    				</div>
					    				<input :tabindex=16 type="text" class="form-control form-control-sm" v-bind:class="{ 'shadow-sm': shadow }" v-model="web.longitud" v-on:blur="formatoLongitud">
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    		</div>
					    		
				    			<!-- ------------------------------------------------------------------ -->
				    			
				    			<!-- FORM ROW ANCHO WEB -->

				    			<div class="form-row mt-3" v-if="checked_dimension && checked_detalleWeb">

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- ANCHO -->

					    			<div class="col-md-12">
					    				<div class="text-left">
					    					<label>Anchura Web</label>
					    				</div>
					    				<input :tabindex=17 type="text" class="form-control form-control-sm" v-bind:class="{ 'shadow-sm': shadow }" v-model="web.ancho" v-on:blur="formatoAncho">
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    		</div>
					    		
				    			<!-- ------------------------------------------------------------------ -->
				    			
				    			<!-- FORM ROW ALTURA WEB -->				    			

				    			<div class="form-row mt-3" v-if="checked_dimension && checked_detalleWeb">

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- ALTURA -->

					    			<div class="col-md-12">
					    				<div class="text-left">
					    					<label>Altura Web</label>
					    				</div>
					    				<input :tabindex=18 type="text" class="form-control form-control-sm" v-bind:class="{ 'shadow-sm': shadow }" v-model="web.altura" v-on:blur="formatoAltura">
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    		</div>
					    		
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
						    				<input :tabindex=19 type="text" class="form-control form-control-sm" id="inlineFormInputGroupUsername" v-model="iva" v-on:blur="formatoIVA"  >
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
					    					<input :tabindex=20 type="text" ref="prueba" class="form-control form-control-sm" v-model="descuento_maximo" v-on:blur="formatoDescuento">
					    					<div class="input-group-append">
									          <div class="input-group-text">%</div>
									        </div>
									    </div> 
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- MONEDA -->

					    			<div class="col-md-4">
					    				<select-moneda :tabIndexPadre=21 v-model="seleccion_moneda" ref="moneda" v-bind:shadow="shadow, validar_moneda" @descripcion_moneda="llamarPrecios" @cantidad_decimales="cantidad_decimal"></select-moneda>
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
					    					<input :tabindex=22 type="text" v-bind:class="{ 'is-invalid': validar_precio_venta }" class="form-control form-control-sm" v-model="precio_venta" v-on:blur="formatoPrecioVenta">
					    				</div>
					    			</div>

					    			<!-- ------O------------------------------------------------------------ -->

					    			<!-- PRECIO MAYORISTA -->

					    			<div class="col-md-6">
					    				<label>Precio Mayorista</label>
					    				<div class="input-group input-group-sm mb-3" v-bind:class="{ 'shadow-sm': shadow }">
					    					<div class="input-group-prepend">
											    <span class="input-group-text" id="inputGroup-sizing-sm">{{descripcion_precio}}</span>
											</div>
					    					<input :tabindex=23 type="text" v-bind:class="{ 'is-invalid': validar_precio_mayorista }" class="form-control form-control-sm" v-model="precio_mayorista" v-on:blur="formatoPrecioMayorista">
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
					    					<input :tabindex=24 type="text" class="form-control form-control-sm" v-model="precio_vip" v-on:blur="formatoPrecioVIP">
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
					    					<input :tabindex=25 type="text" v-bind:class="{ 'is-invalid': validar_precio_costo }" class="form-control form-control-sm" v-model="precio_costo" v-on:blur="formatoPrecioCosto">
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
					    				<input :tabindex=26 type="text" class="form-control form-control-sm" v-bind:class="{ 'shadow-sm': shadow }" v-model="stock_minimo" v-on:blur="formatoStockMinimo">
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    		</div>

					    		<!-- ------------------------------------------------------------------ -->

					    		<!--  PERIODO -->
					    		
					    		<div class="form-row mt-3">

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- PERIODO -->

					    			<div class="col-md-12">
					    				<div class="text-left">
					    					<label>Periodo</label>
					    				</div>
					    				<select :tabindex=27 class="custom-select custom-select-sm" v-model="seleccion.periodo" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar.periodo }">
					    					<option value="0">SELECCIONAR</option>
						                    <option value="1">1 - UN MES</option>
						                    <option value="2">2 - DOS MESES</option>
						                    <option value="3">3 - TRES MESES</option>
						                    <option value="4">4 - CUATROS MESES</option>
						                    <option value="5">5 - CINCO MESES</option>
						                    <option value="6">6 - SEIS MESES</option>
						            	</select>
					    			</div>

					    			<!-- ------------------------------------------------------------------ -->

					    		</div>

					    		<!-- ------------------------------------------------------------------ -->

					    		<!-- GONDOLA -->
					    		
					    		<div class="form-row mt-3">

					    			<!-- ------------------------------------------------------------------ -->

					    			<!-- GONDOLA -->

					    			<div class="col-md-12">
					    				<select-gondola :tabIndexPadre=28 v-model="seleccion_gondola" v-bind:selecciones="seleccion_gondola_modificar" v-bind:shadow="shadow"></select-gondola>
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
						  				<textarea :tabindex=29 id="observacion" class="form-control" v-bind:class="{ 'shadow-sm': shadow }"    v-model="observacion" rows="3"></textarea>
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
					    				<form id="myAwesomeForm">
					    				<div class="card mb-3" v-bind:class="{ 'shadow-sm': shadow }">
										  <div class="row no-gutters">
										    <div class="col-md-4">
										      <img  :src="rutaImagen" class="card-img" alt="..." id="myAwesomeForm">
										    </div>
										    <div class="col-md-8">
										      <div class="card-body">
										        <h5 class="card-title">{{fileName}}</h5>
										    	<p class="card-text">Selecione por favor la imagen.</p>
										    	<div class="custom-file">
												  <input :tabindex=24 type="file" class="custom-file-input" id="customFile" v-on:change="cambiarImagen($event.target.files[0])" lang="es" >
												  <label class="custom-file-label" for="customFile">Elegir Archivo</label>
												</div>
										      </div>
										    </div>
										  </div>
										</div>
									</form>
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
				    					<button class="btn btn-secondary" v-on:click="limpiar_nuevo()">Nuevo</button>
				    				</div>

				    				<!-- ------------------------------------------------------------------ -->	

				    				<!-- GUARDAR, EDITAR -->

				    				<div class="form-group">
				    					<button tabindex=30 v-on:click="guardar" v-bind:class="{ 'btn btn-primary': estado_boton.boton_primary, 'btn btn-warning': estado_boton.boton_warning }" class="btn btn-primary"><font-awesome-icon icon="save" /> {{estado_boton.boton}} </button>
				    				</div>
				    				
				    				<!-- ------------------------------------------------------------------ -->	

				    			</div>	
				    			
				  

				    		<!-- ------------------------------------------------------------------ -->	

				    	</div>
				    		
				    	<!-- ------------------------------------------------------------------ -->

				    </div>	

			    <!-- ------------------------------------------------------------------------------------- -->
				</vs-tab>

			</vs-tabs>	

			<!-- ------------------------------------------------------------------------------------- -->

		</div>

		<div v-else>
			<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div>

		<!-- ------------------------------------------------------------------------ -->

		<!-- MODAL DETALLE PRODUCTO -->

		<producto-detalle ref="detalle_producto" :codigo="codigo_producto"></producto-detalle>

		<!-- ------------------------------------------------------------------------ -->

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
          seleccion_categoria_marca: 'null',
          seleccion_sub_categoria_detalle: 'null',
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
          seleccion_gondola_modificar: [{}],
          seleccion_presentacion: 'UNIDADES',
          seleccion: {
          	temporada: '0',
          	periodo: '0'
          },
          observacion: '',
          iva: '10',
          descuento_maximo: 0,
          generado: true,
          rutaImagen: require('./../../../imagenes/SinImagen.png'),
          fileName: 'Imagen',
          shadow: true,
          mostrar_precios: true,
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
          mostrar: {
          	temporada: false,
          	detalle: false
          },
          checked_codigo_real: false,
          checked_vencimiento: false,
          checked_auto_descripcion: true,
          checked_detalleWeb: false,
          checked_dimension: false,
          checked_online: false,
          descri_color: '',
          descri_tela: '',
          descri_talle: '',
          descri_genero: '',
          descri_marca: '',
          descri_categoria: '',
          descri_sub_categoria: '',
          descri_sub_categoria_detalle: '',
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
          validar_nombreWeb: false,
          validar_descripcionWeb: false,
          validar_pesoWeb: false,
          validar: {
          	temporada: false,
          	periodo: false
          },
          mostrar_error: false,
          mensaje: '',
          estado_boton: {
          		boton: 'Guardar',
          		boton_primary: true,
          		boton_warning: false,
          		mostrar_nuevo: false,
          },
          validar: {
          	periodo: false
          },
          deshabilitar: {
          	subCategoria: true
          },
          subCategorias: '',
          datos: [],
          cb: -2,
          marcas: [],
          web: {
          	nombre: '',
          	descripcion: '',
          	peso: 0,
          	longitud: 0,
          	ancho: 0,
          	altura: 0
          }
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
                               
            		if (data.response === false && data.existe===true) {
            			  Swal.fire({
						  title: '¿Desea Importar el producto?',
						  text: 'Este producto ya existe en otra sucursal!',
						  type: 'warning',
						  showLoaderOnConfirm: true,
						  showCancelButton: true,
						  confirmButtonColor: 'btn btn-success',
						  cancelButtonColor: '#d33',
						  confirmButtonText: 'Si, importalo!',
						  cancelButtonText: 'Cancelar',
						  preConfirm: () => {

						    return Common.importarProductoCommon(this.codigo_producto).then(datos => {

						    	// ------------------------------------------------------------------------
 
						    	// REVISAR SI HAY DATOS 

						    	if (!datos.response === true) {
						          throw new Error(datos.statusText);
						        } else{
						        	this.codigoProducto(this.codigo_producto);
						        	
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
								      'Se ha importado correctamente el producto !',
								      'success'
							)

						  	// ------------------------------------------------------------------------

						  	// LIMPIAR TEXTBOX 

						

							// ------------------------------------------------------------------------

						  }
						})

						return;
            		}else if(data.response===false){
            			//this.limpiar();
            				return;
  
            			
            		}

            		// ------------------------------------------------------------------------
                  
            		// CARGAR DATOS DEL PRODUCTO 
                    
            		this.codigo_producto = data.producto.CODIGO;
                    this.codigo_interno = data.producto.CODIGO_INTERNO; 
                    this.descripcion = data.producto.DESCRIPCION;
                    this.checked_auto_descripcion = data.producto.AUTODESCRIPCION; 
                    this.checked_vencimiento = data.producto.VENCIMIENTO,
                    this.iva = data.producto.IVA; 
                    this.seleccion_categoria = data.producto.LINEA.toString();
                    this.seleccion_sub_categoria = data.producto.SUBLINEA.toString();
                    this.seleccion_sub_categoria_detalle = data.producto.SUBLINEADET.toString();
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
                    this.observacion = data.producto.OBSERVACION;
                    this.seleccion_moneda = data.producto.MONEDA.toString();
                    this.seleccion_gondola_modificar = data.producto.GONDOLAS;
                    this.seleccion_proveedor = data.producto.PROVEEDOR.toString();
                    this.seleccion.temporada = data.producto.TEMPORADA;
                    this.seleccion.periodo = data.producto.PERIODO;
                    this.mostrar.detalle = true;


                    // FILTRAR LOS DATOS DE DETALLE WEB

                    if(data.online !== 0){

	                    this.checked_detalleWeb = true;
	                    this.web.nombre = data.online.NOMBRE;
		                this.web.descripcion = data.online.DESCRIPCION;
		                this.web.peso = data.online.PESO;
		                this.web.longitud = data.online.LONGITUD;
		                this.web.ancho = data.online.ANCHURA;
		                this.web.altura = data.online.ALTURA;

                    }else{

                    	this.checked_detalleWeb = false;
                    }
                    
                    // ------------------------------------------------------------------------

                    // CARGAR CODIGO REAL 

                    if (data.producto.CODIGO_REAL !== '' && data.producto.CODIGO_REAL !== null && data.producto.CODIGO_REAL !== undefined && data.producto.CODIGO_REAL !== '0') {
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

            		// ACTIVAR ONLINE
					
					if(data.producto.ONLINE === 1){

                    	this.checked_online = true;
                    	this.checked_detalleWeb = true;

                    }else{

                    	this.checked_online = false;
                    	this.checked_detalleWeb = false;
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
           			this.mensaje = err+' - ¡ Revise la conexión y recargue la página !';
           		});

           		//$("#codigo_interno").focus();


           		// ------------------------------------------------------------------------

            }, convertDataURIToBinary(dataURI) {
            		
	              var BASE64_MARKER = ';base64,';
				  var base64Index = dataURI.indexOf(BASE64_MARKER) + BASE64_MARKER.length;
				  var base64 = dataURI.substring(base64Index);
				  var raw = window.atob(base64);
				  var rawLength = raw.length;
				  var array = new Uint8Array(new ArrayBuffer(rawLength));
				  for(i = 0; i < rawLength; i++) {
				    array[i] = raw.charCodeAt(i);
				  }
				  return array;
			}, b64toBlob(b64Data, contentType, sliceSize) {
                contentType = contentType || '';
                sliceSize = sliceSize || 512;

                var byteCharacters = atob(b64Data);
                var byteArrays = [];

                for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
                    var slice = byteCharacters.slice(offset, offset + sliceSize);

                    var byteNumbers = new Array(slice.length);
                    for (var i = 0; i < slice.length; i++) {
                        byteNumbers[i] = slice.charCodeAt(i);
                    }

                    var byteArray = new Uint8Array(byteNumbers);

                    byteArrays.push(byteArray);
                }

              var blob = new Blob(byteArrays, {type: contentType});
              return blob;
            },
            cambiarImagen(f){

            	let me = this;

            	if (f) {
					if ( /(jpe?g|png|gif)$/i.test(f.type) ) {
						var r = new FileReader();
						r.onload = function(e) { 
							var base64Img = e.target.result;
							// var binaryImg = me.convertDataURIToBinary(base64Img);
							 //var blob = new Blob([binaryImg], {type: f.type});
							// blobURL = window.URL.createObjectURL(blob);
							me.fileName = f.name;
							me.rutaImagen = base64Img;

							// -------------------------------------------------------------------------------------

							
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

            },

            formatoPeso(){

                // ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;

	            // ------------------------------------------------------------------------

	            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A PRECIO

	            me.web.peso = Common.darFormatoCommon(me.web.peso, 2);

	            // ------------------------------------------------------------------------
	        },

	        formatoLongitud(){

                // ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;

	            // ------------------------------------------------------------------------

	            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A PRECIO

	            me.web.longitud = Common.darFormatoCommon(me.web.longitud, 2);

	            // ------------------------------------------------------------------------
	        },

	        formatoAncho(){

                // ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;

	            // ------------------------------------------------------------------------

	            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A PRECIO

	            me.web.ancho = Common.darFormatoCommon(me.web.ancho, 2);

	            // ------------------------------------------------------------------------
	        },

	        formatoAltura(){

                // ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;

	            // ------------------------------------------------------------------------

	            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A PRECIO

	            me.web.altura = Common.darFormatoCommon(me.web.altura, 2);

	            // ------------------------------------------------------------------------
	        },

            opcionesCategoria(valor){

            	// ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;

	            // ------------------------------------------------------------------------

	            if (valor.length === 0) {
	            	me.deshabilitar.subCategoria = true;
	            	return;
	            }

	            me.deshabilitar.subCategoria = false;

	            // ------------------------------------------------------------------------

	            // CONVERTIR VALOR 1 Y O EN BOOLEANO

	            me.mostrar_color = !!+valor[0].ATRIBCOLOR;
	            me.mostrar_tela = !!+valor[0].ATRIBTELA;
	            me.mostrar_talle = !!+valor[0].ATRIBTALLE;
	            me.mostrar_genero = !!+valor[0].ATRIBGENERO;
	            me.mostrar_marca = !!+valor[0].ATRIBMARCA;
	            me.mostrar.temporada = !!+valor[0].ATRIBTEMPORADA;

	            // ------------------------------------------------------------------------

	            // CARGAR DESCRIPCION

	            me.descri_categoria = valor[0].DESCRIPCION;

	            // ------------------------------------------------------------------------

	            // SELECCION CATEGORIA MARCA 

	            me.obtenerMarca(me.seleccion_categoria);
	            me.obtenerSubCategorias(me.seleccion_categoria);

	            //me.$refs.componente_select_marca.obtenerMarca(me.seleccion_categoria);

	            // ------------------------------------------------------------------------

	            // GENERAR DESCRIPCION 

	            this.generarDescripcion();

	            // ------------------------------------------------------------------------

            }, obtenerSubCategorias(categoria){

      				// ------------------------------------------------------------------------

      				// LLAMAR LAS SUB CATEGORIAS

      				Common.obtenerSubCategoriaCommon(categoria).then(data => {
      				  this.subCategorias = data;
      				  this.llamarSubCategoria(this.seleccion_sub_categoria, data);
      				});
      				

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

            },limpiar_nuevo(){
            		this.limpiar();
            		this.obtenerCodigo();
            },

             obtenerCodigo(){

            	// ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;

            	// ------------------------------------------------------------------------

            	// OBTENER CODIGO GENARADO 

            	Common.generarCodigoCommon().then(data => {
           			me.codigo_producto = data;
           		}).catch((err) => {
           			this.mostrar_error = true;
           			this.mensaje = err+' - ¡ Revise la conexión y recargue la página !';
           		});

           		// ------------------------------------------------------------------------

            	// OBTENER CODIGO INTERNO GENARADO 

            	Common.generarCodigoInternoCommon().then(data => {
           			me.codigo_interno = data;
           		}).catch((err) => {
           			this.mostrar_error = true;
           			this.mensaje = err+' - ¡ Revise la conexión y recargue la página !';
           		});

           		// ------------------------------------------------------------------------

            }, generarDescripcion() {

            	// ------------------------------------------------------------------------

            	// REVISAR SI ESTA ACTIVADO AUTO DESCRIPCION 

            	if (this.checked_auto_descripcion === false) {
            		return;
            	}

            	// ------------------------------------------------------------------------

            	// INICIAR VARIABLES 

            	var descripcion = '';
            	var ultimosDigitos = '';

            	// ------------------------------------------------------------------------

            	// MARCA DESCRIPCION 

            	descripcion = (descripcion.concat(this.mostrar_marca ? this.descri_marca : '')).substring(0, 3);

            	// ------------------------------------------------------------------------

            	// CATEGORIA DESCRIPCION 

       			descripcion = descripcion.concat(' '+this.descri_categoria.substring(0, 3));

       			// ------------------------------------------------------------------------

            	// SUB CATEGORIA DESCRIPCION 

       			descripcion = descripcion.concat(' '+this.descri_sub_categoria.substring(0, 3));

       			// ------------------------------------------------------------------------

            	// GENERO DESCRIPCION 

       			descripcion = descripcion.concat(this.mostrar_genero ? ' '+this.descri_genero.substring(0, 3) : '');

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

            	// SUB CATEGORIA DETALLE DESCRIPCION 

       			descripcion = descripcion.concat(' '+this.descri_sub_categoria_detalle);

       			// ------------------------------------------------------------------------

       			// ULTIMOS TRES DIGITOS 

       			descripcion = descripcion.concat(' '+this.codigo_producto.substring(this.codigo_producto.length - 4));

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
            		alert(this.seleccion_sub_categoria);
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
            	
            	if (this.mostrar.temporada === true && this.seleccion.temporada === '0') {
            		retornar = true;
            		this.validar.temporada = true;
            	} else {
            		this.validar.temporada = false;
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

            	if (this.seleccion.periodo === "0") {
            		retornar = true;
            		this.validar.periodo = true;
            	} else {
            		this.validar.periodo = false;
            	}

            	if (this.web.nombre === '' && this.checked_detalleWeb === true) {
            		retornar = true;
            		this.validar_nombreWeb = true;
            	} else {
            		this.validar_nombreWeb = false;
            	}

            	if (this.web.descripcion === '' && this.checked_detalleWeb === true) {
            		retornar = true;
            		this.validar_descripcionWeb = true;
            	} else {
            		this.validar_descripcionWeb = false;
            	}

            	if (this.web.peso === '0' || this.web.peso === "0.00" && this.checked_detalleWeb === true) {
            		retornar = true;
            		this.validar_pesoWeb = true;
            	} else {
            		this.validar_pesoWeb = false;
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

            }, descripcionSubCategoriaDetalle(valor){
            	
            	// ------------------------------------------------------------------------

            	// CARGAR DESCRIPCION COLOR 

            	this.descri_sub_categoria_detalle = valor;



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
            	var textRegistro = '';
            	var textTitulo = '';
            	var online='';

            	// ------------------------------------------------------------------------

            	// CONTROLAR SI CUMPLES LOS TEXTBOX

            	if (this.controlar() === false){
            		return;
            	}

            	// ------------------------------------------------------------------------

            	// REVISAR RUTA IMAGEN 

            	if (this.rutaImagen.includes('SinImagen') === true) {
            		me.rutaImagen = '';
            	}

            	// ------------------------------------------------------------------------

            	if (this.checked_online===true){

            		online = 1;
            	}else{
            		online = 0;
            	} 

            	// PREPARAR DATOS
            	
            	this.datos = {
            		codigo_producto: this.codigo_producto,
            		codigo_interno: this.codigo_interno,
            		codigo_real: this.codigo_real,
            		descripcion: this.descripcion,
            		categoria: this.seleccion_categoria,
            		subCategoria: this.seleccion_sub_categoria,
            		subCategoriaDet: this.seleccion_sub_categoria_detalle,
            		color: this.seleccion_color,
            		tela: this.seleccion_tela,
            		talle: this.seleccion_talle,
            		genero: this.seleccion_genero,
            		marca: this.seleccion_marca,
            		temporada: this.seleccion.temporada,
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
            		generado: this.generado,
            		modificar: this.estado_boton.boton_warning,
            		vencimiento: this.checked_vencimiento,
            		periodo: this.seleccion.periodo,
            		online: online,
            		nombreWeb: this.web.nombre,
            		descripcionWeb: this.web.descripcion,
            		pesoWeb: this.web.peso,
            		longitudWeb: this.web.longitud,
            		anchoWeb: this.web.ancho,
            		alturaWeb: this.web.altura,
            		categoria1Web: this.descri_categoria,
            		categoria2Web: this.descri_sub_categoria,
            		habilitadoWeb: this.checked_online,
            		marcaWeb: this.descri_marca,
            		detalleWeb: this.checked_detalleWeb
            	}

            	// ------------------------------------------------------------------------

            	// CAMBIAR TEXTO DE ACUERDO A MODIFICAR O GUARDAR 

            	textRegistro = this.estado_boton.boton_warning ? "Modificar el producto " + me.codigo_producto + " !" : "Guardar el producto " + me.codigo_producto + " !";
            	textTitulo = this.estado_boton.boton_warning ? "Modificar" : "Guardar";

            	// ------------------------------------------------------------------------

            	// GUARDAR PRODUCTO

            	Swal.fire({
				  title: '¿ '+textTitulo+' ?',
				  text: textRegistro,
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
	          	this.seleccion_moneda = String(this.monedaCodigo);
	          	this.seleccion_proveedor = 'null';
	          	this.seleccion_gondola = 'null';
	          	this.seleccion_gondola_modificar = '';
	          	this.seleccion_presentacion = 'UNIDADES';
	          	this.seleccion.temporada = '0';
	          	this.seleccion.periodo = '0';
	          	this.observacion = '';
	          	this.iva = '10';
	          	this.descuento_maximo = 0;
	          	this.generado = true;
	          	this.rutaImagen = require('./../../../imagenes/SinImagen.png');
	          	this.fileName = 'Imagen';
	          	this.mostrar_precios = false;
	          	//this.descripcion_precio = '';
	          	this.precio_venta = '';
	          	this.precio_mayorista = '';
	          	this.precio_vip = '';
	          	this.precio_costo = '';
	          	//this.candec_moneda_textbox = 0;
	          	this.stock_minimo = 0;
	          	this.mostrar_color = false;
	          	this.mostrar_tela = false;
	          	this.mostrar_talle = false;
	          	this.mostrar_genero = false;
	          	this.mostrar_marca = false;
	          	this.checked_codigo_real = false;
	          	this.checked_vencimiento = false;
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
	          	this.validar.temporada = false;
	          	this.validar.periodo = false;
	          	this.checked_auto_descripcion = true;
	          	this.mostrar.detalle = false;
	          	this.checked_online = false;
	          	this.checked_dimension = false;
	          	this.checked_detalleWeb = false;
	          	this.web.nombre = '';
	          	this.web.descripcion = '';
	          	this.web.peso = 0;
	          	this.web.longitud = 0;
	          	this.web.ancho = 0;
	          	this.web.altura = 0;

	          	// ------------------------------------------------------------------------

	          	// MOSTRAR PRECIOS 

	          	this.llamarPrecios(this.descripcion_precio);

	          	// ------------------------------------------------------------------------

	          	// CAMBIAR BOTON 

            	this.estado_boton.boton = 'Guardar';
            	this.estado_boton.boton_primary = true;
            	this.estado_boton.boton_warning = false;
            	this.estado_boton.mostrar_nuevo = false;

           		// ------------------------------------------------------------------------

            }, onChangeAutoDescripcion(){

            	// ------------------------------------------------------------------------

            	// AL CAMBIAR AL SWITCH LLAMAR AUTO DESCRIPCION 

            	this.generarDescripcion();

            	// ------------------------------------------------------------------------

            }, detalle() {

            	// ------------------------------------------------------------------------

            	// MOSTRAR DETALLE PRODUCTO 

            	this.$refs.detalle_producto.mostrar();

            	// ------------------------------------------------------------------------

            }, obtenerMarca(categoria){

      			// ------------------------------------------------------------------------

      			// LLAMAR FUNCION PARA FILTRAR PRODUCTOS

      			Common.obtenerMarcaCommon(categoria).then(data => {
      				this.marcas = data;
      			});

      			// ------------------------------------------------------------------------

      		}, seleccionarMarca(valor){

      			// -------------------------------------------------------------------------------------

      			this.seleccion_marca = valor;
      			var seleccion = '';
              	seleccion = (Common.filtrarCommon(this.marcas, parseInt(valor)));
      			this.descripcionMarca(seleccion[0].DESCRIPCION);
      			
      			// -------------------------------------------------------------------------------------

      		},

      		onDetalleWeb(){

      			if(this.checked_online === true){

      				this.checked_detalleWeb=true;
      	
      			}
      		},

      		llamarSubCategoria(valor, subCategorias){

              // ------------------------------------------------------------------------

              // ENVIAR DESCRIPCION TELA A PADRE

              let me = this;

              var seleccion = '';

              this.seleccion_sub_categoria = valor;

            //   subCategorias.filter(function(item){
            //   	alert(item.CODIGO+' '+ valor);
	           //   if (item.CODIGO === valor) {
	           //   	alert("despues");
	           //   	me.descripcionSubCategoria(seleccion[0].DESCRIPCION);
	           //   }
	           // });

              if (valor!=="null"){
              	seleccion = (Common.filtrarCommon(subCategorias, parseInt(valor)));
              
              	this.descripcionSubCategoria(seleccion[0].DESCRIPCION);
              }
              // ------------------------------------------------------------------------

            }
      },
        mounted() {

        	// -------------------------------------------------------------------------------------
        		
   				let me=this;
        	// INICIAR VARIABLES
        	Common.obtenerParametroCommon().then(data => {
		    
				               	me.candec=data.parametros[0].CANDEC;
                               this.descripcion_precio =data.parametros[0].DESCRIPCION;
                               
                            	this.llamarPrecios(this.descripcion_precio);

                                

				                 
		
				    
			 });


        	me.seleccion_moneda = String(me.monedaCodigo);

        	// -------------------------------------------------------------------------------------

        	// GENERAR CODIGOS

        	if (me.$route.params.id === undefined) {
        		me.obtenerCodigo();
        	} else {

        		// -------------------------------------------------------------------------------------

        		// OBTENER CODIGO PRODUCTO POR LINK 

        		me.codigoProducto(me.$route.params.id);

        		// -------------------------------------------------------------------------------------

        	}

        	// -------------------------------------------------------------------------------------

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

			// CODIGO PARA CAMBIAR EL FOCUS CON ENTER EN SELECT
        	
        	$(document).on('keypress', 'select', function(e) {
        		
        	  

			  if(e.keyCode == 13 ) {
			           me.cb = parseInt($(this).attr('tabindex'));
			    		
			           // -------------------------------------------------------------------------------------

			    	   // SI EL TABINDEX ES DE SUBCATEGORIA, EMPEZAR A REVISAR SI ESTAN HABILITADAS LOS ATRIBUTOS 
			    	   
			    	   // if (me.cb == 4) {
			    	   	  
			    	   // 	  if(me.mostrar_color == false && me.mostrar_tela == true) {
			    	   // 	  	me.cb = 6;
			    	   // 	  }

			    	   // } else if (me.cb == 5) {

			    	   // 	  if(me.mostrar_tela == false && me.mostrar_talle == true) {
			    	   // 	  	me.cb = 7;
			    	   // 	  }

			    	   // } else if (me.cb == 6) {

			    	   // 	  if(me.mostrar_genero == false) {
			    	   // 	  	me.cb = 8;
			    	   // 	  }

			    	   // } else if (me.cb == 7) {

			    	   // 	  if(me.mostrar_marca == false) {
			    	   // 	  	me.cb = 9;
			    	   // 	  }

			    	   // } else if (me.cb == 8) {

			    	   // 	  if(me.mostrar.temporada == false) {
			    	   // 	  	me.cb = 10;
			    	   // 	  }

			    	   // }


			    	   // -------------------------------------------------------------------------------------

			    	   

			    	   if (($(':input[tabindex=\'' + (me.cb + 1) + '\']')).length == 0) {

			           		while (($(':input[tabindex=\'' + (me.cb + 1) + '\']')).length == 0 && me.cb < 24) {

				    	   		me.cb = me.cb + 1;

				    	     // if (($(':input[tabindex=\'' + (me.cb + 1) + '\']')).length == 0) {
				           	   
					         //       $(':input[tabindex=\'' + (me.cb + 1) + '\']').focus();
					         //       $(':input[tabindex=\'' + (me.cb + 1) + '\']').select();
					         //       e.preventDefault();
					         //       return false;
					         //       break;
					         //   }
				    	   }

			           } 

			           if ($(':input[tabindex=\'' + (me.cb + 1) + '\']') != null) {
			           	   
			               $(':input[tabindex=\'' + (me.cb + 1) + '\']').focus();
			               $(':input[tabindex=\'' + (me.cb + 1) + '\']').select();
			               e.preventDefault();
			               return false;
			               
			           }


			  }

			});

			// -------------------------------------------------------------------------------------

			// CODIGO PARA CAMBIAR EL FOCUS CON ENTER EN SELECT
        	
        	$(document).on('keypress', 'textarea', function(e) {
        		
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