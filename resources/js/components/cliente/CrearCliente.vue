<template>
	<div v-if="$can('clientes.crear') && $can('clientes')" class="container">
		
		<div class="offset-md-2 col-8">
					
			<div class="card mt-3 shadow-sm">

				<!-- -------------------------------------------- CABECERA DE LA TARJETA ----------------------------------------------- -->

				<div class="card-header">
				    <ul class="nav nav-tabs card-header-tabs">
				      <li class="nav-item">
				        <a class="nav-link active">Datos del Cliente</a>
				      </li>
				      <li class="nav-item">
				        <a class="nav-link">Imagen</a>
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
			    		<label class="col-sm-3 col-form-label">Código Cliente</label>
			    		<div class="col-sm-9">
					    	<cliente-filtrar ref="componente_textbox_cliente" :codigo="codigo" @codigo="cargarCodigo" @nombre="cargarNombre" @cedula="cargarCi" @ruc="cargarRuc" @direccion="cargarDireccion" @ciudad="cargarCiudad" @telefono="cargarTelefono" @celular="cargarCelular" @email="cargarEmail" @tipo="cargarTipo" @limite="cargarLimite" @empresaID="cargarEmpresaID" @empresa="cargarEmpresa" @diaLimite="cargarLimiteDia" @creditoDisponible="cargarCreditoDisponible" @razonSocial="cargarRazonSocial" @retentor="cargarRetentor" v-model='codigo' v-bind:class="{ 'is-invalid': validar.codigo }"></cliente-filtrar>
					    </div>
			    	</div>

					<!-- ---------------------------------------------- SELECT DE TIPO ------------------------------------------------ -->
			    	
			    	<div class="form-group row mt-3">
			    		<label class="col-sm-3 col-form-label">Tipo</label>
			    		<div class="col-sm-9">
					    	<select v-model="tipo" class="custom-select custom-select-sm">
			    				<option>OCASIONAL</option>
			  	  				<option>EXTRANJERO</option>
			    				<option>MAYORISTA</option>
			    				<option>FUNCIONARIO</option>
		  					</select>
					    </div>
			    	</div>			
			   		
			    	<!-- --------------------------------------- TEXTBOX DE EMPLEADOS -------------------------------------------------- -->

			    	<!-- <div class="form-group row" v-if="tipo==='FUNCIONARIO' && existe===false">
			    		<label class="col-sm-3 col-form-label">Código Funcionario</label>
			    		<div class="col-sm-9">
					    	<empleado-filtrar ref="componente_textbox_empleado" :codigo="codigoEmpleado" @codigo="filtrarCodigo" @nombre="filtrarNombre" @cedula="filtrarCi" @direccion="filtrarDireccion" @ciudad="filtrarCiudad" @telefono="filtrarTelefono" @id_sucursal="filtrarID" v-model='codigoEmpleado'></empleado-filtrar>
					    </div>
			    	</div> -->

			    	<!-- ------------------------------------------- TEXTBOX DE EMPRESA------------------------------------------------- -->

			    	<div class="form-group row mt-3" v-if="tipo === 'FUNCIONARIO'">
			    		<label class="col-sm-3 col-form-label">Empresa</label>
			    		<div class="col-sm-9">
				    		<empresa-mostrar ref="componente_textbox_empresa" :nombre="empresa" @id="filtrarIdEmpresa" @nombre="filtrarNombreEmpresa" v-bind:class="{ 'is-invalid': validar.empresa }"></empresa-mostrar>
					    </div>
			    	</div>

			   		<!-- -------------------------------------- INPUT DE NOMBRE Y APELLIDO --------------------------------------------- -->
			   		
			   		<div class="form-group row mt-3">
			    		<label class="col-sm-3 col-form-label">Nombre y Apellido</label>
			    		<div class="col-sm-9">
					    	<input type="text" v-model="cliente" v-bind:class="{ 'is-invalid': validar.cliente }" class="form-control form-control-sm">
					    </div>
			    	</div>

			    	<!-- ---------------------------------- ----- INPUT DE NRO DE DOCUMENTO ------------------------------------------- -->

			    	<div class="form-group row mt-3">
			    		<label class="col-sm-3 col-form-label">Nro. C.I.</label>
			    		<div class="col-sm-9">
					    	<input v-on:change="calcularRUC(ci)" type="text" v-model="ci" v-bind:class="{ 'is-invalid': validar.ci }" class="form-control form-control-sm">
					    </div>
			    	</div>	

			    	<!-- --------------------------------------------- INPUT DE RUC --------------------------------------------------- -->
			   		
			   		<div class="form-group row mt-3">
			    		<label class="col-sm-3 col-form-label">R.U.C.</label>
			    		<div class="col-sm-9">
					    	<input type="text" v-model="ruc" v-bind:class="{ 'is-invalid': validar.ruc }" class="form-control form-control-sm">
					    </div>
			    	</div>	

			    	<!-- ------------------------------------------- INPUT DE RAZON SOCIAL -------------------------------------------- -->
			   		
			   		<div class="form-group row mt-3">
			    		<label class="col-sm-3 col-form-label">Razón Social</label>
			    		<div class="col-sm-9">
					    	<input type="text" v-model="razonSocial" class="form-control form-control-sm">
					    </div>
			    	</div>	

			    	<!-- ---------------------------------------- INPUT DE DIRECCION ----------------------------------------------- -->

			   		<div class="form-group row mt-3">
			    		<label class="col-sm-3 col-form-label">Dirección</label>
			    		<div class="col-sm-9">
					    	<input type="text" v-model="direccion" v-bind:class="{ 'is-invalid': validar.direccion }" class="form-control form-control-sm">
					    </div>
			    	</div>	

			    	<!-- ---------------------------------------- INPUT DE CIUDAD/BARRIO ----------------------------------------------- -->

			   		<div class="form-group row mt-3">
			    		<label class="col-sm-3 col-form-label">Ciudad/Barrio</label>
			    		<div class="col-sm-9">
					    	<input type="text" v-model="ciudad" class="form-control form-control-sm">
					    </div>
			    	</div>	
			   		
			   		<!-- -------------------------------------------- INPUT DE TELEFONO ------------------------------------------------ -->
			   		
			   		<div class="form-group row mt-3">
			    		<label class="col-sm-3 col-form-label">Teléfono</label>
			    		<div class="col-sm-9">
					    	<input type="text" v-model="telefono" v-bind:class="{ 'is-invalid': validar.telefono }" class="form-control form-control-sm">
					    </div>
			    	</div>

			    	<!-- -------------------------------------------- INPUT DE CELULAR ------------------------------------------------- -->

			    	<div class="form-group row mt-3">
			    		<label class="col-sm-3 col-form-label">Celular</label>
			    		<div class="col-sm-9">
					    	<input type="text" v-model="celular" class="form-control form-control-sm">
					    </div>
			    	</div>			
			   		
			   		<!-- --------------------------------------------- INPUT DE EMAIL -------------------------------------------------- -->

			   		<div class="form-group row mt-3">
			    		<label class="col-sm-3 col-form-label">Email</label>
			    		<div class="col-sm-9">
					    	<input type="text" v-model="email" class="form-control form-control-sm">
					    </div>
			    	</div>

			    	<!-- --------------------------------------- INPUT DE LIMITE DIA CREDITO ------------------------------------------ -->
			   		
			   		<div class="form-group row mst-3">
			    		<label class="col-sm-3 col-form-label">Día límite de Crédito</label>
			    		<div class="col-sm-9">
					    	<input type="number" v-model="limiteCreditoDia" class="form-control form-control-sm">
					    </div>
			    	</div>

			   		<!-- --------------------------------------- INPUT DE LIMITE DE CREDITO -------------------------------------------- -->
			   		
			   		<div class="form-group row mst-3">
			    		<label class="col-sm-3 col-form-label">Límite de Crédito</label>
			    		<div class="col-sm-9">
					    	<input type="text" v-model="limiteCredito" class="form-control form-control-sm">
					    </div>
			    	</div>

			    	<!-- ------------------------------------- INPUT DE CREDITO DISPONIBLE -------------------------------------------- -->
			   		
			   		<div class="form-group row mst-3" v-if="existe">
			    		<label class="col-sm-3 col-form-label">Crédito Disponible</label>
			    		<div class="col-sm-9">
					    	<input type="number" v-model="creditoDisponible" class="form-control form-control-sm" disabled>
					    </div>
			    	</div>

			    	<!-- --------------------------------------- RETENTOR -------------------------------------------- -->

			    	<div class="col-md-12">
			    		<hr>
			    	</div>

			    	<!-- --------------------------------------- RETENTOR -------------------------------------------- -->

			    	<div class="row mst-3">
			    		<label class="col-sm-3 col-form-label"></label>
			    		<div class="col-sm-9">
			    			<div class="custom-control custom-switch mr-sm-3">
									<input type="checkbox" class="custom-control-input" id="switchRetentor" v-model="switcher.retentor">
									<label class="custom-control-label" for="switchRetentor">Retentor</label>
								</div>
					    </div>
			    	</div>

			    	<!-- --------------------------------------- CHECKED DE UBICACION -------------------------------------------- -->
					<!-- 
			    	<div class="row ml-2 my-4">
						<div class="custom-control custom-switch mr-sm-2">
							<input type="checkbox" class="custom-control-input" id="switchUbicacion" v-model="checkedUbicacion">
							<label class="custom-control-label" for="switchUbicacion">Agregar Ubicación</label>
						</div>
					</div>
 					-->
			    	<!-- --------------------------------------- COORDENADAS DEL MAPA -------------------------------------------- -->


				    <!-- <div style="max-width: 800px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between">

			            <div class="col-auto">
			                <label><strong>Tus coordenadas:</strong></label>
			                <p>Latitud: {{ myCoordinates.lat }} <br>Longitud: {{ myCoordinates.lng }}</p>
			            </div>
			            <div class="col-auto">
			                <label><strong>Coordenadas del Mapa:</strong></label>
			                <p>Latitud:{{ mapCoordinates.lat }} <br>Longitud: {{ mapCoordinates.lng }}</p>
			            </div>

			        </div> -->
			    	<!-- --------------------------------------- MAPA -------------------------------------------- -->

					<!-- <div>
				        <GmapMap
				            :center="myCoordinates"
				            :zoom="zoom"
				            style="width:640px; height:360px; margin: 32px auto;"
				            ref="mapRef"
				            @dragend="handleDrag">
				        	 <GmapMarker
							    :key=""
							    :position="myCoordinates"
							    :clickable="true"
							    :draggable="true"
							    @click="center=map"
							  />
				        </GmapMap>
					</div> -->

			    	<!-- ------------------------------ BOTONES NUEVO, GUARDAR, MODIFICAR Y ELIMINAR ---------------------------------- -->
					

					  

					<div class="row mt-4">
						<div class="col-12 text-right">
				    
					    	<button v-on:click="limpiar()" type="button" class="btn btn-primary">Nuevo</button>
					   
					    	<button  v-if='btnguardar' v-on:click="guardar" type="button" class="btn btn-success">Guardar</button>
					    
					    	<button v-else v-on:click="guardar" type="button" class="btn btn-warning">Modificar</button>
					   
					    
					    	<button v-on:click="eliminar()" type="button" class="btn btn-danger">Eliminar</button>
					    
					    </div>
				    </div>
				</div>		
	  		</div>	
    	</div>

		<!-- -------------------------------------------------TOAST COMPLETAR CABECERA------------------------------------------------- -->

		<b-toast id="toast-completar-datos" variant="warning" solid>
	      <template v-slot:toast-title>
	        <div class="d-flex flex-grow-1 align-items-baseline">
	          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
	          <strong class="mr-auto">¡Error!</strong>
	          <small class="text-muted mr-2">incompleto</small>
	        </div>
	      </template>
	      ¡Seleccione una Empresa!
	    </b-toast>
	</div>
	<div v-else>
    	<cuatrocientos-cuatro></cuatrocientos-cuatro>
  	</div>
</template>

<script src="vue-google-maps.js"></script>

<script>

	// import VueGeolocation from 'vue-browser-geolocation'

	// Vue.config.productionTip = false
	// Vue.use(VueGeolocation)

	// import * as VueGoogleMaps from 'vue2-google-maps'
	// Vue.use(VueGoogleMaps, {
	//   load: {
	//     key: 'AIzaSyCGcQ1_i9zvFkgWnfgLjD7m-_DKi1XEruc',
	//     libraries: 'places',
	//   }
	// })
	// import {gmapApi} from 'vue2-google-maps'

	export default{
		props: ['candec'],
		data(){

			return{
				cliente: '',
				codigo: '',
				empresa:'',
				idEmpresa: '',
				codigoEmpleado: '',
				ci: '',
				ciudad: '',
				ruc: '',
				direccion: '',
				telefono: '',
				celular: '',
				email:'',
				tipo: 'OCASIONAL',
				limiteCredito: '',
				limiteCreditoDia: '',
				creditoDisponible: '',
				razonSocial: '',
				btnguardar: true,
				existe: false,
				controlar: true,
				validar: {
					cliente: false,
					codigo: false,
					ci: false,
					ruc: false,
					direccion: false,
					telefono: false
				},
                map: null,
                myCoordinates: {
                    lat: -25.5131,
                    lng: -54.6069
                },
                zoom: 16,
                checkedUbicacion: false,
                marker: [],
      			center: { 
      				lat: -25.5131,
                    lng: -54.6069 
                },
                switcher: {
                	retentor: false
                }
			}
		},

        // created() {
          
        //     // OBTENER UBICACION
            

            // this.$getLocation({}).then(coordinates => {
            //             this.myCoordinates = coordinates;
            //         })
            //     .catch(error => alert(error));

           
        // },
		
		methods: {

			//CREAR MARCADOR

			// createMarker: function (latlng) {
		 //      this.marker = new window.google.maps.Marker({
		 //        setMap: this.map,
		 //        position: latlng,
		 //        animation: window.google.maps.Animation.DROP
		 //      })
		 //      this.addYourLocationButton()
		 //    },

			// handleDrag() {

   //              // OBTENER NIVEL DE ZOOM Y COORDENADAS EN EL MAPA

   //              let center = {
   //                  lat: this.map.getCenter().lat(),
   //                  lng: this.map.getCenter().lng()
   //              };
   //              let zoom = this.map.getZoom();
   //              localStorage.center = JSON.stringify(center);
   //              localStorage.zoom = zoom;
   //          },

			// GUARDAR Y MODIFICAR

			guardar(){

				let me = this;

				// CONTROL DE DATOS NULOS

				if(this.controlador() === false){
					this.controlar = true;
					return;
				}

				// CARGA TODOS LOS DATOS EN UNA VARIABLE

				var data = {

					codigo: this.codigo,
					name: this.cliente,
					cedula: this.ci,
					direccion: this.direccion,
					ciudad: this.ciudad,
					ruc: this.ruc,
					telefono: this.telefono,
					celular: this.celular,
					email: this.email,
					tipo: this.tipo,
					limite: this.limiteCredito,
					existe: this.existe,
					idEmpresa: this.idEmpresa,
					diaLimite: this.limiteCreditoDia,
					razonSocial: this.razonSocial,
					retentor: this.switcher.retentor
				}

				// *******************************************************************

				// ENVIA LOS DATOS PARA GUARDAR O MODIFICAR

				Common.guardarClienteCommon(data).then(data=>{


    				// CONFIRMAR QUE GUARDÓ O SI HAY UN ERROR

	                if(data.response===true){

	                	// *******************************************************************

	                  	Swal.fire(
	                     	'¡Guardado!',
	                     	'¡Se ha guardado correctamente el cliente!',
	                     	'success'
	                  	)	

	                  	// *******************************************************************

	                  	// CARGAR LOS VALORES A LAS VARIABLES DE PRODUCTO

            			if (me.existe === false) {

		                    me.enviarPadre({'codigo': data.codigo, 'nombre': me.cliente, 'tipo': me.tipo, 'retentor': me.switcher.retentor});

            			}
	                    
	                    // *******************************************************************

	                    // LIMPIAR DATOS

	                  	this.limpiar();

	                  	// *******************************************************************

	                  	// RECARGAR CLIENTE TEXTBOX 

            			this.$refs.componente_textbox_cliente.recargar();

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

			eliminar(){

				// GUARDA LOS DATOS EN UNA VARIABLE

    			var eliminar = {

    				codigo: this.codigo,
    				nombre: this.cliente,
    				existe: this.existe
    			}

    			// MOSTRAR LA PREGUNTA

      			Swal.fire({
				  title: '¿Estás seguro?',
				  text: "¡Eliminar el Cliente " + this.codigo + "!",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  cancelButtonColor: 'btn btn-success',
				  confirmButtonColor: '#d33',
				  confirmButtonText: '¡Sí, eliminar!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
    				// LLAMAR A UNA FUNCION PARA ELIMINAR LOS DATOS
				    return Common.eliminarClienteCommon(eliminar).then(data => {
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
						      '¡Se ha eliminado correctamente el cliente!',
						      'success'
					)

				  	// ------------------------------------------------------------------------

				  	// RECARGAR TABLA 
				  	
            		this.$refs.componente_textbox_cliente.recargar();

    				this.limpiar();

					// ------------------------------------------------------------------------

				  }
				})
			},

			// CONTROL DE INPUT NULOS

			controlador(){

				let me = this;

				if (me.cliente === '' || me.cliente === " "){
					me.validar.cliente = true;
					me.controlar = false;

				}else{
					me.validar.cliente = false;
				}

				if (me.codigo === '' || me.codigo === " "){
					me.validar.codigo = true;
					me.controlar = false;

				}else{
					me.validar.codigo = false;
				}

				if ((me.ci === '' || me.ci === " ") && me.ruc === ''){
					me.validar.ci = true;
					me.controlar = false;

				}else{
					me.validar.ci = false;
				}

				if (me.direccion === '' || me.direccion === " "){
					me.validar.direccion = true;
					me.controlar = false;

				}else{
					me.validar.direccion = false;
				}

				if ((me.ruc === '' || me.ruc === " ") && me.ci === ''){
					me.validar.ruc = true;
					me.controlar = false;

				}else{
					me.validar.ruc = false;
				}

				if(me.telefono === '' || me.telefono === " "){
					me.validar.telefono = true;
					me.controlar = false;
				}else{
					me.validar.telefono =  false;
				}

				if((me.idEmpresa === 'null' || me.idEmpresa=== '') && me.tipo === 'FUNCIONARIO' && me.existe === false){
					
					me.$bvToast.show('toast-completar-datos');
					me.controlar = false;
				}
				
				return me.controlar;
			},

			// REINICIAR EL FORMULARIO

			limpiar(){
				let me = this;
				me.cliente = '';
				me.ci = '';
				me.ciudad = '';
				me.ruc = '';
				me.direccion = '';
				me.telefono = '';
				me.celular = '';
				me.email = '';
				me.tipo = 'OCASIONAL';
				me.limiteCredito = '';
				me.limiteCreditoDia = '';
				me.razonSocial = '';
				me.btnguardar = true;
				me.existe = false;
				me.validar.cliente = false;
				me.validar.codigo = false;
				me.validar.ci = false;
				me.validar.ciudad = false;
				me.validar.ruc = false;
				me.validar.direccion = false;
				me.validar.telefono = false;
				me.validar.celular = false;
				me.validar.email = false;
				me.validar.tipo = false;
				me.validar.limiteCredito = false;
				Common.clienteNuevoCommon().then(data=> {
		        	me.codigo = data.cliente[0].CODIGO+1;
		        	me.limiteCreditoDia = data.limite.LIMITE_DIAS;
		        	me.btnguardar = true;
		        });		
				me.codigoEmpleado = '';
				me.idEmpresa = '';
				me.empresa = '';
				me.controlar = true;
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

				this.direccion = valor;
			},
			
			cargarCiudad(valor){

				this.ciudad = valor;
			},
			
			cargarTelefono(valor){

				this.telefono = valor;
			},
			
			cargarCelular(valor){

				this.celular = valor;
			},
			
			cargarEmail(valor){

				this.email = valor;
			},
			
			cargarTipo(valor){

				this.tipo = valor;
			},
			
			cargarLimite(valor){

				this.limiteCredito = valor;
			},

			cargarEmpresa(valor){
				
				this.empresa = valor;
			},
			
			cargarEmpresaID(valor){
				
				this.idEmpresa = valor;
			},

			cargarRazonSocial(valor){

				this.razonSocial = valor;
			},

			cargarLimiteDia(valor){

				this.limiteCreditoDia = valor;
			},

			cargarCreditoDisponible(valor){

				this.creditoDisponible = valor;
			},

			filtrarNombreEmpresa(valor){

				this.empresa = valor;
			},

			filtrarIdEmpresa(valor){

				this.idEmpresa = valor;
			},

			calcularRUC(cedula) {

			    var total = 0, resto = '', k = 2, digito = '';
			    var numero = cedula.length;
			    for (var numero = cedula.length-1; numero >= 0 ; numero--) {
			            var numeroInd = cedula.charAt(numero);
			            k = k > 11 ? 2 : k;
			            total += numeroInd * k;
			            k++;
			    }
			    resto = total % 11;
			    digito = resto > 1 ? 11 - resto : 0;
			    this.ruc = cedula+'-'+digito;
			},
			cargarRetentor(valor) {

				// ------------------------------------------------------------------

				this.switcher.retentor = valor;

				// ------------------------------------------------------------------

			},
			enviarPadre(data){

				// ------------------------------------------------------------------------

				// ENVIAR CODIGO
				
                this.$emit('data', data);

				// ------------------------------------------------------------------------

		  	}
		},

		mounted(){

			let me = this;

			Common.clienteNuevoCommon().then(data=> {
		        	me.codigo = data.cliente+1;
		        	me.limiteCreditoDia = data.limite.LIMITE_DIAS;
		        	me.btnguardar = true;
		    });

			// AGREGAR EL MAPA A UN DATA OBJECT

			// this.$refs.mapRef.$mapPromise.then(map => this.map = map);
		},

        // computed: {
        //     mapCoordinates() {
        //         if(!this.map) {
        //             return {
        //                	lat: 0,
        //             	lng: 0
        //             };
        //         }
        //         return {
        //             lat: this.map.getCenter().lat().toFixed(4),
        //             lng: this.map.getCenter().lng().toFixed(4)
        //         }
        //     }
        // }

	}
</script>