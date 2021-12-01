<template>
	<div class="container-fluid mt-4">
		<div class="row" v-if="$can('compra.mostrar') && $can('compra')">

			<!-- ------------------------------------------------------------------------------------- -->

			<!-- TITULO  -->
			
			<div class="col-md-12">
				<vs-divider>
					Mostrar Compras
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

			<!-- ------------------------------------------------------------------------ -->

			<div class="col-md-12">
				<table id="tablaCompras" class="table table-striped table-hover table-bordered table-sm mb-3" style="width:100%">
		            <thead>
		                <tr>
		                    <th>Código</th>
		                    <th>Proveedor</th>
		                    <th>Plan</th>
		                    <th>Nro. Factura</th>
		                    <th>Fecha Factura</th>
		                    <th>Total</th>
		                    <th>Acción</th>
		                </tr>
		            </thead>
		            <tbody>
		                <td></td>
		            </tbody>
		        </table> 


			</div>	


			<!-- MODAL IMPRIMIR DIRECCION ORDEN -->

		    <div class="modal fade agregar-rack-piso" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md" role="document">
		            <div class="modal-content">
		                <div class="modal-header">
		                    <h5 class="modal-title text-primary text-center" >Compra: {{codigoCompra}}</h5>
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

							<!-- ----------------------------------- TEXTBOX CONTAINER ------------------------------------- -->

					        <div class="col-md-12 mt-3">
					            <container-nombre ref="componente_textbox_container" @nombre_container='enviar_nombre' :nombre='nombreContainer' @descripcion='traer_descripcion'></container-nombre>
								<div class="form-text text-danger">{{messageInvalidContainer}}</div>
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
		                    <button type="button" class="btn btn-warning" v-on:click="controlarDatos()">Modificar</button>
		                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
		                </div>
		              </div>
		            </div>
		        </div>
		    </div>
		</div>

		<!-- ------------------------------------------------------------------------ -->

		<div v-else>
			<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div>

		<!-- ------------------------------------------------------------------------ -->

		<!-- MODAL MOSTRAR DETALLE COMPRA -->

		<modal-detalle-compra 
		ref="ModalMostrarDetalleCompra"
		></modal-detalle-compra>
		<autorizacion @data="autorizacionData" ref="autorizacion_componente"></autorizacion>


		<!-- ------------------------------------------------------------------------ -->

	</div>
</template>
<script>


	 export default {
	  props: ['id_sucursal'],	
      data(){
        return {
        	autorizacion: {
	            HABILITAR: 0,
	            CODIGO: 0,
	            ID_USUARIO: 0,
	            PERMITIDO: 0,
	            ID_USER_SUPERVISOR: 0
	        },
          	codigoCompra: '',
          	procesar: false,
          	eliminar: '',
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
	        nombreContainer: '',
	        descripcionContainer: '',
	        messageInvalidContainer: ''
        }
      }, 
      methods: {
      		autorizar(){
		        this.$refs.autorizacion_componente.mostrarModal();
		    },
		    autorizacionData(data){

		        // ------------------------------------------------------------------------

		        // LLAMAR MODAL
		        
		        if (data.response === true) {

		             
		          this.autorizacion.ID_USUARIO = data.usuario;
		          this.autorizacion.ID_USER_SUPERVISOR = data.id_user_supervisor;

		          this.eliminarCompra(this.eliminar);
		        }

		        // ------------------------------------------------------------------------

		    },
      		editarCompra(codigo){

      			// ------------------------------------------------------------------------

      			// MANDAR CODIGO COMPRA

      			 this.$router.push('/cr2/'+ codigo + '');

      			// ------------------------------------------------------------------------
      		}, mostrarModalTranferencia(codigo) {

      			// ------------------------------------------------------------------------

      			// LLAMAR EL METODO DEL COMPONENTE HIJO

      			this.$refs.ModalMostrarDetalleCompra.mostrarModal(codigo);

      			// ------------------------------------------------------------------------
      		}, obtenerCabeceraCompra(){

      			let me = this;

        		Common.obtenerCabeceraCompraCommon(me.codigoCompra).then(data => {

        			// FILTRAR DATOS DE DEPOSITO
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
	        			me.nro_caja = data.NRO_FACTURA;
		        		me.nombreContainer = data.DATOS_DEPOSITO.CODIGO;
		        		me.descripcionContainer = data.DATOS_DEPOSITO.DESCRIPCION;

				        for (var i = me.secciones.length - 1; i >= 0; i--) {
				        	if(me.secciones[i].ID_SECCION === data.DATOS_DEPOSITO.ID_SECCION){
				        		me.selectedSeccion = me.secciones[i]; 
				        	}
						}
						
						// ------------------------------------------------------------------------
					 	// ABRIR EL MODAL
							                     
						$('.agregar-rack-piso').modal('show');
	        		}
        		});
      			

      		}, eliminarCompra(codigo){

      			// ------------------------------------------------------------------------

      			// INICIAR VARIABLES

      			var tableCompras = $('#tablaCompras').DataTable();

      			// ------------------------------------------------------------------------

      			// MOSTRAR LA PREGUNTA DE ELIMINAR 

      			Swal.fire({
				  title: '¿Estás seguro?',
				  text: "¡Eliminar la Compra " + codigo + "!",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  confirmButtonColor: '#d33',
				  cancelButtonColor: '#3085d6',
				  confirmButtonText: '¡Sí, eliminalo!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
				    return Common.eliminarCompraCommon(codigo).then(data => {
				    	if (!data.response === true) {
				          throw new Error(data.statusText);
				        }
				  		return data.response;
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
						      '¡Se ha eliminado la compra y devuelto el stock!',
						      'success'
					)

				  	// ------------------------------------------------------------------------

				  	// RECARGAR TABLA 
				  	
					tableCompras.ajax.reload( null, false );

					// ------------------------------------------------------------------------

				  }
				})

				// ------------------------------------------------------------------------

      		},

		    traer_descripcion(data){
	          
	          // OBTENER DESCRIPCION DEL CONTAINER

	         this.descripcionContainer=data;

	         // GENERAR NUMERO DE CAJA

	         this.generarNroCaja();

	        },

			enviar_nombre(data){

				// OBTENER CODIGO DEL CONTAINER

	          this.nombreContainer=data;
	           
	        },
	        generarNroCaja(){

	        	// GENERADOR DE NRO DE CAJA
	        	
	        	if(this.selectedSeccion.DESC_CORTA !== undefined){
	        		
	        		this.nro_caja = this.selectedSeccion.DESC_CORTA + '' + this.descripcionContainer;
	        	}else{
	        		this.nro_caja = this.descripcionContainer;
	        	}
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

            	if(me.nombreContainer === '' || me.nombreContainer.length === 0){
	        		me.messageInvalidContainer = 'Por favor seleccione un container.';
            		me.controlador  = true;
            	}else{
	        		me.messageInvalidContainer = '';
            	}
				
				// ------------------------------------------------------------------------

	            // RETORNAR CONTROLADOR - SI ES TRUE SE DETIENE EL GUARDADO 
	            // SI ES FALSE CONTINUA LA OPERACION 

	            if(me.controlador === true){
	            	me.controlador = false;
	            	return me.controlador;
	            }

		        var data = {
		            codigo: me.codigoCompra, 
        			codigoContainer: me.nombreContainer,
	        		seccion: me.selectedSeccion.ID_SECCION,
	        		piso: me.pisoRack,
	        		sector: me.sectorRack,
	        		gondola: me.gondolaID,
	        		nro_caja: me.nro_caja,
					sistema_deposito: true
		        }

		        me.editarUbicacionCompra(data);
		       
	        	// ------------------------------------------------------------------------

	        },
	        editarUbicacionCompra(data){

      			// ------------------------------------------------------------------------

      			// INICIAR VARIABLES

      			var tableCompra = $('#tablaCompras').DataTable();
      			let me = this;
      			// ------------------------------------------------------------------------

      			// MOSTRAR LA PREGUNTA DE IMPORTAR 

      			Swal.fire({
					  title: '¿Estás seguro?',
					  text: "¡Cambiar ubicación de la compra " + data.codigo + "!",
					  type: 'warning',
					  showLoaderOnConfirm: true,
					  showCancelButton: true,
					  cancelButtonColor: '#3085d6',
					  confirmButtonText: '¡Sí, cambiar!',
					  cancelButtonText: 'Cancelar',
					  preConfirm: () => {
					    return Common.modificarUbicacionCompraCommon(data).then(data => {
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
							      '¡Se ha cambiado la ubicación de la compra correctamente!',
							      'success'
						)

					  	// ------------------------------------------------------------------------

					  	// RECARGAR TABLA 
					  	
						tableCompra.ajax.reload( null, false );

						// ------------------------------------------------------------------------

					  }
					})

				// ------------------------------------------------------------------------

      		},
      },
        mounted() {
        	
        	let me = this;

            $(document).ready( function () {


            		// ------------------------------------------------------------------------

            		// PREPARAR DATATABLE


            		
	 				var tableCompra = $('#tablaCompras').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                       
                         "searching": {
					            "return": true
					        },
                        "ajax":{
                                 "url": "/compra/datatable",
                                 "dataType": "json",
                                 "type": "GET",
                                 "contentType": "application/json; charset=utf-8"
                               },

                       
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "PROVEEDOR" },
                            { "data": "TIPO" },
                            { "data": "NRO_FACTURA" },
                            { "data": "FEC_FACTURA" },
                            { "data": "TOTAL" },
                            { "data": "ACCION" }
                        ]      
                    });
                    $('#tablaCompras_filter input').unbind();
				    $('#tablaCompras_filter input').bind('keyup', function (e) {
				        if (e.keyCode == 13) {
				            tableCompra.search(this.value).draw();
				        }
				    });
                   
	 				// ------------------------------------------------------------------------

	 				// AJUSTAR COLUMNAS DE ACUERDO AL DATO QUE CONTIENEN

            		/*tableCompra.columns.adjust().draw();*/

            		// ------------------------------------------------------------------------

                	// EDITAR COMPRA

                    $('#tablaCompras').on('click', 'tbody tr #editar', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO COMPRA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                    me.editarCompra(tableCompra.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // ELIMINAR COMPRA

                    $('#tablaCompras').on('click', 'tbody tr #eliminar', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO COMPRA
	                   	
	                   	var row  = $(this).parents('tr')[0];
	                   	me.eliminar = tableCompra.row( row ).data().CODIGO;
	                   	me.autorizar();

	                    // me.eliminarCompra(tableCompra.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    // GENERAR REPORTE PDF

                    $('#tablaCompras').on('click', 'tbody tr #reporte', function() {

	                    // *******************************************************************

	                    // ENVIAR A COMMON FUNCTION PARA GENERAR REPORTE PDF

	                   	me.procesar = true;
	                   	var row  = $(this).parents('tr')[0];
	                   	Common.generarRptPdfCompraCommon(tableCompra.row( row ).data().CODIGO).then( () => {
	                   		me.procesar = false;
	                   	});
	                   	

	                    // *******************************************************************

	                });

                    // ------------------------------------------------------------------------

                    $('#tablaCompras').on('click', 'tbody tr #mostrar', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO COMPRA

	                   	 var row  = $(this).parents('tr')[0];
	                   	 me.mostrarModalTranferencia(tableCompra.row( row ).data().CODIGO);

	                    // *******************************************************************

	                });
	                $('#tablaCompras').on('click', 'tbody tr #qr_caja', function() {

	                    // *******************************************************************

	                    // REDIRIGIR Y ENVIAR CODIGO COMPRA

	                   	 var row  = $(this).parents('tr')[0];
	                   	  	me.procesar = true;
						Common.generarRptPdfCajaCompraQrCommon(tableCompra.row( row ).data().NRO_FACTURA).then( () => {
	                   		me.procesar = false;
	                   	});
	                   	

	                    // *******************************************************************

	                });

	                // ------------------------------------------------------------------------
	                // ------------------------------------------------------------------------

	                // EDITAR UBICACION COMPRA

	               	$('#tablaCompras').on('click', 'tbody tr #editarUbicacion', function() {

		               // *******************************************************************

		               // REDIRIGIR Y ENVIAR CODIGO COMPRA
		              	
		              	var row  = $(this).parents('tr')[0];

					    // *******************************************************************

					    me.codigoCompra = tableCompra.row( row ).data().CODIGO;
					         
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
						me.nombreContainer = '';
						me.descripcionContainer = '';
						me.messageInvalidContainer = '';

					    // ------------------------------------------------------------------------
						// LLAMAR DATOS ANTES DE EDITAR
						me.obtenerCabeceraCompra();

					    // ------------------------------------------------------------------------
					});
	 });	
        }
    }
</script>