<template>
	<div class="container-fluid mt-4">

		<!-- ------------------------------------------------------------------------ -->

		<div v-if="$can('producto.mostrar')">
			
			<div class="mb-3">
				<label for="validationTooltip01">Seleccione Reporte Por Sección</label>
				<select v-model="reporte" class="custom-select custom-select-sm" >
					<option value="0" selected>Seleccionar</option>
					<option value="1">Venta General</option>
          <option value="2">Top Venta</option>
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