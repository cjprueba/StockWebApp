<template>
	<div>
			<div class="text-left"> 
                <label for="validationTooltip01">Codigo categoria:</label>
            </div>
			<div class="input-group" id="validationTooltip01" >
				<div class="input-group-prepend">
					<button  type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".categoria-modal"><font-awesome-icon icon="search"/></button>
				</div>


                <input ref="codigo" id="codigo_usuario" class="custom-select custom-select-sm shadow-sm" type="text"  :value="nombre" @input="$emit('input', $event.target.value)" v-bind:class="{ 'is-invalid': validarCategoria }" v-on:keyup.prevent.13="enterCategoria($event.target.value)" v-on:blur="enterCategoria($event.target.value)">

			</div>

			<!-- ******************************************************************* -->

	        <!-- MODAL PRODUCTOS -->

	                <div class="modal fade categoria-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	                  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
	                    <div class="modal-content">
	                      <div class="modal-header">
	                        <h5 class="modal-title" id="exampleModalCenterTitle">Categorias: </small></h5>
	                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                          <span aria-hidden="true">&times;</span>
	                        </button>
	                      </div>
	                      <div class="modal-body">
	                            <table id="tablaModalCategoria" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
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
      props: ['nombre','validarCategoria'],
      data(){
        return {
          	productos: [],
            roles:[]
        }
      }, 
       
      methods: {
        recargar(){
          var tableCategoria = $('#tablaModalCategoria').DataTable();
       tableCategoria.ajax.reload( null, false );
        },
       enterCategoria(Codigo){

        // ------------------------------------------------------------------------
           let me =this;
        // LLAMAR FUNCION PARA FILTRAR PRODUCTOS
        
           Common.filtrarCategoriasCommon(Codigo).then(data => {

                        if(data.response===true){

                         me.enviarCodigoPadre(Codigo,
                          data.categoria[0].CODIGO,
                          data.categoria[0].DESCRIPCION,
                          data.marcados,
                          data.MarcaMarcados,
                          data.categoria[0].ATRIBGENERO,
                          data.categoria[0].ATRIBMARCA,
                          data.categoria[0].ATRIBTEMPORADA,
                          data.categoria[0].ATRIBCOLOR,
                          data.categoria[0].ATRIBTELA,
                          data.categoria[0].ATRIBTALLE);
        

                         }else{
                          
                           me.$emit('nombre_categoria',Codigo);
                           me.$emit('existe_categoria',false);
                         }
                       
                    })

        // ------------------------------------------------------------------------

      },
       enviarCodigoPadre(nombre,id,descripcion,marcados,marcaMarcados,Genero,Marca,Temporada,Color,Tela,Talle){
      



				// ------------------------------------------------------------------------
       /* console.log(descripcion);*/
				// ENVIAR CODIGO
		    this.$emit('nombre_categoria',nombre);
        this.$emit('id', id);
        this.$emit('traer_Descripcion', descripcion);
        this.$emit('existe_categoria',true);
        this.$emit('marcados_traer',marcados);
        this.$emit('marcaMarcados_traer',marcaMarcados);
        this.$emit('traer_Genero', Genero);
        this.$emit('traer_Color', Color);
        this.$emit('traer_Marca', Marca);
        this.$emit('traer_Tela', Tela);
        this.$emit('traer_Talle', Talle);
        this.$emit('traer_Temporada', Temporada);        
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
                
                var tableCategoria = $('#tablaModalCategoria').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy":true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/categoriaDatatable",
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


                $('#tablaModalCategoria').on('click', 'tbody tr', function() {

                

                    // *******************************************************************

                    // CARGAR LOS VALORES A LAS VARIABLES DE PRODUCTO


                    me.codigo = tableCategoria.row(this).data().CODIGO;
                    me.description = tableCategoria.row(this).data().DESCRIPCION;  
                    Common.filtrarCategoriasCommon(me.codigo).then(data => {
                        
                        me.enviarCodigoPadre(me.codigo,
                          data.categoria[0].CODIGO,
                          data.categoria[0].DESCRIPCION,
                          data.marcados,
                          data.MarcaMarcados,
                          data.categoria[0].ATRIBGENERO,
                          data.categoria[0].ATRIBMARCA,
                          data.categoria[0].ATRIBTEMPORADA,
                          data.categoria[0].ATRIBCOLOR,
                          data.categoria[0].ATRIBTELA,
                          data.categoria[0].ATRIBTALLE);


                          /* me.enviarCodigoPadre(me.codigo,data.categoria.CODIGO,me.description,data.marcados,data.MarcaMarcados);*/
                           
                    })
                  
                    // *******************************************************************

                    // CERRAR EL MODAL
                     
                     $('.categoria-modal').modal('hide');

                    // *******************************************************************

                });

                //FIN TABLA MODAL PRODUCTOS
                // <<   
                // ------------------------------------------------------------------------
            });    
        }
    }
</script>