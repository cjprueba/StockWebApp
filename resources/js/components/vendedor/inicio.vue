<template>
	<div class="container-fluid mt-4">

    <div v-if="$can('reporte.vendedor')">
    		<div class="mb-3">
    			<label for="validationTooltip01">Seleccione Reporte Ventas</label>
    			<select class="custom-select custom-select-sm" v-on:change="cambioReporte">
    				<option selected>Seleccionar</option>
    				<option value="1">Ventas por vendedores </option>
    				<option value="2">Devolucion por vendedores </option>

    			</select>			
    		</div>

    		<!-- VENTA POR MARCA -->
    	<transition name="fade">	
    		<devolucion-vendedor v-if="reporte === '2'" id="reporte2"></devolucion-vendedor>
    	</transition>
    		<!-- FIN DE VENTA POR MARCA -->
    	<transition name="slide-fade">	
    		<vendedor-general v-if="reporte === '1'" id="reporte2"></vendedor-general>
    	</transition>
		</div>

    <!-- ------------------------------------------------------------------------ -->

    <div v-else>
      <cuatrocientos-cuatro></cuatrocientos-cuatro>
    </div>

    <!-- ------------------------------------------------------------------------ -->

	</div>
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