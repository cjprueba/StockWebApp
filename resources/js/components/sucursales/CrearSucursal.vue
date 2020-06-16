<template>
	
	<div class="container">

		<div class="offset-md-3 col-6">

			<div class="card mt-3 shadow-sm">

				<!-- --------------------------------------------- TITULO DE LA TARJETA ------------------------------------------------ -->
				
				<h6 class="card-header text-center">Crear Sucursal</h6>	

				<!-- ------------------------------------------------ CUERPO DE LA TARJETA---------------------------------------------- -->

				<div class="card-body">
					
					<div class="row">

						<!-- ---------------------------------------- TEXTBOX DE CODIGO ------------------------------------------------ -->

						<div class="col-12 mt-2">
							<sucursal-filtrar ref="componente_textbox_sucursal" :codigo="codigo" @codigo='codigoHijo' v-model="codigo" @descripcion='cargarDescrip' @razon_social='cargarRazon' @ruc='cargarRuc' @ciudad='cargarCiudad' @direccion='cargarDire' @telefono='cargarTel' @nombre='cargarNombre'></sucursal-filtrar>	
		            		<hr>
			    		</div> 

			    		<!-- ---------------------------------------- INPUT DE DESCRIPCION --------------------------------------------- -->

			    		<div class="col-12 mt-3">
		    				<label for="" >Descripción</label>
		            		<input v-bind:class="{ 'is-invalid': validar.descripcion }" v-model="descripcion" type="text" class="form-control form-control-sm" id="sucursal-descripcion">

		            		<hr>
			    		</div>

			    		<!-- ---------------------------------------- INPUT DE RAZON SOCIAL -------------------------------------------- -->

			    		<div class="col-12 mt-3">
		    				<label for="" >Razón Social</label>
		            		<input v-bind:class="{ 'is-invalid': validar.razon }" v-model="razon" type="text" class="form-control form-control-sm" id="sucursal-razon_social">

		            		<hr>
			    		</div>

			    		<!-- -------------------------------------------- INPUT DE RUC ------------------------------------------------- -->

			    		<div class="col-12 mt-3">
		    					<label for="" >R.U.C:</label>
		            			<input v-bind:class="{ 'is-invalid': validar.ruc }" v-model="ruc" type="text" class="form-control form-control-sm" id="sucursal-ruc">
		            			
		            			<hr>
			    		</div>

			    		<!-- ------------------------------------------ INPUT DE CIUDAD ------------------------------------------------ -->

			    		<div class="col-12 mt-3">
		    				<label for="" >Ciudad:</label>
		            		<input v-bind:class="{ 'is-invalid': validar.ciudad }" v-model="ciudad" type="text" class="form-control form-control-sm" id="sucursal-ciudad">
		            			
		            		<hr>
			    		</div>

			    		<!-- ---------------------------------------- INPUT DE DIRECCION ----------------------------------------------- -->

			    		<div class="col-12 mt-3">
		    				<label for="" >Dirección:</label>
		            		<input v-bind:class="{ 'is-invalid': validar.direccion }" v-model="direccion" type="text" class="form-control form-control-sm" id="sucursal-direccion">
		            			
		            		<hr>
			    		</div>

			    		<!-- ---------------------------------------- INPUT DE TELEFONO ------------------------------------------------ -->

			    		<div class="col-12 mt-3">
		    				<label for="" >Teléfono:</label>
		            		<input v-bind:class="{ 'is-invalid': validar.telefono }" v-model="telefono" type="text" class="form-control form-control-sm" id="sucursal-telefono">
		            			
		            		<hr>
			    		</div>

					</div>

					<!-- -------------------------------------------- RADIO DE NOMBRE -------------------------------------------------- -->
					
					<div class="row">
						<div class="col-12 mt-3">
		    				<label for="" >Nombre:</label>
		            		
		            		<div class="custom-control custom-radio">
  								<input v-model="nombre" type="radio" id="customRadio1" name="customRadio" class="custom-control-input" value=0>
  								<label  class="custom-control-label" for="customRadio1">Desactivar</label>
							</div>

							<div class="custom-control custom-radio">
  								<input v-model="nombre" type="radio" id="customRadio2" name="customRadio" class="custom-control-input" value=1>
  								<label  class="custom-control-label" for="customRadio2">Activar</label>
							</div>
		            			
		            		<hr>
			    		</div>
					</div>

					<!-- ------------------------------- BOTONES NUEVO, GUARDAR, MODIFICAR Y ELIMINAR ---------------------------------- -->
			    	
			    	<div class="row mt-2 ">

			    		<div class="col text-right">
				    				
				    		<button v-on:click="limpiar()" type="button" class="btn btn-primary">Nuevo</button>

				    	</div>

				    	<div v-if='btnguardar' class="col text-center">

				    		<button v-on:click="guardar" type="button" class="btn btn-success">Guardar</button>
				    		
				    	</div>

				    	<div v-else class="col text-center">

				    		<button v-on:click="guardar" type="button" class="btn btn-warning">Modificar</button>
				    		
				    	</div>

				    	<div class="col text-left">
				    	
				    		<button v-on:click="eliminar()" type="button" class="btn btn-danger">Eliminar</button>
				    		
				    	</div>

			    	</div>
				</div>

			</div>
		</div>
	</div>

</template>

<script>
	export default {
		
		props: ['codigo'],

		data(){
			return {

				validar: {
					descripcion: false,
					razon: false,
					ruc: false,
					ciudad: false,
					direccion: false,
					telefono: false
				},

				codigo: '',
				descripcion: '',
				razon: '',
				ruc: '',
				ciudad: '',
				direccion: '',
				telefono: '',
				nombre: 0,

				btnguardar: true,

				existe: false
			}
		},

		methods: {

			// GUARDAR O MODIFICAR

			guardar(){

				// CONTROL DE DATOS NULOS

				if ((this.controlador()) === false){
					return;
				}

				// GUARDAR LOS DATOS EN UNA VARIABLE

				var data = {

					codigo: this.codigo,
					descripcion: this.descripcion,
					razon: this.razon,
					ruc: this.ruc,
					ciudad: this.ciudad,
					direccion: this.direccion,
					telefono: this.telefono,
					nombre: this.nombre,
					existe: this.existe

				}

				// ENVIAR LOS DATOS PARA GUARDAR O MODIFICAR

				Common.guardarSucursalCommon(data).then(data=>{


    				// CONFIRMAR QUE GUARDÓ O SI HAY UN ERROR

	                if(data.response===true){

	                  	Swal.fire(
	                     	'¡Guardado!',
	                     	'¡Se ha guardado correctamente la sucursal!',
	                     	'success'
	                  	)
	                  	this.$refs.componente_textbox_sucursal.recargar();
	                }else{

	                   	Swal.fire(
	                     	'¡Error!',
	                     	data.statusText,
	                     	'warning'
	                    )
	                }
           		
           		// MOSTRAR ERROR
           			
           		}).catch((err) => {
	                console.log(err);
	                this.mostrar_error = true;
	                this.mensaje = err;
             	});

             	this.limpiar();
								
			},

			eliminar(){

				// GUARDAR LOS DATOS EN UNA VARIABLE

    			var eliminar = {

    				codigo: this.codigo,
    				existe: this.existe
    			}


    			// LLAMAR A UNA FUNCION COMUN PARA ELIMINAR LOS DATOS

    			Common.eliminarSucursalCommon(eliminar).then(data => {

    				// MENSAJE DE CONFIRMACION O ERROR

    				if(data.response===true){
	                  	Swal.fire(
		                    '¡Eliminado!',
		                    '¡Se ha eliminado correctamente la sucursal!',
		                    'success'
	                  	)
	                  	this.$refs.componente_textbox_sucursal.recargar();
	               	}else{
		                Swal.fire(
		                    '¡Error!',
		                    data.statusText,
		                    'warning'
		                )
	               	}

	               	// MENSAJE DE ERROR EN LA FUNCION COMUN

    			}).catch((err) => {
	                console.log(err);
	                this.mostrar_error = true;
	                this.mensaje = err;
              	});

    			this.limpiar();
			},

			// LIMPIA EL FORMULARIO

			limpiar(){

				this.descripcion = '';
				this.razon = '';
				this.ruc = '';
				this.ciudad = '';
				this.direccion = '';
				this.telefono = '';
				this.nombre = 0 ;
				this.btnguardar = true;
				this.validar.descripcion = false;
				this.validar.razon = false;
				this.validar.ruc = false;
				this.validar.ciudad = false;
				this.validar.direccion = false;
				this.validar.telefono = false;
				this.existe = false;
				Common.sucursalNuevaCommon().then(data=> {
		        	this.codigo = data.sucursal[0].CODIGO+1;
		        	this.btnguardar = true;
		        });
			},

			// PASA LOS DATOS DEL DATATABLE AL INPUT DE CADA UNO
			
			codigoHijo(valor){

				this.codigo = valor;
			},

			cargarDescrip(valor){
				this.descripcion = valor;
			},

			cargarRazon(valor){

				this.razon = valor;
			},

			cargarRuc(valor){

				this.ruc = valor;
			},

			cargarCiudad(valor){

				this.ciudad = valor;
			},

			cargarDire(valor){

				this.direccion = valor;
			},

			cargarNombre(valor){

				this.nombre = valor;
			},
			
			cargarTel(valor){

				this.telefono = valor;
				this.btnguardar = false;
				this.existe = true;
			},

			// CONTROLAR QUE LOS CAMPOS NO ESTEN VACIOS PARA GUARDAR

			controlador(){

				if ((this.descripcion).length === 0 || (this.descripcion === " ")){
					this.validar.descripcion = true;

				}else{
					this.validar.descripcion = false;
				}

				if ((this.razon).length === 0 || (this.razon === " ")){
					this.validar.razon = true;

				}else{
					this.validar.razon = false;
				}

				if ((this.ruc).length === 0 || (this.ruc === " ")){
					this.validar.ruc = true;

				}else{
					this.validar.ruc = false;
				}

				if ((this.ciudad).length === 0 || (this.ciudad === " ")){
					this.validar.ciudad = true;

				}else{
					this.validar.ciudad = false;
				}

				if ((this.direccion).length === 0 || (this.direccion === " ")){
					this.validar.direccion = true;

				}else{
					this.validar.direccion = false;
				}

				if ((this.telefono).length === 0 || (this.telefono === " ")){
					this.validar.telefono = true;

				}else{
					this.validar.telefono = false;
				}

				if((this.validar.descripcion === true) || (this.validar.razon === true) || (this.validar.ruc === true) || (this.validar.ciudad === true) || (this.validar.direccion === true) || (this.validar.telefono === true)){
					return false;
				}
			}
		},

		mounted(){

			Common.sucursalNuevaCommon().then(data=> {
		        	this.codigo = data.sucursal[0].CODIGO+1;
		        	this.btnguardar = true;
		        });
		}
	}
</script>
<style></style>