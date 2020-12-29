<template>
  
	<div class="container">
  	  	<div v-if="$can('user.crear')">
       	  	<div class="offset-md-2 col-6">
        		<div class="card mt-3 shadow-sm">

	          		<!-- --------------------------------------- CABECERA DE LA TARJETA ------------------------------------------ -->

					<div class="card-header">
						<ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
							<li class="nav-item">
				                <a class="nav-link active" id="sucursal-tab" data-toggle="tab" href="#sucursal" role="tab" aria-controls="sucursal" aria-selected="true" v-on:click="limpiarSucursal">Sucursal</a>
				            </li>
				            <li class="nav-item">
				                <a class="nav-link" id="sector-tab" data-toggle="tab" href="#sector" role="tab" aria-controls="sector" aria-selected="true" v-on:click="limpiarSector">Sector</a>
				            </li>
				        </ul>
					</div>

	        		<div class="card-body">
							
						<div class="tab-content informacion-tab" id="myTabContent">
					   		<div class="tab-pane fade show active" id="sucursal" role="tabpanel" aria-labelledby="sucursal-tab">

								<!-- ------------------------------ TEXTBOX SUCURSAL RM ------------------------------- -->
							   	
							   	<div class="row mt-3">
							   		<div class="col-12">
								    	<label>ID Sucursal</label>
								    	<sucursal-rm ref="componente_textbox_sucursal_rm" :id="idSucursal" @id="cargarSucursalID" @descripcion="cargarDescSucursal" @desc_corta="cargarDesCortaSucursal" v-model='idSucursal' v-bind:class="{ 'is-invalid': validar.idSucursal }"></sucursal-rm>
							    	</div>
							    </div>

							    <!-- ------------------------------ DESCRIPCION SUCURSAL ------------------------------ -->
							   		
							   	<div class="row mt-3">
							   		<div class="col-12">
								    	<label>Descripción</label>
										<input type="text" v-model="sucursal" class="form-control form-control-sm"  v-bind:class="{ 'is-invalid': validar.sucursal }">
									</div>
							    </div>	

							    <!-- ---------------------------- DESCRIPCION CORTA SUCURSAL ------------------------- -->
							   		
							   	<div class="row mt-3">
							   		<div class="col-12">
								    	<label>Descripción Corta</label>
										<input type="text" v-model="descripCortaSucursal" class="form-control form-control-sm"  v-bind:class="{ 'is-invalid': validar.descripCortaSucursal }">
									</div>
							    </div>

							    <!-- ------------------------------ BOTONES SUCURSAL RM ------------------------------ -->

								<div class="row mt-4">
									<div class="col-12 text-right">
								    	<button type="button" v-on:click="limpiarSucursal()" class="btn btn-primary">Nuevo</button>
								    	<button v-if="btnguardarSucursal" v-on:click="guardarSucursal()" type="button" class="btn btn-success">Guardar</button>
								    	<button v-else  type="button" v-on:click="guardarSucursal()" class="btn btn-warning">Modificar</button>
								    	<button  v-on:click="eliminarSucursal()" type="button" class="btn btn-danger">Eliminar</button>
								    </div>
							    </div>
							</div>

							<div class="tab-pane fade" id="sector" role="tabpanel" aria-labelledby="sector-tab">

								<!-- --------------------------- TEXTBOX SECTOR RM ---------------------------------- -->
							   		
							   	<div class="row mt-3">
							   		<div class="col-12">
								    	<label>ID Sector</label>
								    	<sector-rm ref="componente_textbox_sector_rm" :id="idSector" @id="cargarSectorID" @descripcion="cargarDescSector" @desc_corta="cargarDesCortaSector" v-model='idSector' v-bind:class="{ 'is-invalid': validar.idSector }"></sector-rm>
							    	</div>
							    </div>

							    <!-- ------------------------------ DESCRIPCION SECTOR ---------------------------------- -->
							   		
							   	<div class="row mt-3">
							   		<div class="col-12">
								    	<label>Descripción</label>
										<input type="text" v-model="sector" class="form-control form-control-sm"  v-bind:class="{ 'is-invalid': validar.sector }">
									</div>
							    </div>	

							    <!-- ---------------------------- DESCRIPCION CORTA SECTOR ----------------------------- -->
							   		
							   	<div class="row mt-3">
							   		<div class="col-12">
								    	<label>Descripción Corta</label>
										<input type="text" v-model="descripCortaSector" class="form-control form-control-sm"  v-bind:class="{ 'is-invalid': validar.descripCortaSector }">
									</div>
							    </div>

							    <!-- ---------------------------- BOTONES SECTOR ----------------------------- -->

								<div class="row mt-4">
									<div class="col-12 text-right">
								    	<button type="button" v-on:click="limpiarSector()" class="btn btn-primary">Nuevo</button>
								    	<button v-if="btnguardarSector" v-on:click="guardarSector()" type="button" class="btn btn-success">Guardar</button>
								    	<button v-else  type="button" v-on:click="guardarSector()" class="btn btn-warning">Modificar</button>
								    	<button  v-on:click="eliminarSector()" type="button" class="btn btn-danger">Eliminar</button>
								    </div>
							    </div>
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
				idSucursal: '',
				idSector: '',
				sucursal: '',
				sector: '',
				descripCortaSucursal: '',
				descripCortaSector: '',
				validar: {
					idSector: false,
					idSucursal: false,
					sucursal: false,
					sector: false,
					descripCortaSucursal: false,
					descripCortaSector: false,
				},
				btnguardarSector: true,
				existeSector: false,
				btnguardarSucursal: true,
				existeSucursal: false,
				controlarSucursal: true,
				controlarSector: true,
			}
		},

		methods: {

			eliminarSector(){

				let me = this;

				if(me.controladorSector() === false){
					me.controlarSector = true;
					return;
				}

				// GUARDA LOS DATOS EN UNA VARIABLE

    			var eliminar = {
					id: me.idSector,
					descripcion: me.sector,
					desc_corta: me.descripCortaSector,
					existe: me.existeSector
    			}

    			// MOSTRAR LA PREGUNTA

      			Swal.fire({
				  title: '¿Estás seguro?',
				  text: "¡Eliminar el sector " + me.sector + "!",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  cancelButtonColor: 'btn btn-success',
				  confirmButtonColor: '#d33',
				  confirmButtonText: '¡Sí, eliminar!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
    				// LLAMAR A UNA FUNCION PARA ELIMINAR LOS DATOS
				    return Common.eliminarSectoresRmCommon(eliminar).then(data => {
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
						      '¡Se ha eliminado correctamente el sector!',
						      'success'
					)

    				me.limpiarSector();

					// ------------------------------------------------------------------------

				  }
				})
			},

			eliminarSucursal(){

				let me = this;

				if(me.controladorSucursal() === false){
					me.controlarSucursal = true;
					return;
				}

				// GUARDA LOS DATOS EN UNA VARIABLE

    			var eliminar = {
					id: me.idSucursal,
					descripcion: me.sucursal,
					desc_corta: me.descripCortaSucursal,
					existe: me.existeSucursal
    			}

    			// MOSTRAR LA PREGUNTA

      			Swal.fire({
				  title: '¿Estás seguro?',
				  text: "¡Eliminar la sucursal " + me.sucursal + "!",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  cancelButtonColor: 'btn btn-success',
				  confirmButtonColor: '#d33',
				  confirmButtonText: '¡Sí, eliminar!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
    				// LLAMAR A UNA FUNCION PARA ELIMINAR LOS DATOS
				    return Common.eliminarSucursalesRmCommon(eliminar).then(data => {
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
						      '¡Se ha eliminado correctamente la sucursal!',
						      'success'
					)


    				me.limpiarSucursal();

					// ------------------------------------------------------------------------

				  }
				})
			},

			controladorSucursal(){

				let me = this;

				if (me.idSucursal === '' || me.idSucursal === " "){
					me.validar.idSucursal = true;
					me.controlarSucursal = false;

				}else{
					me.validar.idSucursal = false;
				}

				if (me.sucursal === '' || me.sucursal === " "){
					me.validar.sucursal = true;
					me.controlarSucursal = false;

				}else{
					me.validar.sucursal = false;
				}

				if(me.descripCortaSucursal === '' || me.descripCortaSucursal === " "){
					me.validar.descripCortaSucursal = true;
					me.controlarSucursal = false;
				}else{
					me.validar.descripCortaSucursal =  false;
				}
				
				return me.controlarSucursal;
			},

			controladorSector(){

				let me = this;

				if (me.idSector === '' || me.idSector === " "){
					me.validar.idSector = true;
					me.controlarSector = false;

				}else{
					me.validar.idSector = false;
				}

				if (me.sector === '' || me.sector === " "){
					me.validar.sector = true;
					me.controlarSector = false;

				}else{
					me.validar.sector = false;
				}

				if (me.descripCortaSector === '' || me.descripCortaSector === " "){
					me.validar.descripCortaSector = true;
					me.controlarSector = false;

				}else{
					me.validar.descripCortaSector = false;
				}
				
				return me.controlarSector;
			},

			limpiarSucursal(){

				let me = this;

				me.idSucursal = '';
				me.sucursal = '';
				me.descripCortaSucursal = '';
				me.validar.idSucursal = false;
				me.validar.sucursal = false;
				me.validar.descripCortaSucursal = false;
				me.btnguardarSucursal = true;
				me.existeSucursal = false;
				me.controlarSucursal = true;
				me.nuevaSucursalRm();
				
				// ------------------------------------------------------------------------

				// RECARGAR TABLA 
				  	
            	me.$refs.componente_textbox_sucursal_rm.recargar();
			},

			limpiarSector(){

				let me = this;

				me.idSector = '';
				me.sector = '';
				me.descripCortaSector = '';
				me.validar.idSector = false;
				me.validar.sector = false;
				me.validar.descripCortaSector = false;
				me.btnguardarSector = true;
				me.existeSector = false;
				me.controlarSector = true;
				me.nuevoSectorRm();

				// ------------------------------------------------------------------------

				// RECARGAR TABLA 
				  	
            	me.$refs.componente_textbox_sector_rm.recargar();
			},

			// GUARDAR Y MODIFICAR

			guardarSucursal(){

				let me = this;

				// CONTROL DE DATOS NULOS

				if(me.controladorSucursal() === false){
					me.controlarSucursal = true;
					return;
				}

				// CARGA TODOS LOS DATOS EN UNA VARIABLE

				var data = {
					id: me.idSucursal,
					descripcion: me.sucursal,
					desc_corta: me.descripCortaSucursal,
					existe: me.existeSucursal
				}

				// *******************************************************************

				// ENVIA LOS DATOS PARA GUARDAR O MODIFICAR

				Common.guardarSucursalRmCommon(data).then(data=>{

    				// CONFIRMAR QUE GUARDÓ O SI HAY UN ERROR

	                if(data.response===true){

	                	// *******************************************************************

	                  	Swal.fire(
	                     	'¡Guardado!',
	                     	'¡Se ha guardado correctamente la Sucursal!',
	                     	'success'
	                  	)	
	                    
	                    // *******************************************************************

	                    // LIMPIAR DATOS

	                  	me.limpiarSucursal();

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

			guardarSector(){

				let me = this;

				// CONTROL DE DATOS NULOS

				if(me.controladorSector() === false){
					me.controlarSector = true;
					return;
				}

				// CARGA TODOS LOS DATOS EN UNA VARIABLE

				var data = {
					id: me.idSector,
					descripcion: me.sector,
					desc_corta: me.descripCortaSector,
					existe: me.existeSector
				}

				// *******************************************************************

				// ENVIA LOS DATOS PARA GUARDAR O MODIFICAR

				Common.guardarSectorRmCommon(data).then(data=>{

    				// CONFIRMAR QUE GUARDÓ O SI HAY UN ERROR

	                if(data.response===true){

	                	// *******************************************************************

	                  	Swal.fire(
	                     	'¡Guardado!',
	                     	'¡Se ha guardado correctamente el Sector!',
	                     	'success'
	                  	)	
	                    
	                    // *******************************************************************

	                    // LIMPIAR DATOS

	                  	me.limpiarSector();

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
	                me.mostrar_error = true;
	                me.mensaje = err;
             	});
								
			},
			
			cargarSucursalID(valor){
				
				this.idSucursal = valor;
				this.btnguardarSucursal = false;
				this.existeSucursal = true;
			},

			cargarSectorID(valor){

				this.idSector = valor;
				this.btnguardarSector = false;
				this.existeSector = true;
			},

			cargarDescSucursal(valor){

				this.sucursal = valor;
			},

			cargarDescSector(valor){

				this.sector = valor;
			},

			cargarDesCortaSucursal(valor){

				this.descripCortaSucursal = valor;
			},

			cargarDesCortaSector(valor){

				this.descripCortaSector = valor;
			},

			nuevoSectorRm(){

				Common.nuevoSectorRmCommon().then(data=> {
					if(data !== 1){
		        		this.idSector = data.sector[0].ID+1;
					}else{
						this.idSector = data;
					}
		        	this.btnguardarSector = true;
		        });	
			},

			nuevaSucursalRm(){

				Common.nuevaSucursalRmCommon().then(data=> {
					if(data !== 1){
		        		this.idSucursal = data.sucursal[0].ID+1;
					}else{
						this.idSucursal = data;
					}
		        	this.btnguardarSucursal = true;
		        });	
			}
		},

		mounted(){
			this.nuevaSucursalRm();	
		}
	}
</script>