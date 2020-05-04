<template>
	<div>
            <label for="validationTooltip01">Talle</label>
            <select :tabindex="tabIndexPadre" class="custom-select custom-select-sm" v-on:change="llamarPadre($event.target.value)" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_talle }" @input="$emit('input', $event.target.value)">
                    <option :value="null">0 - Seleccionar</option>
                    <option v-for="talle in talles" :selected="talle.CODIGO === parseInt(value)" :value="talle.CODIGO">{{ talle.CODIGO }} - {{ talle.DESCRIPCION }}</option>
            </select>
			
	</div>	
</template>
<script>
	export default {
      props: {
        'value': String,
        'shadow': Boolean,
        'validar_talle': Boolean,
        'tabIndexPadre': Number
      },
      data(){
        return {
            talles: []
        }
      }, 
      methods: {
            obtenerTalle(){

      				// ------------------------------------------------------------------------

      				// LLAMAR FUNCION PARA FILTRAR PRODUCTOS

      				Common.obtenerTalleCommon().then(data => {
      				  this.talles = data
      				});

      				// ------------------------------------------------------------------------

      			}, llamarPadre(valor){

              // ------------------------------------------------------------------------

              // LLAMAR PADRE

              this.$emit('cambiar_codigo');

              // ------------------------------------------------------------------------

              // ENVIAR DESCRIPCION TELA A PADRE

              var seleccion = '';
              seleccion = (Common.filtrarCommon(this.talles, parseInt(valor)));
              this.$emit('descripcion_talle', seleccion[0].DESCRIPCION);

              // ------------------------------------------------------------------------

            }
      },
        mounted() {
        	
        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES 

        	let me = this;
          me.obtenerTalle();

        	// ------------------------------------------------------------------------
        	
        	 
        }
    }
</script>