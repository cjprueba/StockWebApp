<template>
	<div>
		<div class="container-fluid">
		  <div class="modal fade bg-danger" id="modalAutorizacion" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			  <div class="modal-dialog modal-lg " role="document">
			    <div class="modal-content">
			      <div class="modal-header text-center">
			        <h5 class="modal-title text-primary" id="exampleModalLabel"><small>AUTORIZACION</small></h5>
			      </div>
			      <div class="modal-body">
			      	<label>{{validar.informacion}}</label>
			        <input type="password" v-bind:class="{ 'is-invalid': validar.autorizacion }" v-model="autorizacion.CODIGO" class="form-control form-control-xl" name="" v-on:keyup.13="revisar">
			      </div>
			    </div>
			  </div>
			</div>
		</div>
	</div>
</template>
<script>
	 export default {
	  props: [''],
      data(){
        return {
          open: false,
          codigo_compra: '',
          autorizacion: {
          	CODIGO: '',
          	ID_USUARIO: ''
          },
          validar: {
          	informacion: '',
          	autorizacion: false
          }
        }
      }, 
      methods: {
      		mostrarModal(){

      			// ------------------------------------------------------------------------

      			// LLAMAR MODAL TRANSFERENCIA PRODUCTOS

      			$('#modalAutorizacion').modal('show');

      			// ------------------------------------------------------------------------
            	
            }, 
            enviarOpcionesPadre(data){

            	// ------------------------------------------------------------------------

              	this.$emit('data', data);

              	// ------------------------------------------------------------------------

            },
            revisar(){

            	// ------------------------------------------------------------------------

            	let me = this;

            	// ------------------------------------------------------------------------

            	// OBTENER DATOS DE LA CLAVE 

            	Common.obtenerAutorizacion(me.autorizacion.CODIGO).then(data => {

            		if (data.response === false) {

            			me.validar.autorizacion = true;
            			me.validar.informacion = data.statusText;

            			me.enviarOpcionesPadre({response: false});

            		} else if (data.response === true) {

            			me.enviarOpcionesPadre({id_user_supervisor: data.autorizacion[0].ID, usuario: data.autorizacion[0].FK_USER, response: true});
            			$('#modalAutorizacion').modal('hide');
            		}

            	});

            	// ------------------------------------------------------------------------

            }
      },
        mounted() {

        	// ------------------------------------------------------------------------

            let me = this;

            // ------------------------------------------------------------------------

            // PREPARAR DATATABLE 

	 		

    		// ------------------------------------------------------------------------

        }
    }
</script>