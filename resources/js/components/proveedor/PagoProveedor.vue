<template>
	<div class="container-fluid mt-3">
		<div class="row" v-if="$can('proveedor.pago')">
				<div class="col-md-12">
					<div class="card border-bottom-info mb-3">
					  <div class="card-header">
					  	 <h6>Pago a proveeedor</h6>
					  </div>
					  <div class="card-body">
					  		
					    <h6 class="card-subtitle mb-2 text-muted"></h6>
					    <div class="row">

					    	<!-- ------------------------------------------------------------------------ -->

					    	<!-- PAGOS -->

					    	<div class="col-md-12">
						    	<div class="collapse multi-collapse" id="pagos">
							      <div class="card card-body">
							      <h5 class="card-title">Pagos</h5>
							        <table id="tablaPago" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
		                                <thead>
		                                    <tr>
		                                    	<th>ID</th>
		                                        <th>Pago</th>
		                                        <th>Fecha</th>
		                                        <th>Guaranies</th>
		                                        <th>Dolares</th>
		                                        <th>Pesos</th>
		                                        <th>Reales</th>
		                                        <th>Tarjeta</th>
		                                        <th>Recibo</th>
		                                    </tr>
		                                </thead>
		                                <tbody>
		                                    <td></td>
		                                </tbody>
		                            </table>
							      </div>
							    </div>
							</div>

							<!-- ------------------------------------------------------------------------ -->

							<!-- CUENTAS -->

							<div class="col-md-12">
						    	<div class="collapse multi-collapse" id="cuentas">
							      <div class="card card-body">
							      <h5 class="card-title">Cuentas</h5>	
							        <table id="tablaModalCuentaCompra" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
		                                <thead>
		                                    <tr>
		                                        <th>Vencimiento</th>
		                                        <th>Plan Pago</th>
		                                        <th>Nro. Cuotas</th>
		                                        <th>Total</th>
		                                        <th>Estatus</th>
		                                    </tr>
		                                </thead>
		                                <tbody>
		                                    <td></td>
		                                </tbody>
		                            </table>  
							      </div>
							    </div>
							</div>

					    	<!-- ------------------------------------------------------------------------ -->

					    	<!-- COIGO CUENTA -->

					    	<div class="col-md-4 mt-3">
					    		<label for="validationTooltip01">Código</label>
									<div class="input-group ">
										<div class="input-group-prepend">
										<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".cuenta-modal" v-on:click=""><font-awesome-icon icon="search"/></button>
									</div>
									<input class="form-control form-control-sm" type="text" v-model="cuenta.COMPRA" v-on:blur="obtenerDatosDeuda">
								</div>
					    	</div>

					    	<!-- ------------------------------------------------------------------------ -->

					    	<!-- FECHA -->

					    	<div class="col-md-4 mt-3"">
					    		<label for="validationTooltip01">Fecha</label>
								<div class="input-group input-group-sm date">
									<div class="input-group-prepend ">
										    <span class="input-group-text" id="inputGroup-sizing-sm"><font-awesome-icon icon="calendar" /></span>
									</div>
									<input type="text" class="input-sm form-control form-control-sm" id="fecha" v-model="pago.FECHA" data-date-format="yyyy-mm-dd" v-bind:class="{ 'is-invalid': validar.FECHA }"/>
								</div>
					    	</div>

					    	<!-- ------------------------------------------------------------------------ -->

					    	<!-- NRO. RECIBO -->

					    	<div class="col-md-4 mt-3"">
					    		<label for="validationTooltip01">Nro. de Recibo</label>
								<div class="input-group ">
									<input class="form-control form-control-sm" type="text" v-model="pago.RECIBO" v-on:blur="" v-bind:class="{ 'is-invalid': validar.RECIBO }">
								</div>	
					    	</div>

					    	<!-- ------------------------------------------------------------------------ -->

					    	<!-- SEPARAR -->

					    	<div class="col-md-12 mt-3">
					    		<hr/>
					    	</div>

					    </div>

					    <div class="row" v-if="mostrar_cuerpo">

					    	<!-- ------------------------------------------------------------------------ -->

					    	<!-- PROVEEDOR -->

					    	<div class="col-md-6 mt-3"">
					    		<label for="validationTooltip01">Proveedor</label>
								<h4>{{proveedor.NOMBRE}}</h4>
					    	</div>

					    	<!-- ------------------------------------------------------------------------ -->

					    	<!-- NRO. FACTURA -->

					    	<div class="col-md-6 mt-3"">
					    		<label for="validationTooltip01">Nro. Factura</label>
					    		<span class="float-right">
					    			<a class="text-info" data-toggle="collapse" href="#cuentas" aria-expanded="false" aria-controls="pagos"><small>Cuentas</small></a>&nbsp;
					    			<a class="text-info" data-toggle="collapse" href="#pagos" aria-expanded="false" aria-controls="pagos"><small>Pagos</small></a>
					    		</span>
								<h4>{{cuenta.NRO_FACTURA}}</h4>
					    	</div>
							
					    	<!-- ------------------------------------------------------------------------ -->

					    	<!-- SEPARAR -->

					    	<div class="col-md-12 mt-3">
					    		<hr/>
					    	</div>

					    	<!-- ------------------------------------------------------------------------ -->

					    	<!-- NRO. MOVIMIENTO -->

					    	<div class="col-md-3 mt-3"">
					    		<label class="font-italic" for="validationTooltip01">- Nro. Movimiento</label>
					    		<br/>
								<font-awesome-icon icon="list-ol" />	
								{{cuenta.COMPRA}}	
					    	</div>

					    	<!-- ------------------------------------------------------------------------ -->

					    	<!-- FECHA -->

					    	<div class="col-md-3 mt-3"">
					    		<label class="font-italic" for="validationTooltip01">- Fecha Factura</label>
					    		<br/>
								<font-awesome-icon icon="calendar" />	
								{{cuenta.FECHA_FACTURA}}
					    	</div>

					    	<!-- ------------------------------------------------------------------------ -->

					    	<!-- DEUDA INICIAL -->

					    	<div class="col-md-3 mt-3"">
					    		<label class="font-italic" for="validationTooltip01">- Deuda Inicial</label>
					    		<br/>
								<p class="font-weight-bold">{{cuenta.INICIAL}}</p>
					    	</div>

					    	<!-- ------------------------------------------------------------------------ -->

					    	<!-- DEUDA FINAL -->

					    	<div class="col-md-3 mt-3"">
					    		<label class="font-italic" for="validationTooltip01">- Deuda Actual</label>
								<br/>
								<p class="font-weight-bold">{{cuenta.FINAL}}</p>
					    	</div>

					    	<!-- ------------------------------------------------------------------------ -->

					    	<div class="col-md-3 mt-3"">
					    		<label class="font-italic" for="validationTooltip01">- Cuotas</label>
								<br/>
								{{cuenta.NRO_CUOTA}}	
					    	</div>

					    	<!-- ------------------------------------------------------------------------ -->

					    	<!-- VENCIMIENTO -->

					    	<div class="col-md-3 mt-3"">
					    		<label class="font-italic" for="validationTooltip01">- Primer Vencimiento</label>
								<br/>
								<font-awesome-icon icon="calendar" />	
								{{cuenta.VENCIMIENTO}}	
					    	</div>

					    	<!-- ------------------------------------------------------------------------ -->

					    	<!-- MONEDA -->

					    	<div class="col-md-3 mt-3"">
					    		<label class="font-italic" for="validationTooltip01">- Moneda</label>
								<br/>
								{{cuenta.MONEDA_DESCRIPCION}}
					    	</div>

					    	<!-- ------------------------------------------------------------------------ -->

					    	<!-- STATUS -->

					    	<div class="col-md-3 mt-3"">
					    		<label class="font-italic" for="validationTooltip01">- Estatus</label>
								<br/>
								<span v-html="cuenta.ESTATUS"></span>
					    	</div>

					    	<!-- ------------------------------------------------------------------------ -->

					    	<!-- SEPARAR -->

					    	<div class="col-md-12 mt-3">
					    		<hr/>
					    	</div>

					    	<!-- ------------------------------------------------------------------------ -->

					    	<!-- FORMA DE PAGO -->

					    	<div class="col-md-3">
								<forma-pago-textbox :total="cuenta.FINAL" :total_crudo="cuenta.TOTAL_CRUDO" :procesar="procesar" :moneda="cuenta.MONEDA" :candec="cuenta.CANDEC" @datos="formaPago" ref="compontente_medio_pago"></forma-pago-textbox>	
					    	</div>

					    	<!-- ------------------------------------------------------------------------ -->

					    	<!-- BOTON PROCESAR -->

					    	<div class="col-md-12">
					    		<div class="float-right mt-3">
					    		 	<button class="btn btn-success btn-sm" v-on:click="guardar">Procesar</button>
					    		</div>	
					    	</div>

					    	<!-- ------------------------------------------------------------------------ -->

					    </div>	
					  </div>
					</div>
				</div>

				<!-- ******************************************************************* -->

		        <!-- MODAL PRODUCTOS -->

		                <div class="modal fade cuenta-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		                  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
		                    <div class="modal-content">
		                      <div class="modal-header">
		                        <h5 class="modal-title" id="exampleModalCenterTitle">Cuentas</h5>
		                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		                          <span aria-hidden="true">&times;</span>
		                        </button>
		                      </div>
		                      <div class="modal-body">
		                            <table id="tablaModalCuenta" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
		                                <thead>
		                                    <tr>
		                                        <th>Compra</th>
		                                        <th>Proveedor</th>
		                                        <th>Vencimiento</th>
		                                        <th>Cuotas</th>
		                                        <th>Creación</th>
		                                        <th>Inicial</th>
		                                        <th>Actual</th>
		                                        <th>Estatus</th>
		                                    </tr>
		                                </thead>
		                                <tbody>
		                                    <td></td>
		                                </tbody>
		                            </table>        
		                      </div>
		                    </div>
		                  </div>
		                </div>  

		        <!-- ******************************************************************* -->

		        <!-- MODAL DETALLE PAGO -->

		                <div class="modal fade pago-detalle-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		                  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
		                    <div class="modal-content">
		                      <div class="modal-header bg-info text-white">
		                        <h5 class="modal-title" id="exampleModalCenterTitle"><small>DETALLE PAGO</small></h5>
		                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		                          <span aria-hidden="true">&times;</span>
		                        </button>
		                      </div>
		                      <div class="modal-body">

		                      	<div class="containter">

			                      	<div class="row">

			                            <!-- ------------------------------------------------------------------------ -->

								    	<!-- NRO. MOVIMIENTO -->

								    	<div class="col-md-3 mt-3 text-center">
								    		<label class="font-italic" for="validationTooltip01">- Pago</label>
								    		<br/>
											<font-awesome-icon icon="money-bill-alt"/>	
											{{pagos.PAGO}}	
								    	</div>

								    	<!-- ------------------------------------------------------------------------ -->

								    	<!-- FECHA -->

								    	<div class="col-md-3 mt-3 text-center">
								    		<label class="font-italic" for="validationTooltip01">- Fecha Pago</label>
								    		<br/>
											<font-awesome-icon icon="calendar" />	
											{{pagos.FECHA}}
								    	</div>

								    	<!-- ------------------------------------------------------------------------ -->

								    	<!-- DEUDA INICIAL -->

								    	<div class="col-md-3 mt-3 text-center">
								    		<label class="font-italic" for="validationTooltip01">- Recibo</label>
								    		<br/>
											<p>{{pagos.RECIBO}}</p>
								    	</div>

								    	<!-- ------------------------------------------------------------------------ -->

								    	<!-- TARJETA -->

								    	<div class="col-md-3 mt-3 text-center">
								    		<label class="font-italic" for="validationTooltip01">- Tarjeta</label>
								    		<br/>
											<p><font-awesome-icon icon="credit-card"/> {{pagos.TARJETA}} - {{pagos.TARJETA_DESCRIPCION}}</p>
								    	</div>

								    	<!-- ------------------------------------------------------------------------ -->

								    	<!-- SEPARAR -->

								    	<div class="col-md-12 mt-3">
								    		<hr/>
								    	</div>

								    	<!-- ------------------------------------------------------------------------ -->

								    	<div class="col-md-12 mt-3">
									    	<div class="card">

												  <div class="card-header " >
												    <font-awesome-icon icon="money-bill-alt"/> Monedas
												  </div>

												  <div class="card-body">

													  <div class="row">

												    	<div class="col-md-3 text-center">
												    		<label class="font-italic" for="validationTooltip01">- Guaranies</label>
															<br/>
															{{pagos.GUARANIES}}	
												    	</div>

												    	<!-- ------------------------------------------------------------------------ -->

												    	<!-- VENCIMIENTO -->

												    	<div class="col-md-3 text-center">
												    		<label class="font-italic" for="validationTooltip01">- Dolares</label>
															<br/>
															{{pagos.DOLARES}}	
												    	</div>

												    	<!-- ------------------------------------------------------------------------ -->

												    	<!-- MONEDA -->

												    	<div class="col-md-3 text-center">
												    		<label class="font-italic" for="validationTooltip01">- Reales</label>
															<br/>
															{{pagos.REALES}}
												    	</div>

												    	<!-- ------------------------------------------------------------------------ -->

												    	<!-- STATUS -->

												    	<div class="col-md-3 text-center">
												    		<label class="font-italic" for="validationTooltip01">- Pesos</label>
															<br/>
															{{pagos.PESOS}}
												    	</div>

												      </div>	
											    </div>
											</div>
										</div>

								    	<!-- ------------------------------------------------------------------------ -->

								    	<!-- SEPARAR -->

								    	<div class="col-md-12 mt-3 " v-if="pagos.CHEQUES.length > 0">
								    		<hr/>
								    	</div>

								    	<!-- ------------------------------------------------------------------------ -->

								    	<div class="col-md-12 mt-3" v-if="pagos.CHEQUES.length > 0">

								    		<div class="card mb-3">
											  <div class="card-header " >
											    <font-awesome-icon icon="money-check-alt"/> Cheques
											  </div>
											  <div class="card-body">
											    	<table class="table table-sm table-borderless">
													  <thead>
													    <tr class="">
													      <th scope="col">#</th>
													      <th scope="col">Banco</th>
													      <th scope="col">Número</th>
													      <th scope="col">Fecha</th>
													      <th scope="col">Forma</th>
													      <th scope="col">Monto</th>
													    </tr>
													  </thead>
													  <tbody style="font-size: 12px">
													    <tr v-for="(cheque, index) in pagos.CHEQUES">
													      <th scope="row">{{index + 1}}</th>
													      <td>{{cheque.DESCRIPCION}}</td>
													      <td>{{cheque.NUMERO}}</td>
													      <td>{{cheque.FECHA_COBRO}}</td>
													      <td>{{cheque.FORMA}}</td>
													      <td>{{cheque.MONTO}}</td>
													    </tr>
													  </tbody>
													</table>
											  </div>
											</div>

										</div>

										<!-- ------------------------------------------------------------------------ -->

								    </div>

								</div>

		                      </div>
		                    </div>
		                  </div>
		                </div>  

		        <!-- ******************************************************************* -->

			
		</div>

		<!-- ------------------------------------------------------------------------ -->

		<div v-else>
			<cuatrocientos-cuatro></cuatrocientos-cuatro>
		</div>
		
		<!-- ------------------------------------------------------------------------ -->
		
	</div>	
</template>
<script>

	 export default {
	  props: ['candec', 'monedaCodigo'],	
      data(){
        return {
        	procesar: false,
        	mostrar_cuerpo: false,
        	cuenta: {
        		CODIGO: '',
        		COMPRA: '',
        		VENCIMIENTO: '',
        		NRO_FACTURA: '',
        		FECHA: '',
        		INICIAL: '',
        		FINAL: '',
        		NRO_CUOTA: '',
        		FECHA_FACTURA: 'TEST',
        		ESTATUS: '',
        		TOTAL_CRUDO: '',
        		MONEDA: '',
        		MONEDA_DESCRIPCION: '',
        		CANDEC: ''
        	},
        	pago: {
        		FECHA: '',
        		RECIBO: '',
        	},
        	proveedor: {
        		CODIGO: '',
        		NOMBRE: ''
        	}, moneda: {
        		CODIGO: '',
        		DESCRIPCION: '',
        		DECIMAL: ''
        	}, validar: {
        		FECHA: '',
        		RECIBO: '',
        	},
        	respuesta: [],
        	pagos: {
        		CHEQUES: ''
        	},
        	tables: {
        		pagos: ''
        	}
        }
      }, 
      methods: {
      	descripcionMoneda(valor){

        	// ------------------------------------------------------------------------

        	// DESCRIPCION MONEDA

        	this.moneda.DESCRIPCION = valor;

        	// ------------------------------------------------------------------------

        }, cantidadDecimal(valor){

        	// ------------------------------------------------------------------------

        	// DESCRIPCION MONEDA

        	this.moneda.DECIMAL = valor;

        	// ------------------------------------------------------------------------

        }, obtenerDatatableCompra() {

        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES

        	let me = this;

        	// ------------------------------------------------------------------------

        	var tableCuentaCompra = $('#tablaModalCuentaCompra').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content'),
                                    "codigo": me.cuenta.COMPRA
                                 },
                                 "url": "/deuda/deudaCompraDatatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "FEC_VENC" },
                            { "data": "PLAN_PAGO" },
                            { "data": "NRO_CUOTA" },
                            { "data": "TOTAL" },
                            { "data": "ESTATUS" }
                        ]      
                    });

        	// ------------------------------------------------------------------------

        }, obtenerDatatablePago() {

        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES

        	let me = this;

        	// ------------------------------------------------------------------------

        	this.tables.pagos = $('#tablaPago').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content'),
                                    "codigo": me.cuenta.COMPRA
                                 },
                                 "url": "/pagos_prov/datatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
				        "columnDefs": [
				            {
				                "targets": [ 0 ],
				                "visible": false,
				                "searchable": false
				            },
				        ],       
                        "columns": [
                        	{ "data": "ID" },
                            { "data": "PAGO" },
                            { "data": "FECHA" },
                            { "data": "GUARANIES" },
                            { "data": "DOLARES" },
                            { "data": "PESOS" },
                            { "data": "REALES" },
                            { "data": "TARJETA" },
                            { "data": "RECIBO" }
                        ]      
                    });

        	// ------------------------------------------------------------------------

        }, obtenerDatosDeuda(){

        	// ------------------------------------------------------------------------

        	let me = this;

        	// ------------------------------------------------------------------------

        	Common.datosNotaCuentaCommon(me.cuenta.COMPRA).then(data => {

        		if (data.response === true) {

        			// ------------------------------------------------------------------------

        			me.mostrar_cuerpo = true;
        			me.cuenta.VENCIMIENTO = data.deuda.FEC_VENC;
	        		me.cuenta.NRO_FACTURA = data.deuda.NRO_FACTURA;
	        		me.cuenta.FECHA_FACTURA = data.deuda.FEC_FACTURA;
	        		me.cuenta.INICIAL = data.deuda.TOTAL_COMPRA;
	        		me.cuenta.FINAL = data.deuda.TOTAL;
	        		me.cuenta.NRO_CUOTA = data.deuda.NRO_CUOTA;
	        		me.cuenta.ESTATUS = data.deuda.ESTATUS;
	        		me.proveedor.NOMBRE = data.deuda.NOMBRE;
	        		me.cuenta.TOTAL_CRUDO = data.deuda.TOTAL_CRUDO;
	        		me.cuenta.MONEDA = data.deuda.MONEDA;
	        		me.cuenta.MONEDA_DESCRIPCION = data.deuda.MONEDA_DESCRIPCION;
	        		me.cuenta.CANDEC = data.deuda.CANDEC;

	        		// ------------------------------------------------------------------------

	        		// OBTENER DATATABLE COMPRA 

	        		me.obtenerDatatableCompra();

	        		// ------------------------------------------------------------------------

	        		// OBTENER DATATABLE PAGO

	        		me.obtenerDatatablePago();

	        		// ------------------------------------------------------------------------

        		} else {
        			me.mostrar_cuerpo = false;
        		}
        		
        		
        	});

        	// ------------------------------------------------------------------------

        }, obtenerDatosPago(id){

        	// ------------------------------------------------------------------------

        	let me = this;

        	// ------------------------------------------------------------------------

        	Common.datosPagoUnicoCommon(id).then(data => {

        		if (data.response === true) {

        			// ------------------------------------------------------------------------

        			me.pagos = data.pago;

	        		// ------------------------------------------------------------------------

        		} else {
        			//me.mostrar_cuerpo = false;
        		}
        		
        		
        	});

        	// ------------------------------------------------------------------------

        }, formaPago(datos) {

        	// ------------------------------------------------------------------------

        	let me = this;

        	// ------------------------------------------------------------------------

        	// GUARDAR 

        	this.respuesta = {
        		cabecera: this.pago,
        		codigo: this.cuenta.COMPRA,
        		moneda: this.cuenta.MONEDA,
        		pago: datos
        	}

        	Swal.fire({
				title: 'Estas seguro ?',
				text: "Guardar el pago !",
				type: 'warning',
				showLoaderOnConfirm: true,
				showCancelButton: true,
				confirmButtonColor: '#d33',
				cancelButtonColor: '#3085d6',
				confirmButtonText: 'Si, guardalo!',
				cancelButtonText: 'Cancelar',
				preConfirm: () => {
				    return Common.guardarPagoProveedorCommon(me.respuesta).then(data => {
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
				console.log(result);
				if (result.value.response) {

					// ------------------------------------------------------------------------

					Swal.fire(
						'Guardado !',
						 result.value.statusText,
						'success'
					)

					// ------------------------------------------------------------------------

					// RE OBTENER DATOS DESPUES DEL GUARDADO 

					this.obtenerDatosDeuda();

					// ------------------------------------------------------------------------

				}
			})

        	// ------------------------------------------------------------------------

        }, controlador() {

        	// ------------------------------------------------------------------------

        	let me = this;
        	var falta = false;

        	// ------------------------------------------------------------------------

        	// FECHA 

        	if (me.pago.FECHA.length === 0) {
                me.validar.FECHA = true;
                falta = true;
            } else {
                me.validar.FECHA = false;
            }

            // ------------------------------------------------------------------------

            // RECIBO 

            if (me.pago.RECIBO.length === 0) {
                me.validar.RECIBO = true;
                falta = true;
            } else {
                me.validar.RECIBO = false;
            }

            // ------------------------------------------------------------------------

            // RETORNAR FALTA - SI ES TRUE SE DETIENE EL GUARDADO 
            // SI ES FALSE CONTINUA LA OPERACION 

            return falta;

        	// ------------------------------------------------------------------------

        }, guardar() {

        	// ------------------------------------------------------------------------

        	// CONTROLADOR

        	if (this.controlador() === true) {
        		return;
        	}

        	// ------------------------------------------------------------------------

        	this.$refs.compontente_medio_pago.procesarFormas();

        	// ------------------------------------------------------------------------

        }
      },
        mounted() {

        		// ------------------------------------------------------------------------

        		let me = this;

        		// ------------------------------------------------------------------------

        		// INICIALIZAR MONEDA 

        		me.moneda.CODIGO = String(me.monedaCodigo);

        		// ------------------------------------------------------------------------

        		// FECHAS 

        		$('#sandbox-container .input-daterange').datepicker({
		   		    	    keyboardNavigation: false,
    						forceParse: false
    			});

    			$("#vencimiento").datepicker().on(
			     		"changeDate", () => {me.cuenta.VENCIMIENTO = $('#vencimiento').val()}
				);

				$("#fecha").datepicker().on(
			     		"changeDate", () => {me.pago.FECHA = $('#fecha').val()}
				);

				$("#fecha_factura").datepicker().on(
			     		"changeDate", () => {me.cuenta.FECHA_FACTURA = $('#fecha_factura').val()}
				);

    			$('[data-toggle="tooltip"]').tooltip()

    			// ------------------------------------------------------------------------

    			var tableCuenta = $('#tablaModalCuenta').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/deuda/deudaDatatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "COMPRA" },
                            { "data": "PROVEEDOR" },
                            { "data": "FEC_VENC" },
                            { "data": "NRO_CUOTA" },
                            { "data": "FECALTAS" },
                            { "data": "INICIAL" },
                            { "data": "ACTUAL" },
                            { "data": "ESTATUS" }
                        ]      
                    });

    			// ------------------------------------------------------------------------

    			// OBTENER DATOS DEUDA 

    			tableCuenta.on('click', 'tbody tr', function() {
    				me.cuenta.COMPRA = tableCuenta.row(this).data().COMPRA;
    				$('.cuenta-modal').modal('hide');
    				me.obtenerDatosDeuda();
    			})

    			// ------------------------------------------------------------------------

    			this.tables.pagos = $('#tablaPago').DataTable();

    			this.tables.pagos.on('click', 'tbody tr', function() {
    				me.obtenerDatosPago(me.tables.pagos.row(this).data().ID);
    				$('.pago-detalle-modal').modal('show');
    			})

    			// ------------------------------------------------------------------------

        }	
        	
    }
</script>