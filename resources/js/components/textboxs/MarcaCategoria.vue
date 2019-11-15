<template>
	<div>
            <label for="validationTooltip01">Marca</label>
            <select class="custom-select custom-select-sm" v-on:change="llamarPadre($event.target.value)" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_marca }" @input="$emit('input', $event.target.value)">
                    <option :value="null">0 - Seleccionar</option>
                    <option v-for="marca in marcas" :selected="marca.CODIGO === parseInt(value)" :value="marca.CODIGO">{{ marca.CODIGO }} - {{ marca.DESCRIPCION }}</option>
            </select>
			
	</div>	
</template>
<script>
	export default {
      props: {
        'value': String,
        'shadow': Boolean,
        'validar_marca': Boolean
      },
      data(){
        return {
            marcas: []
        }
      }, 
      methods: {
            obtenerMarca(){

      				// ------------------------------------------------------------------------

      				// LLAMAR FUNCION PARA FILTRAR PRODUCTOS

      				Common.obtenerMarcaCommon().then(data => {
      				  this.marcas = data
      				});

      				// ------------------------------------------------------------------------

      			}, llamarPadre(valor){

              // ------------------------------------------------------------------------

              // ENVIAR DESCRIPCION TELA A PADRE

              var seleccion = '';
              seleccion = (Common.filtrarCommon(this.marcas, parseInt(valor)));
              this.$emit('descripcion_marca', seleccion[0].DESCRIPCION);

              // ------------------------------------------------------------------------

            }
      },
        mounted() {
        	
        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES 

        	let me = this;
          me.obtenerMarca();

        	// ------------------------------------------------------------------------
        	
        	 
        }
    }
</script>