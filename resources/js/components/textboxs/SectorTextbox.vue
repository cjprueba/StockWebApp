<template>
	<div>

		<!-- ----------------------------------------------- INPUT DE Sector DEL EMPLEADO ---------------------------------------------- -->
		
		<div class="input-group ">
	
			<div class="input-group-prepend">
				<button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".sector_modal" ><font-awesome-icon icon="search"/></button>
			</div>

			<input ref="Sector" id="Desc_Sector" onKeyUp="document.getElementById(this.id).value=document.getElementById(this.id).value.toUpperCase()" class="custom-select custom-select-sm" placeholder="Introducir nombre del nuevo sector" type="text" :value="Sect" @input="$emit('input', $event.target.value)" v-on:keyup="enterSector($event.target.value)" v-on:blur="enterSector($event.target.value)" >
		
		</div>	
				
		<!-- ---------------------------------------------- DATATABLE DE LA TABLA EMPLEADO --------------------------------------------- -->
		
		<div class="modal fade sector_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
				<div class="modal-content">
					<div class="modal-header">

					   <h5 class="modal-title" id="exampleModalCenterTitle">Sectores</small></h5>
					   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					     	<span aria-hidden="true">&times;</span>
					   </button>
					</div>

					<!-- ---------------------------------- TITULO DE LAS COLUMNAS A MOSTRAR ------------------------------------------- -->
					
					<div class="modal-body">
					    <table id="tablaModalSector" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
							<thead>
							    <tr>
							    	<th>ID</th>
							        <th>Sector</th>
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
		props: ['Sect'],
		
		data(){
			 return {
			}
		},

		methods: {
			enviarSectorPadre(Sector){

				// ENVIAR Sector
				this.$emit('Sector', Sector);
		        this.$emit('existe_sector',true);


				// ------------------------------------------------------------------------

			},

			enterSector(Codigo){
		      // ------------------------------------------------------------------------
		        let me =this;
		      // LLAMAR FUNCION PARA FILTRAR PRODUCTOS

		      
		        Common.filtrarSectorCommon(Codigo).then(data => {

                    if(data.response===true){
                    	me.enviarSectorPadre(data.sector[0].DESCRIPCION);
                    	
                    }else{
	                    me.$emit('Sector',Codigo);
	                    me.$emit('existe_sector',false);
                    } 
              	})

		      // ------------------------------------------------------------------------
		    },

			recargar(){
		        var table = $('#tablaModalSector').DataTable();
		      	table.ajax.reload( null, false );
		    },

		},

		mounted(){
			let me = this;

			var table = $('#tablaModalSector').DataTable({
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
                         "url": "/sectorDatatable",
                         "dataType": "json",
                         "type": "POST"
                       },
                "columns": [
                    { "data": "ID" },
                    { "data": "DESCRIPCION" },
                ]      
            });


            $('#tablaModalSector').on('click', 'tbody tr', function() {

                // CARGAR LOS VALORES A LAS VARIABLES

                me.id = table.row(this).data().ID;

                Common.filtrarSectorCommon(me.id).then(data => {  
                	 if(data.response===true){
                    	me.enviarSectorPadre(data.sector[0].DESCRIPCION);
                    	
                    }else{
	                    
	                    me.$emit('Sector',"NUEVO");
	                    me.$emit('existe_sector',false);
                    } 
                	
                 });

                // CERRAR EL MODAL
                     
                $('.sector_modal').modal('hide');

               // *******************************************************************

            });
		}
	}
</script>