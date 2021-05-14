<template>
	<div class="container-fluid mt-4">

		<!-- ----v-if="$can('reporte.venta')"-------------------------------------------------------------------- -->

		<div>
			
			<div class="mb-3">
				<label for="validationTooltip01">Seleccione Reporte Transferencia</label>
				<select v-model="reporte" class="custom-select custom-select-sm" >
					<option value="0" selected>Seleccionar</option>
          <option v-if="$can('reporte.transferencia.marcaycat') && $can('reporte.web')" value="2">Transferencias por Marca y Categoria</option>
					<option v-if="$can('reporte.transferencia.ventasconsig') && $can('reporte.web')" value="1">Transferencias por Ventas a Consignación</option>  
				</select>			
			</div>

			<!-- TRANSFERENCIA POR CONSIGNACION -->

			<transition name="slide-fade">	
				<transferencia-consignacion-rpt v-if="reporte === '1'" id="reporte1"></transferencia-consignacion-rpt>
			</transition>
      <transition name="slide-fade">  
        <transferencia-marca-categoria v-if="reporte === '2'" id="reporte2"></transferencia-marca-categoria>
      </transition>

			<!-- FIN DE TRANSFERENCIA POR CONSIGNACION -->
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