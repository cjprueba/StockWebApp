<template>
	<div>
			<div class="text-left"> 
                <label for="validationTooltip01">Góndola</label>
            </div>

            <select multiple class="custom-select custom-select-sm" size="3" v-bind:class="{ 'shadow-sm': shadow }" @input="$emit('input', $event.target.value)" v-on:blur="enviarCodigoPadre($event.target.value)" v-model="seleccionGondola">
                    <option v-for="gondola in gondolas" :selected="gondola.CODIGO === parseInt(value)" :value="gondola.CODIGO">{{ gondola.CODIGO }} - {{ gondola.DESCRIPCION }}</option>
            </select>
			
	</div>	
</template>
<script>
	export default {
      props: {
        'value': String,
        'shadow': Boolean
      },
      data(){
        return {
          	seleccionGondola: [],
            gondolas: []
        }
      }, 
      methods: {
            obtenerGondolas(){

      				// ------------------------------------------------------------------------

      				// LLAMAR FUNCION PARA FILTRAR GONDOLAS

      				Common.obtenerGondolaCommon().then(data => {
      				  this.gondolas = data
      				});

      				// ------------------------------------------------------------------------

      			}, enviarCodigoPadre(valor){

              // ------------------------------------------------------------------------

              // ENVIAR CODIGO

              this.$emit('gondolas_seleccionadas', this.seleccionGondola);

              // ------------------------------------------------------------------------

            }
      },
        mounted() {
        	
        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES 

        	let me = this;
          me.obtenerGondolas();

        	// ------------------------------------------------------------------------
        	
        	 
        }
    }
</script>