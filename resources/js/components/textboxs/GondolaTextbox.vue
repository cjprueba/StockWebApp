<template>
	<div>
			<div class="text-left"> 
                <label for="validationTooltip01">Góndola</label>
            </div>

            <!-- <select multiple class="custom-select custom-select-sm" size="3" v-bind:class="{ 'shadow-sm': shadow }" @input="$emit('input', $event.target.value)" v-on:blur="enviarCodigoPadre($event.target.value)" v-model="seleccionGondola">
                    <option v-for="gondola in gondolas" :selected="gondola.CODIGO === parseInt(value)" :value="gondola.CODIGO">{{ gondola.CODIGO }} - {{ gondola.DESCRIPCION }}</option>
            </select> -->
			      
            <multiselect @input="$emit('input', seleccionGondola)" v-model="seleccionGondola" :options="gondolas" label="DESCRIPCION" track-by="CODIGO" v-bind:class="{ 'shadow-sm': true }" :multiple="true"></multiselect> 
	</div>	
</template>
<script>
	export default {
      props: {
        'shadow': Boolean
      },
      data(){
        return {
          	seleccionGondola: [],
            gondolas: [{CODIGO: '0', DESCRIPCION: 'SELECCIONE'}] ,
            options: [
              { name: 'Vue.js', code: 'vu' },
              { name: 'Javascript', code: 'js' },
              { name: 'Open Source', code: 'os' }
            ]
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