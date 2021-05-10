<template>
	<div>
			<div class="text-left"> 
                <label for="validationTooltip01">Góndola</label>
            </div>

            <!-- <select multiple class="custom-select custom-select-sm" size="3" v-bind:class="{ 'shadow-sm': shadow }" @input="$emit('input', $event.target.value)" v-on:blur="enviarCodigoPadre($event.target.value)" v-model="seleccionGondola">
                    <option v-for="gondola in gondolas" :selected="gondola.CODIGO === parseInt(value)" :value="gondola.CODIGO">{{ gondola.CODIGO }} - {{ gondola.DESCRIPCION }}</option>
            </select> -->
			      
            <multiselect :tabindex="tabIndexPadre" @input="$emit('input', seleccionGondola)" v-model="seleccionGondola" :options="gondolas" label="DESCRIPCION" track-by="ID" v-bind:class="{ 'shadow-sm': true }" :multiple="true"></multiselect> 
	</div>	
</template>
<script>
	export default {
      props: ['shadow', 'selecciones', 'tabIndexPadre'],
      data(){
        return {
          	seleccionGondola: [],
            gondolas: [{ID: '0', CODIGO: '0', DESCRIPCION: 'SELECCIONE'}]
        }
      }, watch: { 
        selecciones: function(newVal, oldVal) { // watch it
          // console.log('Prop changed: ', newVal, ' | was: ', oldVal)
            this.seleccionGondola = newVal;
            console.log(this.selecciones);
        }
      },
      methods: {
            obtenerGondolas(){

      				// ------------------------------------------------------------------------

      				// LLAMAR FUNCION PARA FILTRAR GONDOLAS

      				Common.obtenerGondolaCommon().then(data => {
      				  console.log(data);
                this.gondolas = data;
      				});

      				// ------------------------------------------------------------------------

      			},
            limpiar(){
              this.seleccionGondola=null;
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