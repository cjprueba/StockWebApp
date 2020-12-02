<template>
  
	<div class="container">
  	  	<div v-if="$can('user.crear')">
       		<div class="offset-md-3 col-6">
          		<div class="card mt-3 shadow-sm">
        			<h5 class="card-header">Registro de Máquina</h5>
        			<div class="card-body">

        				<div class="row">
        					<div class="col-sm">
        						<label>ID Registro</label>
								<registro-maquinas ref="componente_textbox_rm" :id="idRegistro" @id="cargarRegistroID" @sucursal="cargarSucursalID" @sector="cargarSectorID" @caracteristica="cargarCaracteristica" @usuario="cargarUsuario" @nombre="cargarNombre" @ip="cargarIP" v-model='idRegistro' v-bind:class="{ 'is-invalid': validar.idRegistro }"></registro-maquinas>
        					</div>
        				</div>
				   		<div class="row mt-3">

				   			<!-- -------------------------------- SELECCIONAR SUCURSAL----------------------------------- -->
							
							<div class="col-sm">
						  		<label for="validationTooltip01">Sucursal</label>
								<select class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validar.sucursal }" v-model="selectedSucursal" v-on:change='ultimoRegistroSucursal'>
									<option value="null" selected>Seleccionar</option>
									<option v-for="sucursal in sucursales" :selected="sucursal === filtroSucursal" :value="sucursal">{{sucursal.ID}}. {{ sucursal.DESCRIPCION }}</option>
								</select>
								<div class="invalid-feedback">
								    {{messageInvalidSucursal}}
								</div>
							</div>
				   			<!-- ---------------------------------- INPUT SECTOR -------------------------------------- -->

				   			<div class="col-sm">
						  		<label for="validationTooltip01">Sector</label>
								<select class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validar.sector }" v-model="selectedSector" v-on:change='ultimoRegistroSector'>
									<option value="null" selected>Seleccionar</option>
									<option v-for="sector in sectores" :selected="sector === filtroSector" :value="sector">{{sector.ID}}. {{ sector.DESCRIPCION }}</option>
								</select>
								<div class="invalid-feedback">
								    {{messageInvalidSector}}
								</div>
							</div>
				    	</div>

				   		<div class="row mt-3">

				    		<!-- ---------------------------------------- CARACTERISTICAS ---------------------------- -->

				   			<div class="col-sm">
					    		<label>Características</label>
							    <textarea id="caracteristica" type="text" rows="4" class="form-control" v-bind:class="{ 'is-invalid': validar.caracteristica }" v-model="caracteristica"></textarea>
							</div>

				    		<!-- ------------------------------------ INPUT IP ASIGNADA ------------------------------ -->
							
							<div class="col-sm">
					    		<label>IP Asignada</label>
				    			<input type="text" v-model="ip" v-bind:class="{ 'is-invalid': validar.ip }" class="form-control form-control-sm">

				    			<!-- ---------------------------------- INPUT USUARIO -------------------------------- -->

					    		<label class="mt-3">Usuario</label>
							    <input type="text" v-model="usuario" v-bind:class="{ 'is-invalid': validar.usuario }" class="form-control form-control-sm">
					    	</div>
				    	</div>

				    	<!-- ---------------------------------------- INPUT NOMBRE ------------------------------------ -->
				   		
				   		<div class="row mt-3">
				   			<div class="col-sm">
					    		<label>Nombre</label>
							    <input type="text" v-model="nombre" class="form-control form-control-sm" v-bind:class="{ 'is-invalid': validar.nombre }">
							</div>
				    	</div>	

				    	<!-- ------------------------ BOTONES NUEVO, GUARDAR, MODIFICAR Y ELIMINAR -------------------- -->
					  
						<div class="row mt-4">
							<div class="col-12 text-right">
						    	<button v-on:click="limpiar()" type="button" class="btn btn-primary">Nuevo</button>
						    	<button v-on:click="guardar" v-if="btnguardar" type="button" class="btn btn-success">Guardar</button>
						    	<button v-else v-on:click="guardar" type="button" class="btn btn-warning">Modificar</button>
						    	<button type="button" v-on:click="eliminar()" class="btn btn-danger">Eliminar</button>
						    </div>
					    </div>
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
				sucursales: [],
				idRegistro:'',
              	selectedSucursal: 'null',
              	messageInvalidSucursal: '',
				sectores: [],
              	selectedSector: 'null',
              	messageInvalidSector: '',
				nombre:'',
				ip: '',
				caracteristica: '',
				usuario: '',
				ultimoCodigoSucursal: '',
				ultimoCodigoSector: '',
				validar: {
					sucursal: false,
					sector: false,
					nombre:false,
					ip: false,
					caracteristica: false,
					usuario: false,
					idRegistro: false
				},
				btnguardar: true,
				existe: false,
				controlar: true,
				filtroSector: 'null',
				filtroSucursal: 'null'
			}
		},

		methods: {

			llamarBusquedas(){	
	          axios.get('busqueda-sector-sucursal/').then((response) => {
	           	this.sucursales = response.data.sucursales;
	           	this.sectores = response.data.sectores;
	          });
	        },

	        ultimoRegistroSucursal(){

	        	var data = this.selectedSucursal.ID;

				Common.obtenerRegistroSucursalRmCommon(data).then(data=> {

		        	this.ultimoCodigoSucursal = data.sucursal[0].CANTIDAD+1;
					this.nombre = this.selectedSucursal.DESC_CORTA+''+ 
								  this.ultimoCodigoSucursal+'-' +
								  this.selectedSector.DESC_CORTA+''+
								  this.ultimoCodigoSector;
		        });	

			},

			ultimoRegistroSector(){

				if (this.selectedSucursal === 'null'){
					this.validar.sucursal = true;
	        		this.messageInvalidSucursal = 'Por favor seleccione sucursal';
	        		this.selectedSector = 'null'
	        		return;
				}

				var data = {
					sector: this.selectedSector.ID,
					sucursal: this.selectedSucursal.ID
				}

				Common.obtenerRegistroSectorRmCommon(data).then(data=> {

		        	this.ultimoCodigoSector = data.sector[0].CANTIDAD+1;
					this.nombre = this.selectedSucursal.DESC_CORTA+''+ 
								this.ultimoCodigoSucursal+'-' +  
								this.selectedSector.DESC_CORTA+''+ 
								this.ultimoCodigoSector;
		        });	
			},

			nuevoRegistro(){

				Common.obtenerUltimoRegistroCommon().then(data=> {
					if(data !== 1){
		        		this.idRegistro = data.registro[0].ID+1;
					}else{
						this.idRegistro = data;
					}
		        	this.btnguardar = true;
		        });	
			},

			controlador(){

				let me = this;

				if (me.selectedSucursal === 'null'){
					me.validar.sucursal = true;
	        		me.messageInvalidSucursal = 'Por favor seleccione sucursal';
					me.controlar = false;

				}else{
					me.validar.sucursal = false;
				}

				if (me.selectedSector === 'null'){
					me.validar.sector = true;
	        		me.messageInvalidSector = 'Por favor seleccione sector';
					me.controlar = false;

				}else{
					me.validar.sector = false;
				}

				if (me.nombre === '' || me.nombre === " "){
					me.validar.nombre = true;
					me.controlar = false;

				}else{
					me.validar.nombre = false;
				}
				
				if (me.caracteristica === '' || me.caracteristica === " "){
					me.validar.caracteristica = true;
					me.controlar = false;

				}else{
					me.validar.caracteristica = false;
				}

				if (me.usuario === '' || me.usuario === " "){
					me.validar.usuario = true;
					me.controlar = false;

				}else{
					me.validar.usuario = false;
				}

				if (me.ip === '' || me.ip === " "){
					me.validar.ip = true;
					me.controlar = false;

				}else{
					me.validar.ip = false;
				}

				return me.controlar;
			},
			
			limpiar(){

				let me = this;

              	me.selectedSucursal = 'null';
              	me.messageInvalidSucursal = '';
              	me.selectedSector = 'null';
              	me.messageInvalidSector = '';
				me.nombre ='';
				me.ip = '';
				me.caracteristica = '';
				me.usuario = '';
				me.ultimoCodigoSucursal = '';
				me.ultimoCodigoSector = '';
				me.validar.sucursal = false;
				me.validar.sector = false;
				me.validar.nombre =false;
				me.validar.ip = false;
				me.validar.caracteristica = false;
				me.validar.usuario = false;
				me.btnguardar = true;
				me.existe = false;

				// ------------------------------------------------------------------------

				// RECARGAR TABLA 
				  	
            	me.$refs.componente_textbox_rm.recargar();

				me.nuevoRegistro();
			},
			
			eliminar(){

				let me = this;

				// GUARDA LOS DATOS EN UNA VARIABLE

    			var eliminar = {
					id: me.idRegistro,
					existe: me.existe
    			}

    			// MOSTRAR LA PREGUNTA

      			Swal.fire({
				  title: '¿Estás seguro?',
				  text: "¡Eliminar el registro " + me.idRegistro + "!",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  cancelButtonColor: 'btn btn-success',
				  confirmButtonColor: '#d33',
				  confirmButtonText: '¡Sí, eliminar!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
    				// LLAMAR A UNA FUNCION PARA ELIMINAR LOS DATOS
				    return Common.eliminarRegistroMaquinaCommon(eliminar).then(data => {
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
						      '¡Se ha eliminado correctamente el registro!',
						      'success'
					)


    				me.limpiar();

					// ------------------------------------------------------------------------

				  }
				})
			},
			// GUARDAR Y MODIFICAR

			guardar(){

				let me = this;

				// CONTROL DE DATOS NULOS

				if(me.controlador() === false){
					me.controlar = true;
					return;
				}

				// CARGA TODOS LOS DATOS EN UNA VARIABLE

				var data = {

					id: me.idRegistro,
					idSucursal: me.selectedSucursal.ID,
					idSector: me.selectedSector.ID,
					nombre: me.nombre,
					ip: me.ip,
					caracteristica: me.caracteristica,
					usuario: me.usuario,
					existe: me.existe
				}

				// *******************************************************************

				// ENVIA LOS DATOS PARA GUARDAR O MODIFICAR

				Common.guardarMaquinaRmCommon(data).then(data=>{

    				// CONFIRMAR QUE GUARDÓ O SI HAY UN ERROR

	                if(data.response===true){

	                	// *******************************************************************

	                  	Swal.fire(
	                     	'¡Guardado!',
	                     	'¡Se ha guardado correctamente!',
	                     	'success'
	                  	)	
	                    
	                    // *******************************************************************

	                    // LIMPIAR DATOS

	                  	me.limpiar();

            			// *******************************************************************

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

			cargarRegistroID(valor){
				
				this.idRegistro = valor;
				this.btnguardar = false;
				this.existe = true;
			},

			cargarSectorID(valor){

				for (var i = this.sectores.length - 1; i >= 0; i--) {
					if(this.sectores[i]['ID'] === valor){
						this.selectedSector = this.sectores[i];
					}
				}
			},

			cargarSucursalID(valor){

				for (var i = this.sucursales.length - 1; i >= 0; i--) {
					if(this.sucursales[i]['ID'] === valor){
						this.selectedSucursal = this.sucursales[i];
					}
				}
			},

			cargarCaracteristica(valor){

				this.caracteristica = valor;
			},

			cargarUsuario(valor){

				this.usuario = valor;
			},

			cargarIP(valor){

				this.ip = valor;
			},

			cargarNombre(valor){

				this.nombre = valor;
			},
		},

		mounted(){
			this.llamarBusquedas();
			this.nuevoRegistro();
		}
	}
</script>