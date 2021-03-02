<template>
		<div>
			<b-modal v-model="open">
				
				<div class="row">
					<div class="col-md-12">
						<b-form-group label="Seleccione Moneda" 
					      	class="text-center">
					      <b-form-radio-group
					        id="btn-radios-1"
					        v-model="selected"
					        :options="options"
					        buttons
					        name="radios-btn-default"
					      ></b-form-radio-group>
					    </b-form-group>
				    </div>
				</div>

			    <template v-slot:modal-footer="{ ok, cancel}">
			      <b-button size="sm" variant="success" @click="seleccionarMoneda()">
			        Enviar
			      </b-button>
			      <b-button size="sm" variant="secondary" @click="mostrarModal()">
			        Cancelar
			      </b-button>
    			</template>

			</b-modal>
			

		</div>
</template>
<script>
	 export default {
	  props: ['moneda_codigo'],
      data(){
        return {
          open: false,
          selected: this.moneda_codigo,
          dolares: '',
          options: [
	          { text: 'Guaranies', value: 1 },
	          { text: 'Dolares ', value: 2 },
	          { text: 'Pesos', value: 3 },
	          { text: 'Reales', value: 4 }
          ],
          cotizacion: ''
        }
      }, 
      methods: {
      		mostrarModal(){
            	this.open = !this.open;
            	if (this.open === true) {
            		this.obtenerCotizacion();
            	}
            },
            seleccionarMoneda(){
            	this.$emit('moneda_enviar', {selected: this.selected, cotizacion: this.cotizacion});
            	this.mostrarModal();
            }, 
            obtenerCotizacion(){

            	// ------------------------------------------------------------------------

            	let me = this;

            	// ------------------------------------------------------------------------

            	// Buscar Cotizacion de las monedas

            	Common.obtenerCotizacionDia().then(data => {

            		me.options = [
				          { text: 'Guaranies', value: 1, disabled: data.activar_guaranies },
				          { text: ('Dolares: '+ data.Dolares), value: 2, disabled: data.activar_dolares },
				          { text: 'Pesos', value: 3, disabled: data.activar_pesos },
				          { text: 'Reales', value: 4, disabled: data.activar_reales }
			        ];

			        me.cotizacion = data;

            	});

            	// ------------------------------------------------------------------------
            }
      },
        mounted() {
           
        }
    }
</script>
