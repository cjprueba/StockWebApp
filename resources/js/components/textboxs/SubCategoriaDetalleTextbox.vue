<template>
	<div v-if="activo">
			<div class="text-left"> 
                <label for="validationTooltip01">Nombre</label>
            </div>

            <select :tabindex="tabIndexPadre" class="custom-select custom-select-sm" v-on:change="llamarPadre($event.target.value)" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_sub_categoria_detalle }" @input="$emit('input', $event.target.value)" :disabled="deshabilitar">
                    <option :value="null">0 - Seleccionar</option>
                    <option v-for="subCategoria in subCategoriasDetalle" :selected="subCategoria.CODIGO === parseInt(value)" :value="subCategoria.CODIGO">{{ subCategoria.CODIGO }} - {{ subCategoria.DESCRIPCION }}</option>
            </select>
			
	</div>	
</template>
<script>
	export default {
      props: [
              'value',
              'shadow',
              'validar_sub_categoria_detalle',
              'deshabilitar',
              'tabIndexPadre'
            ],
      data(){
        return {
          	seleccionSubCategoria: 'null',
            subCategoriasDetalle: [],
            activo: false
        }
      }, 
      methods: {
            obtenerSubCategoriasDetalle(){

      				// ------------------------------------------------------------------------

      				// LLAMAR LAS SUB CATEGORIAS

      				Common.obtenerSubCategoriaDetalleCommon().then(data => {
      				  this.subCategoriasDetalle = data.subCategoriasDetalle;
      				  this.activo = data.ACTIVO;
      				});

      				// ------------------------------------------------------------------------

      		},llamarPadre(valor){

              // ------------------------------------------------------------------------

              // ENVIAR DESCRIPCION TELA A PADRE

              var seleccion = '';
              seleccion = (Common.filtrarCommon(this.subCategoriasDetalle, parseInt(valor)));
              this.$emit('descripcion_sub_categoria_detalle', seleccion[0].DESCRIPCION);

              // ------------------------------------------------------------------------

            }
      },
        mounted() {
        	
        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES 

        	let me = this;
        	this.obtenerSubCategoriasDetalle();

        	// ------------------------------------------------------------------------
        	
        	 
        }
    }
</script>