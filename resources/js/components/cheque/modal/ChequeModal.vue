<template>
	<div class="container-fluid">
		<div class="modal fade" id="modalCheque"  role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="exampleModalLabel"><small>CHEQUE</small></h5>
		      </div>
		      <div class="modal-body bg-light">
		        	<div class="row">

		        		<!-- ------------------------------------------------------------------------ -->

		        		<!-- BANCO -->

		        		<div class="col-md-2">
		        			<label for="validationTooltip01">Banco</label>
		        		</div>
		        			
		        		<div class="col-md-3">
		        			<div class="input-group ">
								<div class="input-group-prepend">
									<button type="button" class="btn btn-info btn-sm" v-on:click="mostrarBancos()"><font-awesome-icon icon="search"/></button>
								</div>
								<input class="form-control form-control-sm" type="text" v-model="cheque.CODIGO_BANCO">
							</div>
		        		</div>

		        		<div class="col-md-7">
							<input class="form-control form-control-sm" type="text" v-model="cheque.DESCRIPCION_BANCO" v-bind:class="{ 'is-invalid': validar.DESCRIPCION_BANCO }" disabled>
		        		</div>

		        		<!-- ------------------------------------------------------------------------ -->

		        		<!-- CLIENTE -->

		        		<div class="col-md-2 mt-3" v-if="mostrar.CLIENTE">
		        			<label for="validationTooltip01">Cliente</label>
		        		</div>
		        			
		        		<div class="col-md-3 mt-3" v-if="mostrar.CLIENTE">
		        			<div class="input-group ">
								<div class="input-group-prepend">
									<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".cuenta-modal" v-on:click=""><font-awesome-icon icon="search"/></button>
								</div>
								<input class="form-control form-control-sm" type="text">
							</div>
		        		</div>

		        		<div class="col-md-7 mt-3" v-if="mostrar.CLIENTE">
							<input class="form-control form-control-sm" type="text">
		        		</div>

		        		<!-- ------------------------------------------------------------------------ -->

		        		<!-- SEPARADOR -->

		        		<div class="col-md-12" v-if="mostrar.TERCERO">
		        			<hr/>
		        		</div>

		        		<!-- ------------------------------------------------------------------------ -->

		        		<!-- TERCERO -->

		        		<div class="col-md-6" v-if="mostrar.TERCERO">
		        			<label>Tercero</label>
							<input class="form-control form-control-sm" type="text">
		        		</div>

		        		<div class="col-md-6" v-if="mostrar.TERCERO">
		        			<label >Telefono</label>
							<input class="form-control form-control-sm" type="text">
		        		</div>

		        		<!-- ------------------------------------------------------------------------ -->

		        		<!-- SEPARADOR -->

		        		<div class="col-md-12">
		        			<hr/>
		        		</div>

		        		<!-- ------------------------------------------------------------------------ -->

		        		<!-- FORMA DE PAGO -->

		        		<div class="col-md-4">
		        			<label>Forma de Pago</label>
		        			<select class="custom-select custom-select-sm" v-model="cheque.FORMA" v-bind:class="{ 'is-invalid': validar.FORMA }">
				                <option value="1">En fecha</option>
				                <option value="2">Diferido</option>
				            </select>
		        		</div>

		        		<!-- ------------------------------------------------------------------------ -->

		        		<!-- NUMERO DE CHEQUE -->

		        		<div class="col-md-4">
		        			<label>N° de Cheque</label>
							<input class="form-control form-control-sm" type="text" v-model="cheque.NUMERO" v-bind:class="{ 'is-invalid': validar.NUMERO }">
		        		</div>

		        		<!-- ------------------------------------------------------------------------ -->

		        		<!-- FECHA COBRO -->

		        		<div class="col-md-4">
		        			<label>Fecha Cobro</label>
		        			<div id="sandbox-container">
								<div class="input-daterange input-group input-group-sm date">
									<div class="input-group-prepend ">
										<span class="input-group-text" id="inputGroup-sizing-sm"><font-awesome-icon icon="calendar" /></span>
									</div>
									<input type="text" class="input-sm form-control form-control-sm" id="fecha_cobro" v-model="cheque.FECHA_COBRO" data-date-format="yyyy-mm-dd" v-bind:class="{ 'is-invalid': validar.FECHA_COBRO }"/>
								</div>
							</div>	
		        		</div>

		        		<!-- ------------------------------------------------------------------------ -->

		        		<!-- MONEDA -->

		        		<div class="col-md-6 mt-3">
							<select-moneda v-model="moneda.CODIGO" @descripcion_moneda="descripcionMoneda" @cantidad_decimales="cantidadDecimal"></select-moneda>
							<span class="badge badge-danger" v-if="mostrar.SINCOTIZACION">No hay cotización</span>
		        		</div>

		        		<!-- ------------------------------------------------------------------------ -->

		        		<!-- IMPORTE -->

		        		<div class="col-md-6 mt-3">
		        			<label>Importe</label>
							<input class="form-control form-control-sm" type="text" v-model="cheque.IMPORTE" v-bind:class="{ 'is-invalid': validar.IMPORTE }" v-on:blur="formatoImporte" :disabled="deshabilitar.IMPORTE">
		        		</div>

		        		<!-- ------------------------------------------------------------------------ -->

			        	<!-- CHEQUES AGREGADOS -->

			        	<div class="col-md-12 mt-3">
				        	<table class="table table-sm" id="tablaCheque" v-if="datas.length > 0" style="font-size: 12px">
							  <thead class="thead-light">
							    <tr>
							      <th scope="col">#</th>
							      <th scope="col">Banco</th>
							      <th scope="col">Cheque</th>
							      <th scope="col">Fecha</th>
							      <th scope="col">Importe</th>
							      <th scope="col">Acción</th>
							    </tr>
							  </thead>
							  <tbody>
							    <tr v-for="(data, index) in datas ">
							      <th>{{index+1}}</th>
							      <td>{{data.BANCO}}</td>
							      <td>{{data.NUMERO}}</td>
							      <td>{{data.FECHA}}</td>
							      <td>{{data.DESCRIPCION_MONEDA}} {{data.IMPORTE}}</td>
							      <td><a href='#' id='eliminarTransferencia' title='Eliminar' v-on:click="eliminar(index)"><i class='fa fa-trash text-danger' aria-hidden='true'></i></a></td>
							    </tr>
							  </tbody>
							</table>
						</div>

						<!-- ------------------------------------------------------------------------ -->

		        	</div>

		      </div>
		      <div class="modal-footer">
		      	<button type="button" class="btn btn-success btn-sm" v-on:click="agregar()"><font-awesome-icon icon="plus" /> Agregar</button>
		        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" v-on:click="confirmar()">Confirmar</button>
		      </div>
		    </div>
		  </div>
		</div>

		<!-- ------------------------------------------------------------------------ -->

		<!-- BANCOS -->

		<div class="modal fade" id="modalBanco" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-xl" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title text-primary" id="exampleModalLabel"><small>Bancos</small></h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        <table id="bancos" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
					            <thead>
					                <tr>
					                    <th>Codigo</th>
					                    <th>Descripcion</th>
					                </tr>
					            </thead>
					            <tbody>
					            </tbody>
					        </table>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
		      </div>
		    </div>
		  </div>
		</div>

		<!-- ------------------------------------------------------------------------ -->

	</div>
</template>

<script>
	 export default {
	  props: ['cotizacion', 'moneda_principal'],
      data(){
        return {
          open: false,
          cheque: {
          	CODIGO_BANCO: '',
          	DESCRIPCION_BANCO: '',
          	FECHA_COBRO: '',
          	FORMA: '1',
          	NUMERO: '',
          	IMPORTE: ''
          },
          mostrar: {
          	CLIENTE: false,
          	TERCERO: false,
          	SINCOTIZACION: false
          },
          validar: {
          	CODIGO_BANCO: '',
          	FECHA_COBRO: '',
          	FORMA: '',
          	NUMERO: '',
          	IMPORTE: ''
          },
          moneda: {
          	CODIGO: '',
          	DECIMAL: '',
          	DESCRIPCION: ''
          },
          deshabilitar: {
          	IMPORTE: false
          },
          datas: []
        }
      }, 
      methods: {
      		mostrarModal(codigo){

      			// ------------------------------------------------------------------------

      			// LLAMAR MODAL CHEQUE

      			$('#modalCheque').modal('show');
      			//$("#modalCheque").appendTo("body");
      			$('#modalCheque').modal({backdrop: 'static', keyboard: false});

      			// ------------------------------------------------------------------------
            	
            }, 
            enviarOpcionesPadre(codigo, descripcion){

            	// ------------------------------------------------------------------------

              	this.$emit('codigo', codigo);
              	this.$emit('descripcion', descripcion);

              	// ------------------------------------------------------------------------

            }, cantidadDecimal(valor){

	        	// ------------------------------------------------------------------------

	        	// DESCRIPCION MONEDA

	        	if (this.moneda.CODIGO === "2") {
	        		if (this.cotizacion.deshabilitar_$ === true) {
		        		this.mostrar.SINCOTIZACION = true;
		        		this.deshabilitar.IMPORTE = true;
		        	} else {
		        		this.mostrar.SINCOTIZACION = false;
		        		this.deshabilitar.IMPORTE = false;
		        	}
	        	}

	        	if (this.moneda.CODIGO === "1") {
	        		if (this.cotizacion.deshabilitar_gs === true) {
	        			this.mostrar.SINCOTIZACION = true;
	        			this.deshabilitar.IMPORTE = true;
	        		} else {
		        		this.mostrar.SINCOTIZACION = false;
		        		this.deshabilitar.IMPORTE = false;
		        	}
	        	}

	        	if (this.moneda.CODIGO === "3") {
	        		if (this.cotizacion.deshabilitar_ps === true) {
	        			this.mostrar.SINCOTIZACION = true;
	        			this.deshabilitar.IMPORTE = true;
	        		} else {
		        		this.mostrar.SINCOTIZACION = false;
		        		this.deshabilitar.IMPORTE = false;
		        	}
	        	}

	        	if (this.moneda.CODIGO === "4") {
	        		if (this.cotizacion.deshabilitar_rs === true) {
	        			this.mostrar.SINCOTIZACION = true;
	        			this.deshabilitar.IMPORTE = true;
	        		} else {
		        		this.mostrar.SINCOTIZACION = false;
		        		this.deshabilitar.IMPORTE = false;
		        	}
	        	}

	        	this.moneda.DECIMAL = valor;
	        	
	        	// ------------------------------------------------------------------------

	        	// VACIAR TEXTBOX AGREGAR PRODUCTO

	            //this.inivarAgregarProducto();

	            // ------------------------------------------------------------------------

	        	// LLAMAR FORMATOS DE PRECIOS AL CAMBIAR DE MONEDA

	        	this.formatoImporte();
	        	// this.formatoExentaFactura();
	        	// this.formatoPrecio();
	        	// this.formatoCostoUnitario();
	        	// this.formatoPreMayorista();

	        	// ------------------------------------------------------------------------

	        }, descripcionMoneda(valor){

	        	// ------------------------------------------------------------------------

	        	// DESCRIPCION MONEDA

	        	this.moneda.DESCRIPCION = valor;

	        	// ------------------------------------------------------------------------

	        }, formatoImporte() {

	        	// ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;

	            // ------------------------------------------------------------------------

	            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A IMPORTE

	            me.cheque.IMPORTE = Common.darFormatoCommon(me.cheque.IMPORTE, me.moneda.DECIMAL);

	            // ------------------------------------------------------------------------

	        }, agregar() {
            	
            	// ------------------------------------------------------------------------

            	// DATA 

            	if (this.controlador() === true) {
            		return;
            	}

            	// ------------------------------------------------------------------------

            	// DATA 

            	this.datas.push({
            						CODIGO_BANCO: this.cheque.CODIGO_BANCO,
            	            		BANCO: this.cheque.DESCRIPCION_BANCO.substring(0, 15),
            	            		FECHA: this.cheque.FECHA_COBRO,
            	            		FORMA: this.cheque.FORMA,
            	            		NUMERO: this.cheque.NUMERO,
            	            		IMPORTE: this.cheque.IMPORTE,
            	            		MONEDA: this.moneda.CODIGO,
            	            		DESCRIPCION_MONEDA: this.moneda.DESCRIPCION
            	            	})

            	// ------------------------------------------------------------------------

            	// LIMPIAR
            	
            	this.limpiar();

				// ------------------------------------------------------------------------

            }, controlador(){

            	// ------------------------------------------------------------------------

	        	let me = this;
	        	var falta = false;

	        	// ------------------------------------------------------------------------

	        	// DESCRIPCION BANCO 

	        	if (me.cheque.DESCRIPCION_BANCO.length === 0) {
	                me.validar.DESCRIPCION_BANCO = true;
	                falta = true;
	            } else {
	                me.validar.DESCRIPCION_BANCO = false;
	            }

	            // ------------------------------------------------------------------------

	            // NUMERO CHEQUE

	            if (me.cheque.NUMERO.length === 0) {
	                me.validar.NUMERO = true;
	                falta = true;
	            } else {
	                me.validar.NUMERO = false;
	            }

	            // ------------------------------------------------------------------------

	            // FECHA COBRO CHEQUE

	            if (me.cheque.FECHA_COBRO.length === 0) {
	                me.validar.FECHA_COBRO = true;
	                falta = true;
	            } else {
	                me.validar.FECHA_COBRO = false;
	            }
	            
	            // ------------------------------------------------------------------------

	            // IMPORTE CHEQUE

	            if (me.cheque.IMPORTE.length === 0) {
	                me.validar.IMPORTE = true;
	                falta = true;
	            } else {
	                me.validar.IMPORTE = false;
	            }
	            
	            // ------------------------------------------------------------------------

	            // MONEDA

	            if (me.deshabilitar.IMPORTE === true) {
	            	falta = true;
	            }

	            // ------------------------------------------------------------------------

	            // RETORNAR FALTA - SI ES TRUE SE DETIENE EL GUARDADO 
	            // SI ES FALSE CONTINUA LA OPERACION 

	            return falta;

	        	// ------------------------------------------------------------------------

            }, limpiar() {

            	// ------------------------------------------------------------------------

            	// LIMPIAR 

            	this.cheque = {
		          	CODIGO_BANCO: '',
		          	DESCRIPCION_BANCO: '',
		          	FECHA_COBRO: '',
		          	FORMA: '1',
		          	NUMERO: '',
		          	IMPORTE: ''
		        }

            	// ------------------------------------------------------------------------

            }, confirmar() {
            	
            	// ------------------------------------------------------------------------

            	// ENVIAR DATOS AL COMPONENTE PADRE 

            	this.$emit('data', this.datas);

            	// ------------------------------------------------------------------------

            }, eliminar(element) {

            	// ------------------------------------------------------------------------

            	const index = this.datas.indexOf(element);
  				this.datas.splice(index, 1);

  				// ------------------------------------------------------------------------

            }, mostrarBancos(){

            	$('#modalBanco').modal('show');
            	 //$('#modalBanco').modal({backdrop: 'static', keyboard: false});
            }
      },
        mounted() {

        	// ------------------------------------------------------------------------

        	let me = this;

        	// ------------------------------------------------------------------------

        	// SELECIONAR MONEDA PRINCIPAL

        	this.moneda.CODIGO = this.moneda_principal.toString();

        	// ------------------------------------------------------------------------

       		//  	$('#sandbox-container .input-daterange').datepicker({
		   	//     	    keyboardNavigation: false,
    		// 			forceParse: false
	    	// });

	         $("#fecha_cobro").datepicker().on(
				     		"changeDate", () => {me.cheque.FECHA_COBRO = $('#fecha_cobro').val()}
				);

	  //        $('#fecha_cobro').datepicker({
			//     dateFormat: 'yy-mm-dd'
			// });

    		// ------------------------------------------------------------------------

    		// PREPARAR DATATABLE 

	 		var tableBanco = $('#bancos').DataTable({
	                "processing": true,
	                "serverSide": true,
	                "destroy": true,
	                "bAutoWidth": true,
	                "select": true,
	                "ajax":{
	                    "url": "/banco/datatable",
	                    "dataType": "json",
	                    "type": "GET",
	                    "contentType": "application/json; charset=utf-8"
	                },
	                "columns": [
	                    { "data": "CODIGO" },
	                    { "data": "DESCRIPCION" }
	                ]    
	        });
                    
	 		// ------------------------------------------------------------------------

	 		// OBTENER DATOS DEUDA 

    		tableBanco.on('click', 'tbody tr', function() {
    			me.cheque.CODIGO_BANCO = tableBanco.row(this).data().CODIGO;
    			me.cheque.DESCRIPCION_BANCO = tableBanco.row(this).data().DESCRIPCION;
    			me.enviarOpcionesPadre(tableBanco.row(this).data().CODIGO, tableBanco.row(this).data().DESCRIPCION);
    			$('#modalBanco').modal('hide');
    		})

    		// ------------------------------------------------------------------------

        }
    }
</script>