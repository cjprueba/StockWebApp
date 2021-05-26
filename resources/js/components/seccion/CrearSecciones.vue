<template>
	<div class="container mt-3">
		<div v-if="$can('secccion.crear') && $can('secccion')" class="row">
			<div class="col-6">
				<div class="card shadow border-bottom-primary mb-3">
					<div class="text-center card-header">Crear Seccion</div>
			  		<div class="card-body">
			  			<div class="row">
		                	<div class="col-12">
		                    	<div class="mb-3">
		                    		<div class="mb-3">

		                    			<div class="mb-3">
							  				<label class="form-label">Código</label>
							  				<seccion-textbox ref= "componente_textbox_seccion" :codigo="codigo" v-model="codigo" v-bind:class="{ 'is-invalid': validar.codigo }" @descripcion="cargarDescripcion" @codigo="cargarCodigo"></seccion-textbox>
							  			</div>

										<label class="form-label">Descripcion</label>
								    	<input type="text" class="form-control form-control-sm" v-model="descripcion"></input>
									
									</div>
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
				codigo:'',
				descripcion:'',
				btn_guardar:true,
				validar:{
					codigo:false,
					descripcion:false,
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

				if(me.descripcion==='' || me.descripcion.length===0){
					me.validar.descripcion= true;
					me.controlar=false;
				}else{
					me.validar.descripcion=false;
				}

				return me.controlar;
			},

			nuevo(){
				this.codigo='';
				this.descripcion='';
				this.btn_guardar= true;
				// console.log(this.$refs.gondola);
				this.validar.codigo=false;
				this.validar.descripcion=false;
				this.controlar=true;
				this.codigoSeccion();
			},

			guardar(){
				if(this.controlador()===false){
					this.controlar=true;
					return;
				}

				var datos={
					descripcion:this.descripcion,
					codigo:this.codigo,
					btn_guardar:this.btn_guardar
				}

				Common.guardarSeccionCommon(datos).then(data=>{

					if(data.response===true){
	                  	Swal.fire(
	                     	'¡Guardado!',
	                     	'¡Se ha guardado correctamente la seccion!',
	                     	'success'
	                  	)
	                    // LIMPIAR DATOS
	                    this.nuevo();

	                    // RECARGAR SECCION TEXTBOX 
            			this.$refs.componente_textbox_seccion.recargar();	                  
	                }

	                else{
	                   	Swal.fire(
	                     	'¡Error!',
	                     	data.statusText,
	                     	'warning'
	                    )
	                }
				}).catch((err) => {console.log(err);});
			},

			eliminar(){

				var datos={
					codigo:this.codigo,
					btn_guardar:this.btn_guardar
				}

				Swal.fire({
				  title: '¿Estás seguro?',
				  text: "¡Eliminar la seccion: " + this.codigo +" "+this.descripcion+ "!",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  confirmButtonColor: '#d33',
				  confirmButtonText: '¡Sí, eliminar!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
    				// LLAMAR A UNA FUNCION PARA ELIMINAR LOS DATOS
				    return Common.eliminarSeccionCommon(datos).then(data => {
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
						      '¡Se ha eliminado correctamente la seccion!',
						      'success'
					)

				  	// ------------------------------------------------------------------------

				  	// RECARGAR TABLA 
				  	
            		this.$refs.componente_textbox_seccion.recargar();

    				this.nuevo();

					// ------------------------------------------------------------------------

				  }
				})
			},

			codigoSeccion(){
				Common.nuevaSeccionCommon().then(data=>{
				this.codigo=data.seccion+1;
		        this.btn_guardar = true;

				});

			},
			cargarCodigo(valor){
				this.codigo=valor;
				this.btn_guardar=false;
			},
			cargarDescripcion(valor){
				this.descripcion=valor;
			}

		}
	}
</script>
<style>
</style>