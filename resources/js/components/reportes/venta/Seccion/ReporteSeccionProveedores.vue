<template>
  <div>
	<div v-if="$can('producto.mostrar')">
		<div class="card shadow border-bottom-primary mt-3">
		  	<div class="card-header">
		  		Venta por Sección y Proveedor
		  	</div>
			<div class="card-body">
			  	<div class="form-row">
			  		<div class="col-md-4 mb-3">
			  			
						<!-- -----------------------------------SELECT SUCURSAL------------------------------------- -->

			  			<label for="validationTooltip01">Seleccione Sucursal</label>
						<select v-on:change="habilitar_insert" class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSucursal }" v-model="selectedSucursal">
							 <option value="null" selected>Seleccionar</option>
							 <option v-for="sucursal in sucursales" :value="sucursal.CODIGO">{{ sucursal.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
					        {{messageInvalidSucursal}}
					    </div>

						<!-- -----------------------------------SELECT SECCION------------------------------------- -->

					    <label class="mt-3" for="validationTooltip01">Seleccione Sección</label>
						<select v-on:change="habilitar_insert" class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSeccion }" v-model="selectedSeccion">
							 <option value="null" selected>Seleccionar</option>
							 <option v-for="seccion in secciones" :value="seccion.ID_SECCION">{{ seccion.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
					        {{messageInvalidSeccion}}
					    </div>

						<!-- -----------------------------------SELECT INTERVALO------------------------------------- -->
						
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
					</div>

					<div class="col-md-4 ml-3">
						<label for="validationTooltip02">Seleccione Proveedor</label> 
						<div class="container_checkbox_seccion rounded">
		                    <div class="ml-3" v-for="proveedor in proveedores">
		                      <div class="custom-control custom-checkbox">
		                        <input  v-on:change="habilitar_insert" type="checkbox" class="custom-control-input" :disabled="onProveedor" 
		                        :value="proveedor.CODIGO" 
		                        :id='"Piso_"+proveedor.CODIGO' 
		                        v-model="selectedProveedor" 
		                        v-bind:class="{ 'is-invalid': validarProveedor }">
		                        <label class="custom-control-label" :for='"Piso_"+proveedor.CODIGO' >{{proveedor.NOMBRE}}</label>
		                      </div>
		                    </div>
		                </div>
						<div>
						    <div class="form-text text-danger">{{messageInvalidProveedor}}</div>
						</div>
						<div class="custom-control custom-switch mt-3">
							<input  type="checkbox" class="custom-control-input" id="customSwitch2" v-model="onProveedor" v-on:change="seleccionarTodo">
							<label class="custom-control-label" for="customSwitch2">Seleccionar todos</label>
						</div>
					</div>
				</div>
				<button class="btn btn-dark btn-sm" type="submit" v-on:click="descargar()"><font-awesome-icon icon="download" /> Descargar</button>
			</div>
		</div>

		<div class="row">

			<!-- SPINNER DESCARGA -->

			<div class="col-md-12">
				<div v-if="descarga" class="d-flex justify-content-center mt-3">
					<strong>Descargando...   </strong>
	                <div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
	             </div>
            </div>
        </div>
	</div>

	<!-- FIN DE VENTA POR SECCION -->

	<!-- ------------------------------------------------------------------------ -->

	<div v-else>
		<cuatrocientos-cuatro></cuatrocientos-cuatro>
	</div>

	<!-- ------------------------------------------------------------------------ -->
  </div>
</template>
<script>
	export default{
        data(){
            return{
				sucursales: [],
              	selectedSucursal: 'null',
              	selectedSeccion: 'null',
				validarSucursal: false,
              	messageInvalidSucursal: '',
				selectedInicialFecha: '',
              	validarInicialFecha: false,
              	messageInvalidFecha: '',
              	selectedFinalFecha: '',
              	validarFinalFecha: false,
				descarga: false,
              	secciones: [],
              	validarSeccion: false,
              	messageInvalidSeccion: '',
              	proveedores: [],
              	selectedProveedor: [],
              	messageInvalidProveedor: '',
              	validarProveedor: false,
              	onProveedor: false,
              	insert:true,
              	controlar: false,
              	datos: []
            }
        }, 
        methods:{

            llamarBusquedas(){	

		        axios.get('busquedas/').then((response) => {
		           	this.sucursales = response.data.sucursales;
		           	this.secciones = response.data.seccion;
	           		this.proveedores = response.data.proveedores;
		        }); 
	        },        

	       habilitar_insert() {
		       	let me = this;
		       	me.insert = true;
	       	},

	      	seleccionarTodo(){

	      		let me = this;

	      		if(me.onProveedor === true) {
			        for (var key in me.proveedores){
			        	me.selectedProveedor[key] = me.proveedores[key].CODIGO;
			        }
			    }

			    this.habilitar_insert();
	      	},

	      	generarConsulta(){
	        	
	        	let me = this;

	        	if (me.selectedSucursal === "null" || me.selectedSucursal === ''){
	        		me.validarSucursal = true;
	        		me.messageInvalidSucursal = 'Por favor seleccione sucursal';
	        		me.controlar = true;
	        	} else {
	        		me.validarSucursal = false;
	        		me.messageInvalidSucursal = '';
	        	}	

	        	if (me.selectedSeccion === "null" || me.selectedSeccion === ''){
	        		me.validarSeccion = true;
	        		me.messageInvalidSeccion = 'Por favor seleccione la sección';
	        		me.controlar = true;
	        	} else {
	        		me.validarSeccion = false;
	        		me.messageInvalidSeccion = '';
	        	}	

				if(me.selectedInicialFecha === "null" || me.selectedInicialFecha === ""){
	        		me.validarInicialFecha = true;
	        		me.messageInvalidFecha = 'Por favor seleccione una fecha Inicial';
	        		me.controlar = true;
	        	} else {
	        		me.validarInicialFecha = false;
	        		me.messageInvalidFecha = '';
	        	}

	        	if(me.selectedFinalFecha === "null" || me.selectedFinalFecha === ""){
	        		me.validarFinalFecha = true;
	        		me.messageInvalidFecha = 'Por favor seleccione una fecha Final';
	        		me.controlar = true;
	        	} else {
	        		me.validarFinalFecha = false;
	        		me.messageInvalidFecha = '';
	        	}

	        	if(me.onProveedor === false && me.selectedProveedor.length === 0) {
	        		me.messageInvalidProveedor = 'Por favor seleccione uno o varios Proveedores.';
	        		me.validarProveedor = true;
	        		me.controlar = true;
	        	} else {
	        		me.messageInvalidProveedor = '';
	        		me.validarProveedor = false;
	        	}
	        			
	        	if(me.controlar === true){
	        		return false;
	        	}

 				if(me.onProveedor === true) {
		        	for (var key in me.proveedores){
		        		me.selectedProveedor[key] = me.proveedores[key].CODIGO;
		        	}
		        }

	        	me.datos = {
		        	Sucursal: me.selectedSucursal,
		        	Inicio: String(me.selectedInicialFecha),
		        	Final: String(me.selectedFinalFecha),
		        	Insert:me.insert,
		        	Seccion: me.selectedSeccion,
					Proveedores: me.selectedProveedor,
					AllProveedores: me.onProveedor
	        	};
	      
	        	return true;
	        },

	        descargar(){
	        	
	        	let me = this;	
	        	
	        	if(me.generarConsulta() === true){
	        		me.descarga = true;
		        	axios({
					  url: '/export_seccion_proveedor',
					  method: 'POST',
					  data: me.datos,
					  responseType: 'blob', // important
					}).then((response) => {
						me.descarga = false;
					   const url = window.URL.createObjectURL(new Blob([response.data]));
					   const link = document.createElement('a');
					   link.href = url;
					   link.setAttribute('download', 'Proveedor-Seccion-'+me.selectedInicialFecha+'al'+me.selectedFinalFecha+'.xlsx'); //or any other extension
					   document.body.appendChild(link);
					   link.click();
					});
				}
				me.insert = false;
				me.controlar = false;
	        },
	    },
        mounted(){

        	let me = this;

        	$(function(){
		   		    $('#sandbox-container .input-daterange').datepicker({
		   		    	    keyboardNavigation: false,
    						forceParse: false
    				});
    				$("#selectedInicialFecha").datepicker().on(
			     		"changeDate", () => {
			     			me.insert=true;
			     			me.selectedInicialFecha = $('#selectedInicialFecha').val();
			     		}
					);
					$("#selectedFinalFecha").datepicker().on(
						
			     		"changeDate", () => {
			     			me.insert=true;
			     			me.selectedFinalFecha = $('#selectedFinalFecha').val();
			     		}
					);

					$('table').dataTable();
			});

			this.llamarBusquedas();
        }
    }    
</script>
<style>
	.container_checkbox_seccion { 
		border:2px solid #ccc; 
		height: 150px; 
		overflow-y: scroll; 
	}
</style>