<template>
	<div class="container-fluid mt-4">

		<!-- ------------------------------------------------------------------------ -->

		<div v-if="$can('producto.mostrar')">
			
			<div class="mb-3">
				<label for="validationTooltip01">Seleccione Reporte Por Sección</label>
				<select v-model="reporte" class="custom-select custom-select-sm" >
					<option value="0" selected>Seleccionar</option>
					<option v-if="$can('reporte.encargadadeseccion.ventageneral') && $can('reporte.web')" value="1">Venta General</option>
          <option v-if="$can('reporte.venta.topventas') && $can('reporte.web')" value="2">Top Venta</option>
          <option v-if="$can('reporte.venta.topventas') && $can('reporte.web')" value="4">Ventas por Góndola</option>
          <option v-if="$can('reporte.venta.topventas') && $can('reporte.web')" value="5">Ventas por Proveedor</option>
          <option v-if="$can('reporte.venta.topventas') && $can('reporte.web')" value="3">Compra - Venta - Transferencia de Proveedor por Entrada</option>
				</select>			
			</div>

      <!-- REPORTE POR SECCION -->

      <transition name="slide-fade">  
        <venta-seccion-rpt v-if="reporte === '1'" id="reporte1"></venta-seccion-rpt>
      </transition>

      <!-- FIN REPORTE POR SECCION -->

      <!-- REPORTE TOP VENTA POR SECCION -->

      <transition name="slide-fade">  
        <top-venta-rpt v-if="reporte === '2'" id="reporte2"></top-venta-rpt>
      </transition>

      <!-- FIN REPORTE TOP VENTA POR SECCION -->

      <!-- REPORTE ENTRADA DE PROVEEDOR POR SECCION -->

      <transition name="slide-fade">  
        <seccion-proveedor-rpt v-if="reporte === '3'" id="reporte3"></seccion-proveedor-rpt>
      </transition>

      <!-- FIN REPORTE ENTRADA DE PROVEEDOR POR SECCION -->

      <!-- REPORTE VENTA DE GONDOLAS POR SECCION -->

      <transition name="slide-fade">  
        <venta-seccion-gondola-rpt v-if="reporte === '4'" id="reporte4"></venta-seccion-gondola-rpt>
      </transition>

      <!-- FIN REPORTE VENTA DE GONDOLAS POR SECCION -->

      <!-- REPORTE VENTAS DE PROVEEDORES POR SECCION -->

      <transition name="slide-fade">  
        <venta-seccion-proveedor-rpt v-if="reporte === '5'" id="reporte5"></venta-seccion-proveedor-rpt>
      </transition>

      <!-- FIN REPORTE VENTAS DE PROVEEDORES POR SECCION -->
		</div>

    <!-- ------------------------------------------------------------------------ -->

    <div v-else>
      <cuatrocientos-cuatro></cuatrocientos-cuatro>
    </div>

		<!-- ------------------------------------------------------------------------ -->
    
	</div>	

	<!-- ------------------------------------------------------------------------ -->


</template>

<script >
	export default {
        data(){
            return {
               reporte: '0'
            }
        }, 
        methods: {
            cambioReporte(e){
                this.reporte = e.target.value;
            }
        },
        mounted() {
        	
        }
    }    
</script>
<style>
	.bounce-enter-active {
  animation: bounce-in .5s;
}
.bounce-leave-active {
  animation: bounce-in .5s reverse;
}
@keyframes bounce-in {
  0% {
    transform: scale(0);
  }
  50% {
    transform: scale(1.5);
  }
  100% {
    transform: scale(1);
  }
}

.fade-enter-active, .fade-leave-active {
  transition: opacity .5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}

.slide-fade-enter-active {
  transition: all .3s ease;
}
.slide-fade-leave-active {
  transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
}
.slide-fade-enter, .slide-fade-leave-to
/* .slide-fade-leave-active below version 2.1.8 */ {
  transform: translateX(10px);
  opacity: 0;
}
</style>