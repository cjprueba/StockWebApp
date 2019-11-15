<template>
	<div>
            <label for="validationTooltip01">Color</label>
            <select class="custom-select custom-select-sm" v-on:change="llamarPadre($event.target.value)" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_color }" @input="$emit('input', $event.target.value)">
                    <option :value="null">0 - Seleccionar</option>
                    <option v-for="color in colores" :selected="color.CODIGO === parseInt(value)" :value="color.CODIGO">{{ color.CODIGO }} - {{ color.DESCRIPCION }}</option>
            </select>
			
	</div>	
</template>
<script>
	export default {
      props: {
        'value': String,
        'shadow': Boolean,
        'validar_color': Boolean
      },
      data(){
        return {
            colores: []
        }
      }, 
      methods: {
            obtenerColor(){

      				// ------------------------------------------------------------------------

      				// LLAMAR FUNCION PARA FILTRAR PRODUCTOS

      				Common.obtenerColorCommon().then(data => {
      				  this.colores = data
      				});

      				// ------------------------------------------------------------------------

      			},llamarPadre(valor){

              // ------------------------------------------------------------------------

              // LLAMAR PADRE
              
              this.$emit('cambiar_codigo');

              // ------------------------------------------------------------------------

              // ENVIAR DESCRIPCION COLOR 

              var seleccion = '';
              seleccion = (Common.filtrarCommon(this.colores, parseInt(valor)));
              this.$emit('descripcion_color', seleccion[0].DESCRIPCION);

              // ------------------------------------------------------------------------

            }
      },
        mounted() {
        	
        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES 

        	let me = this;
            me.obtenerColor();

        	// ------------------------------------------------------------------------
        	
        	 
        }
    }
</script>