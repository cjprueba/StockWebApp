<template>
	<div>
			<div class="text-left"> 
                <label for="validationTooltip01">Codigo SubCategoria:</label>
            </div>
			<div class="input-group" id="validationTooltip01" >
				<div class="input-group-prepend">
					<button  type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".subcategoria-modal"><font-awesome-icon icon="search"/></button>
				</div>


                <input ref="codigo" id="codigo_usuario" class="custom-select custom-select-sm shadow-sm" type="text"  :value="nombre" @input="$emit('input', $event.target.value)" v-bind:class="{ 'is-invalid': validarSubCategoria }" v-on:keyup.prevent.13="enterSubCategoria($event.target.value)" v-on:blur="enterSubCategoria($event.target.value)">

			</div>

			<!-- ******************************************************************* -->

	        <!-- MODAL PRODUCTOS -->

	                <div class="modal fade subcategoria-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	                  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
	                    <div class="modal-content">
	                      <div class="modal-header">
	                        <h5 class="modal-title" id="exampleModalCenterTitle">SubCategorias: </small></h5>
	                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                          <span aria-hidden="true">&times;</span>
	                        </button>
	                      </div>
	                      <div class="modal-body">
	                            <table id="tablaModalSubCategoria" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
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
      props: ['nombre','validarSubCategoria'],
      data(){
        return {
          	productos: [],
            roles:[]
        }
      }, 
       
      methods: {
        recargar(){
          var tableSubCategoria = $('#tablaModalSubCategoria').DataTable();
       tableSubCategoria.ajax.reload( null, false );
        },
       enterSubCategoria(Codigo){

        // ------------------------------------------------------------------------
           let me =this;
        // LLAMAR FUNCION PARA FILTRAR PRODUCTOS
        
           Common.filtrarSubCategoriasCommon(Codigo).then(data => {

                        if(data.response===true){

                         me.enviarCodigoPadre(Codigo,data.subCategoria[0].CODIGO,data.subCategoria[0].DESCRIPCION,data.marcados);
        

                         }else{
                          
                           me.$emit('nombre_Subcategoria',Codigo);
                           me.$emit('existe_Subcategoria',false);
                         }
                       
                    })

        // ------------------------------------------------------------------------

      },
       enviarCodigoPadre(nombre,id,descripcion,marcados){

				// ------------------------------------------------------------------------

				// ENVIAR CODIGO
		  this.$emit('nombre_Subcategoria',nombre);
          this.$emit('id', id);
          this.$emit('descripcion', descripcion);
          this.$emit('existe_Subcategoria',true);
          this.$emit('marcados_traer',marcados);
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
                
                var tableSubCategoria = $('#tablaModalSubCategoria').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy":true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/subCategoriasDatatable",
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


                $('#tablaModalSubCategoria').on('click', 'tbody tr', function() {

                

                    // *******************************************************************

                    // CARGAR LOS VALORES A LAS VARIABLES DE PRODUCTO


                    me.codigo = tableSubCategoria.row(this).data().CODIGO;
                    me.description = tableSubCategoria.row(this).data().DESCRIPCION;  
                    Common.filtrarSubCategoriasCommon(me.codigo).then(data => {
                        
                      


                           me.enviarCodigoPadre(me.codigo,data.subCategoria.CODIGO,me.description,data.marcados);
                           
                    })
                  
                    // *******************************************************************

                    // CERRAR EL MODAL
                     
                     $('.subcategoria-modal').modal('hide');

                    // *******************************************************************

                });

                //FIN TABLA MODAL PRODUCTOS
                // <<   
                // ------------------------------------------------------------------------
            });    
        }
    }
</script>