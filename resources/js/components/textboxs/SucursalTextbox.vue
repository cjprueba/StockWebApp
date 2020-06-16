<template>
	<div>

		<!-- --------------------------------------------------- INPUT CODIGO DE SUCURSAL ---------------------------------------------- -->
		
		<div class="text-left">
			<label for="validationTooltip01">Código</label>
		</div>

		<div class="input-group ">
				
				<div class="input-group-prepend">
					<button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".sucursal-modal" ><font-awesome-icon icon="search"/></button>
				</div>

				<input ref="codigo" :value="codigo" id="codigo_sucursal" class="custom-select custom-select-sm" type="text" @input="$emit('input', $event.target.value)">
			
		</div>
		
		<!-- ---------------------------------------------------- MODAL DE SUCURSAL ---------------------------------------------------- -->
		
		<div class="modal fade sucursal-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
				<div class="modal-content">

					<!-- -------------------------------------------- CABECERA DEL MODAL ---------------------------------------------- -->
		
					<div class="modal-header">

					   <h5 class="modal-title" id="exampleModalCenterTitle">Sucursales: </small></h5>
					   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					     	<span aria-hidden="true">&times;</span>
					   </button>
					</div>

					<!-- -------------------------------------------- TABLA DE SUCURSAL ---------------------------------------------- -->
		
					<div class="modal-body">
					    <table id="tablaModalSucursal" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
							<thead>
							    <tr>
							        <th>Codigo</th>
							        <th>Nombre Sucursal</th>
							        <th>Razon Social</th>
							        <th>Dirección</th>
							        <th>RUC</th>
							    </tr>
							</thead>       
						</table>        
					</div>

					<!-- ------------------------------------------------- BOTON CERRAR  ---------------------------------------------- -->
		
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
		        var tableSucursal = $('#tablaModalSucursal').DataTable();
		      	tableSucursal.ajax.reload( null, false );
		    },
			
			enviarCodigoPadre(codigo, descripcion, razon_social, direccion, ruc, telefono, ciudad, nombre){

				// ENVIAR CODIGO

				this.$emit('codigo', codigo);
		        this.$emit('descripcion', descripcion);
		        this.$emit('ruc', ruc);
		        this.$emit('ciudad', ciudad);
		        this.$emit('direccion', direccion);
		        this.$emit('telefono', telefono);
		        this.$emit('razon_social', razon_social);
		        this.$emit('nombre', nombre);

				// ------------------------------------------------------------------------

			},
		},

		mounted(){

			let me = this;

			var table = $('#tablaModalSucursal').DataTable({
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
                                 "url": "/sucursal/sucursalDatatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "DESCRIPCION" },
                            { "data": "RAZON_SOCIAL" },
                            { "data": "DIRECCION" },
                            { "data": "RUC" }
                        ]      
            });


			$('#tablaModalSucursal').on('click', 'tbody tr', function() {

                // CARGAR LOS VALORES A LAS VARIABLES


                me.codigo = table.row(this).data().CODIGO;

                Common.filtrarSucursalCommon(me.codigo).then(data => {  
                    me.enviarCodigoPadre(me.codigo,
                    data.sucursal[0].DESCRIPCION,
                    data.sucursal[0].RAZON_SOCIAL,
                    data.sucursal[0].DIRECCION,
                    data.sucursal[0].RUC, 
                    data.sucursal[0].TELEFONO, 
                    data.sucursal[0].CIUDAD,
           			data.sucursal[0].CR_DESCRIPCION);
                })

                // CERRAR EL MODAL
                     
                $('.sucursal-modal').modal('hide');

               // *******************************************************************

            });
		}
	}
</script>