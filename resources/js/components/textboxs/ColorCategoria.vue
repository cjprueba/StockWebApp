<template>
	<div>
            <label for="validationTooltip01">Color</label>
            <select :tabindex="tabIndexPadre" class="custom-select custom-select-sm" v-on:change="llamarPadre($event.target.value)" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_color }" @input="$emit('input', $event.target.value)" :disabled="deshabilitar">
                    <option :value="null">Seleccionar</option>
                    <option v-for="color in colores" :selected="color.CODIGO === parseInt(value)" :value="color.CODIGO">{{ color.DESCRIPCION }}</option>
            </select>
			
	</div>	
</template>
<script>
	export default {
      props: {
        'value': String,
        'shadow': Boolean,
        'deshabilitar': Boolean,
        'validar_color': Boolean,
        'tabIndexPadre': Number
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