<template>
	<div class="container col-9">
		<div class="card mt-3 shadow-sm">
          <h5 class="card-header">Factura Exportacion</h5>
          <div class="card-body">
          	<div class="row">
          		<div class="col">
          			<input  type="file" v-bind:class="{ 'is-invalid': validar.excel }" class="form-control" id="archivoExcel"  @change="subirExcel()">
          		</div>
          		
          	</div>
		     <div class="row mt-2">
		     	<div class="col-3 ">
		     		 <div id="sandbox-container">
		     		 	<label for="SelectedFecha">Fecha:</label>
						<div class="input-daterange input-group">
							 <input     type="text" class="input-sm form-control form-control-sm" name="end" id="SelectedFecha" v-model="cabecera.SelectedFecha" v-bind:class="{ 'is-invalid': validar.fecha }" />
						</div>
							
					</div>	
		     	</div>
		     	<div class="col-5">
		     		<label>Señores:</label>
		     		<input class="input-sm form-control form-control-sm" id="SeñoresTxt" v-bind:class="{ 'is-invalid': validar.señores }" v-model="cabecera.señores">
		     	</div>
		     	<div class="col-4">
		     		<label>Pais:</label>
		     		<input class="input-sm form-control form-control-sm" id="PaisTxt" v-bind:class="{ 'is-invalid': validar.pais }" v-model="cabecera.pais">
		     	</div>
		     	
		     </div>
		     <div class="row mt-2">
		     	<div class="col-4">
		     		<label>Ciudad:</label>
		     		<input class="input-sm form-control form-control-sm" id="CiudadTxt" v-bind:class="{ 'is-invalid': validar.ciudad }" v-model="cabecera.ciudad">
		     	</div>
		     	<div class="col-8">
		     		<label>Direccion:</label>
		     		<input class="input-sm form-control form-control-sm" id="DireccionTxt" v-bind:class="{ 'is-invalid': validar.direccion }" v-model="cabecera.direccion">
		     	</div>
		     </div>
		     <div class="row mt-2">
		     	<div class="col-4">
		     		<label>Telefono:</label>
		     		<input class="input-sm form-control form-control-sm" id="TelefonoTxt" v-bind:class="{ 'is-invalid': validar.telefono }" v-model="cabecera.telefono">
		     	</div>
		     	<div class="col-8">
		     		<label>Condiciones de pago:</label>
		     		<input class="input-sm form-control form-control-sm" id="CondPago" v-bind:class="{ 'is-invalid': validar.condiciones_p }" v-model="cabecera.condiciones_p">
		     	</div>
		     </div>
		     <div class="row">
		     	<div class="col-12 mt-3" align="center">
		     	
		     		
		     			<button type="button"  class="btn btn-secondary btn-block" v-on:click="imprimirFact" ><font-awesome-icon icon="print" /> Imprimir</button>
		     		
		     	
		     	</div>
		     </div>
		     
			     <div class="col-md-12 mt-4" v-if="carga">
					<table id="tabla_factura" class="display nowrap table table-striped table-bordered table-sm mb-3" style="width:100%">
		                <thead>
		                    <tr>
		                        <th>ITEM</th>
		                        <th>Descripción</th>
		                        <th class="cantidadColumna">Cantidad</th>
		                        <th class="precioColumna">Precio</th>
		                        <th class="totalColumna">Total</th>
		                    </tr>
		                </thead>
		                <tbody>
		                    
		                </tbody>
		                <tfoot>
		                	<tr>
		                		<th></th>
		                		<th>TOTALES</th>
		                		<th></th>
			                	<th></th>
			                	<th></th>
		                	</tr>
		                </tfoot>	
		            </table>
				</div>
		  </div>
		</div>
	</div>
</template>
<script>
	import readXlsFile from "read-excel-file"
	export default {
	   data(){
	        return {
	          items: [],
	         
	          carga:false,
	          validar: {
	          	fecha:false,
	          	señores:false,
	          	pais:false,
	          	ciudad:false,
	          	direccion:false,
	          	telefono:false,
	          	condiciones_p:false,
	          	excel:true
	          },
	          cabecera:{
	          	SelectedFecha:'',
	          	señores:'',
	          	pais:'',
	          	ciudad:'',
	          	direccion:'',
	          	telefono:'',
	          	condiciones_p:''
	          },
	          controlar:true,
	          
	        }
	   },
		methods:{
			control(){
				
				let me=this;
				me.controlar=true;
				if(me.validar.excel){
					me.controlar=false;
				}else{
					me.controlar=true;
				}
				if(me.cabecera.SelectedFecha==='' || me.cabecera.SelectedFecha==='null' || me.cabecera.SelectedFecha===null){
					me.controlar=false;
					me.validar.fecha=true;
				}else{
					me.validar.fecha=false;
				}
				if(me.cabecera.señores==='' || me.cabecera.señores===null || me.cabecera.señores==='null'){
					me.controlar=false;
					me.validar.señores=true;
				}else{
					me.validar.señores=false;
				}
				if(me.cabecera.pais==='' || me.cabecera.pais===null || me.cabecera.pais==='null'){
					me.controlar=false;
					me.validar.pais=true;
				}else{
					me.validar.pais=false;
				}
				if(me.cabecera.ciudad==='' || me.cabecera.ciudad===null || me.cabecera.ciudad==='null'){
					me.controlar=false;
					me.validar.ciudad=true;
				}else{
					me.validar.ciudad=false;
				}
				if(me.cabecera.direccion==='' || me.cabecera.direccion===null || me.cabecera.direccion==='null'){
					me.controlar=false;
					me.validar.direccion=true;
				}else{
					me.validar.direccion=false;
				}

				if(me.cabecera.telefono==='' || me.cabecera.telefono===null || me.cabecera.telefono==='null'){
					me.controlar=false;
					me.validar.telefono=true;
				}else{
					me.validar.telefono=false;
				}
				if(me.cabecera.condiciones_p==='' || me.cabecera.condiciones_p===null || me.cabecera.condiciones_p==='null'){
					me.controlar=false;
					me.validar.condiciones_p=true;
				}else{
					me.validar.condiciones_p=false;
				}
				return me.controlar;
			},
			imprimirFact(){
				let me=this;
				var tablaFactura = $('#tabla_factura').DataTable();
				if(!me.control()){
					
					return;
				}
				Common.generarPdfFacturaExportacionCommon(me.cabecera,tablaFactura).then(response => {

							var reader = new FileReader();
							 reader.readAsDataURL(new Blob([response])); 
							reader.onloadend = function() {
							     var base64data = reader.result;
							     base64data = base64data.replace("data:application/octet-stream;base64,", "");
							     me.imprimir_factura(base64data);
							 }

				});
			},
			imprimir_factura(base64) {

				let me = this;
				
				qz.websocket.connect().then(function() { 

					return qz.printers.find('NOMBRE IMPRESORA');              // Pass the printer name into the next Promise

				}).then(function(printer) {

					var config = qz.configs.create(printer);
					var data = [{ 
						type: 'pixel',
           				format: 'pdf',
           				flavor: 'base64',
						data: base64
					}];

					return qz.print(config, data).then(function() {
						qz.websocket.disconnect();
						Swal.close();
					});

				}).catch(function(e) { console.error(e); });

			},
			subirExcel(){
				let me=this;
				const input=document.getElementById("archivoExcel");
				readXlsFile(input.files[0]).then((rows) => {
					this.validar.excel=false;
					this.items=rows;
					this.carga=true;
					setTimeout(function(){
						var tablaFactura = $('#tabla_factura').DataTable();
						tablaFactura.clear().draw();
					    me.dar_formato_tabla();
					    me.cargar_tabla();
					    
					   
					}, 200);
					
					
					
					
				}); 
			},
			cargar_tabla(){
				let me=this;
				var tablaFactura = $('#tabla_factura').DataTable();
				for (let i = 1; i < me.items.length; i++) {
				 
				  tablaFactura.rows.add( [ {
		                    "ITEM": i,
		                    "DESCRIPCION":   me.items[i][1].toString() ,
		                    "CANTIDAD":    parseInt(me.items[i][0]) ,
		                    "PRECIO":  Common.darFormatoCommon(me.items[i][2],2) ,
		                    "TOTAL":      Common.darFormatoCommon(me.items[i][3],2) 
		                    
		            } ] ).draw();

				}
			},
			dar_formato_tabla(){
		    
				var tablaFactura = $('#tabla_factura').DataTable({
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "dom": "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
						"<'row'<'col-sm-12'tr>>" +
						"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
				        "buttons": [
				            { extend: 'copy', text: '<i class="fa fa-copy"></i>', titleAttr: 'Copiar', className: 'btn btn-secondary' },
				        	{ extend: 'excelHtml5', text: '<i class="fa fa-file"></i>', titleAttr: 'Excel', className: 'btn btn-success' },
				            { extend: 'pdfHtml5', text: '<i class="fa fa-file"></i>', titleAttr: 'Pdf', className: 'btn btn-danger' }, 
				            { extend: 'print', text: '<i class="fa fa-print"></i>', titleAttr: 'Imprimir', className: 'btn btn-secondary', title: 'Transferencia', messageTop: 'Productos registrados para Transferencia' }
				        ],
                        "columns": [
                            { "data": "ITEM" },
                            { "data": "DESCRIPCION" },
                            { "data": "CANTIDAD" },
                            { "data": "PRECIO" },
                            { "data": "TOTAL" }
                        ],
                        "footerCallback": function(row, data, start, end, display) {
						  var api = this.api();
						 
						  // *******************************************************************

						  // CANTIDAD 

						  api.columns('.cantidadColumna', {
						    
						  }).every(function() {
						    var cantidad = this
						      .data()
						      .reduce(function(a, b) {

						      	// *******************************************************************

						      	// QUITAR LAS COMAS DE LOS VALORES

						      	a = Common.quitarComaCommon(a);
						      	b = Common.quitarComaCommon(b);
						      	
						      	// *******************************************************************

						        var x = parseFloat(a) || 0;
						        var y = parseFloat(b) || 0;

						        // *******************************************************************

						        // SUMAR VALORES

						        return x + y;

						        // *******************************************************************

						      }, 0);

						      // *******************************************************************

						      // CARGAR EN EL FOOTER

						      $( api.columns('.cantidadColumna').footer() ).html(
					                Common.darFormatoCommon(cantidad, 0)
					           );

						      // *******************************************************************

						  });

						  // *******************************************************************

						  // PRECIO

						  api.columns('.precioColumna', {
						    
						  }).every(function() {
						    var precio = this
						      .data()
						      .reduce(function(a, b) {

						      	// *******************************************************************

						      	// QUITAR LAS COMAS DE LOS VALORES

						      	a = Common.quitarComaCommon(a);
						      	b = Common.quitarComaCommon(b);
						      	
						      	// *******************************************************************

						        var x = parseFloat(a) || 0;
						        var y = parseFloat(b) || 0;

						        // *******************************************************************

						        // SUMAR VALORES

						        return x + y;

						        // *******************************************************************

						      }, 0);

						      // *******************************************************************

						      // CARGAR EN EL FOOTER

						      $( api.columns('.precioColumna').footer() ).html(
					                Common.darFormatoCommon(precio,2)
					           );

						      // *******************************************************************

						  });

						  // *******************************************************************

						  // TOTAL 

						  api.columns('.totalColumna', {
						    
						  }).every(function() {
						    var sum = this
						      .data()
						      .reduce(function(a, b) {

						      	// *******************************************************************

						      	// QUITAR LAS COMAS DE LOS VALORES

						      	a = Common.quitarComaCommon(a);
						      	b = Common.quitarComaCommon(b);
						      	
						      	// *******************************************************************

						        var x = parseFloat(a) || 0;
						        var y = parseFloat(b) || 0;

						        // *******************************************************************

						        // SUMAR VALORES

						        return x + y;

						        // *******************************************************************

						      }, 0);

						    // *******************************************************************

						    // CARGAR EN EL FOOTER  

						    $( api.columns('.totalColumna').footer() ).html(
					                Common.darFormatoCommon(sum, 2)
					         );  

						    // *******************************************************************

						  });

						  // *******************************************************************


						  // *******************************************************************

						  // CALCULO SUBTOTAL

						  //me.subtotal = Common.restarCommon(me.total, me.iva, me.candec);

						  // *******************************************************************
						}      
                	});
				
			}
		},
		mounted(){
			let me=this;
			$(function(){
		   		$('#sandbox-container .input-daterange').datepicker({
		   			keyboardNavigation: false,
    				forceParse: false
   				});
    			$("#SelectedFecha").datepicker().on(
     				"changeDate", () => {
			     		
			     		me.cabecera.SelectedFecha = $('#SelectedFecha').val();
			     		
			     	}
				);
				

				$('table').dataTable();
			});
		}
	}

</script>