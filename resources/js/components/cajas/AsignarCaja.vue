<template>
	<div class="container mt-3">
		<div v-if="$can('caja.asignar') && $can('caja')" class="row">
			<div class="col-3">
				<div class="card shadow border-bottom-primary mb-3">
					<div class="text-center card-header">Asignar Caja</div>
			  		<div class="card-body">



			  			<div class="text-center">
			  				<!-- gggggggggggggggg -->
							<div class="text-center">
								<div class="form-group">
								    <label for="inputEmail3" class="col-sm col-form-label text-center">Número de Caja</label>
								    <div class="sm text-center">

								    	<!-- -------------------------- -->
								    	<!-- CODIGO ES CAJA -->
								    	<!-- -------------------------- -->

								      <input v-on:blur="existeCaja()" type="number" aling="center" :disabled="comprobar" class="form-control form-control-sm text-center" id="inputEmail3" v-model="caja.CODIGO" v-bind:class="{ 'is-invalid': validar.codigo}" placeholder="Caja" maxlength="2" oninput="if(this.value.length>this.maxLength)this.value=this.value.slice(0,this.maxLength);"/> <i>(Máximo 2 dígitos)</i>
								    </div>
								</div>
							</div>
						</div><br><br>
						<div class="custom-control custom-switch">
							<input type="checkbox" class="custom-control-input" id="recargarPaginaControlAutosizing" v-model="caja.RECARGAR" >
							<label class="custom-control-label text-center" for="recargarPaginaControlAutosizing">Recargar Pagina</label>
						</div>
						<div class="custom-control custom-switch">
							<input type="checkbox" class="custom-control-input" id="cantidadPersonalizadoControlAutosizing" v-model="caja.CANTIDAD_PERSONALIZADA">
							<label class="custom-control-label text-center" for="cantidadPersonalizadoControlAutosizing">Cantidad Personalizado</label>
						</div>
						<div class="texte ml-4">
							<div class="texte ml-1"><br>
								<label class="form-laber ml-2" type="text">Cantidad de ticket
									<div class="form-group text-center">
										<div class="form-check form-check-inline text-center">
										  	<input class="form-check-input" type="radio" name="inlineRadioOptions" v-model="caja.CANTIDAD_TICKET" id="inlineRadio1" value="1">
										  	<label class="form-check-label" for="inlineRadio1">1</label>
										</div>
										<div class="form-check form-check-inline">
										  	<input class="form-check-input" type="radio" name="inlineRadioOptions" v-model="caja.CANTIDAD_TICKET" id="inlineRadio2" value="2">
										  	<label class="form-check-label" for="inlineRadio2">2</label>
										</div>

									</div>
								</label>
							</div>
						</div>
						<div class="text-center">
							<button type="button" v-if="btn_asignar" aling="center" class="btn btn-primary" :disabled="comprobarbtn" v-on:click="asignar">Asignar</button>
							<button type="button" v-else class="btn btn-warning" v-on:click="asignar()">Modificar</button>
		  					<button type="button" class="btn btn-danger" v-on:click="quitarCaja()">Quitar</button>
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

				caja: {

					//---------------
					// CODIGO ES CAJA
					//---------------

	         		CODIGO: null,
	         		CANTIDAD_PERSONALIZADA: 0,
	         		CANTIDAD_TICKET:1,
	         		RECARGAR: 0
         		},
				btn_asignar: true,
				IP_PC: 0,
				comprobar:false,
				comprobarbtn: true,
				validar:{
					codigo:false,
					existe:false,
				},
				controlar:true
			}
		},
		methods: {
			controlador(){
				let me=this;
				if (me.caja.CODIGO==='' || me.caja.CODIGO===null || me.caja.CODIGO.length==='' || me.validar.existe===true || me.caja.CODIGO==='0') {
					me.validar.codigo= true;
					me.controlar=false;
					return false;
				}else{
					me.validar.codigo=false;
				}
				// console.log(me.controlar);
			},

			existeCaja(){
				console.log(this.IP_PC);
				// if(this.controlador()===false){
				// 	this.controlar=true;
				// 	return;
				// }
				Common.existeCajaCommon(this.caja.CODIGO).then(data=>{
					if(data.response===false){
						this.validar.codigo= true;
						this.controlar=false;
						this.validar.existe=true;
						

					}else{
						this.validar.existe=false;
						this.validar.codigo= false;
						this.controlar=true;
						this.comprobarbtn= false;
						
					}
				});



			},

			asignar(){
				if(this.controlador()===false){
					this.controlar=true;
					return;
				}
				var datos={
					//---------------
					// CODIGO ES CAJA
					//---------------
					CODIGO:this.caja.CODIGO,
					CANTIDAD_PERSONALIZADA:this.caja.CANTIDAD_PERSONALIZADA,
					CANTIDAD_TICKET:this.caja.CANTIDAD_TICKET,
					RECARGAR:this.caja.RECARGAR,
					btn_asignar:this.btn_asignar,
					IP_PC:this.IP_PC
				}
				console.log(datos.CODIGO);
				console.log(datos.CANTIDAD_PERSONALIZADA);
				console.log(datos.CANTIDAD_TICKET);
				console.log(datos.RECARGAR)
				Common.asignarCajaCommon(datos).then(data=>{

					if(data.response===true){
						Swal.fire(
							'¡Guardado!',
	                     	'¡Se ha guardado correctamente la caja!',
	                     	'success'
						)
						this.obtenerCaja();
						// window.location.href = '/AsignacionDeCaja';
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

			quitarCaja(){
				console.log(this.caja.CODIGO);
				if(this.controlador()===false){
					this.controlar=true;
					Swal.fire(
						      '¡Atención!',
						      '¡No se posee ningun registro para aplicar esta acción!',
						      'success'
					)
					return;
				}
				var datos={
					CODIGO:this.caja.CODIGO,
					btn_asignar:this.btn_asignar
				}
				Swal.fire({
				  title: '¿Estás seguro?',
				  text: "¡Liberar la caja número " + this.caja.CODIGO +"!",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  confirmButtonColor: '#d33',
				  confirmButtonText: '¡Sí, quitar!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
    				// LLAMAR A UNA FUNCION PARA ELIMINAR LOS DATOS
				    return Common.quitarCajaCommon(datos).then(data => {
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
						      '¡Se ha liberado correctamente la caja!',
						      'success'
					)

				  	// ------------------------------------------------------------------------

				  	// RECARGAR PAGINA 
				  	this.obtenerCaja();
				  	this.btn_asignar=true;
    				// window.location.href = '/AsignacionDeCaja';

					// ------------------------------------------------------------------------

				  }
				})

			},

			
			obtenerCaja() {

      			let me=this;

          		// OBTENER CAJA 
          		Common.obtenerIPCommon(function(){

          			// OBTENER IP
          			if (window.IPv !== false) {
          				me.IP_PC=window.IPv;
          				axios.post('/cajaObtener', {'id': window.IPv}).then(function (response) {
	                	  if (response.data.response === true) {
	                	  	  me.comprobar=true;
	                	  	  me.caja.CODIGO  =   response.data.caja[0].CAJA;
	                	  	  me.caja.CANTIDAD_PERSONALIZADA  =   response.data.caja[0].CANTIDAD_PERSONALIZADA;
	                	  	  me.caja.CANTIDAD_TICKET = response.data.caja[0].CANTIDAD_TICKET;
	                	  	  me.caja.RECARGAR = response.data.caja[0].RECARGAR;
	                	  	  me.btn_asignar=false;

	                	  	  console.log(me.caja.CODIGO);
	                	  } else {
	                	  	  	Swal.fire({
									title: 'NO DISPONE DE UNA CAJA',
									type: 'warning',
									confirmButtonColor: '#d33',
									confirmButtonText: 'Aceptar',
								})
								me.caja.CODIGO  =   null;
		                	  	me.caja.CANTIDAD_PERSONALIZADA  =   0;
		                	  	me.caja.CANTIDAD_TICKET = 1;
		                	  	me.caja.RECARGAR = 0;
								me.controlar=false;
								me.comprobar=false;
	                	  }
			            })
          			} else {
          				Swal.fire({
							title: 'NO SE PUDO OBTENER LA IP DE LA MAQUINA',
							type: 'warning',
							confirmButtonColor: '#d33',
							confirmButtonText: 'Aceptar',
						}).then((result) => {	
							window.location.href = '/AsignacionDeCaja';
						})
          			}
                });
            },


            cargarCaja(valor){
				this.CODIGO=valor;
				this.btn_asignar=false;
			}
			
		},
		mounted(){
				let me = this;
				me.obtenerCaja();


		}
	}
</script>