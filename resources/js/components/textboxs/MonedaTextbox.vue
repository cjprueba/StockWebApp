<template>
	<div>
			<div class="text-left"> 
                <label for="validationTooltip01">Moneda</label>
            </div>

            <select class="custom-select custom-select-sm" v-bind:class="{ 'shadow-sm': shadow , 'is-invalid': validar_moneda }" @input="$emit('input', $event.target.value)" v-on:change="enviarDescripcionPadre($event.target.value)">
                    <option v-for="moneda in monedas" :selected="moneda.CODIGO === parseInt(value)" :value="moneda.CODIGO">{{ moneda.CODIGO }} - {{ moneda.DESCRIPCION }}</option>
            </select>
			
	</div>	
</template>
<script>
	export default {
      props: {
        'value': String,
        'shadow': Boolean,
        'validar_moneda': Boolean
      },
      data(){
        return {
          	seleccionMoneda: 'null',
            monedas: []
        }
      },
      watch: { 
        value: function(newVal, oldVal) { // watch it
          // console.log('Prop changed: ', newVal, ' | was: ', oldVal)
            this.enviarDescripcionPadre(newVal);
        }
      }, 
      methods: {
            obtenerMonedas(){

      				// ------------------------------------------------------------------------

      				// LLAMAR FUNCION PARA FILTRAR MONEDAS

      				Common.obtenerMonedaCommon().then(data => {
      				  this.monedas = data
                this.enviarDescripcionPadre(this.value);
      				});

      				// ------------------------------------------------------------------------

              // ENVIAR DATOS A PADRE 

              

              // ------------------------------------------------------------------------

      			},filtrar: function(codigo) {
                  return this.monedas.filter(function(item) {
                  return item.CODIGO === codigo;
                })
             },
            enviarDescripcionPadre(valor){
              var seleccion = '';
              seleccion = (this.filtrar(parseInt(valor)));
              if (seleccion.length > 0) {
                  this.$emit('descripcion_moneda', seleccion[0].DESCRIPCION);
                  this.$emit('cantidad_decimales', seleccion[0].CANDEC);
              }
            }
      },
        mounted() {
        	
        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES 

        	let me = this;
          me.obtenerMonedas();

        	// ------------------------------------------------------------------------
        	
        	 
        }
    }
</script>