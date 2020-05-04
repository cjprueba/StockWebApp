<template>
	<div>
            <label for="validationTooltip01">Marca</label>
            <select :tabindex="tabIndexPadre" class="custom-select custom-select-sm" v-on:change="llamarPadre($event.target.value)" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_marca }" @input="$emit('input', $event.target.value)" :disabled="deshabilitar">
                    <option :value="null">0 - Seleccionar</option>
                    <option v-for="marca in marcas" :selected="marca.CODIGO === parseInt(value)" :value="marca.CODIGO">{{ marca.CODIGO }} - {{ marca.DESCRIPCION }}</option>
            </select>
			
	</div>	
</template>
<script>
	export default {
      props: [
              'value',
              'shadow',
              'validar_marca',
              'categoria',
              'deshabilitar',
              'tabIndexPadre'
            ],
      data(){
        return {
            marcas: []
        }
      },
      watch: { 
        categoria: function(newVal, oldVal) { 
            this.obtenerMarca(newVal);
        },
        value: function(newVal, oldVal) {
            alert("entre");
            Common.obtenerMarcaCommon(this.categoria).then(data => {
                this.marcas = data
                this.llamarPadre(newVal);
            });

        }
      }, 
      methods: {
            obtenerMarca(categoria){

      				// ------------------------------------------------------------------------

      				// LLAMAR FUNCION PARA FILTRAR PRODUCTOS

      				Common.obtenerMarcaCommon(categoria).then(data => {
      				  this.marcas = data
                console.log(this.marcas);
                this.llamarPadre(this.value);
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

        	// ------------------------------------------------------------------------
        	
        	 
        }
    }
</script>