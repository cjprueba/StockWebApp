<template>
  <div class="container pt-3 bg-light">

              <div class="row">

                <div class="col-md-12" v-if="agregado.mostrar">
                  <div class="alert alert-success" role="alert">
                    <b>ULTIMO AGREGADO:</b> {{agregado.descripcion}} - <b>CANTIDAD:</b> {{agregado.cantidad}}
                  </div>
                </div>

                <div class="col-md-12 mb-3">
                  <div class="container-fluid">
                    <div class="row">

                    <div class="col-md-6">

                      <!-- ------------------------------------------------------------------ -->

                      <!-- HABILITAR CODIGO REAL -->
                    
                        <div class="custom-control custom-switch">
                         <input type="checkbox" class="custom-control-input" id="customControlAutosizing" v-model="opciones.imagenes">
                         <label class="custom-control-label" for="customControlAutosizing">Solo imágenes</label>
                        </div>
                    

                      <!-- ------------------------------------------------------------------ -->

                    </div>

                      <div class="col-md-6">
                  <ul class="nav navbar-nav text-right">
                    <li class="dropdown">
                      <a href="#" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" v-on:click="checkout()" role="button" aria-expanded="false"> 
                        <font-awesome-icon icon="shopping-basket" /> {{conteo}} - Items / {{total}} <!-- <span class="caret"></span> --></a>
                      <!-- <ul class="dropdown-menu dropdown-cart" role="menu">
                          <li>
                              <span class="item">
                                <span class="item-left">
                                    <img src="http://lorempixel.com/50/50/" alt="" />
                                    <span class="item-info">
                                        <span>Item name</span>
                                        <span>23$</span>
                                    </span>
                                </span>
                                <span class="item-right">
                                    <button class="btn btn-xs btn-danger pull-right">x</button>
                                </span>
                            </span>
                          </li>
                          <li>
                              <span class="item">
                                <span class="item-left">
                                    <img src="http://lorempixel.com/50/50/" alt="" />
                                    <span class="item-info">
                                        <span>Item name</span>
                                        <span>23$</span>
                                    </span>
                                </span>
                                <span class="item-right">
                                    <button class="btn btn-xs btn-danger pull-right">x</button>
                                </span>
                            </span>
                          </li>
                          <li>
                              <span class="item">
                                <span class="item-left">
                                    <img src="http://lorempixel.com/50/50/" alt="" />
                                    <span class="item-info">
                                        <span>Item name</span>
                                        <span>23$</span>
                                    </span>
                                </span>
                                <span class="item-right">
                                    <button class="btn btn-xs btn-danger pull-right">x</button>
                                </span>
                            </span>
                          </li>
                          <li>
                              <span class="item">
                                <span class="item-left">
                                    <img src="http://lorempixel.com/50/50/" alt="" />
                                    <span class="item-info">
                                        <span>Item name</span>
                                        <span>23$</span>
                                    </span>
                                </span>
                                <span class="item-right">
                                    <button class="btn btn-xs btn-danger pull-right">x</button>
                                </span>
                            </span>
                          </li>
                          <li class="divider"></li>
                          <li><a class="text-center" href="">View Cart</a></li>
                      </ul> -->
                    </li>
                  </ul>
                </div>
              </div>
            </div>
                </div> 


                <div class="col-md-9 order-md-2">
                  <div class="container-fluid">
                    <div class="row   mb-5">

                      <div class="col-md-12">
                        <ul class="shop__sorting">
                          <li :class="listar.activo_general"><a v-on:click="general" href="#">General</a></li>
                          <li :class="listar.activo_ofertas"><a v-on:click="ofertas" href="#">Ofertas</a></li>
                          <li :class="listar.activo_nuevos"><a href="#">Nuevos</a></li>
                        </ul>
                      </div>

                      <div class="col-12">
                        <div class="dropdown text-md-left text-center float-md-left mb-3 mt-3 mt-md-0 mb-md-0">
                          <label class="mr-2">Ordenar por:</label>
                          <a class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ordenarText}} <span class="caret"></span></a>
                          <div class="dropdown-menu" aria-labelledby="navbarDropdown" x-placement="bottom-start" style="position: absolute; transform: translate3d(71px, 48px, 0px); top: 0px; left: 0px; will-change: transform;">
                            <a class="dropdown-item" v-on:click="ordenarPor(1)" href="#">Relevancia</a>
                            <a class="dropdown-item" v-on:click="ordenarPor(2)" href="#">Precio Descendente</a>
                            <a class="dropdown-item" v-on:click="ordenarPor(3)" href="#">Precio Ascendente</a>
                          </div>
                        </div>
                        <div class="btn-group float-md-right ml-3">

                          <!-- <button type="button" class="btn btn-sm btn-light" v-on:click="anterior"> <span class="fa fa-arrow-left"></span> </button>
                          <button type="button" class="btn btn-sm btn-light" v-on:click="siguiente"> 
                            <span class="fa fa-arrow-right"></span> 
                          </button> -->

                          <div>
                            <b-pagination v-on:change="cambiarPagina"
                              v-model="opciones.actual"
                              :total-rows="opciones.filas"
                              :per-page="opciones.limite"
                              first-number
                              last-number
                            ></b-pagination>
                          </div>

                        </div>
                        <div class="dropdown float-right">
                          <label class="mr-2">Ver:</label>
                          <a class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{opciones.limite}} <span class="caret"></span></a>
                          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown" x-placement="bottom-end" style="will-change: transform; position: absolute; transform: translate3d(120px, 48px, 0px); top: 0px; left: 0px;">
                            <a class="dropdown-item" href="#" v-on:click="cambiarLimite(9)">9</a>
                            <a class="dropdown-item" href="#" v-on:click="cambiarLimite(12)">12</a>
                            <a class="dropdown-item" href="#" v-on:click="cambiarLimite(24)">24</a>
                            <a class="dropdown-item" href="#" v-on:click="cambiarLimite(48)">48</a>
                            <a class="dropdown-item" href="#" v-on:click="cambiarLimite(96)">96</a>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div :class="'row ' + cargar.class">
                      <div class="col-6 col-md-6 col-lg-4 mb-3"  v-for="producto in productos">

                        <!-- <div class="col-md-4 mb-3" v-for="producto in productos">
                            <div class="product py-4"> <span class="off bg-success">-25% OFF</span>
                                <div class="text-center"> <span v-html="producto.IMAGEN"></span> </div>
                                <div class="about text-center">
                                    <h5>{{producto.DESCRIPCION}}</h5> <span>$1,999.99</span>
                                </div>
                                <div class="cart-button mt-3 mb-3 px-2 d-flex justify-content-between align-items-center"> <button class="btn btn-primary text-uppercase">Agregar</button>
                                    <div class="add">  <span class="product_fav">
                                      <font-awesome-icon icon="shopping-basket" /></i></span> </div>
                                </div>
                            </div>
                        </div> -->

                        <div class="card h-100 border-0 product py-4">
                          <span :class="'off ' + producto.BACKGROUND">{{producto.ESTATUS}}</span>
                          <div class="card-img-top">
                            <span v-html="producto.IMAGEN"></span>
                           
                          </div>
                          <div class="card-body text-center">
                            <h6 class="card-title">
                              <a href="product.html" class=" font-weight-bold text-dark text-uppercase small"> {{producto.DESCRIPCION}}</a>
                            </h6>
                            <h5 class="card-price small text-warning">
                                    
                            </h5>
                            <h5 class="card-price small text-warning">
                              <i>
                                <b>{{producto.PREC_VENTA}} - </b> {{producto.PREMAYORISTA}}</i>
                            </h5>

                            <div class="small mb-2">
                                    <div class="available_title">Disponible: <span>{{producto.STOCK}}</span></div>
                            </div>

                            <button class="btn btn-primary btn-sm " v-on:click="mostrarModal(producto)">Agregar</button>  
                            
                          </div>
                        </div>
                      </div>
                      
                       
                    </div>

                    <!-- <div v-else class="row">
                          <div class="spinner-grow" role="status">
                            <span class="sr-only">Loading...</span>
                          </div>
                    </div>  -->

                    <div class="row sorting mb-5 mt-5">
                      <div class="col-12">
                        <a class="btn btn-sm btn-light">
                          <i class="fas fa-arrow-up mr-2"></i> Regresar Arriba</a>
                        <div class="btn-group float-md-right ml-3">
                          <!-- <button type="button" class="btn btn-sm btn-light" v-on:click="anterior"> <span class="fa fa-arrow-left"></span> </button>
                          <button type="button" class="btn btn-sm btn-light" v-on:click="siguiente"> <span class="fa fa-arrow-right"></span> </button> -->

                          <div>
                            <b-pagination v-on:change="cambiarPagina"
                              v-model="opciones.actual"
                              :total-rows="opciones.filas"
                              :per-page="opciones.limite"
                              first-number
                              last-number
                            ></b-pagination>
                          </div>

                        </div>
                        <div class="dropdown float-md-right">
                          <label class="mr-2">Ver:</label>
                          <a class="btn btn-light btn-sm dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{opciones.limite}} <span class="caret"></span></a>
                          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="#" v-on:click="cambiarLimite(9)">9</a>
                            <a class="dropdown-item" href="#" v-on:click="cambiarLimite(12)">12</a>
                            <a class="dropdown-item" href="#" v-on:click="cambiarLimite(24)">24</a>
                            <a class="dropdown-item" href="#" v-on:click="cambiarLimite(48)">48</a>
                            <a class="dropdown-item" href="#" v-on:click="cambiarLimite(96)">96</a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-4 order-md-1 col-lg-3 sidebar-filter">
                  <label>BUSQUEDA</label>
                  <input type="text" class="form-control mb-3" name="" v-model="opciones.busqueda" v-on:keyup="mostrarProductos()">

                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" v-model="opciones.tipo" name="inlineRadioOptions" id="inlineRadio1" value="1">
                    <label class="form-check-label" for="inlineRadio1">CODIGO</label>
                  </div>

                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" v-model="opciones.tipo" name="inlineRadioOptions" id="inlineRadio2" value="2">
                    <label class="form-check-label" for="inlineRadio2">DESCRIPCION</label>
                  </div>
                  <a href="#" class="btn btn-sm btn-block btn-primary btn-sm mb-3 mt-3" v-on:click="mostrarProductos()">Actualizar Resultados</a>
                  <!-- <h5 class="mt-0 mb-5">Mostrando <span class="text-primary">{{opciones.limite}}</span> Productos</h5> -->
                  <hr/>

                  <h6 class="text-uppercase  mb-3 mt-3">Categorias</h6>

                  <div class="container_checkbox mr-2">

                    <div class="mt-2 mb-2 pl-2" v-for="categoria in categorias">
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" :value="categoria.CODIGO" :id="categoria.CODIGO+'C'" v-model="opciones.categorias">
                        <label class="custom-control-label" :for="categoria.CODIGO+'C'">{{categoria.DESCRIPCION}} </label>
                      </div>
                    </div>

                  </div>  

                  <hr/>

                  <h6 class="text-uppercase  mt-3 mb-3">Marcas</h6>

                  <div class="container_checkbox mr-2">

                    <div class="mt-2 mb-2 pl-2" v-for="marca in marcas">
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" :value="marca.CODIGO" :id="marca.CODIGO" v-model="opciones.marcas">
                        <label class="custom-control-label" :for="marca.CODIGO">{{marca.DESCRIPCION}} </label>
                      </div>
                    </div>

                  </div> 
                  <hr/>
                  <h6 class="text-uppercase  mt-3 mb-3">Proveedores</h6>

                  <div class="container_checkbox mr-2">

                    <div class="mt-2 mb-2 pl-2" v-for="proveedor in proveedores">
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" :value="proveedor.CODIGO" :id="proveedor.CODIGO+'P'" v-model="opciones.proveedores">
                        <label class="custom-control-label" :for="proveedor.CODIGO+'P'">{{proveedor.DESCRIPCION}} </label>
                      </div>
                    </div>

                  </div> 

                  <!-- <div class="divider mt-5 mb-5 border-bottom border-secondary"></div>
                  <h6 class="text-uppercase mt-5 mb-3 font-weight-bold">Tamaño</h6>
                  <div class="mt-2 mb-2 pl-2">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" id="filter-size-1">
                      <label class="custom-control-label" for="filter-size-1">X-Small</label>
                    </div>
                  </div>
                  <div class="mt-2 mb-2 pl-2">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" id="filter-size-2">
                      <label class="custom-control-label" for="filter-size-2">Small</label>
                    </div>
                  </div>
                  <div class="mt-2 mb-2 pl-2">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" id="filter-size-3">
                      <label class="custom-control-label" for="filter-size-3">Medium</label>
                    </div>
                  </div>
                  <div class="mt-2 mb-2 pl-2">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" id="filter-size-4">
                      <label class="custom-control-label" for="filter-size-4">Large</label>
                    </div>
                  </div>
                  <div class="mt-2 mb-2 pl-2">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" id="filter-size-5">
                      <label class="custom-control-label" for="filter-size-5">X-Large</label>
                    </div>
                  </div>
                  <div class="divider mt-5 mb-5 border-bottom border-secondary"></div>
                  <h6 class="text-uppercase mt-5 mb-3 font-weight-bold">Precio</h6>
                  <div class="price-filter-control">
                    <input type="number" class="form-control w-50 pull-left mb-2" value="50" id="price-min-control">
                    <input type="number" class="form-control w-50 pull-right" value="150" id="price-max-control">
                  </div>
                  <input id="ex2" type="text" class="slider " value="50,150" data-slider-min="10" data-slider-max="200" data-slider-step="5" data-slider-value="[50,150]" data-value="50,150" style="display: none;">
                  <div class="divider mt-5 mb-5 border-bottom border-secondary"></div> -->
                  
                </div>

              </div>

              <!-- ------------------------------------------------------------------------ -->

              <!-- MODAL CANTIDAD -->

                  <div class="modal fade" id="modalAgregar" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">

                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Opciones</h5>
                        </div>

                        <div class="modal-body">

                          <div class="row">
                            <div class="col-md-12">
                              <label>Cantidad</label>
                              <input type="number" class="form-control" v-model="producto.cantidad" >
                            </div>
                          </div> 

                        </div>

                        <div class="modal-footer">
                          <button type="button" class="btn btn-dark" data-dismiss="modal" v-on:click="agregarProducto">Agregar</button>
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        </div>

                      </div>
                    </div>
                  </div>
                  
                  <!-- ------------------------------------------------------------------------ -->

            </div>
</template>
<script>
  export default {
      data(){
        return {
            productos: [],
            codigo: '',
            opciones: {
              offset: 0,
              limite: 9,
              filas: 0,
              actual: 1,
              categorias: [],
              marcas: [],
              proveedores: [],
              busqueda: '',
              tipo: '1',
              ordenar: 1,
              estado: 1,
              imagenes: true
            },
            cargando: false,
            categorias: [],
            marcas: [],
            proveedores: [],
            ordenarText: '',
            producto: {
              codigo: '',
              cantidad: 1,
              descripcion: '',
              precio_unit: 0,
              precio: 0,
              precio_mayorista: 0,
              stock: 0,
              descuento: 0,
              linea: 0,
              marca: 0
            }, agregado: {
              descripcion: '',
              cantidad: '',
              mostrar: false
            },
            conteo: 0,
            total: 0,
            listar: {
              activo_general: 'active',
              activo_ofertas: '',
              activo_nuevos: ''
            },
            cargar: {
              class: ''
            }
          }
      }, 
      methods: {
        inicio(){

          // ------------------------------------------------------------------------

          // MOSTRAR PRODUCTOS 

          let me = this;

          Common.inicioProductosViewNewCommon(me.opciones).then(data => {
            me.categorias = data.categorias;
            me.marcas = data.marcas;
            me.proveedores = data.proveedores;
            me.conteo = data.conteo;
            me.total = data.total;
          });

          // ------------------------------------------------------------------------

        },
        mostrarProductos(){

          // ------------------------------------------------------------------------

          // MOSTRAR PRODUCTOS 

          let me = this;

          me.cargar.class = 'special-card';

          Common.mostrarProductosViewNewCommon(me.opciones).then(data => {
            me.productos = data.data;
            me.cargar.class = '';
            me.cargando = false;
            me.opciones.filas = data.recordsFiltered;
          });

          // ------------------------------------------------------------------------

        },
        cambiarPagina(){

          // ------------------------------------------------------------------------

          this.cargar.class = 'special-card';
          this.cargando = true;
          this.opciones.offset = this.opciones.offset + this.opciones.limite;
          this.mostrarProductos();

          // ------------------------------------------------------------------------

        },
        anterior(){

          // ------------------------------------------------------------------------

          if (this.opciones.offset !== 0) {
            this.cargando = true;
            this.opciones.offset = this.opciones.offset - this.opciones.limite;
            this.mostrarProductos();
          }

          // ------------------------------------------------------------------------

        },
        siguiente(){

          // ------------------------------------------------------------------------

          // SIGUIENTE 

          this.cargando = true;
          this.opciones.offset = this.opciones.offset + this.opciones.limite;
          this.mostrarProductos();

          // ------------------------------------------------------------------------

        },
        cambiarLimite(limite) {

          // ------------------------------------------------------------------------

          // CAMBIAR LIMITE

          this.cargando = true;
          this.cargar.class = 'special-card';
          this.opciones.limite = limite;
          this.mostrarProductos();

          // ------------------------------------------------------------------------

        },
        actualizar(){
          console.log(this.checked.categorias);
          console.log(this.checked.marcas);
        },
        ordenarPor(opcion){

          // ------------------------------------------------------------------------

          this.opciones.ordenar = opcion;

          if (this.opciones.ordenar === 1) {
            this.ordenarText = 'Relevancia';
          } else if (this.opciones.ordenar === 2) {
            this.ordenarText = 'Precio Descendente';
          } else if (this.opciones.ordenar === 3) {
            this.ordenarText = 'Precio Ascendente';
          }

          this.mostrarProductos();

          // ------------------------------------------------------------------------

        },
        mostrarModal(producto){

          // ------------------------------------------------------------------------

          // MOSTRAR LA PREGUNTA DE ELIMINAR 

          this.producto.codigo = producto.CODIGO;
          this.producto.descripcion = producto.DESCRIPCION;
          this.producto.precio_unit = producto.PREC_VENTA_CRUDO;
          this.producto.precio_mayorista = producto.PREMAYORISTA_CRUDO;
          this.producto.stock = producto.STOCK;
          this.producto.descuento = producto.DESCUENTO;
          this.producto.marca = producto.MARCA;
          this.producto.linea = producto.LINEA;
          $('#modalAgregar').modal('show');

          // ------------------------------------------------------------------------

        },
        agregarProducto(){

          // ------------------------------------------------------------------------

          // MOSTRAR PRODUCTOS 

          let me = this;

          Common.agregarProductoPedidoCommon(this.producto).then(data => {

            if (data.response === true) {

              me.agregado.descripcion = me.producto.descripcion;
              me.agregado.cantidad = me.producto.cantidad;
              me.agregado.mostrar = true;
              me.conteo = data.conteo;
              me.total = data.total;

              Swal.fire(
                'Agregado !',
                'Se ha agregado correctamente !',
                'success'
              )

            } else if (data.response === false) {
              Swal.fire(
                'Error !',
                data.statusText ,
                'error'
              )
            }

          });

          // ------------------------------------------------------------------------

        },
        checkout(){

          // ------------------------------------------------------------------------

          // CAMBIAR DE LOCALIZACION 

          window.location.href = '/pd2';

          // ------------------------------------------------------------------------

        },
        ofertas(){

          // ------------------------------------------------------------------------
          
          // OFERTAS 

          this.opciones.estado = 2;
          this.listar.activo_ofertas = 'active';
          this.listar.activo_general = '';
          this.listar.activo_nuevos = '';
          this.mostrarProductos();

          // ------------------------------------------------------------------------

        },
        general(){

          // ------------------------------------------------------------------------
          
          // OFERTAS 

          this.opciones.estado = 1;
          this.listar.activo_ofertas = '';
          this.listar.activo_general = 'active';
          this.listar.activo_nuevos = '';
          this.mostrarProductos();

          // ------------------------------------------------------------------------

        }
      },
        mounted() {
          
          // ------------------------------------------------------------------------

          // INICIAR VARIABLES 

          let me = this;
          me.cargar.class = 'special-card';
          me.cargando = true;
          me.inicio();
          me.ordenarPor(1);

          // ------------------------------------------------------------------------

        }
    }
</script>
<style>
  .container_checkbox { border:2px solid #ccc; width:250px; height: 200px; overflow-y: scroll; }

  ul.dropdown-cart{
    min-width:250px;
}
ul.dropdown-cart li .item{
    display:block;
    padding:3px 10px;
    margin: 3px 0;
}
ul.dropdown-cart li .item:hover{
    background-color:#f3f3f3;
}
ul.dropdown-cart li .item:after{
    visibility: hidden;
    display: block;
    font-size: 0;
    content: " ";
    clear: both;
    height: 0;
}

ul.dropdown-cart li .item-left{
    float:left;
}
ul.dropdown-cart li .item-left img,
ul.dropdown-cart li .item-left span.item-info{
    float:left;
}
ul.dropdown-cart li .item-left span.item-info{
    margin-left:10px;   
}
ul.dropdown-cart li .item-left span.item-info span{
    display:block;
}
ul.dropdown-cart li .item-right{
    float:right;
}
ul.dropdown-cart li .item-right button{
    margin-top:14px;
}

.t-products {
    background-image: linear-gradient(to right top, #5629c0, #5625cb, #5620d5, #551ae0, #5412eb);
    color: #fff;
    border-radius: 3px
}

.processor {
    background-color: #fff;
    margin-top: 5px;
    border-bottom: 1px solid #eee
}

.brand {
    background-color: #fff;
    border-bottom: 1px solid #eee
}

.type {
    background-color: #fff
}

.product {
    padding: 10px;
    background-color: #fff;
    border-radius: 5px;
    position: relative
}

.about span {
    color: #5629c0;
    font-size: 16px
}

.cart-button button {
    font-size: 12px;
    color: #fff;
    background-color: #5629c0;
    height: 38px
}

.cart-button button:focus,
button:active {
    font-size: 12px;
    color: #fff;
    background-color: #5629c0;
    box-shadow: none
}

.product_fav i {
    line-height: 40px;
    color: #5629c0;
    font-size: 15px
}

.product_fav {
    display: inline-block;
    width: 36px;
    height: 39px;
    background: #FFFFFF;
    box-shadow: 0px 1px 5px rgba(0, 0, 0, 0.1);
    border-radius: 11%;
    text-align: center;
    cursor: pointer;
    margin-left: 3px;
    -webkit-transition: all 200ms ease;
    -moz-transition: all 200ms ease;
    -ms-transition: all 200ms ease;
    -o-transition: all 200ms ease;
    transition: all 200ms ease
}

.product_fav:hover {
    background: #5629c0
}

.product_fav:hover i {
    color: #fff
}

.about {
    margin-top: 12px
}

.off {
    position: absolute;
    left: 65%;
    top: 6%;
    width: 80px;
    text-align: center;
    height: 30px;
    line-height: 8px;
    border-radius: 5px;
    font-size: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff
}

.shop__sorting {
  list-style: none;
  padding-left: 0;
  margin-bottom: 40px;
  margin-top: -20px;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
  text-align: right;
}
.shop__sorting > li {
  display: inline-block;
}
.shop__sorting > li > a {
  display: block;
  padding: 20px 10px;
  margin-bottom: -1px;
  border-bottom: 2px solid transparent;
  color: #757575;
  -webkit-transition: all .05s linear;
       -o-transition: all .05s linear;
          transition: all .05s linear;
}
.shop__sorting > li > a:hover,
.shop__sorting > li > a:focus {
  color: #333333;
  text-decoration: none;
}
.shop__sorting > li.active > a {
  color: #ed3e49;
  border-bottom-color: #ed3e49;
}
@media (max-width: 767px) {
  .shop__sorting {
    text-align: left;
    border-bottom: 0;
  }
  .shop__sorting > li {
    display: block;
  }
  .shop__sorting > li > a {
    padding: 10px 15px;
    margin-bottom: 10px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
  }
  .shop__sorting > li.active > a {
    font-weight: 600;
  }
}

.special-card {
/* create a custom class so you 
   do not run into specificity issues 
   against bootstraps styles
   which tends to work better than using !important 
   (future you will thank you later)*/

  background-color: rgba(245, 245, 245, 1);
  opacity: .4;
}
</style>