<template>
	<div>
			<label for="validationTooltip01">Código</label>
			<div class="input-group">
				<div class="input-group-prepend">
					<button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".producto-modal"><font-awesome-icon icon="search"/></button>
				</div>

				<datalist id="productos">
					<option v-for="producto in productos" :value="producto.CODIGO"></option>
				</datalist>

				<input ref="codigo" id="codigo_Producto" class="custom-select custom-select-sm" type="text" list="productos" v-model="codigoProducto" v-bind:class="{ 'is-invalid': validarCodigoProducto }" v-on:keyup="filtrarProductos()" v-on:keyup.prevent.13="enviarCodigoPadre()" v-on:blur="enviarCodigoPadre()">

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
      props: [''],
      data(){
        return {
          	codigoProducto: '',
          	productos: [],
          	validarCodigoProducto: false
        }
      }, 
      methods: {
            filtrarProductos(){

				// ------------------------------------------------------------------------

				// LLAMAR FUNCION PARA FILTRAR PRODUCTOS

				Common.filtrarProductosCommon(this.codigoProducto).then(data => {
				  this.productos = data
				});

				// ------------------------------------------------------------------------

			}, enviarCodigoPadre(){

				// ------------------------------------------------------------------------

				// ENVIAR CODIGO

				this.$emit('codigo_producto', this.codigoProducto);

				// ------------------------------------------------------------------------

			}, vaciarDevolver(){

				// ------------------------------------------------------------------------

				// VACIAR Y DEVOLVER FOCUS AL TEXTBOX

				this.codigoProducto = '';
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
                
                var tableProductos = $('#tablaModalProductos').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy": true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "url": "/producto",
                                 "dataType": "json",
                                 "type": "GET",
                                 "contentType": "application/json; charset=utf-8"
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

                setInterval( function () {
				    tableProductos.ajax.reload( null, false ); // user paging is not reset on reload
				}, 30000 );

                // ------------------------------------------------------------------------
                
                // SELECCIONAR UNA FILA - SE PUEDE BORRAR - SIN USO


                $('#tablaModalProductos').on('click', 'tbody tr', function() {

                    if ($(this).hasClass('selected')) {
                        $(this).removeClass('selected');
                    } else {
                        tableProductos.$('tr.selected').removeClass('selected');
                        $(this).addClass('selected');
                    }

                    // *******************************************************************

                    // CARGAR LOS VALORES A LAS VARIABLES DE PRODUCTO

                    me.codigoProducto = tableProductos.row(this).data().CODIGO;
                    me.descripcionProducto = tableProductos.row(this).data().DESCRIPCION;  
                    me.stockProducto = tableProductos.row(this).data().STOCK;

                    Common.calcularCotizaciónPrecioCommon(tableProductos.row(this).data().PREC_VENTA, tableProductos.row(this).data().MONEDA, me.monedaCodigo, me.candec, me.tab_unica).then(data => {
						  me.precioProducto = data
					});

                   	me.ivaProducto = tableProductos.row(this).data().IVA;

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