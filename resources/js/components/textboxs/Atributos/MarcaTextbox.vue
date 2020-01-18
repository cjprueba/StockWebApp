<template>
	<div>
			<div class="text-left"> 
                <label for="validationTooltip01">Codigo Marca:</label>
            </div>
			<div class="input-group" id="validationTooltip01" >
				<div class="input-group-prepend">
					<button  type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".marca-modal"><font-awesome-icon icon="search"/></button>
				</div>


                <input ref="codigo" id="codigo_usuario" class="custom-select custom-select-sm shadow-sm" type="text"  :value="nombre" @input="$emit('input', $event.target.value)" v-bind:class="{ 'is-invalid': validarMarca }" v-on:keyup.prevent.13="enterMarca($event.target.value)" v-on:blur="enterMarca($event.target.value)">

			</div>

			<!-- ******************************************************************* -->

	        <!-- MODAL PRODUCTOS -->

	                <div class="modal fade marca-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	                  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
	                    <div class="modal-content">
	                      <div class="modal-header">
	                        <h5 class="modal-title" id="exampleModalCenterTitle">Marcas: </small></h5>
	                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                          <span aria-hidden="true">&times;</span>
	                        </button>
	                      </div>
	                      <div class="modal-body">
	                            <table id="tablaModalMarca" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
	                                <thead>
	                                    <tr>
	                                        <th>Codigo</th>
	                                        <th>Descripcion</th>
	                                    </tr>
	                                </thead>
	                                <tbody>
	                                    <td></td>
	                                </tbody>
	                            </table>        
	                      </div>
	                      <div class="modal-footer">
	                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
	                      </div>
	                    </div>
	                  </div>
	                </div>  

	        <!-- ******************************************************************* -->
	</div>	
</template>
<script>
	export default {
      props: ['nombre','validarMarca'],
      data(){
        return {
          	productos: [],
            roles:[]
        }
      }, 
       
      methods: {
        recargar(){
          alert("entre");
          var tableMarcas = $('#tablaModalMarca').DataTable();
       tableMarcas.ajax.reload( null, false );
        },
       enterMarca(Codigo){

        // ------------------------------------------------------------------------
           let me =this;
        // LLAMAR FUNCION PARA FILTRAR PRODUCTOS
        
           Common.filtrarMarcasCommon(Codigo).then(data => {

                        if(data.response===true){
                           if(data.existe_descuento===true){
                          me.enviarCodigoPadre(Codigo,data.marcas[0].CODIGO,data.marcas[0].DESCRIPCION,data.descuento[0].DESCUENTO,data.descuento[0].FECHAINI,data.descuento[0].FECHAFIN);
                           }else{
                           me.enviarCodigoPadre(Codigo,data.marcas[0].CODIGO,data.marcas[0].DESCRIPCION,"","","");
                           }

                         }else{
                          
                           me.$emit('nombre_marca',Codigo);
                           me.$emit('existe_marca',false);
                         }
                       
                    })

        // ------------------------------------------------------------------------

      },
       enviarCodigoPadre(nombre,id,descripcion,descuento,fechaini,fechafin){

				// ------------------------------------------------------------------------

				// ENVIAR CODIGO
				this.$emit('nombre_marca',nombre);
        this.$emit('id', id);
        this.$emit('descripcion', descripcion);
        this.$emit('existe_marca',true);
        this.$emit('descuento', descuento);
        this.$emit('fechaini', fechaini);
        this.$emit('fechafin', fechafin);
				// ------------------------------------------------------------------------

			}, vaciarDevolver(){

				// ------------------------------------------------------------------------

				// VACIAR Y DEVOLVER FOCUS AL TEXTBOX

	        	$("#codigo_Producto").focus();
	
				// ------------------------------------------------------------------------

			}
      },
        mounted() {
        	
        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES 

        	let me = this;

        	// ------------------------------------------------------------------------
        	
        	$(document).ready( function () {

        	 	// ------------------------------------------------------------------------
                // >>
                // INICIAR EL DATATABLE PRODUCTOS MODAL
                // ------------------------------------------------------------------------
                
                var tableMarcas = $('#tablaModalMarca').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy":true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/marcasDatatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "DESCRIPCION" }
                        ]      
                    });
                

                // ------------------------------------------------------------------------
                
                // SELECCIONAR UNA FILA - SE PUEDE BORRAR - SIN USO


                $('#tablaModalMarca').on('click', 'tbody tr', function() {

                

                    // *******************************************************************

                    // CARGAR LOS VALORES A LAS VARIABLES DE PRODUCTO


                    me.codigo = tableMarcas.row(this).data().CODIGO;
                    me.description = tableMarcas.row(this).data().DESCRIPCION;  
                    Common.filtrarMarcasCommon(me.codigo).then(data => {
                        
                      
                        if(data.existe_descuento===true){
                        me.enviarCodigoPadre(me.codigo,data.marcas.CODIGO,me.description,data.descuento[0].DESCUENTO,data.descuento[0].FECHAINI,data.descuento[0].FECHAFIN);
                           }else{
                           me.enviarCodigoPadre(me.codigo,data.marcas.CODIGO,me.description,"","","");
                           }
                    })
                  
                    // *******************************************************************

                    // CERRAR EL MODAL
                     
                     $('.marca-modal').modal('hide');

                    // *******************************************************************

                });

                //FIN TABLA MODAL PRODUCTOS
                // <<   
                // ------------------------------------------------------------------------
            });    
        }
    }
</script>