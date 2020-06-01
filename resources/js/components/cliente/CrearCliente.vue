<template>
	<div class="container">
		
		<div class="offset-md-2 col-8">
					
			<div class="card mt-3 shadow-sm">

				<!-- -------------------------------------------- CABECERA DE LA TARJETA ----------------------------------------------- -->

				<div class="card-header">
				    <ul class="nav nav-tabs card-header-tabs">
				      <li class="nav-item">
				        <a class="nav-link active">Datos del Cliente</a>
				      </li>
				      <li class="nav-item">
				        <a class="nav-link"">Imagen</a>
				      </li>
				      <li class="nav-item">
				        <a class="nav-link">Saldo Inicial</a>
				      </li>
				    </ul>
				</div>

				<!-- -------------------------------------------- CUERPO DE LA TARJETA ------------------------------------------------- -->

			  	<div class="card-body">

			  		<!-- ------------------------------------------ TEXTBOX DE CODIGO -------------------------------------------------- -->

			    	<div class="form-group row">
			    		<label class="col-sm-3 col-form-label">Código</label>
			    		<div class="col-sm-5">
					    	<cliente-filtrar ref="componente_textbox_cliente" :codigo="codigo" @codigo="cargarCodigo" @nombre="cargarNombre" @cedula="cargarCi" @ruc="cargarRuc" @direccion="cargarDireccion" @ciudad="cargarCiudad" @telefono="cargarTelefono" @celular="cargarCelular" @email="cargarEmail" @tipo="cargarTipo" @limite="cargarLimite"v-model='codigo' v-bind:class="{ 'is-invalid': validar.codigo }" ></cliente-filtrar>
					    </div>
			    	</div>

			   		<!-- -------------------------------------- INPUT DE NOMBRE Y APELLIDO --------------------------------------------- -->
			   		
			   		<div class="form-group row mt-3">
			    		<label class="col-sm-3 col-form-label" >Nombre y Apellido</label>
			    		<div class="col-sm-9">
					    	<input type="text" v-model="cliente" v-bind:class="{ 'is-invalid': validar.cliente }" class="form-control form-control-sm">
					    </div>
			    	</div>

			    	<!-- --------------------------------------- INPUT DE NRO DE DOCUMENTO --------------------------------------------- -->

			    	<div class="form-group row mt-3">
			    		<label class="col-sm-3 col-form-label" >Nro. C.I.</label>
			    		<div class="col-sm-5">
					    	<input type="text" v-model="ci" v-bind:class="{ 'is-invalid': validar.ci }" class="form-control form-control-sm">
					    </div>
			    	</div>	

			    	<!-- --------------------------------------------- INPUT DE RUC ---------------------------------------------------- -->
			   		
			   		<div class="form-group row mt-3">
			    		<label class="col-sm-3 col-form-label" >R.U.C.</label>
			    		<div class="col-sm-5">
					    	<input type="text" v-model="ruc" v-bind:class="{ 'is-invalid': validar.ruc }" class="form-control form-control-sm">
					    </div>
			    	</div>	

			    	<!-- ---------------------------------------- INPUT DE DIRECCION ----------------------------------------------- -->

			   		<div class="form-group row mt-3">
			    		<label class="col-sm-3 col-form-label" >Dirección</label>
			    		<div class="col-sm-9">
					    	<input type="text" v-model="dire" v-bind:class="{ 'is-invalid': validar.dire }" class="form-control form-control-sm">
					    </div>
			    	</div>	

			    	<!-- ---------------------------------------- INPUT DE CIUDAD/BARRIO ----------------------------------------------- -->

			   		<div class="form-group row mt-3">
			    		<label class="col-sm-3 col-form-label" >Ciudad/Barrio</label>
			    		<div class="col-sm-5">
					    	<input type="text" v-model="ciud" class="form-control form-control-sm">
					    </div>
			    	</div>	

			   		
			   		<!-- -------------------------------------------- INPUT DE TELEFONO ------------------------------------------------ -->
			   		
			   		<div class="form-group row mt-3">
			    		<label class="col-sm-3 col-form-label">Teléfono</label>
			    		<div class="col-sm-5">
					    	<input type="text" v-model="tel" v-bind:class="{ 'is-invalid': validar.tel }" class="form-control form-control-sm">
					    </div>
			    	</div>

			    	<!-- -------------------------------------------- INPUT DE CELULAR ------------------------------------------------- -->

			    	<div class="form-group row mt-3">
			    		<label class="col-sm-3 col-form-label" >Celular</label>
			    		<div class="col-sm-5">
					    	<input type="text" v-model="cel" class="form-control form-control-sm">
					    </div>
			    	</div>			
			   		
			   		<!-- --------------------------------------------- INPUT DE EMAIL -------------------------------------------------- -->

			   		<div class="form-group row mt-3">
			    		<label class="col-sm-3 col-form-label" >Email</label>
			    		<div class="col-sm-5">
					    	<input type="text" v-model="ema" class="form-control form-control-sm">
					    </div>
			    	</div>

			    	<!-- ---------------------------------------------- SELECT DE TIPO ------------------------------------------------ -->

			    	
			    	<div class="form-group row mt-3">
			    		<label class="col-sm-3 col-form-label">Tipo</label>
			    		<div class="col-sm-5">
					    	<select v-model="tipo" class="custom-select custom-select-sm">
			    				<option>OCASIONAL</option>
			  	  				<option>EXTRANJERO</option>
			    				<option>MAYORISTA</option>
		  					</select>
					    </div>
			    	</div>			
			   		
			   		<!-- --------------------------------------- INPUT DE LIMITE DE CREDITO -------------------------------------------- -->
			   		
			   		<div class="form-group row mst-3">
			    		<label class="col-sm-3 col-form-label">Límite de Crédito</label>
			    		<div class="col-sm-5">
					    	<input type="text" v-model="limit" class="form-control form-control-sm">
					    </div>
			    	</div>

			    	<!-- ------------------------------ BOTONES NUEVO, GUARDAR, MODIFICAR Y ELIMINAR ----------------------------------- -->
					<div class="row mt-4">

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
	export default{
		props: ['candec'],
		data(){

			return{

				cliente: '',
				codigo: '',
				ci: '',
				ciud: '',
				ruc: '',
				dire: '',
				tel: '',
				cel: '',
				ema:'',
				tipo: 'OCASIONAL',
				limit: '',
				btnguardar: true,
				existe: false,
				validar: {
					cliente: false,
					codigo: false,
					ci: false,
					ruc: false,
					dire: false,
					tel: false
				}
			}
		},

		methods: {
			
			// GUARDAR Y MODIFICAR

			guardar(){


				// CONTROL DE DATOS NULOS

				if(this.controlador() === false){
					return;
				}

				// CARGA TODOS LOS DATOS EN UNA VARIABLE

				var data = {

					codigo: this.codigo,
					name: this.cliente,
					cedula: this.ci,
					direccion: this.dire,
					ciudad: this.ciud,
					ruc: this.ruc,
					telefono: this.tel,
					celular: this.cel,
					email: this.ema,
					tipo: this.tipo,
					limite: this.limit,
					existe: this.existe
				}

				// ENVIA LOS DATOS PARA GUARDAR O MODIFICAR

				Common.guardarClienteCommon(data).then(data=>{


    				// CONFIRMAR QUE GUARDÓ O SI HAY UN ERROR

	                if(data.response===true){

	                  	Swal.fire(
	                     	'¡Guardado!',
	                     	'¡Se ha guardado correctamente el cliente!',
	                     	'success'
	                  	)
	                  	this.existe = true;
	                  	this.btnguardar = false;
	                  	this.limpiar();

            			this.$refs.componente_textbox_cliente.recargar();

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
								
			},

			eliminar(){

				// GUARDA LOS DATOS EN UNA VARIABLE

    			var eliminar = {

    				codigo: this.codigo,
    				nombre: this.cliente,
    				existe: this.existe
    			}


    			// LLAMAR A UNA FUNCION PARA ELIMINAR LOS DATOS

    			Common.eliminarClienteCommon(eliminar).then(data => {

    				// MENSAJE DE CONFIRMACION O ERROR

    				if(data.response===true){
	                  	Swal.fire(
		                    '¡Eliminado!',
		                    '¡Se ha eliminado correctamente el cliente!',
		                    'success'
	                  	)
            			this.$refs.componente_textbox_cliente.recargar();
	               	}else{
		                Swal.fire(
		                    '¡Error!',
		                    data.statusText,
		                    'warning'
		                )
	               	}

	               	// MENSAJE DE ERROR EN LA FUNCION

    			}).catch((err) => {
	                console.log(err);
	                this.mostrar_error = true;
	                this.mensaje = err;
              	});

    			this.limpiar();

			},

			// CONTROL DE INPUT NULOS

			controlador(){

				if ((this.cliente).length === 0 || (this.cliente === " ")){
					this.validar.cliente = true;

				}else{
					this.validar.cliente = false;
				}

				if ((this.codigo).length === 0 || (this.codigo === " ")){
					this.validar.codigo = true;

				}else{
					this.validar.codigo = false;
				}

				if (((this.ci) === '' || (this.ci === " ")) && (this.ruc) === ''){
					this.validar.ci = true;

				}else{
					this.validar.ci = false;
				}

				if ((this.dire).length === 0 || (this.dire === " ")){
					this.validar.dire = true;

				}else{
					this.validar.dire = false;
				}

				if (((this.ruc) === '' || (this.ruc === " ")) && (this.ci) === ''){
					this.validar.ruc = true;

				}else{
					this.validar.ruc = false;
				}

				if((this.tel).length === 0 || (this.tel)=== " "){
					this.validar.tel = true;
				}else{
					this.validar.tel =  false;
				}

				if ((this.validar.codigo === true) || (this.validar.cliente === true) || (this.validar.ci === true) || (this.validar.ruc === true) || (this.validar.dire === true) || (this.validar.tel === true)){
					return false;
				}

			},

			// REINICIAR EL FORMULARIO

			limpiar(){

				this.cliente = '';
				this.ci = '';
				this.ciud = '';
				this.ruc = '';
				this.dire = '';
				this.tel = '';
				this.cel = '';
				this.ema = '';
				this.tipo = 'OCASIONAL';
				this.limit = '';
				this.btnguardar = true;
				this.existe = false;
				this.validar.cliente = false;
				this.validar.codigo = false;
				this.validar.ci = false;
				this.validar.ciud = false;
				this.validar.ruc = false;
				this.validar.dire = false;
				this.validar.tel = false;
				this.validar.cel = false;
				this.validar.ema = false;
				this.validar.tipo = false;
				this.validar.limit = false;
				Common.clienteNuevoCommon().then(data=> {
		        	this.codigo = data.cliente[0].CODIGO+1;
		        	this.btnguardar = true;
		        });		

			},

			// FILTRA LOS DATOS DEL DATATABLE AL INPUT

			cargarCodigo(valor){

				this.codigo = valor;
				this.btnguardar = false;
				this.existe = true;
			},

			cargarNombre(valor){

				this.cliente = valor;
			},
			
			cargarCi(valor){ 

				this.ci = valor;
			},
			
			cargarRuc(valor){

				this.ruc = valor;
			},
			
			cargarDireccion(valor){

				this.dire = valor;
			},
			
			cargarCiudad(valor){

				this.ciud = valor;
			},
			
			cargarTelefono(valor){

				this.tel = valor;
			},
			
			cargarCelular(valor){

				this.cel = valor;
			},
			
			cargarEmail(valor){

				this.ema = valor;
			},
			
			cargarTipo(valor){

				this.tipo = valor;
			},
			
			cargarLimite(valor){

				this.limit = valor;
			}
		},

		mounted(){

			Common.clienteNuevoCommon().then(data=> {
		        	this.codigo = data.cliente[0].CODIGO+1;
		        	this.btnguardar = true;
		        });
		}

	}
</script>