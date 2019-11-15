<template>
	<div>
			<div class="text-left"> 
                <label for="validationTooltip01">Código</label>
            </div>

            <input ref="codigo" id="codigo_Producto" class="custom-select custom-select-sm" type="text" list="productos" :value="value" @input="$emit('input', $event.target.value)" v-bind:class="{ 'is-invalid': validarCodigoProducto }" v-on:change="cambiarFormato">

	</div>	
</template>
<script>
	export default {
      props: ['value', 'monedaCodigo', 'candec', 'tab_unica'],
      data(){
        return {
          	validarCodigoProducto: false
        }
      }, 
      methods: {
            cambiarFormato(codigo){

	            // ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;

	            // ------------------------------------------------------------------------

	            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A PRECIO

	            me.value = Common.darFormatoCommon(me.value, me.candec);

	            // ------------------------------------------------------------------------

			}
      },
        mounted() {
        	
        	
        }
    }
</script>