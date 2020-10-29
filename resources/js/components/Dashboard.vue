<template>
	 <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column bg-light" >

      <!-- Main Content -->
      <div id="content">

        

        <!-- Begin Page Content -->
        <div class="container-fluid mt-4">

          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4" >
            <h1 class="h3 mb-0 text-gray-800">Inicio</h1>
            
          </div>

          <!-- Content Row -->
          <div>
            
            <!-- Earnings (Monthly) Card Example -->
            	<!-- <cajas></cajas> -->
          </div>

          <!-- Content Row -->
          <div>
            <!-- Area Chart -->
              <!-- <barChart></barChart> -->
          </div>

          <!-- Content Row -->
          <div class="row">


            <div class="col-md-12" v-if="$can('inicio.lotes.vencidos')">
                <caja-lote @codigo_producto="detalle"></caja-lote>
            </div>

            <div class="col-md-12 mt-3" v-if="$can('inicio.stock.minimo')">
                <caja-minimo @codigo_producto="detalle"></caja-minimo>
            </div>

            <div class="col-lg-5" v-if="1 === 0">
                <caja-periodo @codigo_producto="detalle"></caja-periodo>
            </div>

            <div class="col-md-12 mt-3">
                <!-- <caja-lote-cero @codigo_producto="detalle"></caja-lote-cero> -->
            </div>
            
            <div>
              
            </div>

          </div>

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <!-- ------------------------------------------------------------------------ -->

      <!-- Footer -->

      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; Retail 2019</span>
          </div>
        </div>
      </footer>

      <!-- End of Footer -->

      <!-- ------------------------------------------------------------------------ -->

      <!-- MODAL DETALLE PRODUCTO -->

      <producto-detalle ref="detalle_producto" :codigo="codigo_producto"></producto-detalle>

      <!-- ------------------------------------------------------------------------ -->

    </div>
</template>

<script>
    export default {
     
      data(){
        return {
            codigo_producto: ''
        }
      }, 
      methods: {
            detalle(valor) {

              // ------------------------------------------------------------------------

              // MOSTRAR DETALLE PRODUCTO 

              this.codigo_producto = valor;
              this.$refs.detalle_producto.mostrar();

              // ------------------------------------------------------------------------
            }
      },  
        mounted() {

        }
    }
</script>