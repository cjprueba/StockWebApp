<template>
	<div>
			<div class="text-left"> 
                <label for="validationTooltip01">Usuario</label>
            </div>
			<div class="input-group" id="validationTooltip01" >
				<div class="input-group-prepend">
					<button  type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".usuario-modal"><font-awesome-icon icon="search"/></button>
				</div>


                <input ref="codigo" id="codigo_usuario" :disabled="true" class="custom-select custom-select-sm shadow-sm" type="text"  :value="nombre" @input="$emit('input', $event.target.value)" v-bind:class="{ 'is-invalid': validar_usuario }" v-on:keyup="filtrarUsuarios($event.target.value)" v-on:keyup.prevent.13="enviarCodigoPadre($event.target.value)" v-on:blur="enviarCodigoPadre($event.target.value)">

			</div>

			<!-- ******************************************************************* -->

	        <!-- MODAL PRODUCTOS -->

	                <div class="modal fade usuario-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	                  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
	                    <div class="modal-content">
	                      <div class="modal-header">
	                        <h5 class="modal-title" id="exampleModalCenterTitle">Usuarios: </small></h5>
	                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                          <span aria-hidden="true">&times;</span>
	                        </button>
	                      </div>
	                      <div class="modal-body">
	                            <table id="tablaModalUsuarios" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
	                                <thead>
	                                    <tr>
	                                        <th>Codigo</th>
	                                        <th>Nombre</th>
	                                        <th>Email</th>
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
      props: ['nombre', 'monedaCodigo', 'candec', 'tab_unica', 'checked_codigo_real', 'validar_usuario'],
      data(){
        return {
          
          	productos: [],
            roles:[]
        }
      }, 
      methods: {
            filtrarUsuarios(codigo){

				// ------------------------------------------------------------------------

				// LLAMAR FUNCION PARA FILTRAR PRODUCTOS

				Common.filtrarUsuariosCommon(codigo).then(data => {
				  this.productos = data
				});

				// ------------------------------------------------------------------------

			}, enviarCodigoPadre(nombre,id,roles,permisos){

				// ------------------------------------------------------------------------

				// ENVIAR CODIGO
                  
				this.$emit('nombre_usuario',nombre);
                 this.$emit('id', id);
                 this.$emit('roles', roles);
                 this.$emit('permisos', permisos);
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
                
                var tableUsuarios = $('#tablaModalUsuarios').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy":true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/usuariosDatatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "id" },
                            { "data": "name" },
                            { "data": "email" }
                        ]      
                    });
                

                // ------------------------------------------------------------------------
                
                // SELECCIONAR UNA FILA - SE PUEDE BORRAR - SIN USO


                $('#tablaModalUsuarios').on('click', 'tbody tr', function() {

                

                    // *******************************************************************

                    // CARGAR LOS VALORES A LAS VARIABLES DE PRODUCTO

                    me.id = tableUsuarios.row(this).data().id;
                    me.email = tableUsuarios.row(this).data().email;  
                    me.name = tableUsuarios.row(this).data().name;
                    Common.traerRolUsuarioCommon(me.id).then(data => {
                        
                        me.enviarCodigoPadre(me.name,me.id,data.roles,data.permisos);
                    })

                  
                    // *******************************************************************

                    // CERRAR EL MODAL
                     
                     $('.usuario-modal').modal('hide');

                    // *******************************************************************

                });

                //FIN TABLA MODAL PRODUCTOS
                // <<   
                // ------------------------------------------------------------------------
            });    
        }
    }
</script>