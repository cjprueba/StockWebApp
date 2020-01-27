<template>
	<div>
			<div class="text-left"> 
                <label for="validationTooltip01">Proveedor</label>
            </div>

            <select class="custom-select custom-select-sm" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_proveedor }" @input="$emit('input', $event.target.value)" :disabled="deshabilitar">
                    <option :value="null">SELECCIONAR</option>
                    <option v-for="proveedor in proveedores" :selected="proveedor.CODIGO === parseInt(value)" :value="proveedor.CODIGO">{{ proveedor.DESCRIPCION }}</option>
            </select>

            <!-- <v-select :options="proveedores" label="DESCRIPCION" track-by="CODIGO"></v-select> -->
           <!--  <multiselect :option-height="10" @input="$emit('input', seleccionGondola)" v-model="value" :options="proveedores" label="DESCRIPCION" track-by="CODIGO" v-bind:class="{ 'shadow-sm': true }" :multiple="false"></multiselect>  -->
	</div>	
</template>
<script>
	export default {
      props: {
        'value': String,
        'shadow': Boolean,
        'validar_proveedor': Boolean,
        'deshabilitar': Boolean
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