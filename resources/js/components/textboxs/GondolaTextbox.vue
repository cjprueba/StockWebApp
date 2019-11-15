<template>
	<div>
			<div class="text-left"> 
                <label for="validationTooltip01">Góndola</label>
            </div>

            <select class="custom-select custom-select-sm" v-bind:class="{ 'shadow-sm': shadow }" @input="$emit('input', $event.target.value)">
                    <option :value="null">0 - Seleccionar</option>
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
          	seleccionGondola: 'null',
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