<template>
	<div>
		<table id="tablaVentas" class="table table-sm mb-3" style="width:100%">
			<thead>
				<tr>
					<th>#</th>
					<th>ID</th>
					<th>Cliente</th>
					<th>Cantidad</th>
					<th>Precio</th>
					<th>Total</th>
					<th>Fecha</th>
					<th>Hora</th>
				</tr>
			</thead>
		</table>
	</div>
</template>
<script>
	export default{
	  props: ["codigo"],
		data(){
			return {
			}
		},

		methods: {
		},
		mounted(){

			let me = this;

			var table = $('#tablaVentas').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy":true,
                        "bAutoWidth": true,
                        "select": true,
                		"searching": false,
                        "ajax":{
                        		"data": {
	                 				codigo: me.codigo,
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/detalleProductoVentasDatatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "ITEM" },
                            { "data": "ID" },
                            { "data": "CLIENTE" },
                            { "data": "CANTIDAD" },
                            { "data": "PRECIO" },
                            { "data": "TOTAL" },
                            { "data": "FECHA" },
                            { "data": "HORA" }
                        ]      
            });
		}
	}
</script>