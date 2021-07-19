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
      <!-- <li class="nav-item">
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
      </li> -->

        <li v-if="$can('reporte.web')" class="nav-item" >
          <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReporteWeb" aria-expanded="true" aria-controls="collapseReporteWeb">
            <font-awesome-icon icon="chart-area" />
            <span >Reporte web</span>
          </a>
          <div id="collapseReporteWeb" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
              <h6 class="collapse-header">Lista de Reportes:</h6>

              <router-link v-if="$can('reporte.encargadadeseccion')" class="collapse-item" :to="{name: 'rptSeccion'}">Encargada de Sección</router-link>
        <!--        <router-link class="collapse-item" :to="{name: 'rptVencimiento'}">Vencimiento de Producto</router-link>
               <router-link class="collapse-item" :to="{name: 'rptStockWeb'}">Stock</router-link> -->
              <router-link v-if="$can('reporte.productos')" class="collapse-item" :to="{name: 'rptProductoWeb'}">Productos</router-link>
              <router-link v-if="$can('reporte.transferencia')" class="collapse-item" :to="{name: 'rptTransferenciaWeb'}">Transferencias</router-link>
              <router-link v-if="$can('reporte.venta')" class="collapse-item" :to="{name: 'rptVentaWeb'}">Ventas</router-link>
              <!-- <router-link class="collapse-item" :to="{name: 'rptVenta'}">Ventas</router-link> -->
              <router-link v-if="$can('reporte.notadecredito')" class="collapse-item" :to="{name: 'notaDeCraeditoReporte'}">Nota de Credito</router-link>
            </div>
          </div>
        </li>


<!-- ------------REPORTE DE STOCK - NO FUNCIONA------------->
   <!-- <router-link class="collapse-item" :to="{name: 'rptStock'}">Stock</router-link> -->
     <!-- <router-link class="collapse-item" :to="{name: 'rptStockCero'}">Stock Cerado</router-link> -->
      <!-- Nav Item - Utilities Collapse Menu -->
      





<!----------------------------------------------------REPORTE------------------------------------------------------------------------------>
      <!-- <li class="nav-item" >
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
          <font-awesome-icon icon="sticky-note" />
          <span >Reporte</span>
        </a>
        <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Lista de Reportes:</h6> -->
          <!--    <router-link class="collapse-item" :to="{name: 'rptTransferencia'}">Transferencias</router-link> -->
             <!-- <router-link class="collapse-item" :to="{name: 'rptVenta'}">Ventas</router-link>
             <router-link class="collapse-item" :to="{name: 'rptTransferencia'}">Transferencias</router-link>
             <router-link class="collapse-item" :to="{name: 'rptDescuento'}">Descuentos</router-link>
             <router-link class="collapse-item" :to="{name: 'rptVendedor'}">Vendedor</router-link> -->
             <!-- <router-link class="collapse-item" :to="{name: 'rptCliente'}">Clientes</router-link> -->
            <!--  <router-link class="collapse-item" :to="{name: 'rptArticulo'}">Articulos</router-link>
             <router-link class="collapse-item" :to="{name: 'rptProveedor'}">Proveedores</router-link>
             <router-link class="collapse-item" :to="{name: 'rptInventario'}">Inventarios</router-link>
             <router-link class="collapse-item" :to="{name: 'rptCompra'}">Compras</router-link> -->
          
           
             <!-- <router-link class="collapse-item" :to="{name: 'rptVales'}">Vales</router-link>

             <router-link class="collapse-item" :to="{name: 'rptTransferencia2'}">Venta Transferencia</router-link>

             <router-link class="collapse-item" :to="{name: 'rptDelivery'}">Delivery</router-link>

             <router-link class="collapse-item" :to="{name: 'rptTarjeta'}">Venta Tarjeta</router-link>
          </div>
        </div>
      </li> -->






      <li v-if="$can('transferencia')" class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTransferencia" aria-expanded="true" aria-controls="collapseTransferencia">
          <font-awesome-icon icon="truck"/>
          <span>Transferencias</span>
        </a>
        <div id="collapseTransferencia" class="collapse" aria-labelledby="headingTransferencia" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link v-if="$can('transferencia.mostrar') " class="collapse-item" :to="{name: 'transferenciaMostrar'}">Listado de Transferencias</router-link>
            <router-link v-if="$can('transferencia.crear')" class="collapse-item" :to="{name: 'transferencia'}">Realizar Transferencias</router-link>
            <!-- <a class="collapse-item" href="#" v-on:click="llamar(21)">Realizar Transferencias</a> -->
            <router-link v-if="$can('transferencia.importar')" class="collapse-item" :to="{name: 'transferenciaImportar'}">Importar Transferencias</router-link>
            <router-link v-if="$can('transferencia.mostrardevo')" class="collapse-item" :to="{name: 'transferenciaMostrarDevolucion'}">Mostrar Devolucion</router-link>
            <router-link v-if="$can('transferencia.importardevo')" class="collapse-item" :to="{name: 'transferenciaImportarDevolucion'}">Importar Devolucion</router-link>
            <!-- <router-link class="collapse-item" :to="{name: 'transferenciaCobrar'}">Cobrar Devolucion</router-link> -->
          </div>
        </div>
      </li>

      <!-- ------------------------------------------------------------------------------------- -->

      <!-- COMPRA -->

      <li v-if="$can('compra')" class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCompra" aria-expanded="true" aria-controls="collapseCompra">
          <font-awesome-icon icon="cart-plus"/>
          <span>Compras</span>
        </a>
        <div id="collapseCompra" class="collapse" aria-labelledby="headingCompra" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link v-if="$can('compra.mostrar')" class="collapse-item" :to="{name: 'compraMostrar'}">Mostrar Compras</router-link>
            <router-link v-if="$can('compra.crear')" class="collapse-item" :to="{name: 'compra'}">Realizar Compras</router-link>
            <router-link v-if="$can('compra.crearcontainer')" class="collapse-item" :to="{name: 'compraContainer'}">Crear Container</router-link>
          </div>
        </div>
      </li>

      <!-- ------------------------------------------------------------------------------------- -->

      <!-- VENTA -->

      <li v-if="$can('venta')" class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseVenta" aria-expanded="true" aria-controls="collapseVenta">
          <font-awesome-icon icon="shopping-bag"/>
          <span>Ventas</span>
        </a>
        <div id="collapseVenta" class="collapse" aria-labelledby="headingVenta" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link v-if="$can('venta.misventas')" class="collapse-item" :to="{name: 'ventaCaja'}">Mis Ventas</router-link>
            <router-link v-if="$can('venta.mostrar')" class="collapse-item" :to="{name: 'ventaMostrar'}">Mostrar Ventas</router-link>
            <router-link v-if="$can('venta.realizarventas')" class="collapse-item" :to="{name: 'ventaRealizar'}">Realizar Venta</router-link>
            <!-- <router-link class="collapse-item" :to="{name: 'ventaMayorista'}">Realizar Mayorista</router-link> -->
            <!-- <router-link class="collapse-item" :to="{name: 'ventaCuenta'}">Cobro de Cuentas</router-link> -->
            <router-link v-if="$can('venta.anular')" class="collapse-item" :to="{name: 'ventaAnular'}">Anular Venta</router-link>
          </div>
        </div>
      </li>

      <!-- ------------------------------------------------------------------------------------- -->

      <!-- MOVIMIENTOS -->

      <li v-if="$can('movimientos')" class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMovimientos" aria-expanded="true" aria-controls="collapseMovimientos">
          <font-awesome-icon icon="money-bill-alt" />
          <span>Movimientos</span>
        </a>
        <div id="collapseMovimientos" class="collapse" aria-labelledby="headingMovimientos" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded" id="accordionMov">
            <h6 class="collapse-header">Opciones:</h6>

            <a v-if="$can('movimientos.productosal')" class="nav-item accordion collapse-item font-weight-bold text-muted" href="#" data-toggle="collapse" data-target="#collapseProductosMov" aria-expanded="true" aria-controls="collapseProductosMov" >Salida de Productos</a>
            <div id="collapseProductosMov" class="collapse bg-#ced4da"   aria-labelledby="headingProductosMov" data-parent="#accordionMov" >
              <hr>
              <router-link v-if="$can('movimientos.salidadeproducto')" class="collapse-item" :to="{name: 'salidaProducto'}">Realizar Salida</router-link>
              <router-link v-if="$can('movimientos.mostrarsalidadeproducto')" class="collapse-item" :to="{name: 'salidaMostrar'}">Mostrar Salidas</router-link>
              <hr>
            </div>



            <a v-if="$can('movimientos.productopres')" class="nav-item accordion collapse-item font-weight-bold text-muted" href="#" data-toggle="collapse" data-target="#collapseProductoPres" aria-expanded="true" aria-controls="collapseProductoPres" >Préstamo de Productos</a>
            <div id="collapseProductoPres" class="collapse bg-#ced4da"   aria-labelledby="headingProductoPres" data-parent="#accordionMov">
              <hr>
              <router-link v-if="$can('movimientos.prestamodeproductos')" class="collapse-item" :to="{name: 'prestarProducto'}">Realizar Préstamo</router-link>
              <router-link v-if="$can('movimientos.mostrarprestamodeproductos')" class="collapse-item" :to="{name: 'prestamoMostrar'}">Mostrar Préstamos</router-link>
              <hr>
            </div>



            <a v-if="$can('movimientos.nota')" class="nav-item accordion collapse-item font-weight-bold text-muted" href="#" data-toggle="collapse" data-target="#collapseNotaDeCredito" aria-expanded="true" aria-controls="collapseNotaDeCredito" >Nota de Credito</a>
            <div id="collapseNotaDeCredito" class="collapse bg-#ced4da"   aria-labelledby="headingNotaDeCredito" data-parent="#accordionMov">
              <hr>
              <router-link v-if="$can('movimientos.notadecredito')" class="collapse-item" :to="{name: 'notaCredito'}">Realizar Nota</router-link>
              <router-link v-if="$can('movimientos.mostrarnotadecredito')" class="collapse-item" :to="{name: 'notaCreditoMostrar'}">Mostrar Notas</router-link>
              <hr>
            </div>
          </div>
        </div>
      </li>






      <li v-if="$can('producto')" class="nav-item" >
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProdutos" aria-expanded="true" aria-controls="collapseProdutos">
          <font-awesome-icon icon="tags"/>
          <span>Productos</span>
        </a>
        <div id="collapseProdutos" class="collapse" aria-labelledby="headingProdutos" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded" >
            <h6 class="collapse-header">Opciones:</h6>
            <router-link v-if="$can('producto.mostrar')" class="collapse-item" :to="{name: 'productoMostrar'}">Mostrar Productos</router-link>
            <router-link v-if="$can('producto')" class="collapse-item" :to="{name: 'productoRegistrar'}">Registrar Productos</router-link>
            <router-link v-if="$can('producto.descuentolote')" class="collapse-item" :to="{name: 'desccuentoLote'}">Descuento por lote</router-link>
            <router-link v-if="$can('producto.etiquetas')" class="collapse-item" :to="{name: 'productoImprimir'}">Etiquetas</router-link>

            <!-- <router-link v-if="$can('producto')" class="collapse-item" :to="{name: 'productoMostrarUno'}">Producto</router-link> -->
             <!-- ATRIBUTOS -->
            <a v-if="$can('atributos')" class="nav-item accordion collapse-item font-weight-bold text-muted" href="#" data-toggle="collapse" data-target="#collapseAtributos" aria-expanded="true" aria-controls="collapseAtributos" ><font-awesome-icon icon="sort-amount-down" /> Atributos</a>
            

            <div id="collapseAtributos" class="collapse bg-#ced4da" aria-labelledby="headingAtributos" >
              


                <hr>
                <router-link v-if="$can('marca.crear')" class="collapse-item" :to="{name: 'marcaCrear'}">Marca</router-link>
                <router-link v-if="$can('categoria.crear')"  class="collapse-item" :to="{name: 'categoriaCrear'}">Categoria</router-link>
                <router-link v-if="$can('subcategoria.crear')" class="collapse-item" :to="{name: 'subcategoriaCrear'}">SubCategoria</router-link>
                <router-link v-if="$can('color.crear')" class="collapse-item" :to="{name: 'colorCrear'}">Color</router-link>
                <router-link v-if="$can('talle.crear')" class="collapse-item" :to="{name: 'talleCrear'}">Talle</router-link>
                <router-link v-if="$can('tela.crear')" class="collapse-item" :to="{name: 'telaCrear'}">Tela</router-link>
                <router-link v-if="$can('atributo.crearnombre')" class="collapse-item" :to="{name: 'nombreCrear'}">Nombre</router-link>
                <hr>


            </div>
          </div>
        </div>
      </li>


      <!-- ------------------------------------------------------------------------------------- -->

      <!-- SUCURSALES -->

      <li v-if="$can('sucursal')" class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSucursales" aria-expanded="true" aria-controls="collapseSucursales">
          <font-awesome-icon icon="building" />
          <span>Sucursales</span>
        </a>
        <div id="collapseSucursales" class="collapse" aria-labelledby="headingSucursales" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link v-if="$can('sucursal.crearsucursal')" class="collapse-item" :to="{name: 'crearSucursal'}">Crear Sucursal</router-link>
            
          </div>
        </div>
      </li>


      <!-- ------------------------------------------------------------------------------------- -->

      <!-- PROVEEDOR -->

      <li  v-if="$can('proveedor')" class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProveedor" aria-expanded="true" aria-controls="collapseProveedor">
          <font-awesome-icon icon="ambulance"/>
          <span>Proveedores</span>
        </a>
        <div id="collapseProveedor" class="collapse" aria-labelledby="headingProveedor" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link v-if="$can('proveedor.crear')" class="collapse-item" :to="{name: 'proveedorCrear'}">Crear Proveedor</router-link>
            <router-link v-if="$can('proveedor.mostrar')" class="collapse-item" :to="{name: 'proveedorMostrar'}">Mostrar Proveedores</router-link>
            <router-link v-if="$can('proveedor.pago')" class="collapse-item" :to="{name: 'proveedorPago'}">Pago de Cuentas</router-link>
            <router-link v-if="$can('proveedor.devolucion')" class="collapse-item" :to="{name: 'proveedorDevolucion'}">Realizar Devolución</router-link>
            <router-link v-if="$can('proveedor.mostrar.devolucion')" class="collapse-item" :to="{name: 'proveedorDevolucionMostrar'}">Mostrar Devoluciones</router-link>
          </div>
        </div>
      </li>

      <!-- ------------------------------------------------------------------------------------- -->
      
      <!-- CLIENTEs -->

      <li v-if="$can('clientes')" class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseClientes" aria-expanded="true" aria-controls="collapseClientes">
          <font-awesome-icon icon="user-circle" />
          <span>Clientes</span>
        </a>
        <div id="collapseClientes" class="collapse" aria-labelledby="headingClientes" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link v-if="$can('clientes.crear')" class="collapse-item" :to="{name: 'crearCliente'}">Registrar Cliente</router-link>
            <router-link v-if="$can('clientes.credito')" class="collapse-item" :to="{name: 'creditoCliente'}">Crédito Cliente</router-link>
          </div>
        </div>
      </li>
      
      <!-- ------------------------------------------------------------------------------------- -->

      


       <!-- ATRIBUTOS MENI-->
      <!-- <li v-if="$can('atributos')"  class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAtributos" aria-expanded="true" aria-controls="collapseAtributos">
          <font-awesome-icon icon="sort-amount-down" />
          <span>Atributos</span>
        </a>
        <div id="collapseAtributos" class="collapse" aria-labelledby="headingAtributos" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link v-if="$can('marca.crear')" class="collapse-item" :to="{name: 'marcaCrear'}">Marca</router-link>
            <router-link v-if="$can('categoria.crear')"  class="collapse-item" :to="{name: 'categoriaCrear'}">Categoria</router-link>
            <router-link v-if="$can('subcategoria.crear')" class="collapse-item" :to="{name: 'subcategoriaCrear'}">SubCategoria</router-link>
            <router-link v-if="$can('color.crear')" class="collapse-item" :to="{name: 'colorCrear'}">Color</router-link>
            <router-link v-if="$can('talle.crear')" class="collapse-item" :to="{name: 'talleCrear'}">Talle</router-link>
            <router-link v-if="$can('tela.crear')" class="collapse-item" :to="{name: 'telaCrear'}">Tela</router-link>
            <router-link v-if="$can('atributo.crearnombre')" class="collapse-item" :to="{name: 'nombreCrear'}">Nombre</router-link>
          </div>
        </div>
      </li> -->

      


      <!-- ------------------------------------------------------------------------------------- -->

      <!-- INVENTARIO MENU -->

      <li v-if="$can('inventario')" class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseInventario" aria-expanded="true" aria-controls="collapseInventario">
          <font-awesome-icon icon="list-alt" />
          <span>Inventario</span>
        </a>
        <div id="collapseInventario" class="collapse" aria-labelledby="headingInventario" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link v-if="$can('inventario.mostrar')" class="collapse-item" :to="{name: 'inventarioMostrar'}">Mostrar Inventario</router-link>
            <router-link v-if="$can('inventario.crear')" class="collapse-item" :to="{name: 'inventarioRealizar'}">Realizar Inventario</router-link>
          </div>
        </div>
      </li>

        <!-- ------------------------------------------------------------------------------------- -->

      <!-- COTIZACION MENU -->

      <li v-if="$can('cotizacion')" class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCotizacion" aria-expanded="true" aria-controls="collapseCotizacion">
          <font-awesome-icon icon="coins" />
          <span>Cotización</span>
        </a>
        <div id="collapseCotizacion" class="collapse" aria-labelledby="headingCotizacion" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link v-if="$can('cotizacion.crear')" class="collapse-item" :to="{name: 'cotizacionCrearNuevo'}">Crear Cotización</router-link>
            <router-link v-if="$can('cotizacion.mostrar')" class="collapse-item" :to="{name: 'cotizacionMostrarNuevo'}">Mostrar Cotización</router-link>
          </div>
        </div>
      </li>

        <!-- ------------------------------------------------------------------------------------- -->

      <!-- TRANSPORTE MENU -->

      <li v-if="$can('transporte')"  class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTransporte" aria-expanded="true" aria-controls="collapseTransporte">
          <font-awesome-icon icon="truck-moving" />
          <span>Transporte</span>
        </a>
        <div id="collapseTransporte" class="collapse" aria-labelledby="headingTransporte" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link v-if="$can('transporte.crear')" class="collapse-item" :to="{name: 'transporteCrear'}">Crear Transporte</router-link>
          </div>
        </div>
      </li>
        <!-- ------------------------------------------------------------------------------------- -->



       



      <!-- USER MENU -->

      <!-- <li v-if="$can('user')" class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRol" aria-expanded="true" aria-controls="collapseRol">
          <font-awesome-icon icon="user-circle" />
          <span>Usuarios</span>
        </a>
        <div id="collapseRol" class="collapse" aria-labelledby="headingRol" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link v-if="$can('user.crear')" class="collapse-item" :to="{name: 'userCrear'}">Usuarios</router-link>
            <router-link v-if="$can('user.rol')" class="collapse-item" :to="{name: 'rolCrear'}">Roles</router-link>
            <router-link v-if="$can('user.permission')" class="collapse-item" :to="{name: 'permisosCrear'}">Permisos</router-link>
            <router-link v-if="$can('user.rol.user')" class="collapse-item" :to="{name: 'rolAsignar'}">Roles a Usuarios</router-link>
            <router-link v-if="$can('user.permission.user')" class="collapse-item" :to="{name: 'permisoAsignar'}">Permisos a Usuarios</router-link>
            <router-link v-if="$can('user.cambiarsucursal')" class="collapse-item" v-b-modal.meu-modal :to="{name: 'cambiarSucursal'}">Cambiar de Sucursal</router-link>
          </div>
        </div>
      </li> -->
      

      <!-- GONDOLAS MENU -->
      <!-- <li v-if="$can('gondola')" class="nav-item">

        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseGondolas" aria-expanded="true" aria-controls="collapseGondolas">
          <font-awesome-icon icon="user-circle" />
          <span>Gondolas</span>
        </a>
        <div id="collapseGondolas" class="collapse" aria-labelledby="headingGondpñas" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link v-if="$can('gondola.crear')" class="collapse-item" :to="{name: 'gondolaCrear'}">Crear</router-link>
          </div>
        </div>
      </li> -->
      <!-- CUPONES MENU -->

      <li v-if="$can('cupones')" class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCupon" aria-expanded="true" aria-controls="collapseCupon">
          <font-awesome-icon icon="credit-card" />
          <span>Cupones</span>
        </a>
        <div id="collapseCupon" class="collapse" aria-labelledby="headingGondpñas" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link v-if="$can('cupones.mostrar')" class="collapse-item" :to="{name: 'cuponMostrar'}">Mostrar Cupones</router-link>
            <router-link v-if="$can('cupones.crear')" class="collapse-item" :to="{name: 'cuponCrear'}">Crear Cupones</router-link>
          </div>
        </div>
      </li>

      <li v-if="$can('ordenes')" class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOrdenes" aria-expanded="true" aria-controls="collapseOrdenes">
          <font-awesome-icon icon="globe" />
          <span>Ordenes</span>
        </a>
        <div id="collapseOrdenes" class="collapse" aria-labelledby="headingOrdenes" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link v-if="$can('ordenes.completados')" class="collapse-item" :to="{name: 'ordenesMostrar'}">Completados</router-link>
            <router-link v-if="$can('ordenes.pendientes')" class="collapse-item" :to="{name: 'ordenesPendientes'}">Pendientes</router-link>
            <router-link v-if="$can('ordenes.procesando')" class="collapse-item" :to="{name: 'ordenesProcesando'}">Procesando</router-link>
          </div>
        </div>
      </li>

      <li v-if="$can('pedidos')" class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePedidos" aria-expanded="true" aria-controls="collapsePedidos">
          <font-awesome-icon icon="walking" />
          <span>Pedidos</span>
        </a>
        <div id="collapsePedidos" class="collapse" aria-labelledby="headingOrdenes" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link v-if="$can('pedido.mostrar')" class="collapse-item" :to="{name: 'pdMostrar'}">Mostrar</router-link>
            <router-link v-if="$can('pedido.mostrarproductos')" class="collapse-item" :to="{name: 'productoMostrarNew'}">Productos</router-link>
            <router-link v-if="$can('pedido.checkout')" class="collapse-item" :to="{name: 'pdCheckout'}">Checkout</router-link>
          </div>
        </div>
      </li>

      <!-- ------------------------------------------------------------------------------------- -->

      <!-- EMPLEADOS -->

      <!-- <li v-if="$can('empleados')" class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseEmpleados" aria-expanded="true" aria-controls="collapseEmpleados">
          <font-awesome-icon icon="user-circle" />
          <span>Empleados</span>
        </a>
        <div id="collapseEmpleados" class="collapse" aria-labelledby="headingEmpleados" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link v-if="$can('empleados.crear')" class="collapse-item" :to="{name: 'crearEmpleado'}">Crear Empleado</router-link>
            
          </div>
        </div>
      </li> -->


      <!-- ------------------------------------------------------------------------------------- -->

      <!-- SECCIONES -->

      <!-- <li v-if="$can('secccion')" class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSecciones" aria-expanded="true" aria-controls="collapseSecciones">
          <font-awesome-icon icon="user-circle" />
          <span>Secciones</span>
        </a>
        <div id="collapseSecciones" class="collapse" aria-labelledby="headingSecciones" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link v-if="$can('secccion.crear')" class="collapse-item" :to="{name: 'crearSeccion'}">Crear Seccion</router-link>
          </div>
        </div>
      </li> -->



      <!-- ------------------------------------------------------------------------------------- -->
      <!-- CAJA -->
      
      <!-- <li v-if="$can('caja')" class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCaja" aria-expanded="true" aria-controls="collapseCaja">
        <font-awesome-icon icon="user-circle" />
        <span>Caja</span>
      </a>
      <div id="collapseCaja" class="collapse" aria-labelledby="headingCaja" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">Opciones:</h6>
          <router-link v-if="$can('caja.asignar')" class="collapse-item" :to="{name: 'asignarCaja'}">Asignar Caja</router-link>
        </div>
      </div>
    </li> -->




    <!----------------------------------------------------------------------------------------->
      <!-- CONFIGURACIÓN -->
      <li v-if="$can('configuracion')" class="nav-item accordion">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseConfiguración" aria-expanded="true" aria-controls="collapseConfiguración">
          <font-awesome-icon icon="cog" />
          <span>Configuración</span>
        </a>
        <div id="collapseConfiguración" class="collapse" aria-labelledby="headingConfiguración" data-parent="#accordionSidebar" >
          <div class="bg-white collapse-inner rounded " id="accordionConf">



            <!-- USER MENU -->
            <a v-if="$can('user')" class="nav-item collapse-item font-weight-bold text-muted" href="#" data-toggle="collapse" data-target="#collapseOpcion1" aria-expanded="true" aria-controls="collapseOpcion1"><font-awesome-icon icon="user-cog" /> Usuario</a>
            <div id="collapseOpcion1" class="collapse bg-#ced4da" aria-labelledby="headingOpcion1" data-parent="#accordionConf">
              <hr>
              <router-link v-if="$can('user.crear')" class="collapse-item" :to="{name: 'userCrear'}">Usuarios</router-link>
              <router-link v-if="$can('user.rol')" class="collapse-item" :to="{name: 'rolCrear'}">Roles</router-link>
              <router-link v-if="$can('user.permission')" class="collapse-item" :to="{name: 'permisosCrear'}">Permisos</router-link>
              <router-link v-if="$can('user.rol.user')" class="collapse-item" :to="{name: 'rolAsignar'}">Roles a Usuarios</router-link>
              <router-link v-if="$can('user.permission.user')" class="collapse-item" :to="{name: 'permisoAsignar'}">Permisos a Usuarios</router-link>
              <router-link v-if="$can('user.cambiarsucursal')" class="collapse-item" v-b-modal.meu-modal :to="{name: 'cambiarSucursal'}">Cambiar de Sucursal</router-link>
              <hr>  
            </div>



            <!-- EMPLEADO -->
            <a v-if="$can('empleados')" class="collapse-item font-weight-bold text-muted" href="#" data-toggle="collapse" data-target="#collapseOpcion3" aria-expanded="true" aria-controls="collapseOpcion3"><font-awesome-icon icon="user-tie" /> Empleado</a>
            <div id="collapseOpcion3" class="collapse bg-white" aria-labelledby="headingOpcion3" data-parent="#accordionConf">
              <hr>
              <router-link v-if="$can('empleados.crear')" class="collapse-item" :to="{name: 'crearEmpleado'}">Crear Empleado</router-link>
              <hr>  
            </div>



            <!-- SECCION --> 
            <a v-if="$can('secccion')" class="collapse-item font-weight-bold text-muted" href="#" data-toggle="collapse" data-target="#collapseOpcion4" aria-expanded="true" aria-controls="collapseOpcion4"><font-awesome-icon icon="sign" /> Seccion</a>
            <div id="collapseOpcion4" class="collapse bg-#ced4da  rounded" aria-labelledby="headingOpcion4" data-parent="#accordionConf">
              <hr>
              <router-link v-if="$can('secccion.crear')" class="collapse-item" :to="{name: 'crearSeccion'}">Crear Seccion</router-link>
              <hr>  
            </div>
            


            <!-- GONDOLA -->
            <a v-if="$can('gondola')" class="nav-item collapse-item font-weight-bold text-muted" href="#" data-toggle="collapse" data-target="#collapseOpcion2" aria-expanded="true" aria-controls="collapseOpcion2"><font-awesome-icon icon="th-large" /> Gondola</a>
            <div id="collapseOpcion2" class="collapse bg-white  rounded" aria-labelledby="headingOpcion2" data-parent="#accordionConf">
              <hr>
              <router-link v-if="$can('gondola.crear')" class="collapse-item" :to="{name: 'gondolaCrear'}">Crear Gondola</router-link>
              <router-link v-if="$can('gondola.crearpiso')" class="collapse-item" :to="{name: 'crearPiso'}">Crear Piso</router-link>
              <router-link v-if="$can('gondola.crearsector')" class="collapse-item" :to="{name: 'crearSector'}">Crear Sector</router-link>
              <hr>  
            </div>



            <!-- CAJA -->
            <a v-if="$can('caja')" class="collapse-item font-weight-bold text-muted" href="#" data-toggle="collapse" data-target="#collapseUltimaOpcion" aria-expanded="false" aria-controls="collapseUltimaOpcion"><font-awesome-icon icon="cash-register" /> Caja</a>
            <div id="collapseUltimaOpcion" class="collapse bg-#ced4da" aria-labelledby="headingUltimaOpcion" data-parent="#accordionConf" >
              <hr>
              <router-link v-if="$can('caja.asignar')" class="collapse-item text-center" :to="{name: 'asignarCaja'}">Asignar Caja</router-link>
              <hr>  
            </div>
          
          </div>
        </div>
      </li>

      
      

      <!-- <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMaquinas" aria-expanded="true" aria-controls="collapseMaquinas">
          <font-awesome-icon icon="user-circle" />
          <span>Máquinas</span>
        </a>
        <div id="collapseMaquinas" class="collapse" aria-labelledby="headingMaquinas" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opciones:</h6>
            <router-link class="collapse-item" :to="{name: 'crearSectorSucursal'}">Crear Sucursal/Sector</router-link>
            <router-link class="collapse-item" :to="{name: 'regristrarMaquina'}">Registrar Máquina</router-link>
          </div>
        </div>
      </li> -->

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

      <caja-aviso></caja-aviso>
      
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
