<template>
	<div>

		<!-- -------------------------------------------- INPUT DE CODIGO DE LA NOTA DE REMISION --------------------------------------- -->
		
		<div class="input-group">
	
				<div class="input-group-prepend">
					<button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".remision-modal" ><font-awesome-icon icon="search"/></button>
				</div>

				<input ref="codigo" :value="codigo" id="codigo_remision" class="custom-select custom-select-sm" type="text" @input="$emit('input', $event.target.value)" disabled>
		
		</div>	
				
		<!-- ------------------------------------------- DATATABLE DE LA TABL LA NOTA DE REMISION -------------------------------------- -->
		
		<div class="modal fade remision-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
				<div class="modal-content">
					<div class="modal-header">

					   <h5 class="modal-title" id="exampleModalCenterTitle">Notas de Remisión: </small></h5>
					   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					     	<span aria-hidden="true">&times;</span>
					   </button>
					</div>

					<!-- ---------------------------------- TITULO DE LAS COLUMNAS A MOSTRAR ------------------------------------------- -->
					
					<div class="modal-body">
					    <table id="tablaModalRemision" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
							<thead>
							    <tr>
							    	<th></th>
							        <th>Codigo</th>
							        <th>Cliente</th>
							        <th>IVA</th>
							        <th>SubTotal</th>
							        <th>Total</th>
							        <th>Moneda</th>
							    </tr>
							</thead>       
						</table>        
					</div>

					<!-- ----------------------------------------- BOTON CERRAR DATATABLE ---------------------------------------------- -->
					
					<div class="modal-footer">

					  	<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

					</div>
				</div>
			</div>	
		</div>
	</div>
</template>

<script>
	
	export default{
		
		props: ['codigo'],
		
		data(){
			 return {
			}
		},

		methods: {

			recargar(){
		        var tableRemision = $('#tablaModalRemision').DataTable();
		      	tableRemision.ajax.reload( null, false );
		    },
			
			enviarCodigoPadre(codigo, fecha, hora, cliente, direccion, telefono, moneda, total, iva, subtotal, name){

				// ENVIAR CODIGO

				this.$emit('codigo', codigo);
		        this.$emit('cliente', cliente);
		        this.$emit('iva', iva);
		        this.$emit('subtotal', subtotal);
		        this.$emit('total', total);
		        this.$emit('moneda', moneda);
		        this.$emit('hora', hora);
		        this.$emit('fecha', fecha);
		        this.$emit('telefono', telefono);
		        this.$emit('direccion', direccion);
		        this.$emit('usuario', name);

				// ------------------------------------------------------------------------

			},
		},

		mounted(){

			let me = this;

			var table = $('#tablaModalRemision').DataTable({
                        "processing": true,
                        "serverSide": true,
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
				            { extend: 'print', text: '<i class="fa fa-print"></i>', titleAttr: 'Imprimir', className: 'btn btn-secondary' }
				        ],
                        "ajax":{
                        		"data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/remision/NotaDeRemisionDatatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "ID" },
                            { "data": "CODIGO" },
                            { "data": "CLIENTE" },
                            { "data": "IVA" },
                            { "data": "SUB_TOTAL" },
                            { "data": "TOTAL" },
                            { "data": "MONEDA" }
                        ]      
            });


			$('#tablaModalRemision').on('click', 'tbody tr', function() {

                // CARGAR LOS VALORES A LAS VARIABLES

                me.codigo = table.row(this).data().CODIGO;

                Common.filtrarRemisionCommon(me.codigo).then(data => {  
                    
                    me.enviarCodigoPadre(me.codigo,
	               	data.remision[0].FECHA,
	               	data.remision[0].HORA,
	               	data.remision[0].CLIENTE,
	               	data.remision[0].DIRECCION,
	               	data.remision[0].TELEFONO,
	       			data.remision[0].MONEDA,
	               	data.remision[0].TOTAL,
                    data.remision[0].IVA,
	         		data.remision[0].SUB_TOTAL,
	         		data.usuario[0].name);
                })

                // CERRAR EL MODAL
                     
                $('.remision-modal').modal('hide');

               // *******************************************************************

            });
		}
	}
</script>