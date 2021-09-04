<template>
	<div>

		<!-- ----------------------------------------------- INPUT DE Nro_Piso DEL EMPLEADO ---------------------------------------------- -->
		
		<div class="input-group ">
	
			<div class="input-group-prepend">
				<button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".piso_modal" ><font-awesome-icon icon="search"/></button>
			</div>

			<input ref="Nro_Piso" id="Numero_Piso" placeholder="Cargar nombre del nuevo sector" class="custom-select custom-select-sm" type="number" :value="Nr_Piso" @input="$emit('input', $event.target.value)" v-on:keyup="enterNroPiso($event.target.value)" v-on:blur="enterNroPiso($event.target.value)" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
		
		</div>	
				
		<!-- ---------------------------------------------- DATATABLE DE LA TABLA EMPLEADO --------------------------------------------- -->
		
		<div class="modal fade piso_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
				<div class="modal-content">
					<div class="modal-header">

					   <h5 class="modal-title" id="exampleModalCenterTitle">Pisos </small></h5>
					   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					     	<span aria-hidden="true">&times;</span>
					   </button>
					</div>

					<!-- ---------------------------------- TITULO DE LAS COLUMNAS A MOSTRAR ------------------------------------------- -->
					
					<div class="modal-body">
					    <table id="tablaModalPiso" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
							<thead>
							    <tr>
							    	<th>ID</th>
							        <th>Número de Piso</th>
							        <th>Descripción</th>
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
		props: ['Nr_Piso'],
		
		data(){
			 return {
			}
		},

		methods: {
			enviarNro_PisoPadre(Nro_Piso, descripcion){

				// ENVIAR Nro_Piso
				this.$emit('Nro_Piso', Nro_Piso);
		        this.$emit('descripcion', descripcion);
		        this.$emit('existe_piso',true);


				// ------------------------------------------------------------------------

			},

			enterNroPiso(Codigo){
		      // ------------------------------------------------------------------------
		        let me =this;
		      // LLAMAR FUNCION PARA FILTRAR PRODUCTOS

		      
		        Common.filtrarPisoCommon(Codigo).then(data => {

                    if(data.response===true){
                    	me.enviarNro_PisoPadre(data.piso[0].NRO_PISO,data.piso[0].DESCRIPCION);
                    	
                    }else{
                    	// if(typeof Codigo === "string" || Codigo instanceof String){
                    	// 	Codigo = 0;
                    	// }
	                    
	                    me.$emit('Nro_Piso',Codigo);
	                    me.$emit('descripcion', '');
	                    me.$emit('existe_piso',false);
                    } 
              	})

		      // ------------------------------------------------------------------------
		    },

			recargar(){
		        var table = $('#tablaModalPiso').DataTable();
		      	table.ajax.reload( null, false );
		    },

		},

		mounted(){
			let me = this;

			var table = $('#tablaModalPiso').DataTable({
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
                         "url": "/pisoDatatable",
                         "dataType": "json",
                         "type": "POST"
                       },
                "columns": [
                    { "data": "ID" },
                    { "data": "NRO_PISO" },
                    { "data": "DESCRIPCION" },
                ]      
            });


            $('#tablaModalPiso').on('click', 'tbody tr', function() {

                // CARGAR LOS VALORES A LAS VARIABLES

                me.id = table.row(this).data().NRO_PISO;
                Common.filtrarPisoCommon(me.id).then(data => {
                    me.enviarNro_PisoPadre(
                    	data.piso[0].NRO_PISO,
                    	data.piso[0].DESCRIPCION
                	)
                });

                // CERRAR EL MODAL
                     
                $('.piso_modal').modal('hide');

               // *******************************************************************

            });
		}
	}
</script>