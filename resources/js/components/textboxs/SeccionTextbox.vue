<template>
	<div>

		<!-- ----------------------------------------------- INPUT DE CODIGO DEL EMPLEADO ---------------------------------------------- -->
		
		<div class="input-group ">
	
			<div class="input-group-prepend">
				<button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".seccion-modal" ><font-awesome-icon icon="search"/></button>
			</div>

			<input ref="codigo" :value="codigo" id="codigo_seccion" class="custom-select custom-select-sm" type="text" @input="$emit('input', $event.target.value)">
		
		</div>	
				
		<!-- ---------------------------------------------- DATATABLE DE LA TABLA EMPLEADO --------------------------------------------- -->
		
		<div class="modal fade seccion-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
				<div class="modal-content">
					<div class="modal-header">

					   <h5 class="modal-title" id="exampleModalCenterTitle">Secciones </small></h5>
					   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					     	<span aria-hidden="true">&times;</span>
					   </button>
					</div>

					<!-- ---------------------------------- TITULO DE LAS COLUMNAS A MOSTRAR ------------------------------------------- -->
					
					<div class="modal-body">
					    <table id="tablaModalSeccion" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
							<thead>
							    <tr>
							    	<th>ID</th>
							        <th>Código</th>
							        <th>Descripción</th>
							        <th>Descripción Corta</th>
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
			enviarCodigoPadre(id, codigo, descripcion, desc_corta){

				// ENVIAR CODIGO
				
				this.$emit('id', id);
				this.$emit('codigo', codigo);
		        this.$emit('descripcion', descripcion);
		        this.$emit('desc_corta', desc_corta);

				// ------------------------------------------------------------------------

			},

			recargar(){
		        var table = $('#tablaModalSeccion').DataTable();
		      	table.ajax.reload( null, false );
		    },

		},

		mounted(){
			let me = this;

			var table = $('#tablaModalSeccion').DataTable({
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
                         "url": "/seccionDatatable",
                         "dataType": "json",
                         "type": "POST"
                       },
                "columns": [
                    { "data": "ID" },
                    { "data": "CODIGO" },
                    { "data": "DESCRIPCION" },
                    { "data": "DESC_CORTA" },
                ]      
            });


            $('#tablaModalSeccion').on('click', 'tbody tr', function() {

                // CARGAR LOS VALORES A LAS VARIABLES

                me.id = table.row(this).data().ID;

                Common.filtrarSeccionCommon(me.id).then(data => {  

                    me.enviarCodigoPadre(me.id,
                    data.seccion[0].CODIGO,
                    data.seccion[0].DESCRIPCION,
                    data.seccion[0].DESC_CORTA);
                })

                // CERRAR EL MODAL
                     
                $('.seccion-modal').modal('hide');

               // *******************************************************************

            });
		}
	}
</script>
