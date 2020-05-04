<template>
	<div>
			<div class="text-left"> 
                <label for="validationTooltip01">Sub Categoria</label>
            </div>

            <select :tabindex="tabIndexPadre" class="custom-select custom-select-sm" v-on:change="llamarPadre($event.target.value)" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_sub_categoria }" @input="$emit('input', $event.target.value)" :disabled="deshabilitar">
                    <option :value="null">0 - Seleccionar</option>
                    <option v-for="subCategoria in subCategorias" :selected="subCategoria.CODIGO === parseInt(value)" :value="subCategoria.CODIGO">{{ subCategoria.CODIGO }} - {{ subCategoria.DESCRIPCION }}</option>
            </select>
			
	</div>	
</template>
<script>
	export default {
      props: [
              'value',
              'shadow',
              'validar_sub_categoria',
              'deshabilitar',
              'categoria',
              'tabIndexPadre'
            ],
      data(){
        return {
          	seleccionSubCategoria: 'null',
            subCategorias: []
        }
      },
      watch: { 
        categoria: function(newVal, oldVal) { 
          if(newVal !== '') {
            this.obtenerSubCategorias(newVal);
          }
        }
      }, 
      methods: {
            obtenerSubCategorias(categoria){

      				// ------------------------------------------------------------------------

      				// LLAMAR LAS SUB CATEGORIAS

      				Common.obtenerSubCategoriaCommon(categoria).then(data => {
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

        	// ------------------------------------------------------------------------
        	
        	 
        }
    }
</script>