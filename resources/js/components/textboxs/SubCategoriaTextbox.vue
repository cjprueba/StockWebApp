<template>
	<div>
			<div class="text-left"> 
                <label for="validationTooltip01">Sub Categoria</label>
            </div>

            <select class="custom-select custom-select-sm" v-on:change="llamarPadre($event.target.value)" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_sub_categoria }" @input="$emit('input', $event.target.value)">
                    <option :value="null">0 - Seleccionar</option>
                    <option v-for="subCategoria in subCategorias" :selected="subCategoria.CODIGO === parseInt(value)" :value="subCategoria.CODIGO">{{ subCategoria.CODIGO }} - {{ subCategoria.DESCRIPCION }}</option>
            </select>
			
	</div>	
</template>
<script>
	export default {
      props: {
        'value': String,
        'shadow': Boolean,
        'validar_sub_categoria': Boolean
      },
      data(){
        return {
          	seleccionSubCategoria: 'null',
            subCategorias: []
        }
      }, 
      methods: {
            obtenerSubCategorias(){

      				// ------------------------------------------------------------------------

      				// LLAMAR LAS SUB CATEGORIAS

      				Common.obtenerSubCategoriaCommon().then(data => {
      				  this.subCategorias = data
      				});

      				// ------------------------------------------------------------------------

      			},llamarPadre(valor){

              // ------------------------------------------------------------------------

              // ENVIAR DESCRIPCION TELA A PADRE

              var seleccion = '';
              seleccion = (Common.filtrarCommon(this.subCategorias, parseInt(valor)));
              this.$emit('descripcion_sub_categoria', seleccion[0].DESCRIPCION);

              // ------------------------------------------------------------------------

            }
      },
        mounted() {
        	
        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES 

        	let me = this;
            me.obtenerSubCategorias();

        	// ------------------------------------------------------------------------
        	
        	 
        }
    }
</script>