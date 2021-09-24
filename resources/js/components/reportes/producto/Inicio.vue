<template>
	<div class="container-fluid mt-4">

		<!-- ----v-if="$can('reporte.venta')"-------------------------------------------------------------------- -->

		<div>
			
			<div class="mb-3">
				<label for="validationTooltip01">Seleccione Reporte de Productos</label>
				<select v-model="reporte" class="custom-select custom-select-sm" >
					<option value="0" selected>Seleccionar</option>
          <option  v-if="$can('movimientos.salidadeproducto') && $can('reporte.web')" value="1">Salida de productos</option>
          <option  v-if="$can('reporte.productos.stockprod') && $can('reporte.web')" value="2">Stock de productos</option>
					<option  v-if="$can('reporte.productos.vencimientoprod') && $can('reporte.web')" value="3">Vencimiento de productos</option>
          <option  v-if="$can('reporte.productos.inventario') && $can('reporte.web')" value="4">Inventario de productos</option>
          <option  v-if="$can('reporte.productos.gondola') && $can('reporte.web')" value="5">Productos En Gondola</option>
          
				</select>			
			</div>

			<!-- REPORTES POR PRODUCTO -->

      <transition name="slide-fade">  
        <productos-salida-rpt v-if="reporte === '1'" id="reporte1"></productos-salida-rpt>
      </transition>

      <transition name="slide-fade">  
        <productos-stock-rpt v-if="reporte === '2'" id="reporte2"></productos-stock-rpt>
      </transition>

			<transition name="slide-fade">	
				<productos-vencidos-rpt v-if="reporte === '3'" id="reporte3"></productos-vencidos-rpt>
			</transition>
        <transition name="slide-fade">  
        <inventario-seccion-rpt v-if="reporte === '4'" id="reporte4"></inventario-seccion-rpt>
      </transition>
      <transition name="slide-fade">  
        <gondola-producto-rpt v-if="reporte === '5'" id="reporte5"></gondola-producto-rpt>
      </transition>
      
			<!-- FIN DE REPORTES POR PRODUCTO -->
		</div>

		<!-- ------------------------------------------------------------------------ -->

	   	<!-- <div v-else>
	      <cuatrocientos-cuatro></cuatrocientos-cuatro>
    	</div> -->

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