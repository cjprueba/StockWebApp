
<template>
	
<div class="container-fluid">
  
  <!-- ------------------------------------------------------------------------ -->

  <!-- TITULO  -->
  
  <div class="row"> 

    <!-- ------------------------------------------------------------------------ -->

    <div class="col-md-12 mt-3">
        <div class="section-title">
            <h4>Crear Cotizacion</h4>
            <p>Inserte la cotización que desea registrar.</p>
        </div>
    </div>

    <!-- ------------------------------------------------------------------------ -->

    <div class="col-sm-6">
      <select-moneda v-model="cotizacion.de"  :validar_moneda="validar.moneda"></select-moneda>
    </div>

    <div class="col-sm-6">
        <select-moneda v-model="cotizacion.a" :validar_moneda="validar.moneda"></select-moneda>
    </div>

    <!-- ------------------------------------------------------------------------ -->

    <div class="col-sm-6 mt-3">
        <label for="validationTooltip01">Fecha</label>
        <div class="input-group input-group-sm date">
            <div class="input-group-prepend ">
                <span class="input-group-text" id="inputGroup-sizing-sm"><font-awesome-icon icon="calendar" /></span>
            </div>
          <input tabindex="2" v-bind:class="{ 'is-invalid': validar.fecha }" type="text" class="input-sm form-control form-control-sm" id="fecha" v-model="cotizacion.fecha" data-date-format="yyyy-mm-dd"/>
        </div>
    </div>

    <!-- ------------------------------------------------------------------------ -->

    <div class="col-sm-6 mt-3">
        <label for="validationTooltip01">Valor</label>
        <input class="form-control form-control-sm" type="text" v-bind:class="{ 'is-invalid': validar.valor }"  v-model="cotizacion.valor"  v-on:blur="formatoValor">
    </div>

    <!-- ------------------------------------------------------------------------ -->

    <div class="col-md-12 text-right mt-3"> 
        <button v-on:click="guardarCotizacion()" class="btn btn-primary btn-sm" id="guardar">Guardar</button>
    </div>

  </div> 

</div>

</template>
<script>
	 export default {
      props: [''],
      data(){
        return {
            cantidadDecimal: '',
            cotizacion: {
              de: '1',
              a: '1',
              valor: '',
              fecha: ''
            },
            validar: {
              fecha: false,
              valor: false,
              moneda: false
            }
        }
      }, 
      methods: {
          formatoValor(){

            // ------------------------------------------------------------------------

            // INICIAR VARIABLES 

            let me = this;

            // ------------------------------------------------------------------------

            // DAR FORMATO A CANTIDAD
            
            me.cotizacion.valor = Common.darFormatoCommon(me.cotizacion.valor, 2);

            // ------------------------------------------------------------------------

          },
          controlador(){

            // ------------------------------------------------------------------------

            var falta = false;

            if (this.cotizacion.fecha.length === 0) {
                this.validar.fecha = true;
                falta = true;
            } else {
                this.validar.fecha = false;
            }

            if (this.cotizacion.valor.length === 0) {
                this.validar.valor = true;
                falta = true;
            } else {
                this.validar.valor = false;
            }

            if (this.cotizacion.a === this.cotizacion.de) {
                this.validar.moneda = true;
                falta = true;
            } else {
                this.validar.moneda = false;
            }

            // ------------------------------------------------------------------------

            return falta;

            // ------------------------------------------------------------------------

          }, limpiar() {

            // ------------------------------------------------------------------------

            this.cotizacion = {
              de: '1',
              a: '1',
              valor: '',
              fecha: ''
            }

            // ------------------------------------------------------------------------

          },
          guardarCotizacion(){

            // ------------------------------------------------------------------------
            
            let me = this;

            // ------------------------------------------------------------------------

            if (this.controlador() === true){
              return;
            }

            // ------------------------------------------------------------------------

            // SWEET ALERT

            Swal.fire({
            title: 'Estas seguro ?',
            text: "Guardar la cotización !",
            type: 'warning',
            showLoaderOnConfirm: true,
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Si, guardalo!',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                return Common.guardarNuevaCotizacionCommon(me.cotizacion).then(data => {
                  if (!data.response === true) {
                      throw new Error(data.statusText);
                    }
                  return data;
                }).catch(error => {
                    Swal.showValidationMessage(
                      `Request failed: ${error}`
                    )
                });
            }
            }).then((result) => {

            if (result.value.response) {

                Swal.fire(
                  'Guardado!',
                  'Se ha guardado correctamente la cotización !',
                  'success'
                )

                // ------------------------------------------------------------------------

                me.limpiar();
                
                // REDIRIGIR A MOSTRAR COMPRAS
                    
                me.$router.push('/ct4');

                // ------------------------------------------------------------------------

              }
            })

            // ------------------------------------------------------------------------

          }
         
      },
        mounted() {

          let me = this;

          $("#fecha").datepicker().on(
              "changeDate", () => {
                me.cotizacion.fecha = $('#fecha').val();
              }
          );

          

        }
    }
</script>