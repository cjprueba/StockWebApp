<template>
	<div>
			<div class="text-left"> 
                <label for="validationTooltip01">Proveedor</label>
            </div>

            <select class="custom-select custom-select-sm" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_proveedor }" @input="$emit('input', $event.target.value)">
                    <option :value="null">0 - Seleccionar</option>
                    <option v-for="proveedor in proveedores" :selected="proveedor.CODIGO === parseInt(value)" :value="proveedor.CODIGO">{{ proveedor.CODIGO }} - {{ proveedor.DESCRIPCION }}</option>
            </select>
	</div>	
</template>
<script>
	export default {
      props: {
        'value': String,
        'shadow': Boolean,
        'validar_proveedor': Boolean
      },
      data(){
        return {
          	seleccionProveedor: 'null',
            proveedores: []
        }
      }, 
      methods: {
            obtenerProveedores(){

      				// ------------------------------------------------------------------------

      				// LLAMAR FUNCION PARA FILTRAR PROVEEDORES

      				Common.obtenerProveedorCommon().then(data => {
      				  this.proveedores = data
      				});

      				// ------------------------------------------------------------------------

      			}
      },
        mounted() {
        	
        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES 

        	let me = this;
          	me.obtenerProveedores();

        	// ------------------------------------------------------------------------
        	
        	 
        }
    }
</script>