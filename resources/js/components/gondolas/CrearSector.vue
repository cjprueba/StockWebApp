<template>
	<div class="container mt-3">
		<div v-if="$can('gondola.crearsector')" class="row">
			<div class="col-6">
				<div class="card shadow border-bottom-primary mb-3">
					<div class="text-center card-header">Crear Sector</div>
			  		<div class="card-body">
			  			<div class="row mb-3">
		                	<div class="col-12">
							  				
							  	<label class="form-label">Sector</label>
							  	<sector-textbox ref= "componente_textbox_sector" :Sect="Sector" v-model="Sector" v-bind:class="{ 'is-invalid': validar.Sector }" @Sector="cargarSector" @existe_sector="validarSector"></sector-textbox>
							  	
								<fieldset disabled>
									<label class="form-label mt-3">Descripción</label>
									<input type="text" v-model="descripcion" class="form-control form-control-sm" placeholder="SECTOR NUEVO">
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
				Sector:'',
				btn_guardar:true,
				descripcion: '',
				rack: '',
				validar:{
					Sector:false,
					descripcion: false
				},
				controlar:true	
			}	
		},

		methods: {
			controlador(){

				let me = this;


				if(me.Sector === '' || me.Sector.length === 0){
					me.validar.Sector = true;
					me.controlar = false;
				}else{
					me.validar.Sector = false;
				}

				return me.controlar;
			},

			nuevo(){
				this.Sector='';
				this.btn_guardar= true;
				// console.log(this.$refs.gondola);
				this.validar.Sector=false;
				this.controlar=true;
				this.abcSector();
			},

			guardar(){
				if(this.controlador()===false){
					this.controlar = true;
					return;
				}

				var datos = {
					Sector:this.Sector,
					btn_guardar:this.btn_guardar,
				}

				Common.guardarsectorCommon(datos).then(data=>{

					if(data.response===true){
	                  	Swal.fire(
	                     	'¡Guardado!',
	                     	'¡Se ha guardado correctamente el sector!',
	                     	'success'
	                  	)
	                    // LIMPIAR DATOS
	                    this.nuevo();

	                    // RECARGAR sector TEXTBOX 
            			this.$refs.componente_textbox_sector.recargar();	                  
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
					Sector:this.Sector,
					btn_guardar:this.btn_guardar
				}

				Swal.fire({
				  title: '¿Estás seguro?',
				  text: "¡Eliminar : SECTOR "+this.Sector+ "!",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  confirmButtonColor: '#d33',
				  confirmButtonText: '¡Sí, eliminar!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
    				// LLAMAR A UNA FUNCION PARA ELIMINAR LOS DATOS
				    return Common.eliminarsectorCommon(datos).then(data => {
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
						      '¡Se ha eliminado correctamente el Sector!',
						      'success'
					)

				  	// ------------------------------------------------------------------------

				  	// RECARGAR TABLA 
				  	
            		this.$refs.componente_textbox_sector.recargar();

    				this.nuevo();

					// ------------------------------------------------------------------------

				  }
				})
			},

			abcSector(){
				Common.nuevoSectorCommon().then(data=>{
				this.descripcion="SECTOR NUEVO"
		        this.btn_guardar = true;
				});

			},

			cargarSector(valor){
				
				this.Sector=valor;
				this.descripcion="SECTOR "+valor;	
				this.btn_guardar=false;
			},
			validarSector(valor){
				
				this.btn_guardar=!valor;
			},

			cargarDescripcion(valor){
				
				this.descripcion = valor;
			}
		},

		mounted(){

			let me =  this;

        	// ------------------------------------------------------------------------

        	// OBTENER SIGUIENTE Sector 
			
			me.abcSector();

        	// ------------------------------------------------------------------------
		}
	}
</script>