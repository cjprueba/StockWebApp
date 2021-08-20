<template>
	<div class="container-fluid mt-4">

		<!-- ------------------------------------------------------------------------ -->

		<div v-if="$can('reporte.venta')">
			
			<div class="mb-3">
				<label for="validationTooltip01">Seleccione Reporte Ventas</label>
				<select v-model="reporte" class="custom-select custom-select-sm" >
					<option value="0" selected>Seleccionar</option>
            <option v-if="$can('reporte.venta.periodosinmov') && $can('reporte.web')" value="7">Sin movimientos</option> 
            <option v-if="$can('reporte.venta.resumendiario') && $can('reporte.web')" value="4">Reporte Diario</option>
            <option v-if="$can('reporte.venta.topventas') && $can('reporte.web')" value="6">Top Ventas</option>
            <option v-if="$can('reporte.venta.delivery') && $can('reporte.web')"value="12">Ventas por Delivery</option> 
            <option v-if="$can('reporte.venta.gondola') && $can('reporte.web')" value="9">Ventas por Góndola</option>
            <option v-if="$can('reporte.venta.marcaycategoria') && $can('reporte.web')" value="3">Ventas por Marca y Categoría</option>
            <option v-if="$can('reporte.venta.vencidos') && $can('reporte.web')" value="15">Ventas por Productos Vencidos</option>
            <option v-if="$can('reporte.venta.proveedor') && $can('reporte.web')" value="8">Ventas por Proveedor</option>
            <option v-if="$can('reporte.venta.tarjeta') && $can('reporte.web')" value="13">Ventas por Tarjeta</option>
            <option v-if="$can('reporte.venta.transferencia') && $can('reporte.web')" value="11">Ventas por Transferencia</option>
            <option v-if="$can('reporte.venta.vale') && $can('reporte.web')" value="10">Ventas por Vales</option>
            <option v-if="$can('reporte.venta.vendedor') && $can('reporte.web')" value="5">Ventas por Vendedor</option>
            <option v-if="$can('reporte.venta.viejo') && $can('reporte.web')" value="14">.....Reporte de Ventas - Viejo Sistema.....</option>
				</select>			
			</div>

			<!-- VENTA POR MARCA -->

				<!-- FIN DE VENTA POR MARCA -->
			<transition name="slide-fade">	
				<venta-marca-categoria-rpt v-if="reporte === '3'" id="reporte2"></venta-marca-categoria-rpt>
			</transition>

      <!-- REPORTE DIARIO -->

      <transition name="slide-fade">  
        <venta-diaria-rpt v-if="reporte === '4'" id="reporte4"></venta-diaria-rpt>
      </transition>

      <!-- FIN REPORTE DIARIO -->

      <!-- REPORTE VENDEDOR -->

      <transition name="slide-fade">  
        <venta-vendedor-rpt v-if="reporte === '5'" id="reporte5"></venta-vendedor-rpt>
      </transition>

      <!-- FIN REPORTE VENDEDOR -->

      <!-- REPORTE TOP VENTAS -->

      <transition name="slide-fade">  
        <top-venta-rpt v-if="reporte === '6'" id="reporte6"></top-venta-rpt>
      </transition>

      <!-- FIN REPORTE TOP VENTAS -->

      <!-- REPORTE PERIODO DE VENTA -->

      <transition name="slide-fade">  
        <periodo-venta v-if="reporte === '7'" id="reporte7"></periodo-venta>
      </transition>

      <!-- FIN REPORTE PERIODO DE VENTA -->

      <!-- REPORTE VENTA POR PROVEEDOR -->

      <transition name="slide-fade">  
        <venta-proveedor-rpt v-if="reporte === '8'" id="reporte8"></venta-proveedor-rpt>
      </transition>

      <!-- FIN REPORTE VENTA POR PROVEEDOR -->

      <!-- REPORTE VENTA POR GONDOLA -->

      <transition name="slide-fade">  
        <venta-gondola-rpt v-if="reporte === '9'" id="reporte9"></venta-gondola-rpt>
      </transition>

      <!-- REPORTE VENTA POR VALES -->

      <transition name="slide-fade">  
        <venta-vales-rpt v-if="reporte === '10'" id="reporte10"></venta-vales-rpt>
      </transition>

      <!-- REPORTE VENTA POR TRANSFERENCIA -->

      <transition name="slide-fade">  
        <venta-transferencia-rpt v-if="reporte === '11'" id="reporte11"></venta-transferencia-rpt>
      </transition>

      <!-- REPORTE VENTA POR DELIVERY -->

      <transition name="slide-fade">  
        <venta-delivery-rpt v-if="reporte === '12'" id="reporte12"></venta-delivery-rpt>
      </transition>

      <!-- REPORTE VENTA POR TARJETA -->

      <transition name="slide-fade">  
        <venta-tarjeta-rpt v-if="reporte === '13'" id="reporte13"></venta-tarjeta-rpt>
      </transition>

      <transition name="slide-fade">  
        <venta-marca-categoria v-if="reporte === '14'" id="reporte14"></venta-marca-categoria>
      </transition>

      <!-- REPORTE VENTA POR PRODUCTOS VENCIDOS -->

      <transition name="slide-fade">  
        <venta-producto-vencido-rpt v-if="reporte === '15'" id="reporte15"></venta-producto-vencido-rpt>
      </transition>

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