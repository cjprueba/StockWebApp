<template>
	<div>

		<!-- ------------------------------------------------ INPUT DE CODIGO DEL CLIENTE ---------------------------------------------- -->
		
		<div class="input-group ">
	
				<div class="input-group-prepend">
					<button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".cliente-modal" ><font-awesome-icon icon="search"/></button>
				</div>

				<input ref="codigo" :value="codigo" id="codigo_cliente" class="custom-select custom-select-sm" type="text" @input="$emit('input', $event.target.value)">
		
		</div>	
				
		<!-- ----------------------------------------------- DATATABLE DE LA TABLA CLIENTE --------------------------------------------- -->
		
		<div class="modal fade cliente-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
				<div class="modal-content">
					<div class="modal-header">

					   <h5 class="modal-title" id="exampleModalCenterTitle">Clientes: </small></h5>
					   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					     	<span aria-hidden="true">&times;</span>
					   </button>
					</div>

					<!-- ---------------------------------- TITULO DE LAS COLUMNAS A MOSTRAR ------------------------------------------- -->
					
					<div class="modal-body">
					    <table id="tablaModalCliente" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
							<thead>
							    <tr>
							    	<th>ID</th>
							        <th>Nombre</th>
							        <th>Código</th>
							        <th>C.I.</th>
							        <th>R.U.C.</th>
							        <th>Telefono</th>
							        <th>Razón Social</th>
							        <th>Dirección</th>
							        <th>Ciudad</th>
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
			
			enviarCodigoPadre(id, codigo, cedula, nombre, ruc, direccion, ciudad, nacimiento, telefono, celular, email, tipo, limite, empresaID, diaLimite, empresa, creditoDisponible, razonSocial, retentor, porc_retencion){

				// ENVIAR CODIGO

				this.$emit('id', id);
				this.$emit('codigo', codigo);
		        this.$emit('cedula', cedula);
		        this.$emit('nombre', nombre);
		        this.$emit('ruc', ruc);
		        this.$emit('direccion', direccion);
		        this.$emit('ciudad', ciudad);
		        this.$emit('nacimiento', nacimiento);
		        this.$emit('telefono', telefono);
		        this.$emit('celular', celular);
		        this.$emit('email', email);
		        this.$emit('tipo', tipo);
		        this.$emit('limite', limite);
		        this.$emit('empresaID', empresaID);
		        this.$emit('diaLimite', diaLimite);
		        this.$emit('empresa', empresa);
		        this.$emit('creditoDisponible', creditoDisponible);
		        this.$emit('razonSocial', razonSocial);
		        this.$emit('retentor', retentor);
		        this.$emit('porc_retencion', porc_retencion);

				// ------------------------------------------------------------------------

			},

			recargar(){
		        var table = $('#tablaModalCliente').DataTable();
		      	table.ajax.reload( null, false );
		    },
		},

		mounted(){

			let me = this;

			var table = $('#tablaModalCliente').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                        		"data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/cliente/clienteDatatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "ID" },
                            { "data": "NOMBRE" },
                            { "data": "CODIGO" },
                            { "data": "CI" },
                            { "data": "RUC" },
                            { "data": "TELEFONO" },
                            { "data": "RAZON_SOCIAL" },
                            { "data": "DIRECCION" },
                            { "data": "CIUDAD" }
                        ]      
            });


			$('#tablaModalCliente').on('click', 'tbody tr', function() {

                // CARGAR LOS VALORES A LAS VARIABLES

                me.id = table.row(this).data().ID;

                Common.filtrarClienteCommon(me.id).then(data => {  

                    me.enviarCodigoPadre(me.id,
                    data.cliente[0].CODIGO,
                    data.cliente[0].CI,
                    data.cliente[0].NOMBRE,
                    data.cliente[0].RUC,
                    data.cliente[0].DIRECCION,
                    data.cliente[0].CIUDAD, 
                    data.cliente[0].FEC_NAC, 
                    data.cliente[0].TELEFONO,
           			data.cliente[0].CELULAR,
           			data.cliente[0].EMAIL,
           			data.cliente[0].TIPO,
           			data.cliente[0].LIMITE_CREDITO,
           			data.cliente[0].FK_EMPRESA,
           			data.cliente[0].LIMITEDIA,
           			data.cliente[0].EMPRESA,
           			data.cliente[0].CREDITO_DISPONIBLE,
           			data.cliente[0].RAZON_SOCIAL,
           			data.cliente[0].RETENTOR,
           			data.cliente[0].PORC_RETENCION);
                })

                // CERRAR EL MODAL
                     
                $('.cliente-modal').modal('hide');

               // *******************************************************************

            });
		}
	}
</script>