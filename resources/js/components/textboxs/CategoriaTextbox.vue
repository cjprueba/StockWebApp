<template>
	<div>
			<div class="text-left"> 
                <label for="validationTooltip01">Categoria</label>
            </div>

            <select class="custom-select custom-select-sm" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_categoria }" @input="$emit('input', $event.target.value)" v-on:change="enviarOpcionesPadre($event.target.value)">
                    <option :value="null">0 - Seleccionar</option>
                    <option v-for="categoria in categorias" :selected="categoria.CODIGO === parseInt(value)" :value="categoria.CODIGO">{{ categoria.CODIGO }} - {{ categoria.DESCRIPCION }}</option>
            </select>
			
	</div>	
</template>
<script>
	export default {
      props: {
        'value': String,
        'shadow': Boolean,
        'validar_categoria': Boolean
      },
      data(){
        return {
          	seleccionCategoria: 'null',
            categorias: []
        }
      },
      watch: { 
        value: function(newVal, oldVal) { 
            this.enviarOpcionesPadre(newVal);
        }
      }, 
      methods: {
            obtenerCategorias(){

      				// ------------------------------------------------------------------------

      				// LLAMAR FUNCION PARA FILTRAR PRODUCTOS

      				Common.obtenerCategoriasCommon().then(data => {
      				  this.categorias = data
      				});

      				// ------------------------------------------------------------------------

      			},filtrar: function(codigo) {
                        return this.categorias.filter(function(item) {
                        return item.CODIGO === codigo;
                      })
            }, enviarOpcionesPadre(valor){
              var seleccion = '';
              seleccion = (this.filtrar(parseInt(valor)));
              console.log(seleccion);
              this.$emit('opciones', seleccion);
            }
      },
        mounted() {
        	
        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES 

        	let me = this;
            me.obtenerCategorias();

        	// ------------------------------------------------------------------------
        	
        	 
        }
    }
</script>