<template>
	<div>
			<div class="text-left"> 
                <label for="validationTooltip01">Sucursal</label>
            </div>

            <!-- <select multiple class="custom-select custom-select-sm" size="3" v-bind:class="{ 'shadow-sm': shadow }" @input="$emit('input', $event.target.value)" v-on:blur="enviarCodigoPadre($event.target.value)" v-model="seleccionSucursal">
                    <option v-for="gondola in sucursales" :selected="gondola.CODIGO === parseInt(value)" :value="gondola.CODIGO">{{ gondola.CODIGO }} - {{ gondola.DESCRIPCION }}</option>
            </select> -->
			      

            <multiselect :tabindex="tabIndexPadre" @input="$emit('input', seleccionSucursal)" v-model="seleccionSucursal" :options="sucursales" label="DESCRIPCION" track-by="ID" v-bind:class="{ 'shadow-sm': true }" :multiple="false"></multiselect> 
	</div>	
</template>
<script>
	export default {
      props: ['shadow', 'seleccion', 'tabIndexPadre'],
      data(){
        return {
          	seleccionSucursal: [],
            sucursales: [{ID: '0', DESCRIPCION: 'SELECCIONE'}]
        }
      }, watch: { 
        seleccion: function(newVal, oldVal) { // watch it
          // console.log('Prop changed: ', newVal, ' | was: ', oldVal)
            this.seleccionSucursal = newVal;
        }
      },
      methods: {
            obtenersucursales(){

      				// ------------------------------------------------------------------------

      				// LLAMAR FUNCION PARA FILTRAR sucursales
      				Common.obtenerSucursalCommon().then(data => {
      				  this.sucursales = data.sucursal;
                this.seleccionSucursal=data.sucursal_actual;

      				});
              

      				// ------------------------------------------------------------------------

      			},
            limpiar(){
              this.seleccionSucursal=null;
            }
      },
        mounted() {
        	
        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES 

        	let me = this;
          me.obtenersucursales();

        	// ------------------------------------------------------------------------
        	
        	 
        }
    }
</script>