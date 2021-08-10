<template>

				<!-- ------------------------------------------------------------------------------------- -->

				<!-- CARD INVENTARIO -->

				<div>
					<label for="exampleFormControlTextarea1">Sucursal</label>
					<select class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSucursal }" v-model="selectedSucursal" v-on:change="seleccionarSucursal">
						<option value="null" selected>Seleccionar</option>
						<option v-for="sucursal in sucursales" :value="sucursal.CODIGO"> {{ sucursal.CODIGO }} - {{ sucursal.DESCRIPCION }}</option>
					</select>
				</div>

			   <!-- ------------------------------------------------------------------------------------- -->

			</div>

		</div>
	</div>
</template>

<script>
	export default {
      props: [''],
      data(){
        return {
          sucursales : [],
          validarSucursal: false,
          selectedSucursal: 'null'
        }
      }, 
      methods: {
            obtenerSucursales(){

            	// ------------------------------------------------------------------------

            	// INICIAR VARIABLES

            	let me = this;

            	// ------------------------------------------------------------------------

           		Common.obtenerSucursalesInventarioCommon().then(data => {
						  me.sucursales = data
				});

				// ------------------------------------------------------------------------
           }, seleccionarSucursal() {

           		// ------------------------------------------------------------------------

           		// ENVIAR AL COMPONENTE PADRE SUCURSAL SELECCIONADA 

           		this.$emit('sucursal_seleccionada', this.selectedSucursal);

           		// ------------------------------------------------------------------------
           }
      },
        mounted() {
        	this.obtenerSucursales();
        }
    }
</script>