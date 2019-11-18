<template>
	<div class="container-fluid mt-4">
		<div class="mb-3">
			<label for="validationTooltip01">Seleccione Reporte Ventas</label>
			<select class="custom-select custom-select-sm" v-on:change="cambioReporte">
				<option selected>Seleccionar</option>
				<option value="1">Reporte de Inventario </option>

			</select>			
		</div>

		<!-- VENTA POR MARCA -->

		<!-- FIN DE VENTA POR MARCA -->
	<transition name="slide-fade">	
		<articulo-marca v-if="reporte === '1'" id="reporte2"></articulo-marca>
	</transition>
		
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
</style><template>
  <div class="container-fluid mt-4">
    <div class="mb-3">
      <label for="validationTooltip01">Seleccione Reporte Ventas</label>
      <select class="custom-select custom-select-sm" v-on:change="cambioReporte">
        <option selected>Seleccionar</option>
        <option value="1">Productos Por Proveedor</option>

      </select>     
    </div>

    <!-- VENTA POR MARCA -->

    <!-- FIN DE VENTA POR MARCA -->
  <transition name="slide-fade">  
    <producto-proveedor v-if="reporte === '1'" id="reporte2"></producto-proveedor>
  </transition>
    
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