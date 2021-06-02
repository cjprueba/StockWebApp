<template>
	<div class="container mt-3">
		<div v-if="$can('empleados.crear') && $can('empleados') && $can('config')" class="row">
			<div class="col-12">
				<div class="card shadow border-bottom-primary mb-3">
			  		<div class="text-center card-header">Crear Empleados</div>
				  	<div class="card-body">
				  		<div class="row">
				  			<div class="col-6">
				  				<div class="mb-3">
								  <label class="form-label">Nombre</label>
								  <input type="text" class="form-control form-control-sm" v-model="nombre"  v-bind:class="{ 'is-invalid': validar.nombre }">
								</div>
								<div class="mb-3">
								  <label class="form-label">Dirección</label>
								  <input type="text" class="form-control form-control-sm" v-model="direccion"  v-bind:class="{ 'is-invalid': validar.direccion }">
								</div>
								<div class="mb-3">
								  <label class="form-label">Ciudad</label>
								  <input type="text" class="form-control form-control-sm" v-model="ciudad" >
								</div>
								<div class="mb-3">
								  <label class="form-label">Teléfono</label>
								  <input type="number" class="form-control form-control-sm" v-model="telefono"  v-bind:class="{ 'is-invalid': validar.telefono }">
								</div>
								<div class="mb-3">
									  <label class="form-label">Cargo</label>
									  <select class="custom-select custom-select-sm" v-model="cargo" v-bind:class="{ 'is-invalid': validar.cargo}">
									  <option selected>SELECCIONAR</option>
									  <option>CAJERO</option>
									  <option>DEPOSITO</option>
									  <option>GERENTE</option>
									  <option>OPERADOR</option>
									  <option>VENDEDOR</option>
									  </select>
								</div>
				  			</div>
				  			<div class="col-6">
				  				<div class="mb-3">
					  				<label class="form-label">Código</label>
					  				<empleado-textbox ref= "componente_textbox_empleado" :codigo="codigo" v-model="codigo" v-bind:class="{ 'is-invalid': validar.codigo }" @nombre="cargarNombre" @cedula="cargarCedula" @codigo="cargarCodigo" @direccion="cargarDireccion" @ciudad="cargarCiudad" @nacimiento="cargarNacimineto" @telefono="cargarTelefono" @cargo="cargarCargo" @gondolas="cargarGondolas" @imagen="cargarImagen"></empleado-textbox>
				  				</div>
								<div class="mb-3">
								  <label class="form-label">C.I.</label>
								  <input type="number" class="form-control form-control-sm" v-model="ci"  v-bind:class="{ 'is-invalid': validar.ci}">
								</div>
								<div class="mb-3">
								  <label class="form-label">Fecha - Nacimiento</label>
								  <input class="form-control form-control-sm" id='selectedInicialFecha' v-model="fecha_nac">
								</div>
								<select-gondola ref="gondola" v-model="seleccion_gondola" v-bind:selecciones="seleccion_gondola_modificar"> </select-gondola>
								<!-- IMAGEN -->
					    		
					    		<div class="form-row mt-3">
					    			<!-- ------------------------------------------------------------------ -->
					    			<!-- IMAGEN -->
					    			<div class="col-md-12">
					    				<form id="myAwesomeForm">
					    				<div class="card mb-3">
										  <div class="row no-gutters">
										    <div class="col-md-4">
										      <span v-html="rutaImagen"></span>	
										      <!-- <img  :src="rutaImagen" class="card-img" alt="..." id="myAwesomeForm"> -->
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
				  			</div>
				  		</div>
				  		<button type="button" class="btn btn-primary" v-on:click="nuevo()">Nuevo</button>
				  		<button type="button" v-if="btn_guardar"  class="btn btn-success" v-on:click="guardar">Guardar</button>
				  		<button type="button" v-else class="btn btn-warning" v-on:click="guardar()">Modificar</button>
				  		<button type="button" class="btn btn-danger" v-on:click="eliminar()">Eliminar</button>
					</div>
				</div>
			</div>
		</div>
		<div v-else>
    		<cuatrocientos-cuatro></cuatrocientos-cuatro>
    	</div>
	</div>
</template>
<script>
	export default {
		data(){
			return{
				fileName: 'Imagen',
				rutaImagen: "<img src='http://172.16.249.20:8080/storage/imagenes/empleados/empleado.png'  class='card-img-top'>",
				imagen: {
		          	blob: ''
		        },
				codigo:'',
				nombre:'',
				direccion: '',
				ciudad: '',
				ci:'',
				telefono:'',
				cargo:'SELECCIONAR',
				fecha_nac:'',
				btn_guardar: true,
	            seleccion_gondola: [{}],
	            seleccion_gondola_modificar: [{}],
				validar:{
					codigo:false,
					nombre:false,
					direccion: false,
					ci:false,
					telefono:false,
					cargo:false,
				},
				controlar:true
			}
		},
		methods: {
			controlador(){
				let me=this;
				if(me.codigo==='' || me.codigo.length===0){
					me.validar.codigo= true;
					me.controlar=false;
				}else{
					me.validar.codigo=false;
				}

				if(me.cargo==='SELECCIONAR'){
					me.validar.cargo= true;
					me.controlar=false;
				}else{
					me.validar.cargo=false;
				}

				if(me.ci==='' || me.ci.length===0){
					me.validar.ci= true;
					me.controlar=false;
				}else{
					me.validar.ci=false;
				}

				if(me.nombre==='' || me.nombre.length===0){
					me.validar.nombre= true;
					me.controlar=false;
				}else{
					me.validar.nombre=false;
				}

				if(me.telefono==='' || me.telefono.length===0){
					me.validar.telefono= true;
					me.controlar=false;
				}else{
					me.validar.telefono=false;
				}

				if(me.direccion==='' || me.direccion.length===0){
					me.validar.direccion= true;
					me.controlar=false;
				}else{
					me.validar.direccion=false;
				}
				return me.controlar;
			},
			nuevo(){

				this.codigo='';
				this.nombre='';
				this.direccion= '';
				this.ciudad= '';
				this.ci='';
				this.telefono='';
				this.cargo='SELECCIONAR';
				this.fecha_nac='';
				this.btn_guardar= true;
				this.fileName = 'Imagen';
				this.rutaImagen= "<img src='http://172.16.249.20:8080/storage/imagenes/empleados/empleado.png'  class='card-img-top'>";
				this.$refs.gondola.limpiar();
				// console.log(this.$refs.gondola);
         		this.seleccion_gondola= null;
          		this.seleccion_gondola_modificar= null;
				this.validar.codigo=false;
				this.validar.nombre=false;
				this.validar.direccion= false;
				this.validar.ci=false;
				this.validar.telefono=false;
				this.validar.cargo=false;
				this.controlar=true;
				this.codigoEmpleado();

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
							me.rutaImagen = "<img src='"+base64Img+"' id='myImg'  class='card-img-top'>";
							me.imagen.blob = base64Img;

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

			guardar(){
				if(this.controlador()===false){
					this.controlar=true;
					return;
				}

				if (this.rutaImagen.includes('SinImagen') === true) {
            		me.rutaImagen = '';
            	}

				var datos={
					nombre:this.nombre,
					codigo:this.codigo,
					direccion:this.direccion,
					ciudad:this.ciudad,
					cargo:this.cargo,
					imagen: this.imagen.blob,
					telefono:this.telefono,
					fecha_nac:this.fecha_nac,
					cedula:this.ci,
					seleccion_gondola:this.seleccion_gondola,
					btn_guardar:this.btn_guardar
				}
				console.log(datos.seleccion_gondola);

				Common.guardarEmpleadosCommon(datos).then(data=>{

					if(data.response===true){

	                  	Swal.fire(
	                     	'¡Guardado!',
	                     	'¡Se ha guardado correctamente el empleado!',
	                     	'success'
	                  	)
	                  	 // *******************************************************************

	                    // LIMPIAR DATOS

	                    this.nuevo();
	                    // RECARGAR EMPLEADO TEXTBOX 

            			this.$refs.componente_textbox_empleado.recargar();
	                  
	                }else{

	                   	Swal.fire(
	                     	'¡Error!',
	                     	data.statusText,
	                     	'warning'
	                    )
	                }
				}).catch((err) => {
	                console.log(err);
             	});

			},
			eliminar(){

				var datos={
					codigo:this.codigo,
					btn_guardar:this.btn_guardar
				}

				Swal.fire({
				  title: '¿Estás seguro?',
				  text: "¡Eliminar el Empleado " + this.codigo +" "+this.nombre+ "!",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  confirmButtonColor: '#d33',
				  confirmButtonText: '¡Sí, eliminar!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
    				// LLAMAR A UNA FUNCION PARA ELIMINAR LOS DATOS
				    return Common.eliminarEmpleadosCommon(datos).then(data => {
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
				  if (result.value) {
				  	Swal.fire(
						      '¡Eliminado!',
						      '¡Se ha eliminado correctamente el empleado!',
						      'success'
					)

				  	// ------------------------------------------------------------------------

				  	// RECARGAR TABLA 
				  	
            		this.$refs.componente_textbox_empleado.recargar();

    				this.nuevo();

					// ------------------------------------------------------------------------

				  }
				})
			},
			codigoEmpleado(){
				Common.nuevoEmpleadoCommon().then(data=>{
					this.codigo=data.empleado+1;
		        	this.btn_guardar = true;

				});

			},
			cargarCodigo(valor){
				this.codigo=valor;
				this.btn_guardar=false;
			},
			cargarNombre(valor){
				this.nombre=valor;
			},
			cargarCargo(valor){
				this.cargo=valor;
			},
			cargarTelefono(valor){
				this.telefono=valor;
			},
			cargarNacimineto(valor){
				this.fecha_nac=valor;
			},
			cargarCiudad(valor){
				this.ciudad=valor;
			},
			cargarDireccion(valor){
				this.direccion=valor;
			},
			cargarCedula(valor){

				this.ci=valor;
			},
			cargarGondolas(valor){

				this.seleccion_gondola_modificar=valor;
			},

			cargarImagen(valor){

				this.rutaImagen=valor;
			},
		 },

		 mounted(){

		 	let me = this;

		 	me.codigoEmpleado();
        	$(function(){
		   		    $('#sandbox-container .input-daterange').datepicker({
		   		    	    keyboardNavigation: false,
    						forceParse: false
    				});
    				$("#selectedInicialFecha").datepicker().on(
			     		"changeDate", () => {me.fecha_nac = $('#selectedInicialFecha').val()}
					);
					$('table').dataTable();
			});
		 }
	}
</script>

<style></style>