<template>
	<div>
			<div class="text-left"> 
                <label for="validationTooltip01">Código</label>
            </div>
			<div class="input-group" id="validationTooltip01" >
				<div class="input-group-prepend">
					<button :disabled="checked_codigo_real" type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".producto-modal"><font-awesome-icon icon="search"/></button>
				</div>

				<datalist id="productos">
					<option v-for="producto in productos" :value="producto.CODIGO"></option>
				</datalist>

				<!-- <input ref="codigo" id="codigo_Producto" class="custom-select custom-select-sm" type="text" list="productos" v-model="value" v-bind:class="{ 'is-invalid': validarCodigoProducto }" v-on:keyup="filtrarProductos()" v-on:keyup.prevent.13="enviarCodigoPadre()" v-on:blur="enviarCodigoPadre()" > -->

                <input :tabindex="tabIndexPadre" :disabled="checked_codigo_real" ref="codigo" id="codigo_Producto" class="custom-select custom-select-sm shadow-sm mousetrap" type="text" list="productos" :value="value" @input="$emit('input', $event.target.value)" v-bind:class="{ 'is-invalid': validar_codigo_producto }" v-on:keyup="filtrarProductos($event.target.value)" v-on:keyup.prevent.13="enviarCodigoPadre($event.target.value)" v-on:blur="enviarCodigoPadre($event.target.value)">

			</div>

			<!-- ******************************************************************* -->

	        <!-- MODAL PRODUCTOS -->

	                <div class="modal fade producto-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	                  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
	                    <div class="modal-content">
	                      <div class="modal-header">
	                        <h5 class="modal-title" id="exampleModalCenterTitle">Productos: </small></h5>
	                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                          <span aria-hidden="true">&times;</span>
	                        </button>
	                      </div>
	                      <div class="modal-body">
	                            <table id="tablaModalProductos" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
	                                <thead>
	                                    <tr>
	                                        <th>Codigo</th>
	                                        <th>Descripcion</th>
	                                        <th>Precio Venta</th>
	                                        <th>Precio Costo</th>
	                                        <th>Precio Mayorista</th>
	                                        <th>Stock</th>
	                                        <th>IVA</th>
	                                        <th>Imagen</th>
	                                        <th>Moneda</th>
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
      props: ['value', 'monedaCodigo', 'candec', 'tab_unica', 'checked_codigo_real', 'validar_codigo_producto', 'tabIndexPadre'],
      data(){
        return {
          	productos: []
        }
      }, 
      methods: {
            filtrarProductos(codigo){

				// ------------------------------------------------------------------------

				// LLAMAR FUNCION PARA FILTRAR PRODUCTOS

				// Common.filtrarProductosCommon(codigo).then(data => {
				//   this.productos = data
				// });

				// ------------------------------------------------------------------------

			}, enviarCodigoPadre(codigo){

				// ------------------------------------------------------------------------

				// ENVIAR CODIGO

				this.$emit('codigo_producto', codigo);
                
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
        	
            // FOCUS EN SEARCH INPUT DESPUES DE ABRIR MODAL 
            
            $('.producto-modal').on('shown.bs.modal', function() {
              $('div#tablaModalProductos_filter input').focus();
            })

            // ------------------------------------------------------------------------

        	$(document).ready( function () {

        	 	// ------------------------------------------------------------------------
                // >>
                // INICIAR EL DATATABLE PRODUCTOS MODAL
                // ------------------------------------------------------------------------
                
                var tableProductos = $('#tablaModalProductos').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/productoDatatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            { "data": "CODIGO" },
                            { "data": "DESCRIPCION" },
                            { "data": "PREC_VENTA" },
                            { "data": "PRECOSTO" },
                            { "data": "PREMAYORISTA" },
                            { "data": "STOCK" },
                            { "data": "IVA" },
                            { "data": "IMAGEN" },
                            { "data": "MONEDA" }
                        ]      
                    });
                
                // ------------------------------------------------------------------------

                // RECARGAR SIEMPRE TABLA PRODUCTOS 

    //             setInterval( function () {
				//     tableProductos.ajax.reload( null, false ); // user paging is not reset on reload
				// }, 300000 );

                // ------------------------------------------------------------------------
                
                // CAMBIAR TAMAÑO FUENTE 

                $("#tablaModalProductos").css("font-size", 12);
               /* tableProductos.columns.adjust()
                .responsive.recalc()
                .draw();*/

                // ------------------------------------------------------------------------

                // SELECCIONAR UNA FILA - SE PUEDE BORRAR - SIN USO


                $('#tablaModalProductos').on('click', 'tbody tr', function() {

                    // *******************************************************************

                    // CARGAR LOS VALORES A LAS VARIABLES DE PRODUCTO

                    me.codigoProducto = tableProductos.row(this).data().CODIGO;
                    // me.descripcionProducto = tableProductos.row(this).data().DESCRIPCION;  
                    // me.stockProducto = tableProductos.row(this).data().STOCK;

                    me.enviarCodigoPadre(me.codigoProducto);

                   	// me.ivaProducto = tableProductos.row(this).data().IVA;

                    // *******************************************************************

                    // CERRAR EL MODAL
                     
                     $('.producto-modal').modal('hide');

                    // *******************************************************************

                });

                //FIN TABLA MODAL PRODUCTOS
                // <<   
                // ------------------------------------------------------------------------
            });    
        }
    }
</script>