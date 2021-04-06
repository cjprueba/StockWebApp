<template>
	<div class="container">
		<div class="row mt-4">
			<!-- TITULO  -->
			
			<div class="col-md-6">
				<div class="section-title">
                    <h4>Listado de Notas de Crédito</h4>
                </div>
            </div>

            <!-- ------------------------------------ TABLA DE DATOS ------------------------------------ -->

			<div class="col-md-12">
				<table id="tablaNotaMostrar" class="table table-striped table-hover table-bordered table-sm mb-3" style="width:100%">
		            <thead>
		                <tr>
		                	<th>ID</th>
		                	<th>Venta_ID</th>
		                    <th>Cliente</th>
		                    <th>RUC</th>
		                    <th>Sub_Total</th>
		                    <th>IVA</th>
		                    <th>Total</th>
		                    <th>Fecha</th>
		                    <th>Tipo</th>
		                    <th>Caja</th>
		                    <th>Acción</th>
		                </tr>
		            </thead>
		            <tbody>
		                <td></td>
		            </tbody>
		        </table> 
			</div>

		</div>
	</div>
</template>

<script>
	export default{
		data(){
			return {

			}
		},
		methods: {

		},
		mounted(){

			let me = this;

            $(document).ready( function () {

            		// ------------------------------------------------------------------------

            		// PREPARAR DATATABLE 

	 				var tableNotaMostrar = $('#tablaNotaMostrar').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "url": "/nota/credito/mostrar",
                                 "dataType": "json",
                                 "type": "GET",
                                 "contentType": "application/json; charset=utf-8"
                               },
                        "columns": [
                            { "data": "ID" },
                            { "data": "ID_VENTA"},
                            { "data": "CLIENTE" },
                            { "data": "RUC" },
                            { "data": "SUB_TOTAL" },
                            { "data": "IVA" },
                            { "data": "TOTAL" },
                            { "data": "FECHA" },
                            { "data": "TIPO" },
                            { "data": "CAJA" },
                            { "data": "ACCION" }
                        ], 
		                "createdRow": function( row, data, dataIndex){
		                    $(row).addClass(data['ESTATUS']);
		                },      

                    });

	 				// ------------------------------------------------------------------------

	 				// AJUSTAR COLUMNAS DE ACUERDO AL DATO QUE CONTIENEN

            		tableNotaMostrar.columns.adjust().draw();

            		// ------------------------------------------------------------------------

            });
                    
		}
	}
</script>

<style>
	.section-title h4 {
	    font-weight: 700;
	    color: #002A3A;
	    font-size: 30px;
	    margin: 0 0 15px;
	    border-left: 5px solid #0A1FA3;
	    padding-left: 15px;
	    padding-bottom: 10px;
	}
</style>