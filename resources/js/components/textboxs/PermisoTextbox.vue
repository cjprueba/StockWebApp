<template>
	<div>
			<div class="text-left"> 
                <label for="validationTooltip01">Nombre Del permiso</label>
            </div>
			<div class="input-group" id="validationTooltip01" >
				<div class="input-group-prepend">
					<button  type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".permiso-modal"><font-awesome-icon icon="search"/></button>
				</div>


                <input ref="codigo" id="codigo_usuario" class="custom-select custom-select-sm shadow-sm" type="text"  :value="nombre" @input="$emit('input', $event.target.value)" v-bind:class="{ 'is-invalid': validarPermiso }" v-on:keyup.prevent.13="enterPermisos($event.target.value)" v-on:blur="enterPermisos($event.target.value)">

			</div>

			<!-- ******************************************************************* -->

	        <!-- MODAL PRODUCTOS -->

	                <div class="modal fade permiso-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	                  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
	                    <div class="modal-content">
	                      <div class="modal-header">
	                        <h5 class="modal-title" id="exampleModalCenterTitle">Permisos: </small></h5>
	                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                          <span aria-hidden="true">&times;</span>
	                        </button>
	                      </div>
	                      <div class="modal-body">
	                            <table id="tablaModalPermisos" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
	                                <thead>
	                                    <tr>
	                                        <th>Codigo</th>
	                                        <th>Nombre</th>
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
      props: ['nombre','validarPermiso'],
      data(){
        return {
          	productos: [],
            roles:[]
        }
      }, 
      methods: {
       enterPermisos(name){

        // ------------------------------------------------------------------------
           let me =this;
        // LLAMAR FUNCION PARA FILTRAR PRODUCTOS
        
           Common.filtrarPermisosCommon(name).then(data => {
                        if(data.response===true){
                           me.enviarCodigoPadre(name,data.permisos.id,data.permisos.description);
                         }else{
                          
                           me.$emit('nombre_permiso',name);
                           me.$emit('existe_permiso',false);
                         }
                       
                    })

        // ------------------------------------------------------------------------

      },
       enviarCodigoPadre(nombre,id,descripcion){

				// ------------------------------------------------------------------------

				// ENVIAR CODIGO
				this.$emit('nombre_permiso',nombre);
                 this.$emit('id', id);
                 this.$emit('descripcion', descripcion);
          this.$emit('existe_permiso',true);
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
                
                var tableRoles = $('#tablaModalPermisos').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy":true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/permisosDatatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "id" },
                            { "data": "name" },
                            { "data": "description" }
                        ]      
                    });
                

                // ------------------------------------------------------------------------
                
                // SELECCIONAR UNA FILA - SE PUEDE BORRAR - SIN USO


                $('#tablaModalPermisos').on('click', 'tbody tr', function() {

                

                    // *******************************************************************

                    // CARGAR LOS VALORES A LAS VARIABLES DE PRODUCTO


                    me.id = tableRoles.row(this).data().id;
                    me.description = tableRoles.row(this).data().description;  
                    me.name = tableRoles.row(this).data().name;
                    Common.filtrarPermisosCommon(me.name).then(data => {
                        
                       me.enviarCodigoPadre(me.name,data.permisos.id,data.permisos.description);
                    })
                  
                    // *******************************************************************

                    // CERRAR EL MODAL
                     
                     $('.permiso-modal').modal('hide');

                    // *******************************************************************

                });

                //FIN TABLA MODAL PRODUCTOS
                // <<   
                // ------------------------------------------------------------------------
            });    
        }
    }
</script>