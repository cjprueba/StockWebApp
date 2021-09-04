<template>
	<div class="container mt-3">
		<div v-if="$can('gondola.crearpiso')" class="row">
			<div class="col-6">
				<div class="card shadow border-bottom-primary mb-3">
					<div class="text-center card-header">Crear Pisos</div>
			  		<div class="card-body">
			  			<div class="row mb-3">
		                	<div class="col-12">
							  				
							  	<label class="form-label">Número de Piso</label>
							  	<nropiso-textbox ref= "componente_textbox_NroPiso" :Nr_Piso="Nro_Piso" v-model="Nro_Piso" v-bind:class="{ 'is-invalid': validar.Nro_Piso }" @descripcion="cargarDescripcion" @Nro_Piso="cargarPiso" @existe_piso="validarPiso"></nropiso-textbox>
							  	
							
								
								<fieldset disabled>
									<label class="form-label mt-3">Descripción</label>
									<input type="text" v-model="descripcion" class="form-control form-control-sm" placeholder="PISO N°">
								</fieldset>	
			


			  				</div>
			  			</div>

				  		<button type="button" class="btn btn-primary" v-on:click="nuevo()">Nuevo</button>
					  	<button type="button" v-if="btn_guardar"  class="btn btn-success" v-on:click="guardar">Guardar</button>
					  	<button type="button" v-else class="btn btn-outline-secondary" disabled>Guardar</button>
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

	export default{
		data(){
			return{
				Nro_Piso:'',
				btn_guardar:true,
				descripcion: '',
				rack: '',
				validar:{
					Nro_Piso:false,
					descripcion: false
				},
				controlar:true	
			}	
		},

		methods: {
			controlador(){

				let me = this;


				if(me.Nro_Piso === '' || me.Nro_Piso.length === 0){
					me.validar.Nro_Piso = true;
					me.controlar = false;
				}else{
					me.validar.Nro_Piso = false;
				}

				return me.controlar;
			},

			nuevo(){
				this.Nro_Piso='';
				this.btn_guardar= true;
				// console.log(this.$refs.gondola);
				this.validar.Nro_Piso=false;
				this.controlar=true;
				this.numeroPiso();
			},

			guardar(){
				if(this.controlador()===false){
					this.controlar = true;
					return;
				}

				var datos = {
					Nro_Piso:this.Nro_Piso,
					btn_guardar:this.btn_guardar,
					descripcion:"PISO "+this.Nro_Piso
				}

				Common.guardarNroPisoCommon(datos).then(data=>{

					if(data.response===true){
	                  	Swal.fire(
	                     	'¡Guardado!',
	                     	'¡Se ha guardado correctamente el piso!',
	                     	'success'
	                  	)
	                    // LIMPIAR DATOS
	                    this.nuevo();

	                    // RECARGAR NroPiso TEXTBOX 
            			this.$refs.componente_textbox_NroPiso.recargar();	                  
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
					Nro_Piso:this.Nro_Piso,
					btn_guardar:this.btn_guardar
				}

				Swal.fire({
				  title: '¿Estás seguro?',
				  text: "¡Eliminar : "+this.descripcion+ "!",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  confirmButtonColor: '#d33',
				  confirmButtonText: '¡Sí, eliminar!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
    				// LLAMAR A UNA FUNCION PARA ELIMINAR LOS DATOS
				    return Common.eliminarNroPisoCommon(datos).then(data => {
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
						      '¡Se ha eliminado correctamente el número de Piso!',
						      'success'
					)

				  	// ------------------------------------------------------------------------

				  	// RECARGAR TABLA 
				  	
            		this.$refs.componente_textbox_NroPiso.recargar();

    				this.nuevo();

					// ------------------------------------------------------------------------

				  }
				})
			},

			numeroPiso(){
				Common.nuevoNroPisoCommon().then(data=>{
				this.Nro_Piso=data.piso+1;
				this.descripcion="PISO N° (NUEVO)"
		        this.btn_guardar = true;
				});

			},

			cargarPiso(valor){
				// console.log(valor);
				this.Nro_Piso=valor;
				this.btn_guardar=false;
			},
			validarPiso(valor){
				
				this.btn_guardar=!valor;
			},

			cargarDescripcion(valor){
				this.descripcion = valor;
			}
		},

		mounted(){

			let me =  this;

        	// ------------------------------------------------------------------------

        	// OBTENER SIGUIENTE Nro_Piso 
			
			me.numeroPiso();

        	// ------------------------------------------------------------------------
		}
	}
</script>