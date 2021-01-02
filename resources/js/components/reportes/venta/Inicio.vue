<template>
	<div class="container-fluid mt-4">

		<!-- ------------------------------------------------------------------------ -->

	<!-- 	<div v-if="$can('reporte.venta')"> -->
			
			<div class="mb-3">
				<label for="validationTooltip01">Seleccione Reporte Ventas</label>
				<select v-model="reporte" class="custom-select custom-select-sm" >
					<option value="0" selected>Seleccionar</option>
					<option value="3">Ventas por Marca y Categoria</option>
          <option value="4">Reporte Diario</option>
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

<!-- 		</div>
   <div v-else>
      <cuatrocientos-cuatro></cuatrocientos-cuatro>
    </div> -->
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