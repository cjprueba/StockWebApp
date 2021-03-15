<template>
	<div>

		<!-- ------------------------------------------------ INPUT DE CODIGO DEL COMBO ---------------------------------------------- -->
		
		<div class="input-group ">
	
			<div class="input-group-prepend">
				<button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".combo-modal" ><font-awesome-icon icon="search"/></button>
			</div>

			<input ref="codigo" :value="codigo" id="codigo_combo" class="custom-select custom-select-sm" type="text" @input="$emit('input', $event.target.value)">
		
		</div>	
				
		<!-- ----------------------------------------------- DATATABLE DE LA TABLA CLIENTE --------------------------------------------- -->
		
		<div class="modal fade combo-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
				<div class="modal-content">
					<div class="modal-header">

					   <h5 class="modal-title" id="exampleModalCenterTitle">Combos: </small></h5>
					   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					     	<span aria-hidden="true">&times;</span>
					   </button>
					</div>

					<!-- ---------------------------------- TITULO DE LAS COLUMNAS A MOSTRAR ------------------------------------------- -->
					
					<div class="modal-body">
					    <table id="tablaModalCombo" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
							<thead>
							    <tr>
							    	<th>ID</th>
							    	<th>Código</th>
							        <th>Descripción</th>
							        <th>Cantidad</th>
							        <th>Precio</th>
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
			
			enviarCodigoPadre(id, codigo, descripcion, cantidad, precio){

				// ENVIAR CODIGO

				this.$emit('id', id);
				this.$emit('codigo', codigo);
		        this.$emit('descripcion', descripcion);
		        this.$emit('cantidad', cantidad);
		        this.$emit('precio', precio);

				// ------------------------------------------------------------------------

			},

			recargar(){
		        var table = $('#tablaModalCombo').DataTable();
		      	table.ajax.reload( null, false );
		    },
		},

		mounted(){

			let me = this;

			var table = $('#tablaModalCombo').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                        		"data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/combo/comboDatatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "ID" },
                            { "data": "CODIGO" },
                            { "data": "DESCRIPCION" },
                            { "data": "CANTIDAD" },
                            { "data": "PRECIO" }
                        ]      
            });


			$('#tablaModalCombo').on('click', 'tbody tr', function() {

                // CARGAR LOS VALORES A LAS VARIABLES

                me.id = table.row(this).data().ID;

                Common.filtrarComboCommon(me.id).then(data => {  

                    me.enviarCodigoPadre(me.id,
                    data.cliente[0].CODIGO,
                    data.cliente[0].DESCRIPCION,
                    data.cliente[0].CANTIDAD,
                    data.cliente[0].PRECIO);
                })

                // CERRAR EL MODAL
                     
                $('.combo-modal').modal('hide');

               // *******************************************************************

            });
		}
	}
</script>