<template>
	<!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon">
          <font-awesome-icon icon="shopping-basket" />
        </div>
        <div class="sidebar-brand-text mx-3">RETAIL</div>
      </a>

      <hr class="sidebar-divider my-0">

      <!-- ------------------------------------------------------------------------ -->

      <!-- PARAMETROS -->

      <div class="sidebar-heading text-center">Moneda: {{moneda}}</div>

      <!-- ------------------------------------------------------------------------ -->

      <!-- Divider -->

      <hr class="sidebar-divider my-0">

      <!-- ------------------------------------------------------------------------ -->

      <!-- Nav Item - Dashboard -->
      <li class="nav-item active">
        <a class="nav-link" href="/home" >
          <font-awesome-icon icon="home" />
          <span>Inicio</span></a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider">

      <!-- Heading -->
      <div class="sidebar-heading">
        Interfaz
      </div>

      <!-- Nav Item - Pages Collapse Menu -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo" hidden>
          <font-awesome-icon icon="cog" />
          <span>Components</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Custom Components:</h6>
            <a class="collapse-item" href="buttons.html">Buttons</a>
            <a class="collapse-item" href="cards.html">Cards</a>
          </div>
        </div>
      </li>

      <!-- Nav Item - Utilities Collapse Menu -->
      <li class="nav-item" >
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
          <font-awesome-icon icon="sticky-note" />
          <span >Reporte</span>
        </a>
        <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Lista de Reportes:</h6>
             <router-link class="collapse-item" :to="{name: 'rptVenta'}">Ventas</router-link>
             <router-link class="collapse-item" :to="{name: 'rptTransferencia'}">Transferencias</router-link>
             <router-link class="collapse-item" :to="{name: 'rptDescuento'}">Descuentos</router-link>
             <router-link class="collapse-item" :to="{name: 'rptVendedor'}">Vendedor</router-link>
             <!-- <router-link class="collapse-item" :to="{name: 'rptCliente'}">Clientes</router-link> -->
             <router-link class="collapse-item" :to="{name: 'rptArticulo'}">Articulos</router-link>
             <router-link class="collapse-item" :to="{name: 'rptProveedor'}">Proveedores</router-link>
             <router-link class="collapse-item" :to="{name: 'rptInventario'}">Inventarios</router-link>
             <router-link class="collapse-item" :to="{name: 'rptCompra'}">Compras</router-link>
             <router-link class="collapse-item" :to="{name: 'rptStock'}">Stock</router-link>
          </div>
        </div>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTransferencia" aria-expanded="true" aria-controls="collapseTransferencia">
          <font-awesome-icon icon="truck"/>
          <span>Transferencia</span>
        </a>
        <div id="collapseTransferencia" class="collapse" aria-labelledby="headingTransferencia" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link class="collapse-item" :to="{name: 'transferenciaMostrar'}">Mostrar Transferencias</router-link>
            <router-link class="collapse-item" :to="{name: 'transferencia'}">Realizar Transferencias</router-link>
            <!-- <a class="collapse-item" href="#" v-on:click="llamar(21)">Realizar Transferencias</a> -->
            <router-link class="collapse-item" :to="{name: 'transferenciaImportar'}">Importar Transferencia</router-link>

          </div>
        </div>
      </li>

      <!-- ------------------------------------------------------------------------------------- -->

      <!-- COMPRA -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCompra" aria-expanded="true" aria-controls="collapseCompra">
          <font-awesome-icon icon="cart-plus"/>
          <span>Compra</span>
        </a>
        <div id="collapseCompra" class="collapse" aria-labelledby="headingCompra" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link class="collapse-item" :to="{name: 'compraMostrar'}">Mostrar Compras</router-link>
            <router-link class="collapse-item" :to="{name: 'compra'}">Realizar Compras</router-link>
            <router-link class="collapse-item" :to="{name: 'compraContainer'}">Crear Container</router-link>
          </div>
        </div>
      </li>

      <!-- ------------------------------------------------------------------------------------- -->

      <!-- VENTA -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseVenta" aria-expanded="true" aria-controls="collapseVenta">
          <font-awesome-icon icon="shopping-bag"/>
          <span>Venta</span>
        </a>
        <div id="collapseVenta" class="collapse" aria-labelledby="headingVenta" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link class="collapse-item" :to="{name: 'ventaMostrar'}">Mostrar Ventas</router-link>
            <router-link class="collapse-item" :to="{name: 'ventaRealizar'}">Realizar Venta</router-link>
            <router-link class="collapse-item" :to="{name: 'ventaMayorista'}">Realizar Mayorista</router-link>
            <router-link class="collapse-item" :to="{name: 'ventaAnular'}">Anular Venta</router-link>
          </div>
        </div>
      </li>

      <!-- ------------------------------------------------------------------------------------- -->

      <!-- MOVIMIENTOS -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMovimientos" aria-expanded="true" aria-controls="collapseMovimientos">
          <font-awesome-icon icon="money-bill-alt" />
          <span>Movimientos</span>
        </a>
        <div id="collapseMovimientos" class="collapse" aria-labelledby="headingMovimientos" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link class="collapse-item" :to="{name: 'notaRemision'}">Nota de Remisión</router-link>
            
          </div>
        </div>
      </li>


      <!-- ------------------------------------------------------------------------------------- -->

      <!-- SUCURSALES -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSucursales" aria-expanded="true" aria-controls="collapseSucursales">
          <font-awesome-icon icon="building" />
          <span>Sucursales</span>
        </a>
        <div id="collapseSucursales" class="collapse" aria-labelledby="headingSucursales" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link class="collapse-item" :to="{name: 'crearSucursal'}">Crear Sucursal</router-link>
            
          </div>
        </div>
      </li>


      <!-- ------------------------------------------------------------------------------------- -->

      <!-- PROVEEDOR -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProveedor" aria-expanded="true" aria-controls="collapseProveedor">
          <font-awesome-icon icon="ambulance"/>
          <span>Proveedor</span>
        </a>
        <div id="collapseProveedor" class="collapse" aria-labelledby="headingProveedor" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link class="collapse-item" :to="{name: 'proveedorMostrar'}">Mostrar Proveedores</router-link>
            <router-link class="collapse-item" :to="{name: 'proveedorPago'}">Pago de Cuentas</router-link>
            <router-link class="collapse-item" :to="{name: 'proveedorDevolucion'}">Devolución</router-link>
            <router-link class="collapse-item" :to="{name: 'proveedorDevolucionMostrar'}">Mostrar Devoluciones</router-link>
            <router-link class="collapse-item" :to="{name: 'proveedorCrear'}">Crear Proveedor</router-link>
          </div>
        </div>
      </li>

      <!-- ------------------------------------------------------------------------------------- -->
      
      <!-- CLIENTEs -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseClientes" aria-expanded="true" aria-controls="collapseClientes">
          <font-awesome-icon icon="user-circle" />
          <span>Clientes</span>
        </a>
        <div id="collapseClientes" class="collapse" aria-labelledby="headingClientes" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link class="collapse-item" :to="{name: 'crearCliente'}">Crear Cliente</router-link>
            
          </div>
        </div>
      </li>
      
      <!-- ------------------------------------------------------------------------------------- -->

      <li class="nav-item" >
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProdutos" aria-expanded="true" aria-controls="collapseProdutos">
          <font-awesome-icon icon="tags"/>
          <span>Productos</span>
        </a>
        <div id="collapseProdutos" class="collapse" aria-labelledby="headingProdutos" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link class="collapse-item" :to="{name: 'productoMostrar'}">Mostrar Productos</router-link>
            <router-link class="collapse-item" :to="{name: 'productoRegistrar'}">Registrar Productos</router-link>
          </div>
        </div>
      </li>

      <!-- ------------------------------------------------------------------------------------- -->

      <!-- INVENTARIO MENU -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseInventario" aria-expanded="true" aria-controls="collapseInventario">
          <font-awesome-icon icon="list-alt" />
          <span>Inventario</span>
        </a>
        <div id="collapseInventario" class="collapse" aria-labelledby="headingInventario" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link class="collapse-item" :to="{name: 'inventarioMostrar'}">Mostrar Inventario</router-link>
            <router-link class="collapse-item" :to="{name: 'inventarioRealizar'}">Realizar Inventario</router-link>
          </div>
        </div>
      </li>

        <!-- ------------------------------------------------------------------------------------- -->

      <!-- COTIZACION MENU -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCotizacion" aria-expanded="true" aria-controls="collapseCotizacion">
          <font-awesome-icon icon="coins" />
          <span>Cotización</span>
        </a>
        <div id="collapseCotizacion" class="collapse" aria-labelledby="headingCotizacion" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link class="collapse-item" :to="{name: 'cotizacionCrear'}">Crear Cotización</router-link>
          </div>
        </div>
      </li>

        <!-- ------------------------------------------------------------------------------------- -->

      <!-- TRANSPORTE MENU -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTransporte" aria-expanded="true" aria-controls="collapseTransporte">
          <font-awesome-icon icon="truck-moving" />
          <span>Transporte</span>
        </a>
        <div id="collapseTransporte" class="collapse" aria-labelledby="headingTransporte" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link class="collapse-item" :to="{name: 'transporteCrear'}">Crear Transporte</router-link>
          </div>
        </div>
      </li>

        <!-- ------------------------------------------------------------------------------------- -->

      <!-- USER MENU -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRol" aria-expanded="true" aria-controls="collapseRol">
          <font-awesome-icon icon="user-circle" />
          <span>Usuarios</span>
        </a>
        <div id="collapseRol" class="collapse" aria-labelledby="headingRol" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link class="collapse-item" :to="{name: 'userCrear'}">Usuarios</router-link>
            <router-link class="collapse-item" :to="{name: 'rolCrear'}">Roles</router-link>
            <router-link class="collapse-item" :to="{name: 'permisosCrear'}">Permisos</router-link>
            <router-link class="collapse-item" :to="{name: 'rolAsignar'}">Roles a Usuarios</router-link>
            <router-link class="collapse-item" :to="{name: 'permisoAsignar'}">Permisos a Usuarios</router-link>
          </div>
        </div>
      </li>
            <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAtributos" aria-expanded="true" aria-controls="collapseAtributos">
          <font-awesome-icon icon="user-circle" />
          <span>Atributos</span>
        </a>
        <div id="collapseAtributos" class="collapse" aria-labelledby="headingAtributos" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link class="collapse-item" :to="{name: 'marcaCrear'}">Marca</router-link>
            <router-link class="collapse-item" :to="{name: 'categoriaCrear'}">Categoria</router-link>
            <router-link class="collapse-item" :to="{name: 'subcategoriaCrear'}">SubCategoria</router-link>
            <router-link class="collapse-item" :to="{name: 'colorCrear'}">Color</router-link>
            <router-link class="collapse-item" :to="{name: 'talleCrear'}">Talle</router-link>
            <router-link class="collapse-item" :to="{name: 'telaCrear'}">Tela</router-link>
            <router-link class="collapse-item" :to="{name: 'nombreCrear'}">Nombre</router-link>
          </div>
        </div>
      </li>
        <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseGondolas" aria-expanded="true" aria-controls="collapseGondolas">
          <font-awesome-icon icon="user-circle" />
          <span>Gondolas</span>
        </a>
        <div id="collapseGondolas" class="collapse" aria-labelledby="headingGondpñas" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link class="collapse-item" :to="{name: 'gondolaCrear'}">Crear</router-link>
          </div>
        </div>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOrdenes" aria-expanded="true" aria-controls="collapseOrdenes">
          <font-awesome-icon icon="user-circle" />
          <span>Pedidos</span>
        </a>
        <div id="collapseOrdenes" class="collapse" aria-labelledby="headingOrdenes" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link class="collapse-item" :to="{name: 'ordenesMostrar'}">Completados</router-link>
            <router-link class="collapse-item" :to="{name: 'ordenesPendientes'}">Pendientes</router-link>
            <router-link class="collapse-item" :to="{name: 'ordenesProcesando'}">Procesando</router-link>
          </div>
        </div>
      </li>



      <!-- ------------------------------------------------------------------------------------- -->

      <!-- Divider -->
      <!-- <hr class="sidebar-divider"> -->

      <!-- Heading -->
     <!--  <div class="sidebar-heading">
        Addons
      </div> -->

      <!-- Nav Item - Pages Collapse Menu -->
      <!-- <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true" aria-controls="collapsePages">
          <i class="fas fa-fw fa-folder"></i>
          <span>Pages</span>
        </a>
        <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Login Screens:</h6>
            <a class="collapse-item" href="login.html">Login</a>
            <a class="collapse-item" href="register.html">Register</a>
            <a class="collapse-item" href="forgot-password.html">Forgot Password</a>
            <div class="collapse-divider"></div>
            <h6 class="collapse-header">Other Pages:</h6>
            <a class="collapse-item" href="404.html">404 Page</a>
            <a class="collapse-item" href="blank.html">Blank Page</a>
          </div>
        </div>
      </li> -->

      <!-- Nav Item - Charts -->
     <!--  <li class="nav-item">
        <a class="nav-link" href="charts.html">
          <font-awesome-icon icon="chart-area" />
          <span>Graficos</span></a>
      </li> -->

      <!-- Nav Item - Tables -->
      <!-- <li class="nav-item">
        <a class="nav-link" href="tables.html">
          <i class="fas fa-fw fa-table"></i>
          <span>Tables</span></a>
      </li> -->

      <!-- Divider -->
      <hr class="sidebar-divider d-none d-md-block">

      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

    </ul>
    <!-- End of Sidebar -->
</template>
<script>
  export default {
      props: ['moneda'],
      data(){
        return {
          menu : 0
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
                
            },
            llamar(opcion){
              this.$emit('menu', opcion);
            }
      },
        mounted() {
           
        }
    }
</script>
