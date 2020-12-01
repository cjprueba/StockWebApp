<template>
	<div>

		<!-- ---------------------------------------------- TEXTBOX REGISTRO DE MAQUINAS ----------------------------------- -->

		<div class="input-group ">
			<div class="input-group-prepend">
				<button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".maquinas-modal" ><font-awesome-icon icon="search"/></button>
			</div>

			<input ref="id" :value="id" id="id_maquina" class="custom-select custom-select-sm" type="text" @input="$emit('input', $event.target.value)">
		</div>	
				
		<!-- ----------------------------------------------- DATATABLE DE LA TABLA CLIENTE --------------------------------------------- -->
		
		<div class="modal fade maquinas-modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
				<div class="modal-content">
					<div class="modal-header">

					   <h5 class="modal-title" id="exampleModalCenterTitle">Maquinas Registradas: </small></h5>
					   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					     	<span aria-hidden="true">&times;</span>
					   </button>
					</div>

					<!-- ---------------------------------- TITULO DE LAS COLUMNAS A MOSTRAR ------------------------------------------- -->
					
					<div class="modal-body">
					    <table id="tablaModalMaquina" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
							<thead>
							    <tr>
							    	<th>ID</th>
							        <th>Sucursal</th>
							        <th>Sector</th>
							        <th>Característica</th>
							        <th>Usuario</th>
							        <th>IP</th>
							        <th>Nombre</th>
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
		
		props: ['id'],
		
		data(){
			 return {
			}
		},

		methods: {
			
			enviarCodigoPadre(id, sucursal, sector, caract, usuario, ip, nombre){

				// ENVIAR CODIGO

				this.$emit('id', id);
				this.$emit('sucursal', sucursal);
		        this.$emit('sector', sector);
		        this.$emit('caracteristica', caract);
		        this.$emit('usuario', usuario);
		        this.$emit('ip', ip);
		        this.$emit('nombre', nombre);

				// ------------------------------------------------------------------------

			},

			recargar(){
		        var table = $('#tablaModalMaquina').DataTable();
		      	table.ajax.reload( null, false );
		    },
		},

		mounted(){

			let me = this;

			var table = $('#tablaModalMaquina').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                        		"data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/maquinaDatatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "ID" },
                            { "data": "SUCURSAL" },
                            { "data": "SECTOR" },
                            { "data": "CARACTERISTICA" },
                            { "data": "USUARIO" },
                            { "data": "IP" },
                            { "data": "NOMBRE" }
                        ]      
            });


			$('#tablaModalMaquina').on('click', 'tbody tr', function() {

                // CARGAR LOS VALORES A LAS VARIABLES

                me.id = table.row(this).data().ID;

                Common.filtrarMaquinasRegistradasCommon(me.id).then(data => {  

                    me.enviarCodigoPadre(me.id,
                    data.registro[0].SUCURSAL,
                    data.registro[0].SECTOR,
                    data.registro[0].CARACTERISTICA,
                    data.registro[0].USUARIO,
                    data.registro[0].IP,
                    data.registro[0].NOMBRE);
                })

                // CERRAR EL MODAL
                     
                $('.maquinas-modal').modal('hide');

               // *******************************************************************

            });
		}
	}
</script>