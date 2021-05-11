<template>
		<!-- VENTA POR GONDOLA -->
	<div>
		<div class="card shadow border-bottom-primary mt-3" >
		  	<div class="card-header">Ventas Por Góndola</div>
			<div class="card-body">
			  	<div class="form-row">
			  		<div class="col-md-4 mb-3">
			  			
			  			<label for="validationTooltip01">Seleccione Sucursal</label>
						<select class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSucursal }" v-model="selectedSucursal">
							 <option value="null" selected>Seleccionar</option>
							 <option v-for="sucursal in sucursales" :value="sucursal.CODIGO">{{ sucursal.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
					        {{messageInvalidSucursal}}
					    </div>

					  	<label class="mt-3" for="validationTooltip01">Seleccione Intervalo de Tiempo</label>
						<div id="sandbox-container">
							<div class="input-daterange input-group">
								   <input   type="text" class="input-sm form-control form-control-sm" id="selectedInicialFecha" v-model="selectedInicialFecha" v-bind:class="{ 'is-invalid': validarInicialFecha }"/>
								   <div class="input-group-append form-control-sm">
								   		<span class="input-group-text">a</span>
								   </div>
								   <input   type="text" class="input-sm form-control form-control-sm" name="end" id="selectedFinalFecha" v-model="selectedFinalFecha" v-bind:class="{ 'is-invalid': validarFinalFecha }"/>
							</div>
							<div class="invalid-feedback">
					        	{{messageInvalidFecha}}
					    	</div>
						</div>		

						<!-- -------------------------------------------MOSTRAR TIPO----------------------------------------------- -->

						<label class="mt-3">Agregar Apartado De: </label>
						<div class="form-check">
							<input v-model="selectedAgregar" class="form-check-input ml-2" type="checkbox" value="CO" id="mayContado" checked>
							<label class="form-check-label ml-4" for="mayContado">
								Mayorista (Contado)
							</label>
						</div>
						<div class="form-check">
							<input v-model="selectedAgregar" class="form-check-input ml-2" type="checkbox" value="CR" id="mayCredito">
							<label class="form-check-label ml-4" for="mayCredito">
								Mayorista (Crédito)
							</label>
						</div>
						<div class="form-check">
							<input v-model="selectedAgregar" class="form-check-input ml-2" type="checkbox" value="dev" id="delivery">
							<label class="form-check-label ml-4" for="delivery">
								Servicio De Delivery
							</label>
						</div>
					</div>

					<div class="col-md-4">
						<label for="validationTooltip01">Seleccione Sección</label> 
						<div class="container_checkbox1 rounded">
		                    <div class="ml-3" v-for="seccion in secciones">
		                      <div class="custom-control custom-checkbox">
		                        <input type="checkbox" class="custom-control-input" :disabled="onSeccion" 
		                        :value="seccion.ID_SECCION" 
		                        :id="seccion.ID_SECCION" 
		                        v-model="selectedSeccion" 
		                        v-bind:class="{ 'is-invalid': validarSeccion }">
		                        <label class="custom-control-label" :for="seccion.ID_SECCION">{{seccion.DESCRIPCION }}</label>
		                      </div>
		                    </div>
		                </div>
						<div>
					        <div class="form-text text-danger">{{messageInvalidSeccion}}</div>
					    </div>
						<div  class="custom-control custom-switch mt-3">
						  <input type="checkbox" class="custom-control-input" id="customSwitch1" v-model="onSeccion" v-on:change="seleccionarTodo">
						  <label class="custom-control-label" for="customSwitch1" >Seleccionar todas las secciones</label>
						</div>
					</div>

					<!-- ------------------------------------------- GONDOLAS ----------------------------------------------- -->
				
					<div class="col-md-4">
						<label for="validationTooltip02">Seleccione Góndola</label> 
						<div class="container_checkbox1 rounded">
		                    <div class="ml-3" v-for="gondola in gondolas">
		                      <div class="custom-control custom-checkbox">
		                        <input type="checkbox" class="custom-control-input" :disabled="onGondola" 
		                        :value="gondola.ID" 
		                        :id="gondola.ID" 
		                        v-model="selectedGondola" 
		                        v-bind:class="{ 'is-invalid': validarGondola }">
		                        <label class="custom-control-label" :for="gondola.ID">{{gondola.DESCRIPCION}}</label>
		                      </div>
		                    </div>
		                </div>
						<div>
						    <div class="form-text text-danger">{{messageInvalidGondola}}</div>
						</div>
						<div class="custom-control custom-switch mt-3">
							<input type="checkbox" class="custom-control-input" id="customSwitch2" v-model="onGondola" v-on:change="seleccionarTodo">
							<label class="custom-control-label" for="customSwitch2">Seleccionar todos</label>
						</div>
		            </div>
				</div>
				<button class="btn btn-dark btn-sm" type="submit" v-on:click="descargar()"><font-awesome-icon icon="download" /> Descargar</button>
			    <button class="btn btn-primary btn-sm" type="submit" v-on:click="llamarDatos">Generar</button>
			</div>
		</div>


		<!-- CARD PARA MARCA Y SU CATEGORIA -->

		<div class="row">

			<!-- SPINNER DESCARGA -->

			<div class="col-md-12">
				<div v-if="descarga" class="d-flex justify-content-center mt-3">
					<strong>Descargando...   </strong>
	                <div class="spinner-border text-success" role="status" aria-hidden="true"></div>
	             </div>
            </div>

			<!-- SPINNER CONSULTA -->

			<div class="col-md-12">
				<div v-if="cargado" class="d-flex justify-content-center mt-3">
					<strong>Cargando...   </strong>
	                <div class="spinner-grow" role="status" aria-hidden="true"></div>
	             </div>
            </div>
        </div>

		<!-- CARD PARA MARCA Y SU CATEGORIA -->

	</div>
		<!-- FIN DE VENTA POR GONDOLA -->


</template>

<script >
	export default {
        data(){
            return {
              	sucursales: [],
              	selectedSucursal: 'null',
              	secciones: [],
              	selectedSeccion: [],
              	controlar:false,
              	onSeccion: false,
              	validarSucursal: false,
              	messageInvalidSucursal: '',
              	validarSeccion: false,
              	messageInvalidSeccion: '',
              	selectedInicialFecha: '',
              	validarInicialFecha: false,
              	messageInvalidFecha: '',
              	selectedFinalFecha: '',
              	validarFinalFecha: false,
              	datos: {},
              	cargado: false,
              	descarga: false,
              	selectedAgregar: [],
              	gondolas: [],
              	selectedGondola: [],
              	messageInvalidGondola: '',
              	validarGondola: false,
              	onGondola: false
            }
        },
        methods: {
	      	seleccionarTodo(){

	      		let me = this;

	      		if(me.onGondola === true) {
			        for (var key in me.gondolas){
			        	me.selectedGondola[key] = me.gondolas[key].ID;
			        }
			    }else{

			    }

	      		if(me.onSeccion === true) {
			        for (var key in me.secciones){
			        	me.selectedSeccion[key] = me.secciones[key].ID_SECCION;
			        }
			    }else{
			    	me.selectedSeccion = [];
			    }
	      	},

            llamarBusquedas(){	
	          axios.get('busquedas/').then((response) => {
	           	this.sucursales = response.data.sucursales;
	           	this.secciones = response.data.secciones;
	          }); 

      			// LLAMAR FUNCION PARA FILTRAR GONDOLAS

      			Common.obtenerGondolaCommon().then(data => {

	                this.gondolas = data;
      			});
	        },
	        descargar(){
	        	
	        	let me = this;	
	        		
	        	if(this.generarConsulta() === true) {
	        		me.descarga = true;
		        	axios({
					  url: '/export_venta_gondola',
					  method: 'POST',
					  data: me.datos,
					  responseType: 'blob', // important
					}).then((response) => {
						me.descarga = false;
					   const url = window.URL.createObjectURL(new Blob([response.data]));
					   const link = document.createElement('a');
					   link.href = url;
					   link.setAttribute('download', 'Venta_Gondola'+me.selectedInicialFecha+' al '+me.selectedFinalFecha+'.xlsx'); //or any other extension
					   document.body.appendChild(link);
					   link.click();
					});
				}
	        },
	        
	        llamarDatos(){
	        	let me = this;	
	        	if(this.generarConsulta() === true) {
	        		me.cargado = true;
	      //   		Common.generarReporteVentaCommon(this.datos).then(data => {
		        		  
	      //        				me.cargado = false;
							// me.responseVenta = data.ventas;
						 //    const marcaArray = Object.keys(data.secciones).map(i => data.secciones[i])
						 //    me.responseMarca = marcaArray
						   
						   
						 //    const categoriaArray = Object.keys(data.categorias).map(i => data.categorias[i])
						 //    me.responseCategoria = categoriaArray
						 //    me.loadsecciones();
	      //         	});

	        	} 
	        },
	        generarConsulta(){
	        	
	        	let me=this;

	        	if (me.selectedSucursal === 'null' || me.selectedSucursal === '') {
	        		me.validarSucursal = true;
	        		me.messageInvalidSucursal = 'Por favor seleccione sucursal';
	        		me.controlar=true;
	        	} else {
	        		me.validarSucursal = false;
	        		me.messageInvalidSucursal = '';
	        	}	

	        	if(me.onSeccion === false && me.selectedSeccion.length === 0) {
	        		me.validarSeccion = true;
	        		me.messageInvalidSeccion = 'Por favor seleccione una o varias secciones';
	        		me.controlar=true;
	        	} else {
	   
	        		me.validarSeccion = false;
	        		me.messageInvalidSeccion = '';
	        	}

	        	if(me.selectedInicialFecha === null || me.selectedInicialFecha === "") {
	        		me.validarInicialFecha = true;
	        		me.messageInvalidFecha = 'Por favor seleccione una fecha Inicial';
	        		me.controlar=true;
	        	} else {
	        		me.validarInicialFecha = false;
	        		me.messageInvalidFecha = '';
	        	}

	        	if(me.selectedFinalFecha === null || me.selectedFinalFecha === "") {
	        		me.validarFinalFecha = true;
	        		me.messageInvalidFecha = 'Por favor seleccione una fecha Final';
	        		me.controlar=true;
	        	} else {
	        		me.validarFinalFecha = false;
	        		me.messageInvalidFecha = '';
	        	}	

	        	if(me.onGondola === false && me.selectedGondola.length === 0) {
	        		me.messageInvalidGondola = 'Por favor seleccione una o varias Góndolas.';
	        		me.validarGondola = true;
	        		me.controlar = true;
	        	} else {
	        		me.messageInvalidGondola = '';
	        		me.validarGondola = false;
	        	}	
	        	if(me.controlar===true){
	        		me.controlar=false;
	        		return;
	        	}

	        	if(me.onSeccion === true) {
	        		for (var key in me.secciones){
	        			me.selectedSeccion[key] = me.secciones[key].ID_SECCION;
	        		}
	        	} 

 				if(me.onGondola === true) {
		        	for (var key in me.gondolas){
		        		me.selectedGondola[key] = me.gondolas[key].ID;
		        	}
		        }

	        	me.datos = {
		        	Sucursal: me.selectedSucursal,
		        	Inicio: String(me.selectedInicialFecha),
		        	Final: String(me.selectedFinalFecha),
		        	secciones: me.selectedSeccion,
		        	AllSecciones: me.onSeccion,
					Gondolas: me.selectedGondola,
					AllGondolas: me.onGondola,
					Agregar: me.selectedAgregar
	        	};
	        	
	        	return true;
	        },

        },
        mounted() {

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

				$('table').dataTable();
			});

			me.llamarBusquedas();
        }
    }    
</script>
<style>
	.container_checkbox1 { 
		border:2px solid #ccc; 
		height: 224px; 
		overflow-y: scroll; 
	}
</style>