<template>
	<div>
            <label for="validationTooltip01">Género</label>
            <select :tabindex="tabIndexPadre" class="custom-select custom-select-sm" v-on:change="llamarPadre($event.target.value)" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_genero }" @input="$emit('input', $event.target.value)">
                    <option :value="null">0 - Seleccionar</option>
                    <option v-for="genero in generos" :selected="genero.CODIGO === parseInt(value)" :value="genero.CODIGO">{{ genero.CODIGO }} - {{ genero.DESCRIPCION }}</option>
            </select>
			
	</div>	
</template>
<script>
	export default {
      props: {
        'value': String,
        'shadow': Boolean,
        'validar_genero': Boolean,
        'tabIndexPadre': Number
      },
      data(){
        return {
            generos: []
        }
      }, 
      methods: {
            obtenerGenero(){

      				// ------------------------------------------------------------------------

      				// LLAMAR FUNCION PARA FILTRAR PRODUCTOS

      				Common.obtenerGeneroCommon().then(data => {
      				  this.generos = data
      				});

      				// ------------------------------------------------------------------------

      			}, llamarPadre(valor){

              // ------------------------------------------------------------------------

              // ENVIAR DESCRIPCION TELA A PADRE

              var seleccion = '';
              seleccion = (Common.filtrarCommon(this.generos, parseInt(valor)));
              this.$emit('descripcion_genero', seleccion[0].DESCRIPCION);

              // ------------------------------------------------------------------------

            }
      },
        mounted() {
        	
        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES 

        	let me = this;
            me.obtenerGenero();

        	// ------------------------------------------------------------------------
        	
        	 
        }
    }
</script>