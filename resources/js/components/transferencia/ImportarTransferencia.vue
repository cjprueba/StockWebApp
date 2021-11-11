<template>
	<div class="container-fluid mt-4">
		<div v-if="$can('transferencia.importar') && $can('transferencia')">

	<!-- 	<div class="row" v-if="$can('transferencia.importar')"> -->

			<!-- ------------------------------------------------------------------------------------- -->

			<!-- TITULO  -->
			
				<div class="col-md-12">
					<vs-divider>
						Importar Transferencias
					</vs-divider>
				</div>

				<!-- ------------------------------------------------------------------------------------- -->

		        <!-- MOSTRAR LOADING -->

		        <div class="col-md-12">
					<div v-if="procesar" class="d-flex justify-content-center mt-3">
						<strong>Procesando...   </strong>
		                <div class="spinner-grow" role="status" aria-hidden="true"></div>
		             </div>
	            </div>

				<!-- ------------------------------------------------------------------------------------- -->

				<div class="col-md-12">
					<table id="tablaImportarTrans" class="table table-hover table-striped table-bordered table-sm mb-3" style="width:100%">
			            <thead>
			                <tr>
			                    <th>Codigo</th>
			                    <th>Codigo Origen</th>
			                    <th>Origen</th>
			                    <th>Responsable Envio</th>
			                    <th>Fecha</th>
			                    <th>Hora</th>
			                    <th>Total</th>
			                    <th>Estatus</th>
			                    <th>Acción</th>
			                </tr>
			            </thead>
			            <tbody>
			                <td></td>
			            </tbody>
			        </table> 
				</div>	
			<!-- </div> -->

			<!-- <div v-else>
				<cuatrocientos-cuatro></cuatrocientos-cuatro>
			</div> -->
			
			<!-- ------------------------------------------------------------------------ -->

			<!-- MODAL MOSTRAR DETALLE TRANSFERENCIA -->

			<modal-detalle-transferencia 
			ref="ModalImportarTransferencia"
			></modal-detalle-transferencia>

			<!-- ------------------------------------------------------------------------ -->

					<!-- ------------------------------------------------------------------------ -->

			<!-- MODAL MOSTRAR DETALLE TRANSFERENCIA -->

			<modal-devolucion-transferencia 
			ref="ModalDevolucionTransferencia"
			></modal-devolucion-transferencia>

			<!-- ------------------------------------------------------------------------ -->

			<!-- MODAL AGREGAR UBICACION EN RACK  -->

		    <div class="modal fade agregar-rack-piso" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md" role="document">
		            <div class="modal-content">
		                <div class="modal-header">
		                    <h5 class="modal-title text-primary text-center" >Transferencia: {{codigoTransferencia}}</h5>
		                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		                          <span aria-hidden="true">&times;</span>
		                    </button>
		                </div>
		  				
	                	<div class="modal-body">
		                  <div class="row">

							<!-- NRO. CAJA -->

							<div class="col-md-12">
								<label class="mt-1" for="validationTooltip01">Nro. Caja</label>
								<input class="form-control form-control-sm" type="text"  v-model="nro_caja">
							</div>

							<!-- ----------------------------------- TEXTBOX DE GONDOLA ------------------------------------- -->

							<div class="col-md-12 mt-3">
								<gondola-nombre ref="gondola" v-model="gondolaID" @nombre_gondola='enviar_nombre_gondola' :nombre='gondolaID' :validarGondola='validarGondola' @seccion="enviar_seccion"  :rack='rack' @pisos='traer_pisos' @sectores='traer_sectores'></gondola-nombre>
								<div class="form-text text-danger">{{messageInvalidGondola}}</div>
							</div>

							<!-- ----------------------------------- OPCIONES DE SECCION  ------------------------------------- -->

		                    <div class="col-md-12 mt-3">
								<label for="validationTooltip01">Sección</label>
								<select v-model="selectedSeccion" class="custom-select custom-select-sm" disabled>
	                        		<option value="null" selected>Seleccionar</option>
							    	<option  v-for="seccion in secciones" :value="seccion">{{seccion.DESCRIPCION}}</option>
								</select>
								<div class="form-text text-danger">
								    {{messageInvalidSeccion}}
								</div>
		                    </div>

							<!-- ----------------------------------- OPCIONES DE SECTOR  ------------------------------------- -->

		                    <div class="col-md-12 mt-3">
							    <label>Sector Rack</label>
							    <select class="custom-select custom-select-sm" v-model="sectorRack" v-bind:class="{ 'is-invalid': validarSector }">
	                        		<option value="null" selected>Seleccionar</option>
							    	<option v-for="sector in gondolaSector" :value="sector.ID">{{sector.DESCRIPCION}}</option>
								</select>
								<div class="form-text text-danger">
								    {{messageInvalidSector}}
								</div>
							</div>

							<!-- ------------------------------------------- SELECT PISO ------------------------------------------ -->

							<div class="col-md-12 mt-3">
							    <label>Piso Rack</label>
							    <select class="custom-select custom-select-sm" v-model="pisoRack"  v-bind:class="{ 'is-invalid': validarPiso }">
	                        		<option value="null" selected>Seleccionar</option>
							    	<option v-for="piso in gondolaPiso" :value="piso.ID">PISO {{piso.NRO_PISO}}</option>
								</select>
								<div class="form-text text-danger">
								    {{messageInvalidPiso}}
								</div>
							</div>     
		                </div>
		                <div class="modal-footer">
		                    <button  type="button" class="btn btn-success"  v-if="btnguardar" v-on:click="controlarDatos()">Importar</button>
		                    <button  type="button" class="btn btn-warning"  v-else v-on:click="controlarDatos()">Modificar</button>
		                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
		                </div>
		              </div>
		            </div>
		        </div>
		    </div>
			<!-- ------------------------------------------------------------------------ -->

		</div>
		<div v-else>
	    	<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div>
		<autorizacion @data="autorizacionData" ref="autorizacion_componente"></autorizacion>
	</div>

</template>
<script>
	 export default {
      data(){
        return {
        	autorizacion: {
	            HABILITAR: 0,
	            CODIGO: 0,
	            ID_USUARIO: 0,
	            PERMITIDO: 0,
	            ID_USER_SUPERVISOR: 0
	        },
          	codigoTransferencia: '',
          	codigo_origen: '',
          	procesar: false,
          	rack:'',
			nro_caja: '',
            secciones: [],
            selectedSeccion: 'null',
            validarSeccion: false,
            messageInvalidSeccion: '',
			gondolaPiso: [],
          	validarPiso: false,
            pisoRack: 'null',
            messageInvalidPiso: '',
            gondolaSector: [],
        	sectorRack:'null',
            validarSector: false,
			messageInvalidSector: '',
	        gondolaID: '',
	        validarGondola: false,
			messageInvalidGondola: '',
			controlador: false,
			btnguardar: true
          	
        }
      }, 
      methods: {
      		editarTransferencia(codigo){

      			// ------------------------------------------------------------------------

      			// MANDAR CODIGO TRANSFERENCIA

      			 this.$router.push('/tr2/'+ codigo + '');

      			// ------------------------------------------------------------------------
      		}, mostrarModalTranferencia(codigo, codigo_origen) {

      			// ------------------------------------------------------------------------

      			// LLAMAR EL METODO DEL COMPONENTE HIJO

      			this.$refs.ModalImportarTransferencia.mostrarModal(codigo, codigo_origen);

      			// ------------------------------------------------------------------------
      		},
      		 mostrarModalDevolucion(codigo, codigo_origen) {

      			// ------------------------------------------------------------------------

      			// LLAMAR EL METODO DEL COMPONENTE HIJO

      			this.$refs.ModalDevolucionTransferencia.mostrarModal(codigo, codigo_origen);

      			// ------------------------------------------------------------------------
      		},
      		autorizar(){
		        this.$refs.autorizacion_componente.mostrarModal();
		    },
      		rechazarTransferencia(codigo, codigo_origen){

      			// ------------------------------------------------------------------------

      			// INICIAR VARIABLES

      			var tableTransferencia = $('#tablaImportarTrans').DataTable();

      			// ------------------------------------------------------------------------

      			// MOSTRAR LA PREGUNTA DE ELIMINAR 

      			Swal.fire({
				  title: '¿Estás seguro?',
				  text: "¡Rechazar la Transferencia " + codigo + "!",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  confirmButtonColor: '#d33',
				  cancelButtonColor: '#3085d6',
				  confirmButtonText: '¡Sí, rechazalo!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
				    return Common.rechazarTransferenciaCommon(codigo, codigo_origen).then(data => {
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
						      '¡Rechazado!',
						      '¡Se ha rechazado la transferencia y devuelto al origen!',
						      'success'
					)

				  	// ------------------------------------------------------------------------

				  	// RECARGAR TABLA 
				  	
					tableTransferencia.ajax.reload( null, false );

					// ------------------------------------------------------------------------

				  } 
				})

				// ------------------------------------------------------------------------

      		},

      		importarTransferencia(data){

      			// ------------------------------------------------------------------------

      			// INICIAR VARIABLES

      			var tableTransferencia = $('#tablaImportarTrans').DataTable();

      			// MOSTRAR LA PREGUNTA DE IMPORTAR 

      			Swal.fire({
					  title: '¿Estás seguro?',
					  text: "¡Importar la Transferencia " + data.codigo + "!",
					  type: 'warning',
					  showLoaderOnConfirm: true,
					  showCancelButton: true,
					  cancelButtonColor: '#3085d6',
					  confirmButtonText: '¡Sí, importalo!',
					  cancelButtonText: 'Cancelar',
					  preConfirm: () => {
					    return Common.importarTransferenciaCommon(data).then(data => {
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
							      '¡Importado!',
							      '¡Se ha importado la transferencia correctamente!',
							      'success'
						)

					  	// ------------------------------------------------------------------------

					  	// RECARGAR TABLA 
					  	
						tableTransferencia.ajax.reload( null, false );

						// ------------------------------------------------------------------------

					  }
					})

				// ------------------------------------------------------------------------

      		},
      		generarNroCaja(){

	        	// GENERADOR DE NRO DE CAJA

	        	if(this.selectedSeccion.DESC_CORTA !== undefined){
	        		
	        		this.nro_caja = this.selectedSeccion.DESC_CORTA + '-TRA' + this.codigoTransferencia;
	        	}else{
	        		this.nro_caja = 'TRA' + this.codigoTransferencia;
	        	}
	        },
	        autorizacionData(data){

		        // ------------------------------------------------------------------------

		        // LLAMAR MODAL
		        
		        if (data.response === true) {

		             
		          this.autorizacion.ID_USUARIO = data.usuario;
		          this.autorizacion.ID_USER_SUPERVISOR = data.id_user_supervisor;

		          this.importarAutorizado();
		        }

		        // ------------------------------------------------------------------------

		    },
			enviar_nombre_gondola(data){

	          this.gondolaID = data;
	           
	        },
	        enviar_seccion(data){

	        	for (var i = this.secciones.length - 1; i >= 0; i--) {
	        		if(this.secciones[i].ID_SECCION === data){
	        			this.selectedSeccion = this.secciones[i]; 
	        		}
	        	}

				this.generarNroCaja();
	        },
	        
	        traer_pisos(pisos_marcados){

	          	let me = this;
	            me.gondolaPiso = pisos_marcados;
	            me.pisoRack = 'null';
	        },
	        traer_sectores(sectores_marcados){
	          
	          	let me =this;
	            me.gondolaSector = sectores_marcados;
	            me.sectorRack = 'null';

	        },

	      	BusquedaSeccion(){
	        	axios.get('busquedas/').then((response) => {
	          		this.secciones = response.data.secciones;

	        	});
	      	},
			controlarDatos(){

				// ------------------------------------------------------------------------

				let me = this;

	        	
	        	if (me.selectedSeccion ===  "null" || me.selectedSeccion === '') {
	        		me.messageInvalidSeccion = 'Por favor seleccione una sección.';
	        		me.controlador = true;
	        	} else {
	        		me.messageInvalidSeccion = '';
	        	}

	        	if (me.pisoRack === "null" || me.pisoRack === '') {
	        		me.validarPiso = true;
	        		me.messageInvalidPiso = 'Por favor seleccione un piso.';
	        		me.controlador = true;
	        	} else {
	        		me.validarPiso = false;
	        		me.messageInvalidPiso = '';
	        	}

	        	if (me.sectorRack ===  "null" || me.sectorRack === '') {
	        		me.validarSector = true;
	        		me.messageInvalidSector = 'Por favor seleccione un sector.';
	        		me.controlador = true;
	        	} else {
	        		me.validarSector = false;
	        		me.messageInvalidSector = '';
	        	}

            	if(me.gondolaID.length === 0){
	        		me.messageInvalidGondola = 'Por favor seleccione una góndola.';
            		me.controlador = true;
            	}else{
	        		me.messageInvalidGondola = '';
            	}
				
				// ------------------------------------------------------------------------

	            // RETORNAR CONTROLADOR - SI ES TRUE SE DETIENE EL GUARDADO 
	            // SI ES FALSE CONTINUA LA OPERACION 

	            if(me.controlador === true){
	            	me.controlador = false;
	            	return me.controlador;
	            }

		        var data = {
		            codigo: me.codigoTransferencia, 
		            codigo_origen: me.codigo_origen,
	        		seccion: me.selectedSeccion.ID_SECCION,
	        		piso: me.pisoRack,
	        		sector: me.sectorRack,
	        		gondola: me.gondolaID,
	        		nro_caja: me.nro_caja,
	        		autorizacion:me.autorizacion,
					sistema_deposito: true
		        }

		        if(me.btnguardar){

		        	me.importarTransferencia(data);
		        }else{
		        	me.editarUbicacionTransferencia(data);
		        }
		        
	        	// ------------------------------------------------------------------------

	        },
	        editarUbicacionTransferencia(data){

      			// ------------------------------------------------------------------------

      			// INICIAR VARIABLES

      			var tableTransferencia = $('#tablaImportarTrans').DataTable();
      			let me = this;
      			// ------------------------------------------------------------------------

      			// MOSTRAR LA PREGUNTA DE IMPORTAR 

      			Swal.fire({
					  title: '¿Estás seguro?',
					  text: "¡Cambiar ubicación de la Transferencia " + data.codigo + "!",
					  type: 'warning',
					  showLoaderOnConfirm: true,
					  showCancelButton: true,
					  cancelButtonColor: '#3085d6',
					  confirmButtonText: '¡Sí, cambiar!',
					  cancelButtonText: 'Cancelar',
					  preConfirm: () => {
					    return Common.modificarUbicacionTransferenciaCommon(data).then(data => {
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
							      '¡Modificado!',
							      '¡Se ha cambiado la ubicación de la transferencia correctamente!',
							      'success'
						)

					  	// ------------------------------------------------------------------------

					  	// RECARGAR TABLA 
					  	
						tableTransferencia.ajax.reload( null, false );
						me.btnguardar = true;

						// ------------------------------------------------------------------------

					  }
					})

				// ------------------------------------------------------------------------

      		},
      		
	        obtenerCabeceraTransferencia(){

	        	let me = this;

	        	// ------------------------------------------------------------------------

        		// AGREGAR CABECERA, ENVIO CERO PARA CODIGO ORIGEN PARA ESPECIFICAR QUE
        		// NECESITO DATOS DE LA PROPIA SUCURSAL DEL USUARIO
				
				Common.obtenerCabeceraTransferenciaCommon(me.codigoTransferencia, me.codigo_origen).then(data => {
            		

        			if(data.SISTEMA_DEPOSITO === true){
        				me.rack = 'SI';
        			}else{
        				me.rack = '';
        			}
        			
        			me.secciones = data.SECCIONES;
        			if(data.SISTEMA_DEPOSITO === true){
        					
	        			me.nombreContainer = data.DATOS_DEPOSITO.CODIGO;
	        			me.descripcionContainer = data.DATOS_DEPOSITO.DESCRIPCION;
	        			me.gondolaID = data.DATOS_DEPOSITO.GONDOLA;
	        			me.gondolaSector = data.SECTORES;
	        			me.gondolaPiso = data.PISOS;   
	        			me.pisoRack = data.DATOS_DEPOSITO.PISO;
	        			me.sectorRack = data.DATOS_DEPOSITO.SECTOR;
	        			me.nro_caja = data.DATOS_DEPOSITO.NRO_CAJA;

				        for (var i = me.secciones.length - 1; i >= 0; i--) {
				        	if(me.secciones[i].ID_SECCION === data.DATOS_DEPOSITO.ID_SECCION){
				        		me.selectedSeccion = me.secciones[i]; 
				        	}
				        }

				        me.btnguardar = false;

        				// ------------------------------------------------------------------------
					    // ABRIR EL MODAL
					                     
					    $('.agregar-rack-piso').modal('show');
				    }
				        
        			// ------------------------------------------------------------------------

        		});
	        },
	        importarAutorizado(){
      			let me = this;
  				
               	if(me.rack === 'SI'){
		        	// ------------------------------------------------------------------------
					// LIMPIAR LAS VARIABLES ANTES DE ABRIR EL MODAL
					
					me.nro_caja = '';
		            me.secciones = [];
		            me.selectedSeccion = 'null';
		            me.validarSeccion = false;
		            me.messageInvalidSeccion = '';
					me.gondolaPiso = [];
		          	me.validarPiso = false;
		            me.pisoRack = 'null';
		            me.messageInvalidPiso = '';
		            me.gondolaSector = [];
		        	me.sectorRack ='null';
		            me.validarSector = false;
					me.messageInvalidSector = '';
			        me.gondolaID = '';
			        me.validarGondola = false;
					me.messageInvalidGondola = '';
					me.controlador = false;

		        	// ------------------------------------------------------------------------
					// OBTENER SECCIONES

					me.BusquedaSeccion();

		        	// ------------------------------------------------------------------------
					// GENERAR PRIMERA PARTE DEL NRO. DE CAJA

					me.generarNroCaja();
	                // ABRIR EL MODAL
	                     
	                $('.agregar-rack-piso').modal('show');

               	}else{

                   	var data = {
                   		codigo: me.codigoTransferencia, 
                   		codigo_origen: me.codigo_origen,
						sistema_deposito: false,
						autorizacion:me.autorizacion
                   	}

                    me.importarTransferencia(data);
               	}
      		}

      },
        mounted() {
        	
        	let me = this;

        	// ------------------------------------------------------------------------

        	// OBTENER RACK 

	        Common.obtenerParametroCommon().then(data => {
			    me.rack = data.parametros[0].RACK;
			});

            $(document).ready( function () {

            		// ------------------------------------------------------------------------

            		// PREPARAR DATATABLE 

	 				var tableImportarTransferencia = $('#tablaImportarTrans').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                        		 "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/transferencia/mostrar/importar",
                                 "dataType": "json",
                                 "type": "POST"
                               },       
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "CODIGO_ORIGEN", "visible": false },
                            { "data": "ORIGEN" },
                            { "data": "RESPONSABLE" },
                            { "data": "FECHA" },
                            { "data": "HORA" },
                            { "data": "TOTAL" },
                            { "data": "ESTATUS" },
                            { "data": "ACCION" }
                        ]      
                    });
                    
	 				// ------------------------------------------------------------------------

                	// EDITAR TRANSFERENCIA

                    $('#tablaImportarTrans').on('click', 'tbody tr #mostrarTransferencia', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA

	                   	 var row  = $(this).parents('tr')[0];
	                   	 me.mostrarModalTranferencia(tableImportarTransferencia.row( row ).data().CODIGO, tableImportarTransferencia.row( row ).data().CODIGO_ORIGEN);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // ELIMINAR TRANSFERENCIA

                    $('#tablaImportarTrans').on('click', 'tbody tr #rechazarTransferencia', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                    me.rechazarTransferencia(tableImportarTransferencia.row( row ).data().CODIGO, tableImportarTransferencia.row( row ).data().CODIGO_ORIGEN);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // IMPORTAR TRANSFERENCIA

                    $('#tablaImportarTrans').on('click', 'tbody tr #importarTransferencia', function() {

	                    // *******************************************************************
	                    var row  = $(this).parents('tr')[0];
	                    me.codigoTransferencia = tableImportarTransferencia.row( row ).data().CODIGO;
		            	me.codigo_origen = tableImportarTransferencia.row( row ).data().CODIGO_ORIGEN;

	                    // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
	                    
	                    me.autorizar();

	                   	
	                   	


	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // GENERAR REPORTE PDF

                    $('#tablaImportarTrans').on('click', 'tbody tr #imprimirReporte', function() {

	                    // *******************************************************************

	                    // ENVIAR A COMMON FUNCTION PARA GENERAR REPORTE PDF

	                   	me.procesar = true;
	                   	var row  = $(this).parents('tr')[0];
	                   	Common.generarRptPdfTransferenciaCommon(tableImportarTransferencia.row( row ).data().CODIGO, tableImportarTransferencia.row( row ).data().CODIGO_ORIGEN).then( () => {
	                   		me.procesar = false;
	                   	});
	                   	

	                    // *******************************************************************

	                });
	                
	                $('#tablaImportarTrans').on('click', 'tbody tr #devolucion', function() {


	                    // *******************************************************************

	                    // ENVIAR A COMMON FUNCTION PARA GENERAR REPORTE PDF
	                    
	                   	var row  = $(this).parents('tr')[0];
                        me.mostrarModalDevolucion(tableImportarTransferencia.row( row ).data().CODIGO, tableImportarTransferencia.row( row ).data().CODIGO_ORIGEN);
                      

	                    // *******************************************************************

	                });
	                 $('#tablaImportarTrans').on('click', 'tbody tr #qr_caja', function() {

	                    // *******************************************************************

	                    // ENVIAR A COMMON FUNCTION PARA GENERAR REPORTE PDF

	                   	me.procesar = true;
	                   	var row  = $(this).parents('tr')[0];
	                   	 Common.obtenerNumeroCajaCommon(tableImportarTransferencia.row( row ).data().CODIGO, tableImportarTransferencia.row( row ).data().CODIGO_ORIGEN).then(data => {
	                   	 	    me.procesar = false;
							   if(data.response===false){
							  	Swal.fire(
							      'Error!',
							      '¡Esta transferencia no posee posiciones!',
							      'danger'
						         )
							   }else{
							   	 Common.generarRptPdfCajaTransferenciaQrCommon(data.caja).then( () => {
				                   		me.procesar = false;
				                  });
							   }
						 });
	                   	
	                   	

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

			
	                // ------------------------------------------------------------------------

	                // EDITAR UBICACION TRANSFERENCIA

	               	$('#tablaImportarTrans').on('click', 'tbody tr #editarUbicacion', function() {

		               // *******************************************************************

		               // REDIRIGIR Y ENVIAR CODIGO TRANSFERENCIA
		              	
		              	var row  = $(this).parents('tr')[0];

					    // *******************************************************************

					    me.codigoTransferencia = tableImportarTransferencia.row( row ).data().CODIGO;
					    me.codigo_origen = tableImportarTransferencia.row( row ).data().CODIGO_ORIGEN;
					         
					    // ------------------------------------------------------------------------
						// LIMPIAR LAS VARIABLES ANTES DE ABRIR EL MODAL

						me.nro_caja = '';
					    me.secciones = [];
					    me.selectedSeccion = 'null';
					    me.validarSeccion = false;
					    me.messageInvalidSeccion = '';
						me.gondolaPiso = [];
					    me.validarPiso = false;
					    me.pisoRack = 'null';
					    me.messageInvalidPiso = '';
					    me.gondolaSector = [];
					    me.sectorRack ='null';
					    me.validarSector = false;
						me.messageInvalidSector = '';
					    me.gondolaID = '';
					    me.validarGondola = false;
						me.messageInvalidGondola = '';
						me.controlador = false;

					    // ------------------------------------------------------------------------
						// LLAMAR DATOS ANTES DE EDITAR
						me.obtenerCabeceraTransferencia();


					    // ------------------------------------------------------------------------
					});

	 			});

        }
    }
</script>