<template>
	<div v-if="$can('inventario.mostrar') && $can('inventario')" class="container-fluid mt-4">

		<!-- ------------------------------------------------------------------------------------- -->

		<!-- TITULO  -->
		
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				 <li class="breadcrumb-item active" aria-current="page">Mostrar Inventario</li>
			</ol>
		</nav>
     <div class="col-md-12">
        <div v-if="procesar" class="d-flex justify-content-center mt-3">
          <strong>Procesando...   </strong>
                  <div class="spinner-grow" role="status" aria-hidden="true"></div>
        </div>
    </div>
    <!-- ------------------------------------------------------------------------------------- -->

    <!-- TABLA CONTEO -->
    
		<table id="tablaConteo" class="table table-striped table-bordered table-sm" style="width:100%">
		    <thead>
		        <tr>
		            <th>ID</th>
		            <th>Observación</th>
                <th>Motivo</th>
		            <th>Sucursal</th>
		            <th>Fecha Creación</th>
		            <th>Fecha Modificación</th>
		            <th>Acción</th>
                <th>Id_Gondola</th>
		        </tr>
		    </thead>	
		 </table>

		 <!-- ------------------------------------------------------------------------------------- -->

    <!-- MODAL IMPRIMIR DIRECCION ORDEN -->

    <div class="modal fade imprimir-inventario-modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title text-primary text-center" >INVENTARIO: {{inventario.ID}}</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      <div class="row">
                          <div class="col-md-12">
                              <label>Tipo de Datos: </label>
                              <select v-model="inventario.tipo" class="form-control form-control-sm">
                                <option value="1">General</option>
                                <option value="2">Coinciden Stock</option>
                                <option value="3">Varian a Stock</option>
                                <option value="4">Mayor a Stock</option>
                                <option value="5">Menor a Stock</option>
                              </select>
                          </div>
                      </div>      
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-primary" v-on:click="reporte()">Imprimir</button>
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                  </div>
              </div>
          </div>
      </div> 

	</div>
  <div v-else>
    <cuatrocientos-cuatro></cuatrocientos-cuatro>
  </div>
</template>

<script>
	export default {
      props: ['moneda'],
      data(){
        return {
          menu : 0,
          Accion_procesar: 0,
          procesar:false,
          datos:[],
          inventario: {
            ID: '',
            tipo: "1"
          }
        }
      }, 
      methods: {
            nuevaCategoria(){
                const params = {
                    description: this.title
                };
                axios.post('/categorias', params).then((response) => {
                    const categoria = response.data;
                     this.$emit('new', categoria);
                });
                
            },editarInventario(ID){

              // ------------------------------------------------------------------------

              // MANDAR ID INVENTARIO

               this.$router.push('/in2/'+ ID + '');

              // ------------------------------------------------------------------------
            }, reporte(){

              // ------------------------------------------------------------------------

              Common.generarRptPdfInventarioCommon(this.inventario.ID, this.inventario.tipo).then( () => {
                me.procesar = false;
              });

              // ------------------------------------------------------------------------

            }
      },
        mounted() {
            
            // ------------------------------------------------------------------------

            // INICIAR VARIABLES

            let me = this;

            // ------------------------------------------------------------------------

            $(document).ready( function () {

        		var table = $('#tablaConteo').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "url": "/inventarioMostrar",
                                 "dataType": "json",
                                 "type": "GET"
                               },
                        "columns": [
                            { "data": "ID" },
                            { "data": "OBSERVACION" },
                            { "data": "MOTIVO" },
                            { "data": "SUCURSAL" },
                            { "data": "FECALTAS" },
                            { "data": "FECMODIF" },
                            { "data": "ACCION" },
                            { "data": "ID_GONDOLA" }
                        ],    
                        "columnDefs": [
                        {
                        "targets": [ 7 ],
                        "visible": false,
                        "searchable": false
                        }
                    ]   
                    });

                  // ------------------------------------------------------------------------

                  // EDITAR TRANSFERENCIA

                    $('#tablaConteo').on('click', 'tbody tr #editarInventario', function() {

                      // *******************************************************************

                      // REDIRIGIR Y ENVIAR CODIGO Inventario
                      
                      var row  = $(this).parents('tr')[0];
                      me.editarInventario(table.row( row ).data().ID);

                      // *******************************************************************

                  });

                  // ------------------------------------------------------------------------

                  // GENERAR REPORTE PDF

                  $('#tablaConteo').on('click', 'tbody tr #imprimirInventario', function() {

                      // *******************************************************************

                      // ENVIAR A COMMON FUNCTION PARA GENERAR REPORTE PDF
                       var row  = $(this).parents('tr')[0];
                      me.inventario.ID = table.row( row ).data().ID;
                      $('.imprimir-inventario-modal').modal('show');

                     
                      
                      

                      // *******************************************************************

                  });
                   $('#tablaConteo').on('click', 'tbody tr #reporte', function() {

                      // *******************************************************************

                      // ENVIAR A COMMON FUNCTION PARA GENERAR REPORTE PDF
                      let me=this;
                       var row  = $(this).parents('tr')[0];
                      
                        me.datos={
                          ID_INV: table.row( row ).data().ID,
                          ID_GONDOLA: table.row( row ).data().ID_GONDOLA
                        };
                      me.procesar = true;
                        axios({
                          url: '/export_inventario_gondola',
                          method: 'GET',
                          data: me.datos,
                          responseType: 'blob', // important
                        }).then((response) => {
                          me.procesar = false;
                           const url = window.URL.createObjectURL(new Blob([response.data]));
                           const link = document.createElement('a');
                           link.href = url;
                           link.setAttribute('download', 'Inventario_Gondola_'+table.row( row ).data().ID+'.xlsx'); //or any other extension
                           document.body.appendChild(link);
                           link.click();

                        });
                      

                      // *******************************************************************

                  });

                  // ------------------------------------------------------------------------

                  // GENERAR REPORTE PDF

                  $('#tablaConteo').on('click', 'tbody tr #procesarInventario', function() {

                      // *******************************************************************

                      // ENVIAR A COMMON FUNCTION PARA GENERAR REPORTE PDF
                      
                      var row  = $(this).parents('tr')[0];
                      me.inventario.ID = table.row( row ).data().ID;

                      Swal.fire({
                          title: '¿ Procesar ?',
                          text: 'Procesar el inventario',
                          type: 'warning',
                          showLoaderOnConfirm: true,
                          showCancelButton: true,
                          confirmButtonColor: 'btn btn-success',
                          cancelButtonColor: '#d33',
                          confirmButtonText: 'Si !',
                          cancelButtonText: 'Cancelar',
                          preConfirm: () => {

                            return Common.procesarInventarioCommon(table.row( row ).data().ID).then(data => {

                                // ------------------------------------------------------------------------

                                // REVISAR SI HAY DATOS 

                                if (!data.response === true) {
                                  throw new Error(data.statusText);
                                } 

                                // ------------------------------------------------------------------------

                                return true;

                                // ------------------------------------------------------------------------

                            }).catch(error => {
                                Swal.showValidationMessage(
                                  `Request failed: ${error}`
                                )
                            });
                          }

                        }).then((result) => {
                          if (result.value) {
                            Swal.fire(
                                      'Procesado !',
                                      'Se ha procesado el inventario correctamente !',
                                      'success'
                            )

                            table.ajax.reload( null, false );

                            // ------------------------------------------------------------------------

                          }
                        })

                      // *******************************************************************

                  });

                  // ------------------------------------------------------------------------

        	});
        }
    }
</script>