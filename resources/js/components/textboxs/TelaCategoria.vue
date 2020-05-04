<template>
	<div>
            <label for="validationTooltip01">Tela</label>
            <select class="custom-select custom-select-sm" v-on:change="llamarPadre($event.target.value)" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_tela }" @input="$emit('input', $event.target.value)" :tabindex="tabIndexPadre">
                    <option :value="null">0 - Seleccionar</option>
                    <option v-for="tela in telas" :selected="tela.CODIGO === parseInt(value)" :value="tela.CODIGO">{{ tela.CODIGO }} - {{ tela.DESCRIPCION }}</option>
            </select>
			
	</div>	
</template>
<script>
	export default {
      props: {
        'value': String,
        'shadow': Boolean,
        'validar_tela': Boolean,
        'deshabilitar': Boolean,
        'tabIndexPadre': Number
      },
      data(){
        return {
            telas: []
        }
      }, 
      methods: {
            obtenerTela(){

      				// ------------------------------------------------------------------------

      				// LLAMAR FUNCION PARA FILTRAR PRODUCTOS

      				Common.obtenerTelaCommon().then(data => {
      				  this.telas = data
      				});

      				// ------------------------------------------------------------------------

      			},llamarPadre(valor){

              // ------------------------------------------------------------------------

              // ENVIAR DESCRIPCION TELA A PADRE

              var seleccion = '';
              seleccion = (Common.filtrarCommon(this.telas, parseInt(valor)));
              this.$emit('descripcion_tela', seleccion[0].DESCRIPCION);

              // ------------------------------------------------------------------------

            }
      },
        mounted() {
        	
        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES 

        	let me = this;
            me.obtenerTela();

        	// ------------------------------------------------------------------------
        	
        	 
        }
    }
</script>