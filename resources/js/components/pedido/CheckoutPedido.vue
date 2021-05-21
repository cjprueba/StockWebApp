<template>
	<div class="container-fluid bg-dark" >
  <div v-if="$can('pedido.checkout') && $can('pedidos')">
	<div class="px-4 px-lg-0 mt-3" v-if="productos.length > 0">	

	<div class="text-white text-center mb-3">
		<div>
			<h1 class="display-6">CHECKOUT PEDIDO</h1>
			<!-- <p class="lead mb-0">Build a fully structred shopping cart page using Bootstrap 4. </p>
		    <p class="lead">Snippet by <a href="https://bootstrapious.com/snippets" class="text-white font-italic">
		            <u>Bootstrapious</u></a>
		    </p> -->
		</div>	
	</div>	

  <!-- End -->

  <div class="row py-5 p-4 bg-white rounded shadow-sm">
        <div class="col-lg-6">
          <div class="bg-light rounded-pill px-4 py-3 text-uppercase font-weight-bold">Código Cupón</div>
          <div class="p-4">
            <p class="font-italic mb-4">Si se tiene un cupón, agregar abajo</p>
            <div class="input-group mb-4 border rounded-pill p-2">
              <input type="text" placeholder="Aplicar cupón" aria-describedby="button-addon3" class="form-control border-0">
              <div class="input-group-append border-0">
                <button id="button-addon3" type="button" class="btn btn-dark px-4 rounded-pill"><i class="fa fa-gift mr-2"></i>Aplicar cupón</button>
              </div>
            </div>
          </div>
          <div class="bg-light rounded-pill px-4 py-3 text-uppercase font-weight-bold">Instrucciones para el pedido</div>
          <div class="p-4">
            <p class="font-italic mb-4">Si tienes información extra puedes dejar esa observación abajo.</p>
            <textarea v-model="pedido.observacion" name="" cols="30" rows="2" class="form-control"></textarea>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="bg-light rounded-pill px-4 py-3 text-uppercase font-weight-bold">Resumen pedido </div>
          <div class="p-4">
            <p class="font-italic mb-4">La tarifa del envio y costos adicionales son calculados en valores ingresados.</p>
            <ul class="list-unstyled mb-4">
              <li class="d-flex justify-content-between py-3 border-bottom"><strong class="text-muted">Pedido Subtotal </strong><strong>{{pedido.total}}</strong></li>
              <li class="d-flex justify-content-between py-3 border-bottom"><strong class="text-muted">Items</strong><strong>{{pedido.cantidad_items}}</strong></li>
              <li class="d-flex justify-content-between py-3 border-bottom"><strong class="text-muted">Cantidad</strong><strong>
              {{pedido.cantidad}}</strong></li>
              <li class="d-flex justify-content-between py-3 border-bottom"><strong class="text-muted">Total</strong>
                <h5 class="font-weight-bold">{{pedido.total}} </h5>
              </li>
            </ul><a href="#" class="btn btn-dark rounded-pill py-2 btn-block" v-on:click="confirmar">Confirmar pedido</a>
          </div>
        </div>
      </div>

  <div class="pb-5">
    <div class="">
      <div class="row">
        <div class="col-lg-12 p-5 bg-white rounded shadow-sm mt-5">

          <!-- Shopping cart table -->
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th scope="col" class="border-0 bg-light">
                    <div class="p-2 px-3 text-uppercase">Producto</div>
                  </th>
                  <th scope="col" class="border-0 bg-light">
                    <div class="py-2 text-uppercase">Unitario</div>
                  </th>
                  <th scope="col" class="border-0 bg-light">
                    <div class="py-2 text-uppercase">Precio</div>
                  </th>
                  <th scope="col" class="border-0 bg-light">
                    <div class="py-2 text-uppercase">Cantidad</div>
                  </th>
                  <th scope="col" class="border-0 bg-light">
                    <div class="py-2 text-uppercase">Remover</div>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="producto in productos">
                  <th scope="row" class="border-0">
                    <div class="p-2">
                      <span v-html="producto.IMAGEN"></span>
                      <!-- <img src="https://res.cloudinary.com/mhmd/image/upload/v1556670479/product-1_zrifhn.jpg" alt="" width="70" class="img-fluid rounded shadow-sm"> -->
                      <div class="ml-3 d-inline-block align-middle">
                        <h5 class="mb-0"> <a href="#" class="text-dark d-inline-block align-middle" style="font-size: 14px">{{producto.DESCRIPCION}} </a></h5><span class="text-muted font-weight-normal font-italic d-block">Categoria: {{producto.CATEGORIA}}</span>
                      </div>
                    </div>
                  </th>
                  <td class="border-0 align-middle"><strong>{{producto.PREC_VENTA}}</strong></td>
                  <td class="border-0 align-middle"><strong>{{producto.PRECIO}}</strong></td>
                  <td class="border-0 align-middle">
                  	<div class="quantity">
                        <input type="button" value="+" class="plus">
                        <input type="number" step="1" v-on:change="cambiarCantidad(producto.CODIGO, $event.target.value)" :value="producto.CANTIDAD" max="99" min="1" value="1" title="Qty" class="qty"
                                           size="4">
                        <input type="button" value="-" class="minus">
                    </div>
                  </td>
                  <td class="border-0 align-middle"><a v-on:click="eliminar(producto.CODIGO)" href="#" class="text-dark"><i class="fa fa-trash"></i></a></td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- End -->
        </div>
      </div>

      

    </div>
  </div>
</div>

<div v-else>
	<b-progress v-if="progressBar === true" class="mt-3" :max="max" show-value>
	      <b-progress-bar :value="value * (1.5 / 10)" variant="info"></b-progress-bar>
	</b-progress>

	<div v-else class="mt-3 alert alert-dark" role="alert">
  		No se registrado nada en el checkout !
    </div>
</div>
</div>
<!-- ------------------------------------------------------------------------ -->

  <div v-else>
    <cuatrocientos-cuatro></cuatrocientos-cuatro>
  </div>

<!-- ------------------------------------------------------------------------ -->

				<!-- MODAL CANTIDAD -->

                  <div class="modal fade" id="modalCliente" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">

                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">CLIENTE: {{ pedido.descripcionCliente }} </h5>
                        </div>

                        <div class="modal-body">
                          <busqueda-cliente-modal @codigo="codigoCliente" @nombre="nombreCliente"></busqueda-cliente-modal>	
                        </div>

                        <div class="modal-footer">
                          <button type="button" class="btn btn-dark" v-on:click="confirmarCliente">Aceptar</button>
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        </div>

                      </div>
                    </div>
                  </div>
                  
            <!-- ------------------------------------------------------------------------ -->

            <!-- TOAST STOCK CERO -->

			<b-toast id="toast-error-cliente" variant="warning" solid>
		      <template v-slot:toast-title>
		        <div class="d-flex flex-grow-1 align-items-baseline">
		          <b-img blank blank-color="#ff5555" class="mr-2" width="12" height="12"></b-img>
		          <strong class="mr-auto">Error !</strong>
		          <small class="text-muted mr-2">Cliente</small>
		        </div>
		      </template>
		      Necesita seleccionar un cliente !
		    </b-toast>

			<!-- ------------------------------------------------------------------------ -->
</div>
</template>
<script>
	 export default {
      data(){
        return {
            productos: [],
            opciones: {},
            pedido: {
            	total: 0,
            	cantidad: 0,
            	cantidad_items: 0,
            	codigocliente: 0,
            	descripcionCliente: '',
            	observacion: ''
            },
            max: 100,
            value: 0,
            progressBar: true,
          }
      }, 
      methods: {
        inicio(){

          // ------------------------------------------------------------------------

          // MOSTRAR PRODUCTOS 

          let me = this;

          Common.mostrarProductosPedidoCommon(me.pedido).then(data => {
          	me.progressBar = false;
            me.productos = data.data;
            me.pedido.total = data.total;
            me.pedido.cantidad = data.cantidad;
            me.pedido.cantidad_items = data.cantidad_items;
          });

          // ------------------------------------------------------------------------

        },
        confirmar(){

          // ------------------------------------------------------------------------

          $('#modalCliente').modal('show');

          // ------------------------------------------------------------------------

        }, codigoCliente(codigo){

          // ------------------------------------------------------------------------

          // CODIGO CLIENTE 

          this.pedido.codigocliente = codigo;

          // ------------------------------------------------------------------------

        }, nombreCliente(nombre){

          // ------------------------------------------------------------------------
          
          // NOMBRE CLIENTE

          this.pedido.descripcionCliente = nombre;

          // ------------------------------------------------------------------------

        }, controlador(){

          // ------------------------------------------------------------------------

          var falta = false;
          	
          // ------------------------------------------------------------------------

          if (this.pedido.codigocliente === 0) {
          	this.$bvToast.show('toast-error-cliente');
	        falta = true;
	      } else {

	      }

	      // ------------------------------------------------------------------------

	      // RETORNAR VALOR 

	      return falta;
	      
	      // ------------------------------------------------------------------------

        },
        confirmarCliente(){

        	// ------------------------------------------------------------------------

        	// CONFIRMAR PEDIDO 

        	if (this.controlador() === true) {
        		return;
        	}

        	Common.confirmarPedidoCommon(this.pedido).then(data => {
	          	if(data.response === true) {

	          		Swal.fire({
						title: 'Guardado ',
						type: 'success',
						confirmButtonColor: 'btn btn-dark',
						confirmButtonText: 'Aceptar',
					}).then((result) => {
						window.location.href = '/pd2';
					})	
	          	}
	        });

        	$('#modalCliente').modal('hide');

        	// ------------------------------------------------------------------------
        	
        }, progress(){

        	let me = this;
        	var i = 0;

			var counterBack = setInterval(function () {
			  i = i + 100;
			  if (i > 0) {
			    me.value = i;
			  } else {
			    clearInterval(counterBack);
			  }

			}, 1000);

        }, cambiarCantidad(codigo, cantidad){

        	// ------------------------------------------------------------------------

        	let me = this;

        	// ------------------------------------------------------------------------

        	Common.cambiarCantidadPedidoCommon({'codigo': codigo, 'cantidad':cantidad}).then(data => {

	          	if(data.response === true) {

	          		Swal.fire({
						title: 'Cambiado ',
						type: 'success',
						confirmButtonColor: 'btn btn-dark',
						confirmButtonText: 'Aceptar',
					})	


	          			
	          	} else if (data.response === false) {
	              Swal.fire(
	                'Error !',
	                data.statusText ,
	                'error'
	              )
	            }

	            me.inicio();


	        });

        	// ------------------------------------------------------------------------

        }, eliminar(codigo) {

        	// ------------------------------------------------------------------------
        	
        	let me = this;

        	// ------------------------------------------------------------------------

        	Swal.fire({
				title: 'Estas seguro ?',
				text: "Eliminar producto !",
				type: 'warning',
				showLoaderOnConfirm: true,
				showCancelButton: true,
				confirmButtonColor: '#d33',
				cancelButtonColor: '#3085d6',
				confirmButtonText: 'Eliminar !',
				cancelButtonText: 'Cancelar',
				preConfirm: () => {
				    return Common.eliminarProductoPedidoCommon(codigo).then(data => {
				    	if (!data.response === true) {
				          throw new Error(data.statusText);
				        }
				  		return data;
				  	}).catch(error => {
				        Swal.showValidationMessage(
				          `Request failed: ${error}`
				        )
				    });
				}
			}).then((result) => {

				if (result.value.response) {

					// ------------------------------------------------------------------------

					Swal.fire(
						'Eliminado!',
						'Se ha correctamente el producto  !',
						'success'
					)

					me.inicio();

					// ------------------------------------------------------------------------

				}
			})

        	// ------------------------------------------------------------------------

        }
      },
        mounted() {
          
          // ------------------------------------------------------------------------

          // INICIAR VARIABLES 

          let me = this;
          me.cargando = true;
          me.inicio();
          me.progress();

          // ------------------------------------------------------------------------

          

        }
    }
</script>
<style>
	.bg-checkout {
	  background: #EEEEA3;
	  background: -webkit-linear-gradient(to right, #EEEEA3, #FAFA52);
	  background: linear-gradient(to right, #A3ECEE, #2EA7F2);
	  min-height: 100vh;
	}

.quantity {
    float: left;
    margin-right: 15px;
    background-color: #eee;
    position: relative;
    width: 80px;
    overflow: hidden
}

.quantity input {
    margin: 0;
    text-align: center;
    width: 15px;
    height: 15px;
    padding: 0;
    float: right;
    color: #000;
    font-size: 20px;
    border: 0;
    outline: 0;
    background-color: #F6F6F6
}

.quantity input.qty {
    position: relative;
    border: 0;
    width: 100%;
    height: 40px;
    padding: 10px 25px 10px 10px;
    text-align: center;
    font-weight: 400;
    font-size: 15px;
    border-radius: 0;
    background-clip: padding-box
}

.quantity .minus, .quantity .plus {
    line-height: 0;
    background-clip: padding-box;
    -webkit-border-radius: 0;
    -moz-border-radius: 0;
    border-radius: 0;
    -webkit-background-size: 6px 30px;
    -moz-background-size: 6px 30px;
    color: #bbb;
    font-size: 20px;
    position: absolute;
    height: 50%;
    border: 0;
    right: 0;
    padding: 0;
    width: 25px;
    z-index: 3
}

.quantity .minus:hover, .quantity .plus:hover {
    background-color: #dad8da
}

.quantity .minus {
    bottom: 0
}
</style>